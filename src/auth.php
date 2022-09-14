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

    $checkedUserEmail = checkUsersEmail($con, $_POST['email']);
    if(!$errors['email']) {
        if ($checkedUserEmail===false) {
            $errors['email'] = 'Неверный логин или пароль';
        } else {
            $checkedUserPassword = validateUserPassword($con, $_POST['email']);
            if(!$errors['password']) {
                if(!password_verify($_POST['password'], $checkedUserPassword)) {
                    $errors['password'] = 'Неверный логин или пароль';
                }
            }
        }
    };

    $errors = array_filter($errors);

    if (empty($errors)) {
        $_SESSION['userEmail'] =  $_POST['email'];
        header('Location: /index.php');
    };
}

$pageContent = include_template('auth.php', ['errors' => $errors,]);

$layoutContent = include_template(
    'layout.php', [
        'content' => $pageContent,
        'title' => 'Дела в порядке'
    ]
);

print($layoutContent);
