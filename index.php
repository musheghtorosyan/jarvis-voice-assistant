<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voice Recorder Chat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0d1117;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 50px;
        }
        button {
            margin: 10px;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        #chatMessages {
            margin-top: 30px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
            max-width: 500px;
        }
        audio {
            width: 100%;
        }
    </style>
</head>
<body>

<h1>Voice Recorder Chat</h1>
<button id="recordButton">🎤 Record</button>
<button id="stopButton" disabled>⏹ Stop</button>
<button id="sendButton" disabled>📤 Send</button>

<div id="chatMessages"></div>

<script>
    const recordButton = document.getElementById('recordButton');
    const stopButton = document.getElementById('stopButton');
    const sendButton = document.getElementById('sendButton');
    const chatMessages = document.getElementById('chatMessages');

    let mediaRecorder;
    let recordedChunks = [];
    let currentBlob;

    recordButton.addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            recordedChunks = [];
            mediaRecorder = new MediaRecorder(stream, { mimeType: 'audio/webm;codecs=opus' });

            mediaRecorder.ondataavailable = e => {
                if (e.data.size > 0) recordedChunks.push(e.data);
            };

            mediaRecorder.onstop = () => {
                currentBlob = new Blob(recordedChunks, { type: 'audio/webm' });
                const audioUrl = URL.createObjectURL(currentBlob);

                const audioElement = document.createElement('audio');
                audioElement.controls = true;
                audioElement.src = audioUrl;
                chatMessages.appendChild(audioElement);

                sendButton.disabled = false;
            };

            mediaRecorder.start();
            recordButton.disabled = true;
            stopButton.disabled = false;
            sendButton.disabled = true;
        } catch (err) {
            console.error('Microphone access denied:', err);
            alert('Microphone access denied');
        }
    });

    stopButton.addEventListener('click', () => {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        recordButton.disabled = false;
        stopButton.disabled = true;
    });

    sendButton.addEventListener('click', () => {
        if (!currentBlob) return;

        const formData = new FormData();
        const filename = 'voice_' + Date.now() + '.webm';
        formData.append('audio_data', currentBlob, filename);

        fetch('upload.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    console.log('Voice message sent:', data.filePath);
                    alert('Voice message sent successfully!');
                    sendButton.disabled = true;
                } else {
                    console.error('Failed to send voice message:', data.error);
                    alert('Failed to send voice message: ' + data.error);
                }
            })
            .catch(console.error);
    });
</script>

</body>
</html>
