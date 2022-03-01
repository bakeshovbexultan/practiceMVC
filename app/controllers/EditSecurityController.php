<?php

namespace App\controllers;

use League\Plates\Engine;
use Delight\Auth\Auth;
use PDO;
use App\controllers\QueryBuilder;

class EditSecurityController {
	private $templates;
	private $qb;
	private $pdo;
	private $auth;

	public function __construct(QueryBuilder $qb, Engine $engine, PDO $pdo, Auth $auth) {
		$this->templates = $engine;
		$this->pdo = $pdo;
		$this->auth = $auth;
		$this->qb = $qb;
	}

	public function page_security($vars) {
	    if (!$this->auth->isLoggedIn()) {
	        header('Location: /page_login');
	    }

	    if (!$this->auth->hasRole(\Delight\Auth\Role::ADMIN)) {
	        if (!$this->auth->getUserId() == $user['id']) {
	            flash()->message("Можно редактировать только свой профиль", 'error');
	            header('Location: /page_login');
	        }
	    }
		$user = $this->qb->getOne('users', $vars['id']);
		echo $this->templates->render('page_security', ['user' => $user]);
	}

	public function editSecurity() {
		$user = $this->qb->getOne('users', $_POST['editId']);
		if (!$_POST['email'] == $user['id']) {
			$this->editEmail();
		}
		$this->editPassword();
		$this->qb->update([
			'email' => $_POST['email']],
			$_POST['editId'], 'users'
		);
		flash()->message('Профиль успешно обновлен', 'success');
		header("Location: /page_profile" . $_POST['editId']);
		/*$this->qb->update([
			'email' => $email,
			'password' => password_hash($password, PASSWORD_DEFAULT),
		]);*/
	}

	public function editEmail() {
		try {
		    if ($this->auth->reconfirmPassword($_POST['oldPassword'])) {
		        $this->auth->changeEmail($_POST['email'], function ($selector, $token) {
		        });
		    }
		    else {
		        echo 'We can\'t say if the user is who they claim to be';
		    }
		}
		catch (\Delight\Auth\InvalidEmailException $e) {
			flash()->message('Invalid email address', 'error');
			header('Location: /page_security' . $_POST['editId']);
		    die();
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
			flash()->message('Email address already exists', 'error');
			header('Location: /page_security' . $_POST['editId']);
		    die();
		}
		catch (\Delight\Auth\EmailNotVerifiedException $e) {
			flash()->message('Account not verified', 'error');
			header('Location: /page_security' . $_POST['editId']);
		    die();
		}
		catch (\Delight\Auth\NotLoggedInException $e) {
			flash()->message('Not logged in', 'error');
			header('Location: /page_security' . $_POST['editId']);
		    die();
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
			flash()->message('Too many requests', 'error');
			header('Location: /page_security' . $_POST['editId']);
		    die();
		}
	}

	public function editPassword() {
		try {
		    $this->auth->changePassword($_POST['oldPassword'], $_POST['password']);
		}
		catch (\Delight\Auth\NotLoggedInException $e) {
			flash()->message('Not logged in', 'error');
			header('Location: /page_security' . $_POST['editId']);
		    die();
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
			flash()->message('Invalid password(s)', 'error');
			header('Location: /page_security' . $_POST['editId']);
		    die();
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
			flash()->message('Too many requests', 'error');
			header('Location: /page_security' . $_POST['editId']);
		    die();
		}
	}
}