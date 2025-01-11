<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use OpenApi\Annotations as OA;

use app\modules\api\modules\v1\models\RefreshToken;
use app\modules\api\modules\v1\models\User;
use app\modules\api\modules\v1\components\security\JwtAuth;
use app\modules\api\modules\v1\models\form\LoginForm;

/**
 * Класс контроллера для аутентификации
 */
class AuthController extends ApiController
{
    /**
     * Actions, для которых необходимо исключить проверку access токена
     *
     * @var array $exceptActions
     */
    public array $exceptActions = [
        'login',
        'refresh-tokens'
    ];

    /**
     * Вход пользователя (аутентификация)
     *
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Authentication"},
     *     operationId="login",
     *     summary="Вход пользователя",
     *     description="Вход пользователя",
     *     security={{ "JWTAuthentification": {} }},
     *     @OA\RequestBody(
     *         description="Вход пользователя.<br/>
     *                      Для входа использовать: admin/admin<br/>
     *                      см. также Insomnia, тот же метод (после импорта данных)<br/>
     *                      Изменить время жизни access токена см. config/params.php, параметр accessTokenTime,<br/>
     *                      (также TTL refresh токена - параметр refreshTokenTime, там же)",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"username", "password"},
     *                 @OA\Property(
     *                     property="username",
     *                     description="Имя пользователя",
     *                     @OA\Schema(
     *                         type="string",
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="Пароль",
     *                     @OA\Schema(
     *                         type="string",
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Вход - успешно",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Tokens"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Пользователь не прошел аутентификацию",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь не найден",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибки валидации данных",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Error")
     *         ),
     *     ),
     * )
     *
     * @return LoginForm|array
     * @thows NotFoundHttpException
     * @thows UnauthorizedHttpException
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        $post = Yii::$app->request->post();
        $model->username = $post['username'];
        $model->password = $post['password'];

        if ($model->validate()) {
            $model->login();
            Yii::$app->getResponse()->setStatusCode(201);
            Yii::$app->getResponse()->statusText = 'Вход - успешно';

            return [
                'accessToken' => JwtAuth::createAccessToken($model->user),
                'refreshToken' => JwtAuth::createRefreshToken($model->user)
            ];
        } else {
            return $model;
        }
    }

    /**
     * Обновление токенов
     *
     * @OA\Post(
     *     path="/api/v1/auth/refresh-tokens",
     *     tags={"Authentication"},
     *     operationId="refresh",
     *     summary="Обновление токенов",
     *     description="Обновление токенов",
     *     security={{ "JWTAuthentification": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"refreshToken"},
     *                 @OA\Property(
     *                     property="refreshToken",
     *                     description="Refresh токен",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Токены",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Tokens"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Токен отсутствует или не найден на сервере",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Время жизни токена истекло",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Пользователь (или токен) не найден",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     * )
     *
     * @return array
     * @throws UnauthorizedHttpException
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionRefreshTokens()
    {
        $refreshToken = Yii::$app->request->post('refreshToken');
        $user = JwtAuth::checkRefreshToken($refreshToken);

        return [
            'accessToken' => JwtAuth::createAccessToken($user),
            'refreshToken' => JwtAuth::createRefreshToken($user)
        ];
    }

    /**
     * Выход пользователя
     *
     * @OA\Patch(
     *     path="/api/v1/auth/logout",
     *     tags={"Authentication"},
     *     operationId="logout",
     *     summary="Выход пользователя",
     *     description="Выход пользователя",
     *     security={{ "JWTAuthentification": {} }},*
     *     @OA\Response(
     *          response=200,
     *          description="Выход - успешно"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Токен отсутствует или структура токена некорректна",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Время жизни токена истекло",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     * )
     *
     * @return void
     * @throws BadRequestHttpException
     * @throws UnauthorizedHttpException
     */
    public function actionLogout()
    {
        $accessToken = JwtAuth::getAccessToken();

        $user = User::findIdentityByAccessToken($accessToken);
        $refreshToken = RefreshToken::findOne(['user_id' => $user->id]);

        $refreshToken->delete();
        Yii::$app->getResponse()->setStatusCode(200);
        Yii::$app->getResponse()->statusText = 'Выход - успешно';
    }
}