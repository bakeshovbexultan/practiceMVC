<?php

namespace App\controllers;

use League\Plates\Engine;
use Delight\Auth\Auth;
use App\controllers\QueryBuilder;
use PDO;

class EditUserController {
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

	public function edit_user($vars) {
		$user = $this->qb->getOne('users', $vars['id']);
	    if (!$this->auth->isLoggedIn()) {
	        header('Location: /page_login');
	    }

	    if (!$this->auth->hasRole(\Delight\Auth\Role::ADMIN)) {
	        if (!$this->auth->getUserId() == $user['id']) {
	            flash()->message("Можно редактировать только свой профиль", 'error');
	            header('Location: /page_login');
	        }
	    }
		echo $this->templates->render('page_edit', ['user' => $user]);
	}

	public function edit() {
		$username = $_GET['username'];
		$profession = $_GET['profession'];
		$address = $_GET['address'];
		$phone_number = $_GET['phone_number'];
		$editId = $_GET['editId'];
		$this->qb->update([
			'username' => $username,
			'profession' => $profession,
			'address' => $address,
			'phone_number' => $phone_number,
		],
		$editId, 'users');
		flash()->message('Профиль успешно обновлен', 'success');
		header("Location: page_profile$editId");
	}
}