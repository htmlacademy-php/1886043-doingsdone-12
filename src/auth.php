<?php

require_once 'init.php';
require_once 'functions.php';

$con = getConnection();

$errors = [];

if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['submit'])) {

    $rules = [
        'email' => function () {
            return validateEmail('email');
        },
        'password' => function () {
            return validateFilled('password');
        }
    ];

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }

    if(!$errors['email']) {
        $userData = getUsersDataOnEmail ( $con, $_POST['email']);
        if (empty($userData)) {
            $errors['email'] = 'Неверный логин или пароль';
        } else {
            if(!$errors['password']) {
                if(!password_verify($_POST['password'], $userData['password'])) {
                    $errors['password'] = 'Неверный логин или пароль';
                }
            }
        }
    }

    $errors = array_filter($errors);

    if (empty($errors)) {
        $user = getUsersData($con, $userData['id']);
        $_SESSION['userId'] =  $user['id'];
        $_SESSION['userName'] =  $user['name'];
        $_SESSION['userEmail'] =  $user['email'];
        header('Location: /index.php');
    }
}

$pageContent = include_template('auth.php', ['errors' => $errors,]);

$layoutContent = include_template(
    'layout.php', [
        'content' => $pageContent,
        'title' => 'Дела в порядке'
    ]
);

print($layoutContent);

