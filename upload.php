<?php
header('Content-Type: application/json'); // Важно, чтобы JS понимал JSON

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

if (isset($_FILES['audio_data']) && $_FILES['audio_data']['error'] === 0) {
    $input = $_FILES['audio_data']['tmp_name'];
    $originalName = pathinfo($_FILES['audio_data']['name'], PATHINFO_FILENAME); // безопасное имя без расширения
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName); // заменяем опасные символы
    $output = $uploadDir . $safeName . '_' . time() . '.wav'; // добавляем timestamp, чтобы избежать конфликтов

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
