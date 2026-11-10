<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('student');

$uid = (int)$_SESSION['user']['id'];
$stmt = $pdo->prepare('SELECT s.*, u.name, u.email FROM students s JOIN users u ON u.id = s.user_id WHERE u.id = ?');
$stmt->execute([$uid]);
$row = $stmt->fetch();
if (!$row) {
    redirect('/logout.php');
}

$errors = [];
$data = $row;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data = clean_student_input($_POST);
    $data['student_no'] = $row['student_no']; // students cannot change their own number
    $password = $_POST['password'] ?? '';
    $errors = validate_student($pdo, $data, $password, false, $uid, (int)$row['id']);

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            if ($password !== '') {
                $pdo->prepare('UPDATE users SET name=?, email=?, password=? WHERE id=?')
                    ->execute([$data['name'], $data['email'], password_hash($password, PASSWORD_DEFAULT), $uid]);
            } else {
                $pdo->prepare('UPDATE users SET name=?, email=? WHERE id=?')
                    ->execute([$data['name'], $data['email'], $uid]);
            }
            $pdo->prepare('UPDATE students SET course=?, year_level=?, phone=?, address=? WHERE id=?')
                ->execute([$data['course'], $data['year_level'], $data['phone'], $data['address'], $row['id']]);
            $pdo->commit();
            $_SESSION['user']['name'] = $data['name'];
            flash('success', 'Profile updated.');
            redirect('/student/dashboard.php');
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = 'Your changes could not be saved. Try again.';
        }
    }
}

$lockStudentNo = true;
$pageTitle = 'Edit profile';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><h1>Edit profile</h1></div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
<form method="post" class="panel">
  <?= csrf_field() ?>
  <?php require __DIR__ . '/../includes/student_form_fields.php'; ?>
  <div class="form-actions">
    <button class="btn" type="submit">Save changes</button>
    <a class="btn btn-ghost" href="dashboard.php">Cancel</a>
  </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
