<?php

/**
 * @param mixed $con
 * @param int $taskId
 * @param int $isFinished
 * @return void
 */
function changeTaskStatus(mixed $con, int $taskId, int $isFinished): void
{
    $sqlChangeTaskStatus = 'UPDATE tasks
                               SET is_finished = ?
                             WHERE id = ?';
    $stmt = mysqli_prepare($con, $sqlChangeTaskStatus);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare в функции changeTaskStatus: '.mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'ii', $isFinished, $taskId);

    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_stmt_execute в функции changeTaskStatus');
    }
}

/**
 * @param mixed $con
 * @return array
 */
function todayTasks ($con): array
{
    $today = date('Y-m-d');

    $sqlProjectsQuery = 'SELECT u.name as username, u.email, t.name as taskname
                           FROM users as u
                           JOIN projects as p ON u.id = p.user_id
                           JOIN tasks as t ON p.id = t.project_id
                          WHERE t.is_finished = 0 AND t.deadline = "'.$today.'"
                       ORDER BY u.name';

    $stmt = mysqli_prepare($con, $sqlProjectsQuery);

    if ($stmt === false) {
    exit('Ошибка mysqli_prepare'.mysqli_error($con));
    }

    if (!mysqli_stmt_execute($stmt)) {
    exit('Ошибка stmt_execute');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
    exit('Ошибка stmt_get_result');
    }
    $foundTasks = mysqli_fetch_all($res, MYSQLI_ASSOC);

    $restructArray = restructArray($foundTasks);
    return $restructArray;
}

/**
 * @param mixed $con
 * @param integer $userId
 * @param integer $projectId
 * @param string $deadline
 * @return array
 */
function getUserTasksInTimeInterval ($con, int $userId, string $deadline, ?int $projectId): array
{
    $sqlTaskQuery = 'SELECT t.id, t.name, t.deadline, t.project_id, t.is_finished, t.path_to_file
                               FROM tasks as t
                               JOIN projects as p ON p.id = t.project_id
                              WHERE p.user_id = ?';
    switch ($deadline) {
        case 'today':
            $today = date('Y-m-d');
            $sqlTaskQuery = $sqlTaskQuery.' AND t.deadline = "'.$today.'"';
            break;
        case 'tomorrow':
            $tomorrow = (new DateTime('1 days'))->format('Y-m-d');
            $sqlTaskQuery = $sqlTaskQuery.' AND t.deadline = "'.$tomorrow.'"';
            break;
        case 'yesterday':
            $today = date('Y-m-d');
            $sqlTaskQuery = $sqlTaskQuery.' AND t.deadline = "'.$today.'"';
            break;
    }

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
    if (isset($tasks)) {
        foreach ($tasks as $task) {
            if ($task['is_finished'] === '0') {
                $task['is_finished'] = false;
            } else {
                $task['is_finished'] = true;
            }
        }
    }
    return $tasks;
}

/**
 * @param mixed $con
 * @param int $userId
 * @param string $searchTaskName
 * @return array
 */
function searchUserTasks(mixed $con, int $userId, string $seachTaskName): array
{
    $sqlSearchUserTasksQuery = 'SELECT t.name, t.deadline, t.project_id, t.is_finished, t.path_to_file
                                  FROM tasks as t
                                  JOIN projects as p ON p.id = t.project_id
                                 WHERE p.user_id = ?
                                   AND MATCH t.name AGAINST (?)
                              ORDER BY t.creation_date DESC';

    $stmt = mysqli_prepare($con, $sqlSearchUserTasksQuery);

    if ($stmt === false) {
    exit('Ошибка mysqli_prepare'.mysqli_error($con));
    }

    if (!mysqli_stmt_bind_param($stmt, 'is', $userId, $seachTaskName,)) {
    exit('Ошибка stmt_bind 1');
    }

    if (!mysqli_stmt_execute($stmt)) {
    exit('Ошибка stmt_execute');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
    exit('Ошибка stmt_get_result');
    }
    $foundTasks = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $foundTasks;

}

/**
 * @param mixed $con
 * @param string $title
 * @param int $userId
 * @return void
 */
function addNewProject(mixed $con, string $title, int $userId): void
{
    $sqlAddNewProjecQuery = 'INSERT INTO projects
                              SET title = ?, user_id = ?';
    $stmt = mysqli_prepare($con, $sqlAddNewProjecQuery);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare в функции addNewProject: '.mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'si', $title, $userId);

    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_stmt_execute в функции addNewProject');
    }
}

/**
 * @param mixed $con
 * @param string $name
 * @param integer $projectId
 * @param string $deadline
 * @param string  $filePath
 * @return void
 */
