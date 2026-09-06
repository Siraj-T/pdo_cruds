<?php

require_once 'db.php';

$testEmail = 'john.test' . time() . '@example.com';
$insertSql = "INSERT INTO users (name, email, role, status) VALUES (:name, :email, :role, :status)";
$stmt = $pdo->prepare($insertSql);
$stmt->execute([
    ':name'   => 'John Doe (Test)',
    ':email'  => $testEmail,
    ':role'   => 'Developer',
    ':status' => 'active'
]);

echo "<h3>1. CREATE: User inserted successfully!</h3>";

$stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
$allUsers = $stmt->fetchAll();

echo "<h3>2. READ (All Users):</h3>";
foreach ($allUsers as $user) {
    echo "ID: " . $user['id'] . " | Name: " . $user['name'] . " | Email: " . $user['email'] . " | Role: " . $user['role'] . "<br>";
}

$stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 1");
$firstUser = $stmt->fetch();
$idToFind = $firstUser ? $firstUser['id'] : 1;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $idToFind]);
$singleUser = $stmt->fetch();

echo "<h3>2. READ (Single User ID #" . $idToFind . "):</h3>";
if ($singleUser) {
    echo "Found User: " . $singleUser['name'] . " (Email: " . $singleUser['email'] . ")<br>";
}

$updateSql = "UPDATE users SET name = :name, role = :role, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
$stmt = $pdo->prepare($updateSql);
$stmt->execute([
    ':name' => 'Alice Smith (Updated)',
    ':role' => 'Principal Architect',
    ':id'   => $idToFind
]);

echo "<h3>3. UPDATE: User #" . $idToFind . " updated successfully!</h3>";

$deleteSql = "DELETE FROM users WHERE email = :email";
$stmt = $pdo->prepare($deleteSql);
$stmt->execute([':email' => $testEmail]);

echo "<h3>4. DELETE: Temporary test user deleted successfully!</h3>";
