<?php

/**
*@return mixed
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
