<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;

/**
 * Присваивает задаче с определенным идентификатором переданный статус ("Закончено"/"Не закончено")
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param int $taskId Идентификатор задачи, статус которой необходимо задать
 * @param int $isFinished параметр - "флаг", "1" - задача закончена, "0" - не закончена
 * @return void Результатом действия функции есть запись статуса задачи в БД,
 *              у функции нет оператора return
 */
function changeTaskStatus(mysqli $con, int $taskId, int $isFinished): void
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
 * Возвращает все задания всех пользователей, у которых
 * дата выполнения совпадает с текущей датой
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @return array возвращаемый массив, с именем и email-ом пользователя, также все его задачи
 */
function todayTasks (mysqli $con): array
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
    $result = [];
    foreach($foundTasks as $task) {
        $email = $task['email'];
        if(!array_key_exists($email, $result)) {
            $result[$email] = [
                'username' => $task['username'],
                'email' => $task['email'],
                'task' => [],
            ];
        }
        $result[$email]['task'][] = $task['taskname'];
    }

    return $result;
}

/**
 * Возвращает задания конкретного пользователя
 * дата выполнения которых попадает в определенный временной промежуток
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param int $userId идентификатор пользователя в БД
 * @param int $projectId идентификатор пректа в БД
 * @param string $deadline дата запланированого выполнения задачи
 * @return array
 */
function getUserTasksInTimeInterval (mysqli $con, int $userId, string $deadline, ?int $projectId): array
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
            $sqlTaskQuery = $sqlTaskQuery.' AND t.deadline < "'.$today.'"';
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
 * Возвращает задачи, названия которых соответствуют
 * поисковому  запросу
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param int $userId идентификатор пользователя в БД
 * @param string $searchTaskName
 * @return array
 */
function searchUserTasks(mysqli $con, int $userId, string $seachTaskName): array
{
    $sqlSearchUserTasksQuery = 'SELECT t.id, t.name, t.deadline, t.project_id, t.is_finished, t.path_to_file
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
 * Записывает данные нового проекта в БД
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param string $title
 * @param int $userId идентификатор пользователя в БД
 * @return void Результатом действия функции есть добавление нового проекта в БД,
 *              у функции нет оператора return
 */
function addNewProject(mysqli $con, string $title, int $userId): void
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
 * Записывает данные новой задачи в БД
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param string $name название новой задачи, в таблице tasks
 * @param int $projectId идентификатор проектав БД
 * @param string|null $deadline дата запланированого выполнения задачи
 * @param string|null  $filePath полное имя (путь) к сохраняемому файлу
 * @return void Результатом действия функции есть запись новой задачи в БД,
 *              у функции нет оператора return
 */
function addNewTask(mysqli $con, string $name, int  $projectId, ?string  $deadline, ?string  $filePath): void
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
 * Записывает данные нового пользвователя в БД
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param string $name имя пользователя в таблице users
 * @param string $email адрес електронной почты пользователя
 * @param string $password пароль пользвателя
 * @return void Результатом действия функции есть создание нового пользователя в БД,
 *              у функции нет оператора return
 */
function createNewUser(mysqli $con, string $email, string  $password, string  $name): void
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
 * Проверяет наличие email-а в БД
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param string $email адрес електронной почты пользователя
 * @return bool возвращает ИСТИНА, если такой email существует
 */
function checkUsersEmail (mysqli $con, string $email): bool
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
 * Выбирает идентификатор и пароль пользователя с заданным email-ом тз БД
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param string $email адрес електронной почты пользователя
 * @return array массив с идентификатором, email-ом и паролем пользователя с заданным email-ом
 */
function getUsersDataOnEmail (mysqli $con, string $email): array
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
 * Выбирает данные пользователя с заданным идентификатором
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param int $id идентификатор пользователя в таблице users
 * @return array массив с идентификатором, датой регистрации, email-ом, именем и паролем пользователя
 */
function getUsersData (mysqli $con, int $id): array
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
 * Выбирает из БД проекти определенного пользователя
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param int $userId идентификатор пользователя в БД
 * @return array массив с идентификаторами и названиями проектов с заданным идентификатором пользователя
 */
function getUserProjects(mysqli $con, int $userId): array
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
}

/**
 * Проверяет, есть ли у определенного пользователя какие-либо задачи
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param int $userId идентификатор пользователя в БД
 * @return bool возвращает ИСТИНА, если количество задач проектов заданного пользователя отлично от нуля
 */
function checkUserTasks(mysqli $con, int $userId): bool
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
}

