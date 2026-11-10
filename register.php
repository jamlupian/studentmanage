<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) {
    redirect(home_for_role($_SESSION['user']['role']));
}

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name     = trim($_POST['name'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($name === '') $errors[] = 'Enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    } else {
        $s = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $s->execute([$email]);
        if ($s->fetch()) $errors[] = 'That email is already registered.';
    }
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "student")')
                ->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $uid = (int)$pdo->lastInsertId();
            $studentNo = 'STU-' . date('Y') . '-' . str_pad((string)$uid, 4, '0', STR_PAD_LEFT);
            $pdo->prepare('INSERT INTO students (user_id, student_no) VALUES (?, ?)')
                ->execute([$uid, $studentNo]);
            $pdo->commit();
            flash('success', 'Account created. You can log in now.');
            redirect('/login.php');
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = 'Something went wrong while creating your account. Try again.';
        }
    }
}

$pageTitle = 'Create account';
require __DIR__ . '/includes/header.php';
?>
<div class="auth-card">
  <h1>Create your account</h1>
  <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post">
    <?= csrf_field() ?>
    <label>Full name
      <input type="text" name="name" value="<?= e($name) ?>" required autofocus>
    </label>
    <label>Email
      <input type="email" name="email" value="<?= e($email) ?>" required>
    </label>
    <label>Password
      <input type="password" name="password" minlength="6" required>
    </label>
    <label>Confirm password
      <input type="password" name="confirm" minlength="6" required>
    </label>
    <button class="btn" type="submit">Create account</button>
  </form>
  <p class="muted">Already registered? <a href="<?= BASE_URL ?>/login.php">Log in</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
