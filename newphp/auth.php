<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'manager' && $password === '1234') {
        $_SESSION['user_role'] = 'manager';
        header("Location: dashboard.php");
        exit();
    } elseif ($username === 'employee' && $password === '4567') {
        $_SESSION['user_role'] = 'employee';
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: index.php?error=1");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
