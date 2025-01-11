<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use OpenApi\Annotations as OA;

use app\modules\api\modules\v1\components\SerializerExample;

/**
 * Class ApiController - базовый класс этого API
 *
 * @OA\Info(
 *     title="Books API",
 *     version="1.0",
 *     description="Пример простого REST API",
 *     @OA\Contact(
 *         email="iagmail@mail.ru"
 *     ),
 * ),
 *
 * @OA\Server(
 *     description="Swagger Yii2 API Example - пример простого API",
 *     url="/",
 * ),
 *
 * @OA\ExternalDocumentation(
 *     description="Спецификация OpenAPI (v3.0, русская)",
 *     url="https://spec.openapis.org/oas/v3.0.0.html"
 * ),
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Authentification by JWT token",
 *     name="JWT",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="JWTAuthentification",
 * ),
 *
 * @OA\Schema(
 *     schema="Meta",
 *     title="Meta",
 *     description="Мета-данные постраничной выдачи",
 *     @OA\Property(
 *         property="meta",
 *         description="Мета-данные постраничной выдачи",
 *         @OA\Property(
 *             property="total",
 *             type="integer",
 *             description="Общее количество",
 *         ),
 *         @OA\Property(
 *             property="offset",
 *             type="integer",
 *             description="''Сдвиг'' выборки от начала",
 *         ),
 *         @OA\Property(
 *             property="limit",
 *             type="integer",
 *             description="Ограничение количества выводимых записей",
 *         ),
 *     )
 * ),
 *
 * @OA\Schema(
 *     title="Exception",
 *     schema="Exception",
 *     description="Данные при возникновении исключения",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Наименование исключения",
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Текст ошибки",
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="int",
 *         description="Код",
 *         enum={10, 20, 30, 40, 50, 60, 70},
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="int",
 *         description="HTTP статус",
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="Тип исключения",
 *     )
 * ),
 *
 * @OA\Schema(
 *     schema="Error",
 *     @OA\Property(
 *         property="field",
 *         type="string",
 *         description="Поле",
 *
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Текст ошибки",
 *     ),
 * )
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

    // По необходимости еще какой-то код, общий для контроллеров API
}
