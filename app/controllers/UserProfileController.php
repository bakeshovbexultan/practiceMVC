<?php

namespace App\controllers;

use League\Plates\Engine;
use \Delight\Auth\Auth;
use PDO;
use App\controllers\QueryBuilder;

class UserProfileController {
	private $templates;
	private $pdo;
	private $qb;
	private $auth;

	public function __construct(QueryBuilder $qb, Engine $engine, PDO $pdo, Auth $auth) {
		$this->templates = $engine;
		$this->pdo = $pdo;
		$this->auth = $auth;
		$this->qb = $qb;
	}

	public function page_profile($var) {
	    if (!$this->auth->isLoggedIn()) {
	        header('Location: /page_login');
	    }
		$user = $this->qb->getOne('users', $var['id']);
		echo $this->templates->render('page_profile', ['user' => $user]);
	}

	public function logout() {
		$this->auth->logOut();
		header("Location: /page_login");
	}

	public function delete_profile($vars) {
		$this->qb->delete('users', $vars['id']);
		flash()->message("Пользователь удален", 'success');
		if ($vars['id'] == $this->auth->getUserId()) {
			$this->auth->logOut();
			header("Location: /page_register");
		} else {
			header("Location: /users");
		}
	}
}