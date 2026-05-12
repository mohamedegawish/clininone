<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
$pdo->exec('DROP DATABASE IF EXISTS clinicone');
$pdo->exec('CREATE DATABASE clinicone');
echo "Database reset.\n";
