<?php
require_once 'c:/xampp/htdocs/workify/config/database.php';
$pdo = Database::getInstance()->getPdo();
echo "USERS:\n";
print_r($pdo->query('SELECT id FROM utilisateurs')->fetchAll(PDO::FETCH_ASSOC));
echo "\nCATEGORIES:\n";
print_r($pdo->query('SELECT id FROM categories')->fetchAll(PDO::FETCH_ASSOC));
echo "\nEVENTS:\n";
print_r($pdo->query('SELECT id, organizer_id, category_id FROM events')->fetchAll(PDO::FETCH_ASSOC));
