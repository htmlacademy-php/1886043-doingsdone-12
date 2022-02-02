<?php

function connection(string $login, ?string $password): mixed
{
    $con = mysqli_connect('127.0.0.1', $login, $password, 'doingsdone');
    mysqli_set_charset($con, 'utf8');
    return $con;

}
