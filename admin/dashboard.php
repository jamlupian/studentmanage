<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$total = (int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$thisMonth = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())")->fetchColumn();
$noCourse = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE course = ''")->fetchColumn();

$byCourse = $pdo->query("SELECT course, COUNT(*) AS n FROM students WHERE course <> '' GROUP BY course ORDER BY n DESC LIMIT 8")->fetchAll();
$recent = $pdo->query('SELECT s.id, s.student_no, s.course, u.name, u.email, s.created_at
                       FROM students s JOIN users u ON u.id = s.user_id
                       ORDER BY s.created_at DESC, s.id DESC LIMIT 5')->fetchAll();

$pageTitle = 'Admin dashboard';
require __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
  <h1>Welcome back, <?= e($_SESSION['user']['name']) ?></h1>
  <a class="btn" href="<?= BASE_URL ?>/admin/student_add.php">Add student</a>
</div>

<div class="stats">
  <div class="stat"><strong><?= $total ?></strong><span>Students enrolled</span></div>
  <div class="stat"><strong><?= $thisMonth ?></strong><span>Joined this month</span></div>
  <div class="stat"><strong><?= $noCourse ?></strong><span>Without a course set</span></div>
</div>

<div class="two-col">
  <section class="panel">
    <h2>Students by course</h2>
    <?php if (!$byCourse): ?>
      <p class="muted">No courses recorded yet. Edit a student to assign one.</p>
    <?php else: ?>
      <table>
        <?php foreach ($byCourse as $row): ?>
          <tr><td><?= e($row['course']) ?></td><td class="num"><?= (int)$row['n'] ?></td></tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </section>

  <section class="panel">
    <h2>Newest students</h2>
    <?php if (!$recent): ?>
      <p class="muted">No students yet. Add one or wait for a registration.</p>
    <?php else: ?>
      <table>
        <?php foreach ($recent as $r): ?>
          <tr>
            <td><a href="<?= BASE_URL ?>/admin/student_edit.php?id=<?= (int)$r['id'] ?>"><?= e($r['name']) ?></a><br><small class="muted"><?= e($r['email']) ?></small></td>
            <td class="num"><?= e($r['student_no']) ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </section>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
