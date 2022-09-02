<?php

require_once 'init.php';
require_once 'functions.php';

$userId = 3;
$showCompleteTasks = rand(0, 1);
$projectId = null;

if (!empty($_GET['projectId'])) {
    $projectId = (int)$_GET['projectId'];
}

$con = getConnection();
$projects = getUserProjects($con, $userId);
$tasks = getUserTasks($con, $userId, $projectId);

$errors = [];

if ($_SERVER['REQUEST_METHOD']=='POST') {

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

    if (isset($_FILES)) {
        $fileName = $_FILES['task']['name'];
        $filePath = __DIR__;
        $fileUrl = $fileName;
        move_uploaded_file($_FILES['task']['tmp_name'], $filePath . $fileName);
        $fullFileName = $filePath . $fileName;
    }

    if (empty($errors)) {
        $date = ($_POST['date'] === '') ? null : ($_POST['date']);
        $fullName = !isset($fullFileName) ? null: $fullFileName;
        addNewTask($con, $_POST['name'], intval($_POST['project']), $date, $fullName);
        header("Location: /index.php");
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
        'userName' => 'Юджин'
    ]
);

print($layoutContent);
