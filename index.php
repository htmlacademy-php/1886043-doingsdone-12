<?php

require_once 'src/init.php';
require_once 'src/functions.php';

if (!isset($_SESSION['userEmail'])) {
    header('Location: /src/guest.php');
}

$con = getConnection();
$user = getUsersData($con, $_SESSION['userEmail']);

$_SESSION['userId'] = $user['id'];
$_SESSION['userName'] = $user['name'];

$showCompleteTasks = rand(0, 1);

$projectId = null;
if (!empty($_GET['projectId'])) {
    $projectId = (int)$_GET['projectId'];
}

$anyProjects = checkUserProjects($con, $user['id']);

if (empty($anyProjects )) {
    $projects = [
        [
        'id' => 0,
        'count' => null,
        'title' => 'Нет проектов',
        ],
    ];
    $tasks = [
        [
        'name' => 'Нет заданий',
        'deadline' => null,
        'project_id' => 0,
        'is_finished' => 0,
        'path_to_file' => null,
        ],
    ];
} else {
    $projects = getUserProjects($con, $user['id']);
    if (empty ($projects)) {
        $projects = $anyProjects;
        $tasks = [
            [
            'name' => 'Нет заданий',
            'deadline' => null,
            'project_id' => 0,
            'is_finished' => 0,
            'path_to_file' => null,
            ],
        ];
    } else {
        $tasks = getUserTasks($con, $user['id'], $projectId);
    }
}

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
    ]
);

print($layoutContent);
