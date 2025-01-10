<?php

namespace tests\api;

use \ApiTester;
use tests\fixtures\BookFixture;
//use app\tests\models\TestUser;

class GetBookCest
{
    public function _before(ApiTester $I)
    {
        $I->haveFixtures([
            'book' => [
                'class' => BookFixture::class,
                'dataFile' => codecept_data_dir() . 'book.php'
            ]
        ]);

        $I->amBearerAuthenticated('accessToken');
    }

    public function testGetBooks(ApiTester $I)
    {
        $I->wantTo('Get books via API');
        $I->sendGet('/api/v1/book');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"title":"new book5"');
    }

    public function testViewBook(ApiTester $I)
    {
        $I->wantTo('View book via API');
        $I->sendGet('/api/v1/book/5');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseEquals('{"id":5,"title":"new book5","author":"author5","year of publishing":2025}');
    }

    public function testNotFoundBook(ApiTester $I)
    {
        $I->wantTo('View book via API - not found book');
        $I->sendGet('/api/v1/book/2000000');

        $I->seeResponseCodeIs(404);
    }

    public function testCreateBook(ApiTester $I)
    {
        $I->wantTo('Create book via API');
        $I->sendPost('/api/v1/book', [
            'title' => 'new book',
            'author' => 'author',
            'year' => 2024,
        ]);

        $I->seeResponseCodeIs(201);
        $I->seeHttpHeader('Location', '/books/6');
    }

    public function testUpdateBook(ApiTester $I) //PUT method
    {
        $I->wantTo('Update book via API');
        $I->sendPut('/api/v1/book/2', [
            'title' => 't777',
            'author' => 'a777',
            'year' => 2024,
        ]);;

        $I->seeResponseCodeIs(204);
    }

    public function testDeleteBook(ApiTester $I)
    {
        $I->wantTo('Delete book via API');
        $I->sendDelete('/api/v1/book/1');

        $I->seeResponseCodeIs(204);
    }
}
