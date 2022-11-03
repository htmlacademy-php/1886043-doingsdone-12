<?php

session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/**
* Создает ресурс соединения с SQL БД
*@return mysqli ресурс соединения с SQL БД
*/
function getConnection()
{
    $config = include __DIR__.'/../config.php';
    $con = mysqli_connect(
        $config['db']['host'],
        $config['db']['login'],
        $config['db']['password'],
        $config['db']['dbName']
    );
    if (!$con) {
        exit('Ошибка подключения: ' .mysqli_connect_error());
    } else {
        if (!mysqli_set_charset($con, 'utf8')) {
            exit('Ошибка подключения');
        }
        return $con;
    }
}
