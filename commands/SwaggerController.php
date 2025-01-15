<?php

declare(strict_types=1);

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

class SwaggerController extends Controller
{
    const NOT_CREATE_FILE = 'Не удалось создать файл';

    const FILE_CREATED = 'Файл успешно создан';

    /**
     * Создание файла документации Swagger через вызо в exec()
     *
     * @return int
    */
    public function actionSwaggerExec()
    {
        $res = exec(
            './vendor/bin/openapi modules/api/modules/v1 '.
            '-o web/docs/swagger.json --legacy'
        );

        if ($res === false) {
            echo self::NOT_CREATE_FILE."\n";
            return ExitCode::CANTCREAT;
        }

        echo self::FILE_CREATED."\n";
        return ExitCode::OK;
    }
}