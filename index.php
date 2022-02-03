<?php

require 'src\init.php';
require 'src\functions.php';
require 'src\config.php';

$showCompleteTasks = rand(0, 1);

$con = connection(HOST, LOGIN, PASSWORD, DBNAME);
$projects = getUsersProjects($con, USERID);
$tasks = getUsersTasks($con, USERID);

$pageContent = include_template('main.php', ['projects' => $projects, 'tasks' => $tasks, 'showCompleteTasks' => $showCompleteTasks,]);
$layoutContent = include_template('layout.php', ['content' => $pageContent, 'title' => 'Дела в порядке', 'userName' => 'Константин']);
print($layoutContent);
