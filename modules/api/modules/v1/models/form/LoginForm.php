<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\models\form;

use Yii;
use yii\base\Model;

use app\modules\api\modules\v1\models\User;

/**
 * Класс формы для аутентификации
 *
 * @property-read User|null $user
 *
 */
class LoginForm extends Model
{
    /**
     * Имя пользователя (вход)
     *
     * @var string
     */
    public $username;

    /**
     * Пароль
     *
     * @var string
     */
    public $password;

    /**
     * Пользователь
     *
     * @var User
     */
    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
