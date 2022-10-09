<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;

session_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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

function getMailer()
{
    $config = include __DIR__.'/../config.php';
    $dsn = $config['mailer']['dsn'];
    $transport = Transport::fromDsn($dsn);
    $mailer = new Mailer($transport);
    return $mailer;
}

