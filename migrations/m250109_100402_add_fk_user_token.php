<?php

use yii\db\Migration;

class m250109_100402_add_fk_user_token extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey(
            'fk_user_token',
            'refresh_token', 'user_id',
            'user', 'id','CASCADE','CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_user_token', 'refresh_token');
    }
}