function addNewTask(mixed $con, string $name, int  $projectId, ?string  $deadline, ?string  $filePath): void
{
    $sqlTaskInsertQuery = 'INSERT INTO tasks
                              SET name = ?, project_id = ?,
                              deadline = ?, path_to_file = ?';
    $stmt = mysqli_prepare($con, $sqlTaskInsertQuery);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare в функции addNewTask: '.mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'siss', $name, $projectId, $deadline, $filePath);

    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_stmt_execute в функции addNewTask');
    }
}

/**
 * @param mixed $con
 * @param string $name
 * @param string $email
 * @param string $password
 * @return void
 */
function createNewUser(mixed $con, string $email, string  $password, string  $name): void
{
    $sqlCreateNewUserQuery = 'INSERT INTO users
                                       SET email = ?,
                                  password = ?,
                                      name = ?';
    $stmt = mysqli_prepare($con, $sqlCreateNewUserQuery);

        if ($stmt === false) {
        exit('Ошибка mysqli_prepare в функции createNewUser: '.mysqli_error($con));
    }
    mysqli_stmt_bind_param($stmt, 'sss', $email, $password, $name);

    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_stmt_execute в функции createNewUser');
    }
}

/**
 * @param mixed $con
 * @param string $email
 * @return string
 */
function validateUserPassword (mixed $con, string $email): string
{
    $sqlValidateUserPassword = 'SELECT u.password
                        FROM users as u
                        WHERE u.email = ?';

    $stmt = mysqli_prepare($con, $sqlValidateUserPassword);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare в функции validateUserPassword'.mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, 's', $email);

    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute в функции validateUserPassword');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result в функции validateUserPassword');
    }
    $validatedUserPassword = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $validatedUserPassword['0']['password'];
}

/**
 * @param mixed $con
 * @param string $email
 * @return bool
 */
function checkUsersEmail (mixed $con, string $email): bool
{
    $sqlcheckUsersEmail = 'SELECT u.email
                        FROM users as u
                        WHERE u.email = ?';

    $stmt = mysqli_prepare($con, $sqlcheckUsersEmail);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare в функции checkUsersEmail'.mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, 's', $email);

    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute в функции checkUsersEmail');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result в функции checkUsersEmail');
    }
    $checkedUsersEmail = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return (!empty($checkedUsersEmail));
}

/**
 * @param mixed $con
 * @param string $email
 * @return array
 */
function getUsersDataOnEmail (mixed $con, string $email): array
{
    $sqlgetUsersDataOnEmail = 'SELECT id, email, password
                                 FROM users
                                WHERE email = ?';
    $stmt = mysqli_prepare($con, $sqlgetUsersDataOnEmail);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare в функции getUsersDataOnEmail'.mysqli_error($con));
    }
    if (!mysqli_stmt_bind_param($stmt, 's', $email)) {
        exit('Ошибка mysqli_bind в функции getUsersDataOnEmail');
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute в функции getUsersDataOnEmail');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result в функции getUsersDataOnEmail');
    }
    $getUsersDataOnEmailResult = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$getUsersDataOnEmailResult) {
	    $error = mysqli_error($con);
	    exit('Ошибка mysqli_fetch в функции getUsersDataOnEmail' . $error);
    }
    return $getUsersDataOnEmailResult[0];
}

/**
 * @param mixed $con
 * @param int $id
 * @return array
 */
function getUsersData (mixed $con, int $id): array
{
    $sqlUsersDataQuery = 'SELECT id, registration_date, email, name, password
                           FROM users
                          WHERE id = ?';
    $stmt = mysqli_prepare($con, $sqlUsersDataQuery);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare в функции getUsersData'.mysqli_error($con));
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $id)) {
        exit('Ошибка mysqli_bind в функции getUsersData');
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute в функции getUsersData');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result в функции getUsersData');
    }
    $UsersDataQueryResult = mysqli_fetch_all($res, MYSQLI_ASSOC);
    if (!$UsersDataQueryResult) {
	    $error = mysqli_error($con);
	    exit('Ошибка mysqli_fetch в функции getUsersData' . $error);
    }
    return $UsersDataQueryResult[0];
}

/**
 * @param mixed $con
 * @param integer $userId
 * @return array
 */
function getUserProjects(mixed $con, int $userId): array
{
    $sqlCheckUserProjects = 'SELECT id, title
                               FROM projects
                              WHERE user_id = ?';
    $stmt = mysqli_prepare($con, $sqlCheckUserProjects);

    if ($stmt === false) {
      exit('Ошибка mysqli_prepare в функции checkUserProjects'.mysqli_error($con));
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
        exit('Ошибка mysqli_bind в функции checkUserProjects');
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute в функции checkUserProjects');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result в функции checkUserProjects');
    }
    $checkUserProjectsQueryResult = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $checkUserProjectsQueryResult;
};

