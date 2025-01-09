<?php

use yii\db\Migration;
use yii\db\Expression;
use app\modules\api\modules\v1\models\RefreshToken;

/**
 * Handles the creation of table `{{%refresh_tokens}}`.
 */
class m250109_100140_create_refresh_tokens_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%refresh_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()
                ->notNull()
                ->comment('id пользователя'),
            'refresh_token' => $this->string(RefreshToken::TOKEN_LENGTH)
                ->notNull()
                ->comment('JWT refresh token'),
            'created_at' => $this->dateTime()
                ->defaultValue(new Expression('CURRENT_TIMESTAMP'))
                ->comment('Дата\время создания токена'),
        ]);

        $this->createIndex('idx-refresh-token', '{{%refresh_token}}', 'refresh_token', true);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%refresh_token}}');
    }
}
