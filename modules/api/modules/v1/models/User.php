<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\models;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

use app\modules\api\modules\v1\components\security\JwtAuth;

/**
 * Класс User для таблицы пользователей user
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_INACTIVE = 5;
    const STATUS_ACTIVE = 10;

    const AUTH_KEY_LENGTH = 32;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'],
                'required'
            ],
            [
                ['status', 'created_at', 'updated_at'],
                'integer'
            ],
            [
                ['username', 'password_hash', 'password_reset_token', 'email', 'verification_token'],
                'string',
                'max' => 255
            ],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return User|null
     */
    public static function findIdentity($id): ?User
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $token
     *
     * @return User|null
     */
    public static function findIdentityByAccessToken($token, $type = null): ?User
    {
        $userId = JwtAuth::getClaim($token, 'sub');

        return self::findIdentity($userId);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Finds user by username
     * @param string $username
     *
     * @return static
     * @thows NotFoundHttpException
     * @thows UnauthorizedHttpException
     */
    public static function findByUsername($username): static
    {
        $user = static::findOne(['username' => $username]);
        if (!$user) {
            throw new NotFoundHttpException(
                JwtAuth::$messages[JwtAuth::CODE_USER_NOT_FOUND],
                JwtAuth::CODE_USER_NOT_FOUND
            );
        }

        if ($user->status === self::STATUS_INACTIVE) {
            throw new UnauthorizedHttpException(
                JwtAuth::$messages[JwtAuth::CODE_USER_INACTIVE],
                JwtAuth::CODE_USER_INACTIVE
            );
        }

        return $user;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRefreshToken()
    {
        return $this->hasMany(RefreshToken::class, ['user_id' => 'id']);
    }
}