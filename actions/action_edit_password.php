<?php
    include_once('../includes/config.php');
    include_once('../database/user_queries.php');

    if(!isset($_SESSION['username']) || 
        !isset($_POST['username']) || 
        !isset($_POST['password']) || 
        !isset($_POST['pwConfirmation']) ||
        !isset($_POST['csrf'])) {
        header('Location: ../pages/front_page.php');
        die();
    }

    $username = $_POST['username'];
    $csrf = $_POST['csrf'];

    if($_SESSION['csrf'] !== $csrf) {
        die(header('Location: ../pages/login.php'));
    }

    if ($username != $_SESSION['username']) {
        header('Location: ../pages/front_page.php');
        die();
    }

    $password = $_POST['password'];
    $pwConfirmation = $_POST['pwConfirmation'];

    if (strlen($password) < 6 or strpos($password, ' ') !== false or $password !== $pwConfirmation) {
        die(header('Location: ../pages/edit_profile.php'));        
    }

    updateUserPassword($username, $password);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
?>