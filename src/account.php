<?php

require_once 'init.php';
require_once 'functions.php';

$userId;

$con = getConnection();
$usersEmail = getUsersEmail($con);

$errors = [];

if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['submit'])) {

    /*var_dump($_POST['email']);


    var_dump($mailboxIsBusy);*/

    $rules = [
        'email' => function () {
            return validateEmail('email');
        },
        'password' => function () {
            return validateFilled('password');
        },
        'name' => function() {
            return validateFilled('name');
        }
    ];

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }

    $mailboxIsBusy = isMailboxBusy($usersEmail, $_POST['email']);
    if(!$errors['email']) {
        if ($mailboxIsBusy) {
            $errors['email'] = 'Указанный email уже используэться';
        }
    };

    $errors = array_filter($errors);

    if (empty($errors)) {
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        createNewUser($con, $_POST['email'], $passwordHash, $_POST['name']);
        header('Location: /index.php');
    };
}

$pageContent = include_template('register.php', ['errors' => $errors,]);

$layoutContent = include_template(
    'layout.php', [
        'content' => $pageContent,
        'title' => 'Дела в порядке',
        'userName' => 'Юджин'
    ]
);

print($layoutContent);
