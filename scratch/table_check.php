<?php
require_once 'c:/xampp/htdocs/workify/config/database.php';
$pdo = Database::getInstance()->getPdo();
foreach(['events', 'categories', 'event_categories'] as $t) {
    echo "STRUCTURE FOR $t:\n";
    try {
        $stmt = $pdo->query("DESCRIBE $t");
        if ($stmt) {
            print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
        } else {
            echo "Not found (query returned false)\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "-------------------\n";
}
