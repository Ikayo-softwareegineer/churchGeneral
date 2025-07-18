<?php
session_start(); // Start session at the top

file_put_contents('php://stderr', print_r($_POST, true));

header('Content-Type: application/json');
require_once(__DIR__ . '/../../includes/php/database.php');

$username = trim($_POST['loginUsername'] ?? '');
$password = trim($_POST['loginPassword'] ?? '');

try {
    $pdo = Database::connection();
    $stmt = $pdo->prepare(
        "SELECT user_id, new_member_id
         FROM users
         WHERE username = :uname AND password = :pass
         LIMIT 1"
    );
    $stmt->execute([':uname' => $username, ':pass' => $password]);
    $user = $stmt->fetch();

    if ($user) {
        // Store session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['new_member_id'] = $user['new_member_id'];

        $redirect = ($user['new_member_id'] !== null)
            ? '/CMS/appointment/appointment.html'
            : '#';

        echo json_encode([
            'success' => true,
            'redirect' => $redirect
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Username or password incorrect.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error — please try again later.'
    ]);
}
?>
