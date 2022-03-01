<?php

namespace App\controllers;

use PDO;
use League\Plates\Engine;
use \Delight\Auth\Auth;
use App\controllers\QueryBuilder;

class RegisterController {
	private $auth;
	private $templates;
	private $selector;
	private $token;
	private $qb;
	private $userId;

	public function __construct(QueryBuilder $qb, Engine $engine, PDO $pdo, Auth $auth) {
		$this->templates = $engine;
		$this->pdo = $pdo;
		$this->auth = $auth;
		$this->qb = $qb;
	}

	public function page_register() {
		echo $this->templates->render('page_register');
	}

	public function register() {
		try {
		    $this->userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {
		        //echo 'Send ' . $selector . ' and ' . $token . ' to the user (e.g. via email)';
		        /*
		        echo '  For emails, consider using the mail(...) function, Symfony Mailer, Swiftmailer, PHPMailer, etc.';
		        echo '  For SMS, consider using a third-party service and a compatible SDK';*/
		    });

		    //echo 'We have signed up a new user with the ID ' . $userId;
		}
		//Сформировать флеш сообщение и отправить пользователя обратно на страницу
		catch (\Delight\Auth\InvalidEmailException $e) {
			flash()->message('Invalid email address!', 'error');
			header('Location: /page_register');
		    die();
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
			flash()->message('Invalid password!', 'error');
			header('Location: /page_register');
		    die();
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
			flash()->message('Уведомление! Этот эл. адрес уже занят другим пользователем.', 'error');
			header('Location: /page_register');
		    die();
		}
		/*catch (\Delight\Auth\TooManyRequestsException $e) {
			flash()->message('Too many requests!', 'error');
			header('Location: /page_register');
		    die();
		}*/
		flash()->message('Регистрация успешна', 'success');
		header('Location: /page_login');
	}

	public function email_verification() {
		/*$user = $this->qb->getOne('users_confirmations', $this->userId);
		var_dump($user);
		exit;
		try {
		    $this->auth->confirmEmail($this->selector, $this->token);
		    //echo 'Email address has been verified';
		}
		catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
		    die('Invalid token');
		}
		catch (\Delight\Auth\TokenExpiredException $e) {
		    die('Token expired');
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
		    die('Email address already exists');
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    die('Too many requests');
		}*/
	}
}