/**
 * Выбирает из БД проекты определенного пользователя
 * и считает количество задач привязаных к каждому проекту
 * @param mysqli $con ресурс соединения с SQL БД, возвращаемый функцией con
 * @param int $userId идентификатор пользователя в БД
 * @return array массив с насваниями проектов и их идентификаторами, также количествами задач, относящихся к данному проекту
 */
function getUserProjectsWithTasksQuantities (mysqli $con, int $userId): ?array
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
 * Возвращает время до планового завершения задачи
 * @param string $taskFinishDate
 * @return int возвращает время в формате Timestamp, что осталось до конца выполнения задачи
 */
function timeToFinish(string $taskFinishDate): int
{
    $date = new DateTime($taskFinishDate);
    return $date->getTimestamp() - time();
}

/**
 * Возвращает название класа, если время до завершения задачи менше суток
 * @param string $taskFinishDate
 * @return string возвращает название класса, если время до окончания задания менше суток, иначе пустую строку
 */
function less24hours (?string $taskFinishDate): string
{
    if (!is_null($taskFinishDate) && timeToFinish($taskFinishDate) < 86401) {
        return 'task--important';
    }
    return '';
}

/**
 *
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
 * Для пользователя, у которого нет задач
 * возвращает асоциативный массив, с структурой ключей
 * для использования в шаблонах
 * @return array возвращаемый массив с необходимой структурой ключей
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
}

/**
 * Формирует строку запроса для метода GET c идентификатором проекта
 * @param int $projectId идентификатор проекта в БД
 * @return string Возвращает строку запроса
 */
function getProjectUrl(?int $projectId): string
{
    $params['projectId'] = $projectId;
    $query = http_build_query($params);
    $url = '/' . 'index.php' . '?' . $query;
    return $url;
}

/**
 * Формирует строку запроса для метода GET c датой выполнения задачи
 * @param string $deadline дата запланированого выполнения задачи
 * @param int $showCompleteTasks
 * @return string Возвращает строку запроса
 */
function getDeadlineUrl(string $deadline, int $showCompleteTasks): string
{
    $params = [
        'deadline' => $deadline,
    ];
    $scriptname = 'index.php';
    $url = '/' . $scriptname . '?' . http_build_query($params);
    if ($showCompleteTasks===1) {
        $url = $url.'&show_completed=1';
    }
    return $url;
}

/**
 * Для нового пользователя, у которого нет проектов,
 * создает временный массив для использования в шаблонах
 * @return array Возвращает пустой временный массив
 */
function getEmptyProjectArray(): array
{
    return [
        [
            'id' => 0,
            'count' => null,
            'title' => 'Нет проектов',
        ],
    ];
}

/**
 * Валидирует то поле, которое используется в массиве POST
 * @return array возвращает массив с результатом валидации текущего поля
 */
function validateFields(): array
{
    return [
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
}

/**
 * Возвращает текущее значения для параметра с заданным именем из массива POST
 * @param string $name имя параметра
 * @return string возвращает текущее значение массива POST с заданным именем
 */
function getPostVal(string $name): string
{
    return $_POST[$name] ?? '';
}

/**
 * Проверяет заполнен ли, параметр с заданным именем из массива POST,
 * если нет, возвращает строку с требованием заполнить данное поле
 * @param string $name имя параметра в массиве POST
 * @return string строка с требованием заполнить обязательное поле
 */
function validateFilled(string $name): ?string
{
    if (empty($_POST[$name])) {
        return 'Это поле должно быть заполнено';
    }
    return null;
}

/**
 * Проверяет коректность и заполненость поля с email-ом
 * @param string $email адрес електронной почты пользователя
 * @return string строка с текстом ошибки, если нет ошибки, то возвращает null
 */
function validateEmail(string $email): ?string
{
    $result = null;
    if (empty($_POST['email'])) {
        $result = 'Это поле должно быть заполнено';
    }
    if (!filter_input(INPUT_POST, $email, FILTER_VALIDATE_EMAIL)) {
        $result = "E-mail введен некорректно";
    }
    return $result;
}

/**
 * Проверка соответствия правильности написания или своевременности вводимой даты
 * @param string $dateForVerification
 * @return string возвращает текст ошибки, (если она существует), при некорекном формате даты,
 *                и если дата менше текущей, иначе null
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

/**
 * Функция использует библиотеку Symfony Mailer
 * @return Mailer объект соединения с почтовым сервером
 */
function getMailer()
{
    $config = include __DIR__.'/../config.php';
    $dsn = $config['mailer']['dsn'];
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);
    return $mailer;
}
