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
<button id="recordBtn">Record</button>
<button id="stopBtn" disabled>Stop</button>
<div id="chatMessages"></div>

<script>
    const recordBtn = document.getElementById('recordBtn');
    const stopBtn = document.getElementById('stopBtn');
    const chatMessages = document.getElementById('chatMessages');

    let mediaRecorder;
    let recordedChunks = [];

    recordBtn.addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            recordedChunks = [];

            // Проверяем поддержку mimeType
            let mimeType = 'audio/webm;codecs=opus';
            if (!MediaRecorder.isTypeSupported(mimeType)) mimeType = '';

            mediaRecorder = new MediaRecorder(stream, { mimeType });

            mediaRecorder.ondataavailable = e => {
                if (e.data.size > 0) recordedChunks.push(e.data);
            };

            mediaRecorder.onstop = () => {
                if (recordedChunks.length === 0) {
                    alert('No audio recorded!');
                    return;
                }
                const blob = new Blob(recordedChunks, { type: mimeType || 'audio/webm' });
                const url = URL.createObjectURL(blob);

                const audio = document.createElement('audio');
                audio.controls = true;
                audio.src = url;
                chatMessages.appendChild(audio);
            };

            mediaRecorder.start();
            recordBtn.disabled = true;
            stopBtn.disabled = false;
        } catch (err) {
            alert('Microphone access denied or error: ' + err);
        }
    });

    stopBtn.addEventListener('click', () => {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') mediaRecorder.stop();
        recordBtn.disabled = false;
        stopBtn.disabled = true;
    });
</script>


</body>
</html>
