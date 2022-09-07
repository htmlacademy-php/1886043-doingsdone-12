<?php

/**
 * @param mixed $con
 * @param string $name
 * @param integer $projectId
 * @param string $deadline
 * @param string  $filePath
 */
function addNewTask(mixed $con, string $name, int  $projectId, ?string  $deadline, ?string  $filePath)
{
    $sqlTaskInsertQuery = 'INSERT INTO tasks
                              SET name = ?, project_id = ?,
                              deadline = ?, path_to_file = ?';
    $stmt = mysqli_prepare($con, $sqlTaskInsertQuery);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare: '.mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'siss', $name, $projectId, $deadline, $filePath);

    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_stmt_execute');
    }
}

/**
 * @param mixed $con
 * @param string $name
 * @param string $email
 * @param string $password
 */
function createNewUser(mixed $con, string $email, string  $password, string  $name)
{
    $sqlCreateNewUserQuery = 'INSERT INTO users
                                       SET email = ?,
                                  password = ?,
                                      name = ?';
    $stmt = mysqli_prepare($con, $sqlCreateNewUserQuery);

        if ($stmt === false) {
        exit('Ошибка mysqli_prepare: '.mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'sss', $email, $password, $name);

    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_stmt_execute');
    }
}

/**
 * @param mixed $con
 * @return mixed
 */
function getUsersEmail (mixed $con): mixed
{
    $sqlUsersEmailQuery = 'SELECT u.email
                        FROM users as u';

    $stmt = mysqli_prepare($con, $sqlUsersEmailQuery);

    if ($stmt === false) {
        exit('mysqli_prepare'.mysqli_error($con));
    }

    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result');
    }
    $UsersEmailQueryResult = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$UsersEmailQueryResult) {
	    $error = mysqli_error($con);
	    exit('Ошибка mysqli_fetch' . $error);
    }
    return $UsersEmailQueryResult;
}

/**
 * @param mixed $con
 * @param integer $userId
 * @return mixed
 */
function getUsersData (mixed $con, ?int $userId): mixed
{
    $sqlUsersDataQuery = 'SELECT u.id, u.registration_date, u.email, u.name, u.password
                           FROM users as u';
    if (!empty ($userId)) {
        $sqlUsersDataQuery = $sqlUsersDataQuery . ' WHERE u.id = ?';
    }
    $stmt = mysqli_prepare($con, $sqlUsersDataQuery);

    if ($stmt === false) {
        exit('mysqli_prepare'.mysqli_error($con));
    }
    if (!empty ($userId)) {
        if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
            exit('Ошибка mysqli_bind');
        }
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result');
    }
    $UsersDataQueryResult = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$UsersDataQueryResult) {
	    $error = mysqli_error($con);
	    exit('Ошибка mysqli_fetch' . $error);
    }
    return $UsersDataQueryResult;
}

/**
 * @param mixed $con
 * @param integer $userId
 * @return mixed
 */
function getUserProjects (mixed $con, int $userId): mixed
{
    $sqlProjectsQuery = 'SELECT COUNT(t.id), p.title, p.id
                           FROM projects as p
                           JOIN tasks as t ON p.id = t.project_id
                          WHERE p.user_id = ?
                       GROUP BY p.id';
    $stmt = mysqli_prepare($con, $sqlProjectsQuery);

    if ($stmt === false) {
        exit('mysqli_prepare'.mysqli_error($con));
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
        exit('Ошибка mysqli_bind');
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result');
    }
    $projectsQueryResult = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$projectsQueryResult) {
	    $error = mysqli_error($con);
	    exit('Ошибка mysqli_fetch' . $error);
    }
    return $projectsQueryResult;
}

/**
 * @param mixed $con
 * @param integer $userId
 * @param integer $projectId
 * @return mixed
 */
function getUserTasks ($con, int $userId, ?int $projectId): mixed
{
    $sqlTaskQuery = 'SELECT t.name, t.deadline, t.project_id, t.is_finished, t.path_to_file
                       FROM tasks as t
                       JOIN projects as p ON p.id = t.project_id
                      WHERE p.user_id = ?';
    if (!empty ($projectId)) {
        $sqlTaskQuery = $sqlTaskQuery . ' AND t.project_id = ?';
    }
    $sqlTaskQuery = $sqlTaskQuery . ' ORDER BY t.creation_date DESC';

    $stmt = mysqli_prepare($con, $sqlTaskQuery);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare'.mysqli_error($con));
    }
    if (!empty ($projectId)) {
        if (!mysqli_stmt_bind_param($stmt, 'ii', $userId, $projectId)) {
          exit('Ошибка stmt_bind 1');
        }
    }else{
        if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
            exit('Ошибка stmt_bind 2');
        }
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка stmt_execute');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка stmt_get_result');
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


/**
 * @param string $taskFinishDate
 * @return int
 */
function timeToFinish(string $taskFinishDate): int
{
    $date = new DateTime($taskFinishDate);
    return $date->getTimestamp() - time();
}

/**
 * @param string $taskFinishDate
 * @return string
 */
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

/**
 * @param string $name
 * @param array $data
 * @return string
 */
function include_template(string $name, array $data = []): string
{
    $root = $_SERVER['DOCUMENT_ROOT'];
    $name = $root.'/src/templates/' . $name;
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

/**
 * @param int $projectId
 * @return string
 */
function getProjectUrl(int $projectId): string
{
    $params['projectId'] = $projectId;
    $scriptname = 'index.php';
    $query = http_build_query($params);
    $url = '/' . $scriptname . '?' . $query;
    return $url;
}

/**
 * @param string $name
 * @return string
 */
function getPostVal(string $name): string
{
    return $_POST[$name] ?? '';
}

/**
 * @param string $name
 * @return string
 */
function validateFilled(string $name): ?string
{
    if (empty($_POST[$name])) {
        return 'Это поле должно быть заполнено';
    }
    return null;
}

/**
 * @param mixed $usersEmail
 * @param string $newAccountEmail
 * @return bool
 */
function isMailboxBusy(mixed $usersEmail, string $newAccountEmail): bool
{
    foreach($usersEmail as $userEmail) {
        if ($userEmail['email'] === $newAccountEmail) {
            return true;
        }
    }
    return false;
}

/**
 * @param string $email
 * @return string
 */
function validateEmail(string $email): ?string
{
    if (empty($_POST['email'])) {
        return 'Это поле должно быть заполнено';
    }
    if (!filter_input(INPUT_POST, $email, FILTER_VALIDATE_EMAIL)) {
        return "Введите корректный email";
    }
    return null;
}

/**
 * @param string $dateForVerification
 * @return string
 */
function dateValidate(string $dateForVerification): ?string
{
    if (!empty($_POST[$dateForVerification])) {
        $intermediateDateVariable = $_POST[$dateForVerification];
        $selectedFormat = 'Y-m-d';
        $d = DateTime::createFromFormat($selectedFormat, $intermediateDateVariable);
        if (!($d && $d->format($selectedFormat) === $intermediateDateVariable)) {
            return 'Неверный формат (нужно ГГГГ-ММ-ДД)';
        } elseif(strtotime($intermediateDateVariable) < strtotime(date('Y-m-d'))) {
            return 'Дата должна быть больше или равна текущей';
        }
    }
    return null;
}
