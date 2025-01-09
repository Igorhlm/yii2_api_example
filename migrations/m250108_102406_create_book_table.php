<?php

use yii\db\Migration;
use yii\db\Expression;
use app\modules\api\modules\v1\models\Book;

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

            'title' => $this->string(Book::TITLE_LENGTH)
                ->notNull()
                ->comment('Наименование'),
            'author' => $this->string(Book::AUTHOR_LENGTH)
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
