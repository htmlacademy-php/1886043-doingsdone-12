<?php

require 'src/init.php';
require 'src/functions.php';

$userId = 3;

$showCompleteTasks = rand(0, 1);

$projectId = null;
if (!empty($_GET['projectId'])) {
    $projectId = (int)$_GET['projectId'];
}

$con = getConnection();
$projects = getUserProjects($con, $userId);
$tasks = getUserTasks($con, $userId, $projectId);

$pageContent = include_template(
    'main.php', [
        'projects' => $projects,
        'tasks' => $tasks,
        'showCompleteTasks' => $showCompleteTasks,
        'projectId' => $projectId,
    ]
);

$layoutContent = include_template(
    'layout.php', [
        'content' => $pageContent,
        'title' => 'Дела в порядке',
        'userName' => 'Константин'
    ]
);

print($layoutContent);
