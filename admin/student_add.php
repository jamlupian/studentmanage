<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$errors = [];
$data = ['name'=>'','email'=>'','student_no'=>'','course'=>'','year_level'=>1,'phone'=>'','address'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $data = clean_student_input($_POST);
    $password = $_POST['password'] ?? '';
    $errors = validate_student($pdo, $data, $password, true);

    if (!$errors) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, "student")')
                ->execute([$data['name'], $data['email'], password_hash($password, PASSWORD_DEFAULT)]);
            $uid = (int)$pdo->lastInsertId();
            $pdo->prepare('INSERT INTO students (user_id, student_no, course, year_level, phone, address) VALUES (?,?,?,?,?,?)')
                ->execute([$uid, $data['student_no'], $data['course'], $data['year_level'], $data['phone'], $data['address']]);
            $pdo->commit();
            flash('success', 'Student added.');
            redirect('/admin/students.php');
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = 'The student could not be saved. Try again.';
        }
    }
}

$passwordRequired = true;
$pageTitle = 'Add student';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><h1>Add student</h1></div>
<?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>
<form method="post" class="panel">
  <?= csrf_field() ?>
  <?php require __DIR__ . '/../includes/student_form_fields.php'; ?>
  <div class="form-actions">
    <button class="btn" type="submit">Add student</button>
    <a class="btn btn-ghost" href="students.php">Cancel</a>
  </div>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
