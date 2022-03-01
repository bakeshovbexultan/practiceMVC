<?php

namespace App\controllers;

use League\Plates\Engine;
use PDO;
use Delight\Auth\Auth;

class LoginController {
	private $templates;
	private $pdo;
	private $auth;

	public function __construct(Engine $engine, PDO $pdo, Auth $auth) {
		$this->templates = $engine;
		$this->pdo = $pdo;
		$this->auth = $auth;
	}

	public function page_login() {
		echo $this->templates->render('page_login');
	}

	public function login() {
		try {
		    $this->auth->login($_POST['email'], $_POST['password']);

		    echo 'User is logged in';
		}
		catch (\Delight\Auth\InvalidEmailException $e) {
			flash()->message('Неправильный адрес или пароль', 'error');
			header('Location: /page_login');
		    die();
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
			flash()->message('Неправильный адрес или пароль', 'error');
			header('Location: /page_login');
		    die();
		}
		catch (\Delight\Auth\EmailNotVerifiedException $e) {
			flash()->message('Email не подтвержден', 'error');
			header('Location: /page_login');
		    die();
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
			flash()->message('Слишком много запросов', 'error');
			header('Location: /page_login');
		    die();
		}
		flash()->message('Success login', 'success');
		header('Location: /users');
	}
}