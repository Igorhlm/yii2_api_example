<?php

return [
    'adminEmail' => 'iagmail@mail.ru',
    'senderEmail' => 'iagmail@mail.ru',
    'senderName' => 'mailer',

    // Количество отдаваемых элементов по умолчанию
    'limit' => 3,

    'jwtSecret' => '2kTNZm42aP6Ye5sP8osJSovJhdkNftfsZATBuRnpr3pxIYWYHSsqAVKdsmVuvglG',

    // TTL access токена (в секундах)
    'accessTokenTime' => 60*2,

    // TTL refresh токена (месяцы)
    'refreshTokenTime' => 1,

    // Формат дата\время (БД)
    'DATETIME_FORMAT' => 'Y-m-d H:i:s',
];