/**
 * @param mixed $con
 * @param int $userId
 * @return bool
 */
function checkUserTasks(mixed $con, int $userId): bool
{
    $sqlCheckUserTasks = 'SELECT COUNT(t.id) as count, p.id
                            FROM projects as p
                            JOIN tasks as t ON p.id = t.project_id
                           WHERE p.user_id = ?
                        GROUP BY p.id';
    $stmt = mysqli_prepare($con, $sqlCheckUserTasks);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare в функции checkUserTasks'.mysqli_error($con));
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
        exit('Ошибка mysqli_bind в функции checkUserTasks');
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute в функции checkUserTasks');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result в функции checkUserTasks');
    }
    $checkUserTasksQueryResult = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return empty($checkUserTasksQueryResult) ? false : true;
};

/**
 * @param mixed $con
 * @param integer $userId
 * @return array
 */
function getUserProjectsWithTasksQuantities (mixed $con, int $userId): ?array
{
    $sqlProjectsQuery = 'SELECT COUNT(t.id) as count, p.title, p.id
                           FROM projects as p
                           JOIN tasks as t ON p.id = t.project_id
                          WHERE p.user_id = ?
                       GROUP BY p.id';
    $stmt = mysqli_prepare($con, $sqlProjectsQuery);

    if ($stmt === false) {
        exit('Ошибка mysqli_prepare getUserProjects'.mysqli_error($con));
    }
    if (!mysqli_stmt_bind_param($stmt, 'i', $userId)) {
        exit('Ошибка mysqli_bind getUserProjects');
    }
    if (!mysqli_stmt_execute($stmt)) {
        exit('Ошибка mysqli_execute getUserProjects');
    }
    $res = mysqli_stmt_get_result($stmt);
    if ($res === false) {
        exit('Ошибка get_result getUserProjects');
    }
    $projectsQueryResult = mysqli_fetch_all($res, MYSQLI_ASSOC);

    return $projectsQueryResult;
}


/**
 * @param array $array
 * @return array
 */
function restructArray (array $array): array
{
    $restructArray[0] = ['username' => $array[0]['username'], 'email' => $array[0]['email'],'taskname' => []];

    $arrayLength = count($array);

    $j = 0;
    for ($i = 1; $i <= $arrayLength - 1; $i++) {
        $k=0;
        foreach ($restructArray as $arrayElement) {
            if ($arrayElement['username']===$array[$i]['username']) {
                $k++;
            };
        };
        if (!$k>0) {
            $j++;
            $restructArray[$j]['username'] = $array[$i]['username'];
            $restructArray[$j]['email'] = $array[$i]['email'];
            $restructArray[$j]['taskname'] = [];
        }
    }
    $restructArrayLength = count($restructArray);
    for ($i = 0; $i <= $restructArrayLength - 1; $i++) {
        $k = 0;
        for ($j = 0; $j <= $arrayLength - 1; $j++) {
            if ($restructArray[$i]['username'] === $array[$j]['username']) {
                $k++;
                $restructArray[$i]['taskname'][$k] = $array[$j]['taskname'];
            };
        };
    }
    return $restructArray;
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
 * @return array
 */
function getEmptyArray(): array
{
    return
        [
            [
                'id' => 0,
                'name' => '',
                'deadline' => null,
                'project_id' => 0,
                'is_finished' => 0,
                'path_to_file' => null,
            ],
        ];
};

/**
 * @param int $projectId
 * @return string
 */
function getProjectUrl(int $projectId): string
{
    $params['projectId'] = $projectId;
    $query = http_build_query($params);
    $url = '/' . 'index.php' . '?' . $query;
    return $url;
}

/**
 * @param string $deadline
 * @param int $showCompleteTasks
 * @return string
 */
function getDeadlineUrl(string $deadline, int $showCompleteTasks): string
{
    $params['deadline'] = $deadline;
    $scriptname = 'index.php';
    $url = '/' . $scriptname . '?' . 'deadline='. $deadline;
    if ($showCompleteTasks===1) {
        $url = $url.'&show_completed=1';
    }
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
 * @param string $email
 * @return string
 */
function validateEmail(string $email): ?string
{
    if (empty($_POST['email'])) {
        return 'Это поле должно быть заполнено';
    }
    if (!filter_input(INPUT_POST, $email, FILTER_VALIDATE_EMAIL)) {
        return "E-mail введен некорректно";
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
