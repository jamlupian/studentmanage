<?php
// Usage: set $pageTitle before including. Requires functions.php to be loaded.
$user  = $_SESSION['user'] ?? null;
$flash = flash();
$current = basename($_SERVER['PHP_SELF']);
if (!function_exists('nav_link')) {
    function nav_link(string $file, string $href, string $label, string $current): string {
        $active = ($current === $file) ? ' class="active"' : '';
        return '<a href="' . BASE_URL . $href . '"' . $active . '>' . $label . '</a>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Student Management') ?> | Student Records</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="<?= BASE_URL ?>/index.php">Student Records</a>
  <?php if ($user): ?>
  <nav>
    <?php if ($user['role'] === 'admin'): ?>
      <?= nav_link('dashboard.php', '/admin/dashboard.php', 'Dashboard', $current) ?>
      <?= nav_link('students.php', '/admin/students.php', 'Students', $current) ?>
    <?php else: ?>
      <?= nav_link('dashboard.php', '/student/dashboard.php', 'My profile', $current) ?>
      <?= nav_link('profile_edit.php', '/student/profile_edit.php', 'Edit profile', $current) ?>
    <?php endif; ?>
  </nav>
  <div class="who">
    <span><?= e($user['name']) ?> <small>(<?= e($user['role']) ?>)</small></span>
    <a class="btn btn-ghost" href="<?= BASE_URL ?>/logout.php">Log out</a>
  </div>
  <?php endif; ?>
</header>
<main class="container">
<?php if ($flash): ?>
  <div class="alert alert-<?= e($flash[0]) ?>"><?= e($flash[1]) ?></div>
<?php endif; ?>
