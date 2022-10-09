<?php

require_once 'vendor/autoload.php';
require_once 'src/init.php';
require_once 'src/functions.php';

use Symfony\Component\Mime\Email;

$con = getConnection();
$allUsersTasks = todayTasks($con);
$today = date('Y-m-d');

foreach ($allUsersTasks as $userTask)
{
    $mailText = "\n" . 'Уважаемый, ' . $userTask['username'] . "\n" . 'У Вас запланирована задача - ' . "\n";
    foreach ($userTask['taskname'] as $todayTask)
    {
        $mailText = $mailText.$todayTask . ' на ' . $today . "\n";
    };

    $message = new Email();
    $message->to($userTask['email']);
    $message->from("keks@phpdemo.ru");
    $message->subject("Уведомление от сервиса «Дела в порядке»");
    $message->text($mailText);

    $mailer = getMailer();
    $mailer->send($message);
}
