<?php
include_once __DIR__ . '/../config/config.php';
try {
    $db = config::getConnexion();
    $db->exec("ALTER TABLE comment_likes ADD COLUMN reaction_type ENUM('like','love','haha','wow','angry','sad') DEFAULT 'like'");
    echo "Success";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Success (already exists)";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
