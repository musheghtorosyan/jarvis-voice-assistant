    

    <input type="file" id="voiceInput" accept="audio/*">
    <button id="recordButton">Record</button>
    <button id="stopButton" disabled>Stop</button>
    <button id="sendButton" disabled>Send</button>

    <div id="chatMessages"></div>

    <script>
        const recordButton = document.getElementById('recordButton');
        const stopButton = document.getElementById('stopButton');
        const sendButton = document.getElementById('sendButton');
        const voiceInput = document.getElementById('voiceInput');
        const chatMessages = document.getElementById('chatMessages');

        let mediaRecorder;
        let recordedChunks = [];

        recordButton.addEventListener('click', startRecording);
        stopButton.addEventListener('click', stopRecording);
        sendButton.addEventListener('click', sendVoiceMessage);

        function startRecording() {
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(stream => {
                    mediaRecorder = new MediaRecorder(stream)
;
                    mediaRecorder.start();

                    mediaRecorder.addEventListener('dataavailable', event => {
                        recordedChunks.push(event.data);
                    });

                    stopButton.disabled = false;
                    recordButton.disabled = true;
                })
                .catch(console.error);
        }

        function stopRecording() {
            mediaRecorder.stop();
            mediaRecorder.addEventListener('stop', () => {
                const blob = new Blob(recordedChunks, { type: 'audio/mp3' });
                const audioUrl = URL.createObjectURL(blob);

                console.log(blob,'blobbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb');
                console.log(audioUrl,'audioUrl----------------------------');

                const audioElement = document.createElement('audio');
                audioElement.controls = true;
                audioElement.src = audioUrl;
                
                chatMessages.appendChild(audioElement);

                recordedChunks = [];
                recordButton.disabled = false;
                stopButton.disabled = true;
                sendButton.disabled = false;
            });
        }

        function sendVoiceMessage() {
            const formData = new FormData();
            formData.append('voiceMessage', voiceInput.files[0]);
            
            fetch('upload.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Send the file path or any other metadata to the server for processing
                    // For simplicity, we'll just log the file path here
                    console.log('Voice message sent:', data.filePath);
                } else {
                    console.error('Failed to send voice message:', data.error);
                }
            })
            .catch(console.error);
        }
    </script>











    <audio controls="" id="audio" src="blob:https://gist.githack.com/5769ee61-fa9d-450a-bf01-42891f81c90d"></audio>