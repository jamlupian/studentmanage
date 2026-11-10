<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) {
    redirect(home_for_role($_SESSION['user']['role']));
}
redirect('/login.php');
