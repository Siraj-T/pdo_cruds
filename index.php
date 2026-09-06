<?php

require_once 'db.php';

$message = '';
$errorMessage = '';

$editUser = null;
$customResults = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    try {
        $sql = "INSERT INTO users (name, email, role, status) VALUES (:name, :email, :role, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'   => $_POST['name'],
            ':email'  => $_POST['email'],
            ':role'   => $_POST['role'],
            ':status' => $_POST['status']
        ]);
        header("Location: index.php?msg=created");
        exit;
    } catch (PDOException $e) {
        $errorMessage = "Failed to insert user: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    try {
        $sql = "UPDATE users SET name = :name, email = :email, role = :role, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'   => $_POST['name'],
            ':email'  => $_POST['email'],
            ':role'   => $_POST['role'],
            ':status' => $_POST['status'],
            ':id'     => $_POST['id']
        ]);
        header("Location: index.php?msg=updated");
        exit;
    } catch (PDOException $e) {
        $errorMessage = "Failed to update user: " . $e->getMessage();
    }
}

if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $_GET['delete']]);
        header("Location: index.php?msg=deleted");
        exit;
    } catch (PDOException $e) {
        $errorMessage = "Failed to delete user: " . $e->getMessage();
    }
}

if (isset($_GET['edit'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $_GET['edit']]);
        $editUser = $stmt->fetch();
    } catch (PDOException $e) {
        $errorMessage = "Failed to fetch user: " . $e->getMessage();
    }
}

$executedCode = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pdo_code'])) {
    $executedCode = trim($_POST['pdo_code']);
    if (!empty($executedCode)) {
        if (preg_match('/^\s*(SELECT|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER)\b/i', $executedCode)) {
            try {
                if (preg_match('/^\s*SELECT\b/i', $executedCode)) {
                    $stmt = $pdo->query($executedCode);
                    $customResults = $stmt->fetchAll();
                    $message = "✓ SELECT executed successfully! Returned " . count($customResults) . " row(s).";
                } else {
                    $stmt = $pdo->prepare($executedCode);
                    $stmt->execute();
                    $rowsAffected = $stmt->rowCount();
                    $message = "✓ Query executed successfully! ($rowsAffected row(s) affected). Table updated below.";
                }
            } catch (PDOException $e) {
                $errorMessage = "SQL Error: " . $e->getMessage();
            }
        } else {
            $cleanCode = preg_replace('/^\s*<\?(php)?/i', '', $executedCode);
            $cleanCode = preg_replace('/\?>\s*$/', '', $cleanCode);

            ob_start();
            try {
                eval($cleanCode . ';');
                $printedOutput = ob_get_clean();

                if (!empty($printedOutput)) {
                    $message = "✓ PDO Code executed successfully! Output: " . strip_tags($printedOutput);
                } else {
                    $message = "✓ PHP PDO code executed successfully! Table updated below.";
                }

                if (isset($users) && is_array($users)) {
                    $customResults = $users;
                }
            } catch (Throwable $e) {
                ob_end_clean();
                $errorMessage = "PHP Execution Notice: " . $e->getMessage();
            }
        }
    } else {
        $errorMessage = "Please enter some PHP PDO code before clicking Run.";
    }
}

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'created') $message = "✓ User created successfully!";
    if ($_GET['msg'] === 'updated') $message = "✓ User updated successfully!";
    if ($_GET['msg'] === 'deleted') $message = "✓ User deleted successfully!";
}

