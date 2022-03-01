<?php

namespace App\controllers;

use League\Plates\Engine;
use \Delight\Auth\Auth;
use PDO;
use App\controllers\QueryBuilder;

class UsersController {
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

	public function listOfUsers() {

	    if (!$this->auth->isLoggedIn()) {
	        header('Location: /page_login');
	    }
	    $isAdmin = $this->auth->hasRole(\Delight\Auth\Role::ADMIN);
	    $editorName = $this->auth->getUsername();

		$users = $this->qb->getAll('users');
		echo $this->templates->render('users', ['users' => $users, 'isAdmin' => $isAdmin, 'editorName' => $editorName]);
	}
}