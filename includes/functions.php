<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

/** Escape output to prevent XSS */
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void {
    header('Location: ' . BASE_URL . $path);
    exit;
}

/** Set a flash message, or read and clear it when called without arguments */
function flash(?string $type = null, ?string $message = null): ?array {
    if ($message !== null) {
        $_SESSION['flash'] = [$type, $message];
        return null;
    }
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

/* ---------- CSRF ---------- */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        die('Invalid or expired form. Go back, refresh the page, and try again.');
    }
}

/* ---------- Auth ---------- */
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}
function require_role(string $role): void {
    if (!is_logged_in()) {
        redirect('/login.php');
    }
    if ($_SESSION['user']['role'] !== $role) {
        redirect('/index.php');
    }
}
function home_for_role(string $role): string {
    return $role === 'admin' ? '/admin/dashboard.php' : '/student/dashboard.php';
}

/* ---------- Validation ---------- */
function clean_student_input(array $src): array {
    return [
        'name'       => trim($src['name'] ?? ''),
        'email'      => strtolower(trim($src['email'] ?? '')),
        'student_no' => trim($src['student_no'] ?? ''),
        'course'     => trim($src['course'] ?? ''),
        'year_level' => (int)($src['year_level'] ?? 1),
        'phone'      => trim($src['phone'] ?? ''),
        'address'    => trim($src['address'] ?? ''),
    ];
}

function validate_student(PDO $pdo, array $d, string $password, bool $requirePassword, int $userId = 0, int $studentId = 0): array {
    $errors = [];

    if ($d['name'] === '') {
        $errors[] = 'Enter the student\'s name.';
    }

    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    } else {
        $s = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id <> ?');
        $s->execute([$d['email'], $userId]);
        if ($s->fetch()) {
            $errors[] = 'That email is already registered.';
        }
    }

    if ($d['student_no'] === '') {
        $errors[] = 'Enter a student number.';
    } else {
        $s = $pdo->prepare('SELECT id FROM students WHERE student_no = ? AND id <> ?');
        $s->execute([$d['student_no'], $studentId]);
        if ($s->fetch()) {
            $errors[] = 'That student number is already in use.';
        }
    }

    if ($d['year_level'] < 1 || $d['year_level'] > 6) {
        $errors[] = 'Year level must be between 1 and 6.';
    }

    if ($requirePassword || $password !== '') {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
    }

    return $errors;
}
