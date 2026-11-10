<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/students.php');
}
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$stmt = $pdo->prepare('SELECT user_id FROM students WHERE id = ?');
$stmt->execute([$id]);
$userId = $stmt->fetchColumn();

if ($userId) {
    // Deleting the user also removes the student row (ON DELETE CASCADE)
    $pdo->prepare('DELETE FROM users WHERE id = ? AND role = "student"')->execute([$userId]);
    flash('success', 'Student deleted.');
} else {
    flash('error', 'That student no longer exists.');
}
redirect('/admin/students.php');
