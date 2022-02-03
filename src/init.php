<?php

function connection(string $host, string $login, ?string $password, string $dbName): mixed
{
    $con = mysqli_connect($host, $login, $password, $dbName);
    if (!$con) {

        exit('Ошибка подключения: ' .mysqli_connect_error());
    } else {
        mysqli_set_charset($con, 'utf8');
        return $con;
    }
}
