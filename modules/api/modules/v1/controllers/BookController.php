<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use OpenApi\Annotations as OA;

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
     * @OA\Get(
     *     path="/api/v1/book",
     *     tags={"Books"},
     *     operationId="listBooks",
     *     description="Получение списка книг",
     *     security={{ "JWTAuthentification": {} }},
     *     @OA\Parameter(
     *         name="expand",
     *         in="query",
     *         description="Дополнительные данные: createdAt, updatedAt",
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Ограничение количества выводимых записей",
     *     ),
     *     @OA\Parameter(
     *         name="offset",
     *         in="query",
     *         description="''Сдвиг'' выборки от начала",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список книг",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="items",
     *                         type="array",
     *                         @OA\Items(ref="#/components/schemas/Book")
     *                     ),
     *                 ),
     *             },
     *             @OA\Schema(
     *                 ref="#/components/schemas/Meta",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Токен отсутствует или структура токена некорректна",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Время жизни токена истекло",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     * )
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
     * @OA\Get(
     *     path="/api/v1/book/{id}",
     *     tags={"Books"},
     *     operationId="getBook",
     *     description="Получение данных отдельно взятой книги",
     *     security={{ "JWTAuthentification": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id книги, данные которой необходимо получить",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="expand",
     *         in="query",
     *         description="Дополнительные данные",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ответ при выводе указанного элемента",
     *         @OA\JsonContent(
     *            ref="#/components/schemas/Book",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Токен отсутствует или структура токена некорректна",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Время жизни токена истекло",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Книга не найдена",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception",
     *         ),
     *     ),
     * )
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
     * @OA\Post(
     *     path="/api/v1/book",
     *     tags={"Books"},
     *     operationId="addBook",
     *     description="Добавление новой книги",
     *     security={{ "JWTAuthentification": {} }},
     *     @OA\RequestBody(
     *         description="Добавление новой книги",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "author", "year"},
     *                 @OA\Property(
     *                     property="title",
     *                     description="Наименование",
     *                     @OA\Schema(
     *                         type="string",
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="author",
     *                     description="Автор(ы)",
     *                     @OA\Schema(
     *                         type="string",
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="year",
     *                     description="Год издания",
     *                     @OA\Schema(
     *                         type="integer",
     *                         format="int32",
     *                     )
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Книга добавлена",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Токен отсутствует или структура токена некорректна",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Время жизни токена истекло",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибки валидации данных",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Error")
     *         ),
     *     ),
     * )
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
     * @OA\Put(
     *     path="/api/v1/book/{id}",
     *     tags={"Books"},
     *     operationId="updateBook",
     *     description="Изменение данных отдельно взятой книги",
     *     security={{ "JWTAuthentification": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id книги, данные которой необходимо изменить",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Изменение данных отдельно взятой книги (все поля)",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "author", "year"},
     *                 @OA\Property(
     *                     property="title",
     *                     description="Наименование",
     *                     @OA\Schema(
     *                         type="string",
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="author",
     *                     description="Автор(ы)",
     *                     @OA\Schema(
     *                         type="string",
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="year",
     *                     description="Год издания",
     *                     @OA\Schema(
     *                         type="integer",
     *                         format="int32",
     *                     )
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Данные обновлены",
     *         @OA\JsonContent(
     *            ref="#/components/schemas/Book",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Токен отсутствует или структура токена некорректна",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Время жизни токена истекло",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Книга не найдена",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибки валидации данных",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Error")
     *         ),
     *     ),
     * )
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
     * Изменение данных отдельно взятой книги (отдельные поля)
     *
     * @OA\Patch(
     *     path="/api/v1/book/{id}",
     *     tags={"Books"},
     *     operationId="updateBookPart",
     *     description="Изменение данных отдельно взятой книги (отдельные поля)",
     *     security={{ "JWTAuthentification": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id книги, данные которой необходимо изменить",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\RequestBody( description="Изменение данных отдельно взятой книги",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="title",
     *                     description="Наименование",
     *                     @OA\Schema(
     *                         type="string",
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="author",
     *                     description="Автор(ы)",
     *                     @OA\Schema(
     *                         type="string",
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="year",
     *                     description="Год издания",
     *                     @OA\Schema(
     *                         type="integer",
     *                         format="int32",
     *                     )
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Данные обновлены",
     *         @OA\JsonContent(
     *            ref="#/components/schemas/Book",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Токен отсутствует или структура токена некорректна",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Время жизни токена истекло",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Книга не найдена",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception",
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Ошибки валидации данных",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Error")
     *         ),
     *     ),
     * )
     *
     * @param int $id
     * @return Book
     * @throws NotFoundHttpException
     */
    public function actionUpdatePart($id)
    {
        $book = $this->findModel($id);
        $requestParams = Yii::$app->request->bodyParams;
        if ($book->load($requestParams, '') && $book->save()) {
            Yii::$app->getResponse()->setStatusCode(200);
            Yii::$app->getResponse()->statusText = 'Данные обновлены';
        }

        return $book;
    }

    /**
     * Удаление книги
     *
     * @OA\Delete(
     *     path="/api/v1/book/{id}",
     *     tags={"Books"},
     *     operationId="deleteBook",
     *     description="Удаление книги",
     *     security={{ "JWTAuthentification": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id удаляемой книги",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Книга успешно удалена",
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Токен отсутствует или структура токена некорректна",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Время жизни токена истекло",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Книга не найдена",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/Exception",
     *         ),
     *     ),
     * )
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
