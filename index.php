<?php

require_once 'src/init.php';
require_once 'src/functions.php';

if (!isset($_SESSION['userEmail'])) {
    header('Location: /src/guest.php');
}

$con = getConnection();

$showCompleteTasks = rand(0, 1);

$projectId = null;

if (!empty($_GET['projectId'])) {
    $projectId = (int)$_GET['projectId'];
}

$anyProjects = getUserProjects($con, $_SESSION['userId']);

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
    if (!checkUserTasks($con, $_SESSION['userId'])) {
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
        $projects = getUserProjectsWithTasksQuantities($con, $_SESSION['userId']);
        if (!empty($_GET['searchTaskName'])) {
            $_GET['searchTaskName'] = trim($_GET['searchTaskName']);
            var_dump($_GET);
            var_dump($_SESSION);
            var_dump($_GET['searchTaskName']);
            $tasks = searchUserTasks($con, $_SESSION['userId'], $_GET['searchTaskName']);
            var_dump($tasks);
            if (empty($tasks)) {
                $tasks[0]['name'] = 'Ничего не найдено по Вашему запросу';
                $tasks[0]['deadline'] = null;
                $tasks[0]['project_id'] = null;
                $tasks[0]['is_finished'] = 0;
                $tasks[0]['path_to_file'] = null;
            }
        } else {
            $tasks = getUserTasks($con, $_SESSION['userId'], $projectId);
        }

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
