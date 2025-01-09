<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use app\modules\api\modules\v1\components\SerializerExample;

/**
 * Class ApiController - базовый класс этого API
 */

class ApiController extends Controller
{
    /**
     * Actions, для которых необходимо исключить проверку access токена
     *
     * @var array $exceptActions
     */
    public array $exceptActions = [];

    /**
     * @var string
     */
    public $serializer = SerializerExample::class;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => $this->exceptActions,
        ];

        return $behaviors;
    }

    // Возможно еще какой-то код, общий для контроллеров API
}
