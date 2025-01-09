<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

use app\modules\api\modules\v1\controllers\ApiController;
use app\modules\api\modules\v1\models\Book;

/**
 * Класс контроллера для работы с данными книг
 */
class BookController extends ApiController
{
    /**
     * Actions, для которых необходимо исключить проверку access токена
     *
     * @var array $exceptActions
     */
    public array $exceptActions = [];

    /**
     * @var string $modelClass
     */
    public $modelClass = 'app\modules\api\modules\v1\models\Book';

    /**
     * Список книг
     *
     * @return ActiveDataProvider
     */
    public function actionIndex()
    {
        $query = Book::find();

        $request = Yii::$app->request->get();

        if (isset($request['limit'])) {
            $query->limit((int)$request['limit']);
        } else {
            $query->limit((int)Yii::$app->params['limit']);
        }

        if (isset($request['offset'])) {
            $query->offset((int)$request['offset']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    /**
     * Получение данных отдельно взятой книги
     *
     * @param int $id
     *
     * @return Book
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $book = $this->findModel($id);
        Yii::$app->response->statusCode = 200;

        return $book;
    }

    /**
     * Добавление новой книги
     *
     * @return Book|void
     */
    public function actionCreate()
    {
        $book = new Book();
        $requestParams = Yii::$app->request->bodyParams;

        if ($book->load($requestParams, '') && $book->save()) {
            Yii::$app->getResponse()->setStatusCode(201);
            Yii::$app->getResponse()->statusText = 'Книга добавлена';
            Yii::$app->response->headers->add('Location', '/books/' . $book->id);
        } else {
            return $book;
        }
    }

    /**
     * Изменение данных отдельно взятой книги
     *
     * @param int $id
     *
     * @return Book|void
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $book = $this->findModel($id);
        $requestParams = Yii::$app->request->bodyParams;
        if ($book->load($requestParams, '') && $book->save()) {
            Yii::$app->getResponse()->setStatusCode(204);
            Yii::$app->getResponse()->statusText = 'Данные обновлены';
        }
        else {
            return $book;
        }
    }

    /**
     * Удаление книги
     *
     * @param int $id
     *
     * @return void
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete() === false) {
            throw new BadRequestHttpException('Ошибка удаления');
        }

        Yii::$app->getResponse()->setStatusCode(204);
        Yii::$app->getResponse()->statusText = 'Книга успешно удалена';
    }

    /**
     * @param int $id
     *
     * @return Book
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $book = Book::findOne(['id' => $id]);
        if ($book !== null) {
            return $book;
        }

        throw new NotFoundHttpException('Книга не найдена');
    }
}
