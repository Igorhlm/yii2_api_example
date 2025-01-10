<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\components\security;

use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Carbon\Carbon;

use app\modules\api\modules\v1\models\RefreshToken;
use app\modules\api\modules\v1\models\User;

use UnexpectedValueException;

/**
 * Класс для работы с токенами
 */
class JwtAuth
{
    const CODE_TOKEN_EMPTY = 10;
    const CODE_TOKEN_INCORRECT = 20;
    const CODE_TOKEN_EXPIRED = 30;

    const CODE_USER_INACTIVE = 40;
    const CODE_USER_LOGOUT = 50;
    const CODE_USER_NOT_FOUND = 60;

    const CODE_BOOK_NOT_FOUND = 70;

    private const PREFIX = 'Bearer ';

    /**
     * Массив сообщений, соответствующих кодам
     *
     * @var array
     */
    public static $messages = [
        self::CODE_TOKEN_EMPTY => 'Токен отсутствует',
        self::CODE_TOKEN_INCORRECT => 'Структура токена некорректна',
        self::CODE_TOKEN_EXPIRED =>'Время жизни токена истекло',
        self::CODE_USER_INACTIVE =>'Пользователь неактивный',
        self::CODE_USER_LOGOUT => 'Пользователь выполнил выход',
        self::CODE_USER_NOT_FOUND => 'Пользователь не найден',
        self::CODE_BOOK_NOT_FOUND => 'Книга не найдена',
    ];

    private const REFRESH_TOKEN_TTL = 3;

    private const HS256 = 'HS256';

    /**
     * Создание access токена
     *
     * @param User $user
     *
     * @return string
     */
    public static function createAccessToken(User $user): string
    {
        $current = time();
        $expireAccessToken = $current + Yii::$app->params['accessTokenTime'];
        $payload = [
            'iss' => Url::base(true),
            'aud' => Url::base(true),
            'sub' => $user->id,
            'iat' => $current,
            'nbf'  => $current,
            'exp' => $expireAccessToken,
        ];
        $accessToken = JWT::encode($payload, Yii::$app->params['jwtSecret'], self::HS256);
        $accessToken = self::PREFIX.$accessToken;

        return $accessToken;
    }

    /**
     * Создание refresh token`а
     *
     * @param User $user
     *
     * @return RefreshToken
     */
    public static function createRefreshToken(User $user): RefreshToken
    {
        $refreshToken = RefreshToken::findOne(['user_id' => $user->id]);
        if (!$refreshToken) {
            $refreshToken = new RefreshToken();
            $refreshToken->user_id = $user->id;
            $refreshToken->refresh_token = Yii::$app->security->generateRandomString(RefreshToken::TOKEN_LENGTH);
            $refreshToken->save();
        }

        return $refreshToken;
    }

    /**
     * @return string;
     */
    public static function getAccessToken(): string
    {
        $authorizationHeader = (string)Yii::$app->request->headers->get('authorization');
        preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches);
        $accessToken = !empty($matches[1]) ? $matches[1] : '';

        return $accessToken;
    }

    /**
     * Проверка refresh token`а
     *
     * @param string|null refreshTokenString
     *
     * @return mixed
     * @throws UnauthorizedHttpException
     * @throws NotFoundHttpException
     */
    public static function checkRefreshToken(?string $refreshTokenString)
    {
        if (empty($refreshTokenString)) {
            throw new BadRequestHttpException('Токен отсутствует');
        }

        $refreshToken = RefreshToken::find()
            ->where(['refresh_token' => $refreshTokenString])
            ->with('user')
            ->one();

        if (!$refreshToken) {
            throw new NotFoundHttpException('Токен не найден на сервере');
        }

        $expRefreshToken = self::calcExpirationTimeRefreshToken($refreshToken);

        if (time() > $expRefreshToken) {
            throw new UnauthorizedHttpException('Время жизни токена истекло');
        }

        return $refreshToken->user;
    }

    /**
     * Получение отдельно взятого параметра access token`а
     *
     * @param string $accessToken
     * @param string $claim
     *
     * @return mixed
     */
    public static function getClaim(string $accessToken, string $claim): mixed
    {
        $claimObject = self::getAccessTokenData($accessToken);

        return $claimObject->$claim;
    }

    /**
     * Извлечение данных access token`а
     *
     *@param string $accessToken
     *
     *@return object
     */
    private static function getAccessTokenData(string $accessToken): object
    {
        if (empty($accessToken)) {
            throw new BadRequestHttpException(
                self::$messages[self::CODE_TOKEN_EMPTY],
                self::CODE_TOKEN_EMPTY
            );
        }

        try {
            $claims = JWT::decode(
                $accessToken,
                new Key(Yii::$app->params['jwtSecret'], 'HS256')
            );
        } catch(ExpiredException $e) {
            throw new UnauthorizedHttpException(
                self::$messages[self::CODE_TOKEN_EXPIRED],
                self::CODE_TOKEN_EXPIRED
            );
        } catch(UnexpectedValueException $e) {
            throw new BadRequestHttpException(
                self::$messages[self::CODE_TOKEN_INCORRECT],
                self::CODE_TOKEN_INCORRECT);
        }

        $user = User::find()
            ->where(['id' => $claims->sub])
            ->with('refreshToken')
            ->one();

        if (!$user) {
            throw new NotFoundHttpException(
                self::$messages[self::CODE_USER_NOT_FOUND],
                self::CODE_USER_NOT_FOUND
            );
        } elseif ($user->status == User::STATUS_INACTIVE) {
            throw new UnauthorizedHttpException(
                self::$messages[self::CODE_USER_INACTIVE],
                self::CODE_USER_INACTIVE
            );
        } elseif (!$user->refreshToken) {
            throw new UnauthorizedHttpException('Необходимо выполнить вход');
        }

        return $claims;
    }

    /**
     * Вычисление времени истечения refresh токена
     *
     * @param RefreshToken $refreshToken
     *
     * @return int
     */
    private static function calcExpirationTimeRefreshToken(RefreshToken $refreshToken): int
    {
        return
            Carbon::createFromFormat(
                Yii::$app->params['DATETIME_FORMAT'],
                $refreshToken->created_at)
                ->addMonths((int)Yii::$app->params['refreshTokenTime'], self::REFRESH_TOKEN_TTL)
                ->getTimestamp();
    }
}