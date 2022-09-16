<?php

require_once 'init.php';
require_once 'functions.php';


$pageContent = include_template('guest.php');

$layoutContent = include_template(
    'layout.php', [
        'content' => $pageContent,
        'title' => 'Дела в порядке',
    ]
);

print($layoutContent);
