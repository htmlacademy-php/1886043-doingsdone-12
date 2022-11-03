<?php

require_once 'src/init.php';
require_once 'src/functions.php';

if (!isset($_SESSION['userEmail'])) {
    header('Location: /src/guest.php');
}

$con = getConnection();

$showCompleteTasks = isset($_GET['show_completed']) ? (int)$_GET['show_completed'] : 0;

$projectId = null;

if (!empty($_GET['projectId'])) {
    $projectId = (int)$_GET['projectId'];
}

if (isset($_GET['check'])) {
    changeTaskStatus($con, (int)$_GET['task_id'], (int)$_GET['check']);
}

$anyProjects = getUserProjects($con, $_SESSION['userId']);

if (empty($anyProjects )) {
    $projects = getEmptyProjectArray();

    $tasks = getEmptyArray();
    $tasks[0]['name'] = 'Нет заданий';
} else {
    if (!checkUserTasks($con, $_SESSION['userId'])) {
        $projects = $anyProjects;
        $tasks = getEmptyArray();
        $tasks[0]['name'] = 'Нет заданий';
    } else {
        $projects = getUserProjectsWithTasksQuantities($con, $_SESSION['userId']);
        if (!empty($_GET['searchTaskName'])) {
            $_GET['searchTaskName'] = trim($_GET['searchTaskName']);
            $tasks = searchUserTasks($con, $_SESSION['userId'], $_GET['searchTaskName']);
            if (empty($tasks)) {
                $tasks = getEmptyArray();
                $tasks[0]['name'] = 'Ничего не найдено по Вашему запросу';
            }
        } elseif (isset($_GET['deadline'])) {
            $tasks = getUserTasksInTimeInterval($con, $_SESSION['userId'], $_GET['deadline'], $projectId);
        } else {
            $tasks = getUserTasksInTimeInterval($con, $_SESSION['userId'], 'withoutTimeLimits', $projectId);
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
