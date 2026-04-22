<?php
require_once 'config/config.php';
$db = config::getConnexion();
$stmt = $db->query('SELECT id, user_id, user_name FROM publication');
foreach($stmt->fetchAll() as $row) {
    echo $row['id'] . ' -> ' . $row['user_id'] . ' (' . $row['user_name'] . ')' . PHP_EOL;
}
?>