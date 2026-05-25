<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=mcims', 'root', '');
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $pdo->exec("DROP TABLE IF EXISTS $table");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    echo 'Tables dropped successfully';
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

