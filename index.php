<?php
require_once 'helpers.php';

$showCompleteTasks = rand(0, 1);

$connection = mysqli_connect('127.0.0.1', 'root', '', 'doingsdone');
if (!$connection) {
    print('Ошибка подключения: ' .mysqli_connect_error());
} else {
    mysqli_set_charset($connection, 'utf8');
    $sqlProjectsQuery = 'SELECT projects.title
                             FROM projects
                             JOIN users ON users.id = projects.user_id
                            WHERE users.id = "3"';
    $projectsQueryResult = mysqli_query($connection, $sqlProjectsQuery);
    $projects = array_column((mysqli_fetch_all($projectsQueryResult, MYSQLI_ASSOC)), 'title');

    $sqlTaskQuery = 'SELECT tasks.name, tasks.deadline, projects.title, tasks.is_finished
                       FROM projects
                       JOIN users ON users.id = projects.user_id
                       JOIN tasks ON projects.id = tasks.project_id
                      WHERE users.id = "3"';
    $taskQueryResult = mysqli_query($connection, $sqlTaskQuery);
    $tasks = mysqli_fetch_all($taskQueryResult, MYSQLI_ASSOC);
    foreach ($tasks as $task) {
        if ($task['is_finished'] === '0') {
            $task['is_finished'] = false;
        } else{
            $task['is_finished'] = true;
        }
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

$pageContent = include_template('main.php', ['projects' => $projects, 'tasks' => $tasks, 'showCompleteTasks' => $showCompleteTasks,]);
$layoutContent = include_template('layout.php', ['content' => $pageContent, 'title' => 'Дела в порядке', 'userName' => 'Константин']);
print($layoutContent);
