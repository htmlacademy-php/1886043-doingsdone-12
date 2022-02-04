<?php

require 'src\init.php';
require 'src\functions.php';

$userId = 3;

$showCompleteTasks = rand(0, 1);

$con = getConnection();
$projects = getUsersProjects($con, $userId);
$tasks = getUsersTasks($con, $userId);

$pageContent = include_template('main.php', ['projects' => $projects, 'tasks' => $tasks, 'showCompleteTasks' => $showCompleteTasks,]);
$layoutContent = include_template('layout.php', ['content' => $pageContent, 'title' => 'Дела в порядке', 'userName' => 'Константин']);
print($layoutContent);
