<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Класс Book для таблицы книг books
 *
 * @property int $id
 * @property string $title Наименование
 * @property string $author Автор
 * @property int $year Год издания
 * @property string|null $created_at Дата/время внесения данных
 * @property string|null $updated_at Дата/время изменения данных
 */
class Book extends ActiveRecord
{
    public const TITLE_LENGTH = 10;

    public const AUTHOR_LENGTH = 7;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%book}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'author', 'year'], 'required',],
            [['year'], 'integer'],
            [['title', 'author', 'year', 'created_at', 'updated_at',], 'safe'],
            [['title'], 'string', 'max' => self::TITLE_LENGTH],
            [['author'], 'string',],
            [['author'], 'checkAuthorLength'], // Собственная функция для проверки длины поля
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * @inheritDoc
     *
     * Отображаемые поля
     */
    public function fields()
    {
        return [
            'id' => 'id',
            'title' => 'title',
            'author' => 'author',
            'year of publishing' => 'year',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Дополнительные поля
     */
    public function extraFields()
    {
        return [
            'createdAt' => 'created_at',
            'updatedAt'=> 'updated_at' ,
        ];
    }

    /**
     * @param $arre string
     *
     * @return bool
     */
    public function checkAuthorLength($attr)
    {
        if (strlen(trim($this->$attr)) > self::AUTHOR_LENGTH) {
            $this->addError(
                $attr,
                'Размер поля '.$attr.' должен быть не более ' . self::AUTHOR_LENGTH . ' символов'
            );

            return false;
        }

        return true;
    }
}
