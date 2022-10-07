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

$projects = getUserProjectsWithTasksQuantities($con, $_SESSION['userId']);
$tasks = getUserTasksInTimeInterval($con, $_SESSION['userId'], 'withoutTimeLimits', $projectId);

$errors = [];

if ($_SERVER['REQUEST_METHOD']==='POST' && !empty($_POST['submit'])) {

    $rules = [
        'name' => function () {
            return validateFilled('name');
        },
        'project' => function() {
            return validateFilled('project');
        },
        'date' => function () {
            return dateValidate('date');
        }
    ];

    foreach ($_POST as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule();
        }
    }

    $errors = array_filter($errors);
    $pathToUploadFile;

    if (isset($_FILES)) {
        $currentTime = date("YmdHiss");
        $fileExtension = pathinfo($_FILES['task']['name'], PATHINFO_EXTENSION);
        $randomName = $currentTime.'.'.$fileExtension;
        move_uploaded_file($_FILES['task']['tmp_name'], '../uploads' . '/'.$randomName);
        $pathToUploadFile = $_SERVER['DOCUMENT_ROOT'].'/uploads'.'/'.$randomName;
    }

    if (empty($errors)) {
        $date = ($_POST['date'] === '') ? null : ($_POST['date']);
        addNewTask($con, $_POST['name'], intval($_POST['project']), $date, $pathToUploadFile);
        header('Location: /index.php');
    };

}

$pageContent = include_template(
    'form-task.php', [
        'projects' => $projects,
        'tasks' => $tasks,
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
