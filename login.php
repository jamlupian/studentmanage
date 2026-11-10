<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) {
    redirect(home_for_role($_SESSION['user']['role']));
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();

    if ($u && password_verify($password, $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$u['id'], 'name' => $u['name'], 'role' => $u['role']];
        redirect(home_for_role($u['role']));
    }
    $error = 'Email or password is incorrect.';
}

$pageTitle = 'Log in';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-card">
  <h1>Log in</h1>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <label>Email
      <input type="email" name="email" value="<?= e($email) ?>" required autofocus>
    </label>
    <label>Password
      <input type="password" name="password" required>
    </label>
    <button class="btn" type="submit">Log in</button>
  </form>
  <p class="muted">New student? <a href="<?= BASE_URL ?>/register.php">Create an account</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
