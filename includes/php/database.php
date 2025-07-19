<?php
/**
 * PDO singleton for MariaDB connection.
 * Usage:
 *   require_once 'database.php';
 *   $pdo = Database::connection();
 */
class Database {
    private static $pdo = null;

    private function __construct() {}  // prevent instantiation

    public static function connection(): PDO {
        if (self::$pdo === null) {
            $host = 'localhost';
            $dbname = 'church';
            $user = 'root';
            $pass = '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            self::$pdo = new PDO($dsn, $user, $pass, $options);
        }
        return self::$pdo;
    }
}

// Only echo success if this file is accessed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    try {
        Database::connection();
        echo "success";
    } catch (Exception $e) {
        echo "connection failed";
    }
}
?>
