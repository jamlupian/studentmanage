<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$q = trim($_GET['q'] ?? '');
$sql = 'SELECT s.id, s.student_no, s.course, s.year_level, s.phone, u.name, u.email
        FROM students s JOIN users u ON u.id = s.user_id';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE u.name LIKE :q OR u.email LIKE :q OR s.student_no LIKE :q OR s.course LIKE :q';
    $params[':q'] = "%$q%";
}
$sql .= ' ORDER BY u.name ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

$pageTitle = 'Students';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
  <h1>Students <small class="muted">(<?= count($students) ?>)</small></h1>
  <a class="btn" href="<?= BASE_URL ?>/admin/student_add.php">Add student</a>
</div>

<form class="search" method="get">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search by name, email, student number, or course">
  <button class="btn" type="submit">Search</button>
  <?php if ($q !== ''): ?><a class="btn btn-ghost" href="students.php">Clear</a><?php endif; ?>
</form>

<div class="table-wrap">
<table>
  <thead>
    <tr><th>Student no.</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Phone</th><th></th></tr>
  </thead>
  <tbody>
  <?php if (!$students): ?>
    <tr><td colspan="7" class="muted">No students match your search.</td></tr>
  <?php endif; ?>
  <?php foreach ($students as $s): ?>
    <tr>
      <td><?= e($s['student_no']) ?></td>
      <td><?= e($s['name']) ?></td>
      <td><?= e($s['email']) ?></td>
      <td><?= e($s['course'] ?: '—') ?></td>
      <td><?= (int)$s['year_level'] ?></td>
      <td><?= e($s['phone'] ?: '—') ?></td>
      <td class="actions">
        <a class="btn btn-small btn-ghost" href="student_edit.php?id=<?= (int)$s['id'] ?>">Edit</a>
        <form method="post" action="student_delete.php" onsubmit="return confirm('Delete <?= e(addslashes($s['name'])) ?>? This also removes their login.');">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
          <button class="btn btn-small btn-danger" type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
