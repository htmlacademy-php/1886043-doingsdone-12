<?php

function getUsersProjects (mixed $con, $userId): mixed
{
    $sqlProjectsQuery = "SELECT projects.title
                           FROM projects
                           JOIN users ON users.id = projects.user_id
                          WHERE users.id = ?";
    $stmt = mysqli_prepare($con, $sqlProjectsQuery);

    if ($stmt === false) {
        exit('Ошибка подключения');
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
        exit('Ошибка подключения');
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка подключения');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка подключения');
    }
    $projectsQueryResult = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$projectsQueryResult) {
	    $error = mysqli_error($con);
	    exit("Ошибка MySQL: " . $error);
    } else {
        return array_column($projectsQueryResult, 'title');
    }
}

function getUsersTasks (mixed $con, int $userId): mixed
{
    $sqlTaskQuery = "SELECT tasks.name, tasks.deadline, projects.title, tasks.is_finished
                       FROM projects
                       JOIN users ON users.id = projects.user_id
                       JOIN tasks ON projects.id = tasks.project_id
                      WHERE users.id = ?";
    $stmt = mysqli_prepare($con, $sqlTaskQuery);
    if ($stmt === false) {
        exit('Ошибка подключения');
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
        exit('Ошибка подключения');
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка подключения');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка подключения');
    }
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$tasks) {
	    $error = mysqli_error($con);
	    exit("Ошибка MySQL: " . $error);
    } else {
        foreach ($tasks as $task) {
            if ($task['is_finished'] === '0') {
                $task['is_finished'] = false;
            } else {
                $task['is_finished'] = true;
            }
        }
        return $tasks;
    }
}

function countTasks(array $innertasks, string $projectName): int
{
    $numberOfTasks = 0;
    foreach ($innertasks as $task) {
        if ($task['title'] === $projectName) {
             $numberOfTasks++;
        }
    }
    return $numberOfTasks;
}

function timeToFinish(string $taskFinishDate): int
{
    $date = new DateTime($taskFinishDate);
    return $date->getTimestamp() - time();
}

function less24hours (string $taskFinishDate = null): string
{
    if ($taskFinishDate === null) {
        return '';
    } elseif (timeToFinish($taskFinishDate) < 86401) {
        return 'task--important';
    } else {
        return '';
    }
}

function include_template($name, array $data = []) {
    $name = 'src/templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}
