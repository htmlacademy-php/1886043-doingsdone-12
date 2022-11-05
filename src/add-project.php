<?php

require_once 'init.php';
require_once 'functions.php';

if (!isset($_SESSION['userId'])) {
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
    $projects = getEmptyProjectArray();
} else {
    $projects = getUserProjectsWithTasksQuantities($con, $_SESSION['userId']);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['submit'])) {

    $errors ['project_name'] = validateFilled('project_name');
    if (empty($errors['project_name'])) {
        foreach($projects as $project)
        {
            if ($project['title'] === $_POST['project_name']) {
                $errors ['project_name'] = 'Такой проект уже существует';
                break;
            }

        }
    }
    $errors = array_filter($errors);

    if (empty($errors)) {
        addNewProject($con, $_POST['project_name'], $_SESSION['userId']);
        header('Location: /index.php');
    }

}

$pageContent = include_template(
    'form-project.php', [
        'projects' => $projects,
        'showCompleteTasks' => $showCompleteTasks,
        'projectId' => $projectId,
        'errors' => $errors,
    ]
);

$layoutContent = include_template(
    'layout.php', [
        'content' => $pageContent,
        'title' => 'Дела в порядке',
    ]
);

print($layoutContent);
