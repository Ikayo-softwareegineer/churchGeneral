<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['new_member_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Please log in.'
    ]);
    exit;
}

require_once(__DIR__ . '/../../includes/php/database.php');

try {
    $pdo = Database::connection();

    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $new_member_id = $_SESSION['new_member_id'];
    $audioPath = null;

    // Handle audio upload
    if (!empty($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
        $audioDir = __DIR__ . '/../audios/';
        if (!is_dir($audioDir)) {
            mkdir($audioDir, 0777, true); // Create audios directory if it doesn't exist
        }

        $originalName = basename($_FILES['audio']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $newFileName = uniqid('audio_', true) . '.' . $extension;
        $destination = $audioDir . $newFileName;

        if (move_uploaded_file($_FILES['audio']['tmp_name'], $destination)) {
            // Store relative path
            $audioPath = '/CMS/appointment/audios/' . $newFileName;
        } else {
            throw new Exception('Failed to save the audio file.');
        }
    }

    // Insert into DB
    $stmt = $pdo->prepare(
        "INSERT INTO appointments (new_member_id, subject, message, audio_path)
         VALUES (:member_id, :subject, :message, :audio_path)"
    );

    $stmt->execute([
        ':member_id' => $new_member_id,
        ':subject' => $subject,
        ':message' => $message,
        ':audio_path' => $audioPath
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Appointment saved successfully.'
    ]);
} catch (Exception $e) {
    error_log("Appointment Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to submit appointment.'
    ]);
}
?>
