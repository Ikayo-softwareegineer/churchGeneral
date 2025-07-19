<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once(__DIR__ . '/../includes/php/database.php'); // <-- corrected path

try {
    // Grab POSTed values
    $first  = $_POST['regFirstName'] ?? '';
    $second = $_POST['regSecondName'] ?? null;
    $gender = $_POST['regGender'] ?? null;
    $addr   = $_POST['regAddress'] ?? null;
    $contact= $_POST['regContact'] ?? '';

    // Basic sanity check
    if (!$first || !$contact || strlen($contact) !== 10) {
        throw new Exception('Required fields missing or invalid.');
    }

    $pdo = Database::connection();
    $pdo->beginTransaction();

    // 1) Insert into new_members
    $stmt = $pdo->prepare(
        "INSERT INTO new_members (first_name, second_name, Gender, Address, Contact)
         VALUES (:first, :second, :gender, :addr, :contact)"
    );
    $stmt->execute([
        ':first'   => $first,
        ':second'  => $second,
        ':gender'  => $gender,
        ':addr'    => $addr,
        ':contact' => $contact
    ]);
    $memberId = (int)$pdo->lastInsertId();

    // 2) Insert into users (username = first name, password = contact)
    $stmt = $pdo->prepare(
        "INSERT INTO users (username, password, new_member_id)
         VALUES (:uname, :pass, :mid)"
    );
    $stmt->execute([
        ':uname' => $first,
        ':pass'  => $contact,
        ':mid'   => $memberId,
    ]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($pdo && $pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
