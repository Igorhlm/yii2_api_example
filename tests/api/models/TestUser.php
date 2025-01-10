<?php

namespace app\tests\api\models;

use yii\web\IdentityInterface;
use app\modules\api\modules\v1\models\User;

class TestUser extends User implements IdentityInterface
{
    public $id = "testId";

    /**
     * {@inheritdoc}
     *
     * @return User|null
     */
    public static function findIdentity($id): ?User
    {
        return new static ([
            'id' => 'testUser',
            'email' => 'testUser@testdomain',
            'username' => 'testUser'

        ]);
    }

    /**
     * This method is needed to satisfy the interface.
     *
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): ?User
    {
        return self::findIdentity('testUser');
    }

    /**
     * This method is needed to satisfy the interface.
     *
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * This method is needed to satisfy the interface.
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return 'authKey';
    }

    /**
     * This method is needed to satisfy the interface.
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return true;
    }
}