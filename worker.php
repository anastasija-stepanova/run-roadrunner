<?php

declare(strict_types=1);

include 'vendor/autoload.php';

$worker = Spiral\RoadRunner\Worker::create();
$psr17Factory = new Nyholm\Psr7\Factory\Psr17Factory();
$psr7Worker = new Spiral\RoadRunner\Http\PSR7Worker($worker, $psr17Factory, $psr17Factory, $psr17Factory);

while ($request = $psr7Worker->waitRequest()) {
    try {
        $response = $psr17Factory->createResponse();
        $response->getBody()->write('RoadRunner started!');
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $mysqliConn = mysqli_connect('mysql', 'root', 'password', 'roadrunner');
        if ($mysqliConn == false) {
            $response->getBody()->write('Ошибка: Невозможно подключиться к MySQL ' . mysqli_connect_error());
        } else {
            $response->getBody()->write('Соединение установлено успешно');
        }
        mysqli_query(
            $mysqliConn,
            "CREATE TABLE IF NOT EXISTS user (
                   id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                   firstname VARCHAR(30) NOT NULL,
                   lastname VARCHAR(30) NOT NULL
            )"
        );
        mysqli_query(
            $mysqliConn,
            "INSERT INTO user (firstname, lastname) VALUES ('MY first name', 'MY last name')"
        );
        $result = mysqli_query($mysqliConn, "SELECT * FROM my_table");
        $response->getBody()->write("Запрос SELECT вернул {$result->num_rows} строк");

        $psr7Worker->respond($response);
    } catch (Throwable $e) {
        $psr7Worker->getWorker()->error((string)$e);
    }
}