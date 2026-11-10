<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('student');

$stmt = $pdo->prepare('SELECT s.*, u.name, u.email, u.created_at AS joined
                       FROM students s JOIN users u ON u.id = s.user_id WHERE u.id = ?');
$stmt->execute([$_SESSION['user']['id']]);
$me = $stmt->fetch();
if (!$me) {
    session_destroy();
    die('Your student record was not found. Ask an administrator for help.');
}

$incomplete = ($me['course'] === '' || $me['phone'] === '' || $me['address'] === '');

$pageTitle = 'My profile';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
  <h1>Hi, <?= e($me['name']) ?></h1>
  <a class="btn" href="profile_edit.php">Edit profile</a>
</div>

<?php if ($incomplete): ?>
  <div class="alert alert-info">Your profile is missing some details. <a href="profile_edit.php">Add your course, phone, and address.</a></div>
<?php endif; ?>

<section class="panel">
  <dl class="details">
    <dt>Student number</dt><dd><?= e($me['student_no']) ?></dd>
    <dt>Email</dt><dd><?= e($me['email']) ?></dd>
    <dt>Course</dt><dd><?= e($me['course'] ?: 'Not set') ?></dd>
    <dt>Year level</dt><dd>Year <?= (int)$me['year_level'] ?></dd>
    <dt>Phone</dt><dd><?= e($me['phone'] ?: 'Not set') ?></dd>
    <dt>Address</dt><dd><?= e($me['address'] ?: 'Not set') ?></dd>
    <dt>Member since</dt><dd><?= e(date('F j, Y', strtotime($me['joined']))) ?></dd>
  </dl>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
