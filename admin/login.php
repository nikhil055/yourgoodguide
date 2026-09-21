<?php
if (session_status() === PHP_SESSION_NONE) {
    if (ini_get('session.cookie_path') !== '/') {
        @ini_set('session.cookie_path', '/');
    }
    session_start();
}
require_once __DIR__ . '/../db.php';

$error = '';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            session_write_close();
            header("Location: index.php");
            exit;
        } else {
            $error = 'Invalid username/email or password.';
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Finchskills</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
        }
        .brand-icon {
            width: 54px;
            height: 54px;
            background: #4361ee;
            color: #fff;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <div class="brand-icon">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Admin Portal</h3>
        <p class="text-muted small">Sign in to Finchskills management dashboard</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger py-2 px-3 small d-flex align-items-center mb-3">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label class="form-label fw-semibold small text-secondary">Username or Email</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="admin" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small text-secondary">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-3 mb-3" style="background-color: #4361ee; border: none;">
            Sign In to Dashboard
        </button>

        <div class="text-center">
            <a href="../index.php" class="text-decoration-none text-muted small"><i class="fa-solid fa-arrow-left me-1"></i> Back to Website</a>
        </div>
    </form>
</div>

</body>
</html>
