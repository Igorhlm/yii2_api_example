<?php

use yii\db\Migration;
use yii\db\Expression;

/**
 * Handles the creation of table `{{%book}}`.
 */
class m250108_102406_create_book_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%book}}', [
            'id' => $this->primaryKey(),

            'title' => $this->string(50)
                ->notNull()
                ->comment('Наименование'),
            'author' => $this->string(50)
                ->notNull()
                ->comment('Автор(ы)'),
            'year' => $this->integer()
                ->notNull()
                ->comment('Год издания'),

            'created_at' => $this->dateTime()
                ->defaultValue(new Expression('CURRENT_TIMESTAMP'))
                ->comment('Дата внесения данных'),
            'updated_at' => $this->dateTime()
                ->defaultValue(new Expression('CURRENT_TIMESTAMP'))
                ->comment('Дата изменения данных')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%book}}');
    }
}
