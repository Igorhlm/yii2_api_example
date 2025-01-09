<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\models;

use Yii;

/**
 * Класс RefreshToken для таблицы refresh токенов refresh_token
 *
 * @property int $id
 * @property int $user_id id пользователя
 * @property string $refresh_token JWT refresh token
 * @property string|null $created_at Дата\время создания токена
 */
class RefreshToken extends \yii\db\ActiveRecord
{
    public const TOKEN_LENGTH = 200;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%refresh_token}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'refresh_token'], 'required'],
            [['user_id'], 'integer'],
            [['created_at'], 'safe'],
            [['refresh_token'], 'string', 'max' => self::TOKEN_LENGTH],
            [['refresh_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'refresh_token' => 'Refresh Token',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}