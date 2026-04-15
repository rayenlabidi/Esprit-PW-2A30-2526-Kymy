<?php
try {
    // Connect with root
    $pdo = new PDO('mysql:host=localhost', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Show all users
    $stmt = $pdo->query("SELECT user, host FROM mysql.user");
    $users = $stmt->fetchAll();
    
    echo "<h2>MySQL Users in Your XAMPP:</h2>";
    echo "<ul>";
    foreach($users as $user) {
        echo "<li>Username: <strong>{$user['user']}</strong> @ {$user['host']}</li>";
    }
    echo "</ul>";
    
    echo "<p>✅ Valid usernames for XAMPP: <strong>root</strong> (and possibly others)</p>";
    echo "<p>❌ 'system' is NOT a valid MySQL username in XAMPP</p>";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>