if ($customResults !== null) {
    $users = $customResults;
} else {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQLite Users Management & PHP PDO Runner</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <div class="header-row">
        <div>
            <h2>Users Management (PHP PDO Interactive App)</h2>
            <p class="subtitle">Complete Create, Read, Update, Delete with SQLite & live PHP PDO execution</p>
        </div>
        <div class="header-buttons">
            <a href="examples.php" class="btn btn-gray" style="background:#0f172a; color:#38bdf8; border:1px solid #334155; text-decoration: none; font-weight: 600;">
                📖 Full CRUD Examples Guide
            </a>
            <a href="CRUD.php" target="_blank" class="btn btn-blue">Open Raw CRUD.php</a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-error">
            <strong>⚠ Notice:</strong> <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="card" style="margin-top: 15px;">
        <h3>
            <?= $editUser ? '✎ Edit User #' . htmlspecialchars($editUser['id']) . ' (UPDATE)' : '+ Add New User (CREATE)' ?>
        </h3>

        <form method="POST" action="index.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end; margin-top: 10px;">
            <input type="hidden" name="action" value="<?= $editUser ? 'update' : 'create' ?>">

            <?php if ($editUser): ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($editUser['id']) ?>">
            <?php endif; ?>

            <div>
                <label style="display:block; font-weight:600; font-size:13px; margin-bottom:5px;">Name:</label>
                <input type="text" name="name" class="form-input" style="height:38px;" required value="<?= htmlspecialchars($editUser['name'] ?? '') ?>" placeholder="e.g. Alice Smith">
            </div>

            <div>
                <label style="display:block; font-weight:600; font-size:13px; margin-bottom:5px;">Email:</label>
                <input type="email" name="email" class="form-input" style="height:38px;" required value="<?= htmlspecialchars($editUser['email'] ?? '') ?>" placeholder="e.g. alice@example.com">
            </div>

            <div>
                <label style="display:block; font-weight:600; font-size:13px; margin-bottom:5px;">Role:</label>
                <input type="text" name="role" class="form-input" style="height:38px;" required value="<?= htmlspecialchars($editUser['role'] ?? 'Developer') ?>">
            </div>

            <div>
                <label style="display:block; font-weight:600; font-size:13px; margin-bottom:5px;">Status:</label>
                <select name="status" class="form-input" style="height:38px;">
                    <option value="active" <?= ($editUser['status'] ?? '') === 'active' ? 'selected' : '' ?>>active</option>
                    <option value="pending" <?= ($editUser['status'] ?? '') === 'pending' ? 'selected' : '' ?>>pending</option>
                    <option value="inactive" <?= ($editUser['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>inactive</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn <?= $editUser ? 'btn-blue' : 'btn-green' ?>" style="height:38px; width:100%;">
                    <?= $editUser ? 'Save Update' : 'Add User' ?>
                </button>
                <?php if ($editUser): ?>
                    <a href="index.php" class="btn btn-gray" style="display:block; text-align:center; margin-top:5px;">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="query-box" style="margin-top: 25px; padding: 22px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
        <div class="query-box-header">
            <h3 style="font-size: 18px; color: #1e293b;">⚡ Live PHP PDO Code Runner</h3>
            <span class="query-box-tag" style="background: #ede9fe; color: #6b21a8; font-weight: bold; padding: 4px 10px;">SQLite PDO Ready</span>
        </div>
        <p class="query-desc" style="font-size: 13.5px; color: #475569; margin: 4px 0 12px 0;">
            Type or paste <strong>any PHP PDO code snippet</strong> below (like <code>$stmt = $pdo-&gt;prepare(...); $stmt-&gt;execute([...]);</code>) and click <strong>▶ Run PDO Code</strong> to execute in real-time!
        </p>
        
        <div class="template-bar" style="margin-bottom: 12px;">
            <span style="font-weight: 600; font-size: 13px; color: #334155;">Quick PDO Templates:</span>
            <button type="button" class="btn-template" onclick="fillQuery('insert')">+ Insert via PDO</button>
            <button type="button" class="btn-template" onclick="fillQuery('update')">✎ Update via PDO</button>
            <button type="button" class="btn-template" onclick="fillQuery('delete')">✕ Delete via PDO</button>
            <button type="button" class="btn-template" onclick="fillQuery('select')">⟳ Select via PDO</button>
        </div>

        <form method="POST" action="index.php" style="margin-top: 10px;">
            <textarea id="sql_input" name="pdo_code" rows="11" class="sql-textarea" style="width: 100%; min-height: 250px; font-family: Consolas, Monaco, 'Courier New', monospace; font-size: 13.5px; line-height: 1.6; padding: 14px; background-color: #ffffff; color: #0f172a; border: 2px solid #cbd5e1; border-radius: 8px;" placeholder="Type or paste your PHP PDO code here... Example:
$sql = 'INSERT INTO users (name, email, role, status) VALUES (:name, :email, :role, :status)';

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':name'   => 'Alice Smith',
    ':email'  => 'alice@test.com',
    ':role'   => 'Developer',
    ':status' => 'active'
]);" required><?= htmlspecialchars($executedCode) ?></textarea>
            
            <div style="margin-top: 12px; display: flex; gap: 12px; align-items: center;">
                <button type="submit" class="btn btn-blue" style="padding: 10px 22px; font-size: 14px; font-weight: 600;">
                    ▶ Run PDO Code
                </button>
                <button type="button" class="btn btn-gray" onclick="document.getElementById('sql_input').value=''">
                    Clear Box
                </button>
                <?php if ($customResults !== null): ?>
                    <a href="index.php" class="btn btn-gray" style="text-decoration:none;">⟲ View Full Database Table</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div style="margin-top: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <h3 style="margin: 0; color: #1e293b; font-size: 18px;">
                Live Database Table: <code>users</code>
                <span style="font-size: 13px; font-weight: normal; color: #64748b;">(<?= count($users) ?> record(s) loaded)</span>
            </h3>
            <a href="index.php" class="btn btn-gray" style="padding: 6px 12px; font-size: 12px; text-decoration: none;">🔄 Refresh Table</a>
        </div>

        <table class="styled-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th style="width: 160px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($user['id'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($user['name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                            <td><span class="badge badge-role"><?= htmlspecialchars($user['role'] ?? '') ?></span></td>
                            <td><span class="badge badge-<?= htmlspecialchars($user['status'] ?? 'active') ?>"><?= htmlspecialchars($user['status'] ?? '') ?></span></td>
                            <td><?= htmlspecialchars($user['created_at'] ?? '') ?></td>
                            <td class="actions-cell">
                                <?php if (isset($user['id'])): ?>
                                    <a href="index.php?edit=<?= $user['id'] ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="index.php?delete=<?= $user['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars(addslashes($user['name'] ?? 'this user')) ?>?');">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" align="center" style="padding: 30px; color: #888;">No records found in database. Use the PDO runner or Add form above.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function fillQuery(type) {
    const textarea = document.getElementById('sql_input');
    const randomNum = Math.floor(Math.random() * 9000) + 1000;
    
    if (type === 'insert') {
        textarea.value = `$sql = "INSERT INTO users (name, email, role, status) 
        VALUES (:name, :email, :role, :status)";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':name'   => 'Alice Smith ' + randomNum,
    ':email'  => 'alice' + randomNum + '@test.com',
    ':role'   => 'Developer',
    ':status' => 'active'
]);`;
    } else if (type === 'update') {
        textarea.value = `$sql = "UPDATE users 
        SET role = :role, status = :status, updated_at = CURRENT_TIMESTAMP 
        WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':role'   => 'Principal Lead Architect',
    ':status' => 'active',
    ':id'     => 1
]);`;
    } else if (type === 'delete') {
        textarea.value = `$sql = "DELETE FROM users WHERE id = (SELECT MAX(id) FROM users)";

$stmt = $pdo->prepare($sql);
$stmt->execute();`;
    } else if (type === 'select') {
        textarea.value = `$stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
$users = $stmt->fetchAll();`;
    }
    textarea.focus();
}
</script>

</body>
</html>
