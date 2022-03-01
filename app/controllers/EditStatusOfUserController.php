<?php

namespace App\controllers;

use League\Plates\Engine;
use App\controllers\QueryBuilder;
use Delight\Auth\Auth;
use PDO;

class EditStatusOfUserController {
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

	public function page_status_edit($var) {
	    if (!$this->auth->isLoggedIn()) {
	        header('Location: /page_login');
	    }

	    if (!$this->auth->hasRole(\Delight\Auth\Role::ADMIN)) {
	        if (!$this->auth->getUserId() == $user['id']) {
	            flash()->message("Можно редактировать только свой профиль", 'error');
	            header('Location: /page_login');
	        }
	    }
		$user = $this->qb->getOne('users', $var['id']);
		$statuses = $this->qb->getAll('user_statuses');
		echo $this->templates->render('page_status_edit', ['user' => $user, 'statuses' => $statuses]);
	}

	public function editUserStatus($var) {
		$this->qb->update([
			'status_condition' => $_POST['status_condition']],
			$_POST['editId'], 'users'
		);
		flash()->message("Профиль успешно обновлен", 'success');
		header("Location: /page_profile" . $_POST['editId']);
		//echo $this->templates->render('page_status_edit', ['user' => $user]);
	}
}