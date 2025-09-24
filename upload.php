<?php
header('Content-Type: application/json');

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

if (isset($_FILES['audio_data']) && $_FILES['audio_data']['error'] === 0) {
    $input = $_FILES['audio_data']['tmp_name'];
    $filename = 'voice_' . time() . '.webm';
    $output = $uploadDir . $filename;

    if (move_uploaded_file($input, $output)) {
        echo json_encode([
            'success' => true,
            'filePath' => $output
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Cannot move uploaded file'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No file uploaded or error'
    ]);
}
