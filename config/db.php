<?php

// Параметры БД, если используется сервер разработки фреймворка
// Предполагается, что возможно подключение root без пароля
// (имеются соотвестующие настройки)
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2_api_example',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];

/*
// Параметры БД, если используется Docker
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=db-yii2-api-example;dbname=yii2_api_example;',
    'username' => 'root',
    'password' => 'root',
    'charset' => 'utf8',
];
*/
