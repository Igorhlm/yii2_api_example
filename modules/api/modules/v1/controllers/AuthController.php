<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;

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