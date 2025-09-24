<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voice Recorder with RecordRTC</title>
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
<button id="recordBtn">🎤 Record</button>
<button id="stopBtn" disabled>⏹ Stop</button>
<button id="sendBtn" disabled>📤 Send</button>
<div id="chatMessages"></div>

<script src="https://cdn.webrtc-experiment.com/RecordRTC.js"></script>
<script>
    let recorder;
    let blob;

    const recordBtn = document.getElementById('recordBtn');
    const stopBtn = document.getElementById('stopBtn');
    const sendBtn = document.getElementById('sendBtn');
    const chatMessages = document.getElementById('chatMessages');

    recordBtn.addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

            // Создаём RecordRTC только после успешного getUserMedia
            recorder = RecordRTC(stream, { type: 'audio', mimeType: 'audio/wav' });
            recorder.startRecording();

            recordBtn.disabled = true;
            stopBtn.disabled = false;
            sendBtn.disabled = true;
        } catch (err) {
            alert('Microphone access denied: ' + err);
        }
    });

    stopBtn.addEventListener('click', () => {
        if (!recorder) return;

        stopBtn.disabled = true;
        recordBtn.disabled = false;

        recorder.stopRecording(() => {
            blob = recorder.getBlob();

            if (!blob) {
                alert('Recording failed, blob is empty');
                return;
            }

            const audioUrl = URL.createObjectURL(blob);
            const audio = document.createElement('audio');
            audio.controls = true;
            audio.src = audioUrl;
            chatMessages.appendChild(audio);

            sendBtn.disabled = false;
        });
    });

    sendBtn.addEventListener('click', () => {
        if (!blob) return;

        const formData = new FormData();
        formData.append('audio_data', blob, 'voice_' + Date.now() + '.wav');

        fetch('upload.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Voice message sent: ' + data.filePath);
                    sendBtn.disabled = true;
                } else {
                    alert('Failed: ' + data.error);
                }
            })
            .catch(console.error);
    });
</script>

</body>
</html>
