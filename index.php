<?php

require_once 'helpers.php';
require 'init.php';

const USERID = 3;

$showCompleteTasks = rand(0, 1);
$con = connection('root','');

function getUsersProjects (mixed $con, $userId): mixed
{
    $sqlProjectsQuery = "SELECT projects.title
                           FROM projects
                           JOIN users ON users.id = projects.user_id
                          WHERE users.id = '$userId'";
    $projectsQueryResult = mysqli_query($con, $sqlProjectsQuery);
    return array_column((mysqli_fetch_all($projectsQueryResult, MYSQLI_ASSOC)), 'title');
}

function getUsersTasks (mixed $con, int $userId): mixed
{
    $sqlTaskQuery = "SELECT tasks.name, tasks.deadline, projects.title, tasks.is_finished
                       FROM projects
                       JOIN users ON users.id = projects.user_id
                       JOIN tasks ON projects.id = tasks.project_id
                      WHERE users.id = '$userId'";
    $taskQueryResult = mysqli_query($con, $sqlTaskQuery);
    $tasks = mysqli_fetch_all($taskQueryResult, MYSQLI_ASSOC);
    foreach ($tasks as $task) {
        if ($task['is_finished'] === '0') {
            $task['is_finished'] = false;
        } else {
            $task['is_finished'] = true;
        }
    }
    return $tasks;
}

if (!$con) {
    print('Ошибка подключения: ' .mysqli_connect_error());
    } else {
    $projects = getUsersProjects($con, USERID);
    $tasks = getUsersTasks($con, USERID);
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
