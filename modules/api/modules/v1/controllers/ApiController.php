<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\controllers;

use yii\rest\Controller;
use app\modules\api\modules\v1\components\SerializerExample;

/**
 * Class ApiController - базовый класс этого API
 */

class ApiController extends Controller
{
    /**
     * @var string
     */
    public $serializer = SerializerExample::class;

    // Возможно еще какой-то код, общий для контроллеров API
}
