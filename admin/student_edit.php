<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT s.*, u.name, u.email FROM students s JOIN users u ON u.id = s.user_id WHERE s.id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();
if (!$row) {
    flash('error', 'That student no longer exists.');
    redirect('/admin/students.php');
}

$errors = [];
$data = $row;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data = clean_student_input($_POST);
    $password = $_POST['password'] ?? '';
    $errors = validate_student($pdo, $data, $password, false, (int)$row['user_id'], (int)$row['id']);

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            if ($password !== '') {
                $pdo->prepare('UPDATE users SET name=?, email=?, password=? WHERE id=?')
                    ->execute([$data['name'], $data['email'], password_hash($password, PASSWORD_DEFAULT), $row['user_id']]);
            } else {
                $pdo->prepare('UPDATE users SET name=?, email=? WHERE id=?')
                    ->execute([$data['name'], $data['email'], $row['user_id']]);
            }
            $pdo->prepare('UPDATE students SET student_no=?, course=?, year_level=?, phone=?, address=? WHERE id=?')
                ->execute([$data['student_no'], $data['course'], $data['year_level'], $data['phone'], $data['address'], $row['id']]);
            $pdo->commit();
            flash('success', 'Student updated.');
            redirect('/admin/students.php');
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = 'The changes could not be saved. Try again.';
        }
    }
}

$pageTitle = 'Edit student';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><h1>Edit <?= e($row['name']) ?></h1></div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
<form method="post" class="panel">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
  <?php require __DIR__ . '/../includes/student_form_fields.php'; ?>
  <div class="form-actions">
    <button class="btn" type="submit">Save changes</button>
    <a class="btn btn-ghost" href="students.php">Cancel</a>
  </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
