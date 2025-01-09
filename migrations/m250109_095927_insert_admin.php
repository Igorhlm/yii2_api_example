<?php

use yii\db\Migration;

class m250109_095927_insert_admin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%user}}', array(
            'username' => 'admin',
            'auth_key' => 'zldSf4jeNi8fXWUuouZCteYszaZboPFS',
            'password_hash' => '$2y$13$XEMTTDe0RyUrxB68sZIjc.i4SC090Jsb1v/5/9y3VIhpQMm6x0iV6',
            'password_reset_token' => NULL,
            'email' => 'admin@example.com',
            'status' => 10,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' =>  date('Y-m-d H:i:s'),
            'verification_token' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand()->delete('{{%user}}', ['username' => 'admin'])->execute();
    }
}
