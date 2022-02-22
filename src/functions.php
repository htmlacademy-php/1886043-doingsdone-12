<?php

/**
 * @param mixed $con
 * @param integer $userId
 * @return mixed
 */
function getUserProjects ($con, int $userId)
{
    $sqlProjectsQuery = "SELECT COUNT(t.id), p.title, p.id
                           FROM projects as p
                           JOIN tasks as t ON p.id = t.project_id
                          WHERE p.user_id = ?
                       GROUP BY p.id";
    $stmt = mysqli_prepare($con, $sqlProjectsQuery);

    if ($stmt === false) {
        exit('Ошибка подключения 1: '.mysqli_error($con));
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
        exit('Ошибка подключения2');
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка подключения3');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка подключения4');
    }
    $projectsQueryResult = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$projectsQueryResult) {
	    $error = mysqli_error($con);
	    exit("Ошибка 1 MySQL: " . $error);
    }
    return $projectsQueryResult;
}

/**
 * @param mixed $con
 * @param integer $userId
 * @param integer $projectId
 * @return mixed
 */
function getUserTasks ($con, int $userId, ?int $projectId)
{
    $sqlTaskQuery = "SELECT t.name, t.deadline, t.project_id, t.is_finished
                       FROM tasks as t
                       JOIN projects as p ON p.id = t.project_id
                      WHERE p.user_id = ?";
    if (!empty ($projectId)) {
        $sqlTaskQuery = $sqlTaskQuery . " AND t.project_id = ?";
    }

    $stmt = mysqli_prepare($con, $sqlTaskQuery);

    if ($stmt === false) {
        exit('Ошибка подключения5: '.mysqli_error($con));
    }
    if (!empty ($projectId)) {
        if (!mysqli_stmt_bind_param($stmt, 'ii', $userId, $projectId)) {
          exit('Ошибка подключения6');
        }
    }else{
        if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
            exit('Ошибка подключения6');
        }
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка подключения7');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка подключения8');
    }
    $tasks = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$tasks) {
        exit(http_response_code(404));
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

function getProjectUrl(int $projectId): string
{
    $params['projectId'] = $projectId;
    $scriptname = 'index.php';
    $query = http_build_query($params);
    $url = "/" . $scriptname . "?" . $query;
    return $url;
}
