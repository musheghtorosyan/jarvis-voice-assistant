<input type="file" id="voiceInput" accept="audio/*" style="display:none;">
<button id="recordButton">Record</button>
<button id="stopButton" disabled>Stop</button>
<button id="sendButton" disabled>Send</button>

<div id="chatMessages"></div>

<script>
    const recordButton = document.getElementById('recordButton');
    const stopButton = document.getElementById('stopButton');
    const sendButton = document.getElementById('sendButton');
    const chatMessages = document.getElementById('chatMessages');

    let mediaRecorder;
    let recordedChunks = [];
    let currentBlob; // текущая запись

    recordButton.addEventListener('click', startRecording);
    stopButton.addEventListener('click', stopRecording);
    sendButton.addEventListener('click', sendVoiceMessage);

    function startRecording() {
        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                mediaRecorder = new MediaRecorder(stream);
                recordedChunks = [];
                mediaRecorder.start();

                mediaRecorder.addEventListener('dataavailable', event => {
                    if (event.data.size > 0) recordedChunks.push(event.data);
                });

                mediaRecorder.addEventListener('stop', () => {
                    currentBlob = new Blob(recordedChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(currentBlob);

                    const audioElement = document.createElement('audio');
                    audioElement.controls = true;
                    audioElement.src = audioUrl;

                    chatMessages.appendChild(audioElement);

                    recordedChunks = [];
                    sendButton.disabled = false;
                });

                stopButton.disabled = false;
                recordButton.disabled = true;
                sendButton.disabled = true;
            })
            .catch(err => console.error('Microphone access denied:', err));
    }

    function stopRecording() {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        stopButton.disabled = true;
        recordButton.disabled = false;
    }

    function sendVoiceMessage() {
        if (!currentBlob) return;

        const formData = new FormData();
        const filename = 'voice_' + new Date().toISOString() + '.webm';
        formData.append('audio_data', currentBlob, filename);

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log('Voice message sent:', data.filePath);
                    sendButton.disabled = true; // можно отправлять только один раз
                } else {
                    console.error('Failed to send voice message:', data.error);
                }
            })
            .catch(console.error);
    }
</script>
