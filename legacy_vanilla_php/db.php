<?php

$dbPath = __DIR__ . '/database.sqlite';

$pdo = new PDO("sqlite:" . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        role TEXT NOT NULL DEFAULT 'Developer',
        status TEXT NOT NULL DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
");

$checkCount = $pdo->query("SELECT COUNT(*) AS total FROM users")->fetch();
if ((int)$checkCount['total'] === 0) {
    $seedStmt = $pdo->prepare("
        INSERT INTO users (name, email, role, status) VALUES 
        ('Alice Smith', 'alice@example.com', 'Lead Architect', 'active'),
        ('Bob Jones', 'bob@example.com', 'Backend Developer', 'active'),
        ('Charlie Brown', 'charlie@example.com', 'UI/UX Designer', 'pending')
    ");
    $seedStmt->execute();
}
