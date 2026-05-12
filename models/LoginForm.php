<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm — formulário de autenticação do cidadão.
 *
 * @property-read Cidadao|null $user
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    public function rules()
    {
        return [
            [['username', 'password'], 'required',
                'message' => 'Este campo é obrigatório.'],
            ['rememberMe', 'boolean'],
            ['password',   'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username'   => 'Usuário ou E-mail',
            'password'   => 'Senha',
            'rememberMe' => 'Lembrar-me',
        ];
    }

    public function validatePassword($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Usuário ou senha incorretos.');
            }
        }
    }

    public function login(): bool
    {
        if ($this->validate()) {
            return Yii::$app->user->login(
                $this->getUser(),
                $this->rememberMe ? 3600 * 24 * 30 : 0
            );
        }
        return false;
    }

    public function getUser(): ?Cidadao
    {
        if ($this->_user === false) {
            $this->_user = Cidadao::findByUsername((string) $this->username);
        }
        return $this->_user;
    }
}
