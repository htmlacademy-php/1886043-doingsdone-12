<?php
require_once 'helpers.php';

$showCompleteTasks = rand(0, 1);
$projectsCategories = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasksLists = [
    [
        'task' => 'Собеседование в IT компании',
        'finishDate' => '01.07.2022',
        'category' => 'Работа',
        'finishFlag' => false,
    ],
    [
        'task' => 'Выполнить тестовое задание',
        'finishDate' => '25.01.2022',
        'category' => 'Работа',
        'finishFlag' => false,
    ],
    [
        'task' => 'Сделать задание первого раздела',
        'finishDate' => '31.12.2021',
        'category' => 'Учеба',
        'finishFlag' => true,
    ],
    [
        'task' => 'Встреча с другом',
        'finishDate' => '30.12.2021',
        'category' => 'Входящие',
        'finishFlag' => false,
    ],
    [
        'task' => 'Купить корм для кота',
        'finishDate' => null,
        'category' => 'Домашние дела',
        'finishFlag' => false,
    ],
    [
        'task' => 'Заказать пиццу',
        'finishDate' => null,
        'category' => 'Домашние дела',
        'finishFlag' => false,
    ],
];

function countTasks(array $innerTasksLists, string $projectName): int
{
    $numberOfTasks = 0;
    foreach ($innerTasksLists as $task) {
        if ($task['category'] === $projectName) {
             $numberOfTasks++;
        }
    }
    return $numberOfTasks;
}

function timeToFinish(string $finishPoint): int
{
    $date = new DateTime($finishPoint);
    return $date->getTimestamp() - time();
}

function less24hours(string $Endpoint = NULL, int $timeLimit = 86401): bool
{
    if (($Endpoint != '') && (timeToFinish($Endpoint) < $timeLimit)) {
        return TRUE;
    } else {
        return FALSE;
    };
}

$pageContent = include_template('main.php', ['projectsCategories' => $projectsCategories, 'tasksLists' => $tasksLists, 'showCompleteTasks' => $showCompleteTasks,]);
$layoutContent = include_template('layout.php', ['content' => $pageContent, 'title' => 'Дела в порядке', 'userName' => 'Константин']);
print($layoutContent);
