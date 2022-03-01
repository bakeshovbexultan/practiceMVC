<?php

namespace App\controllers;

use League\Plates\Engine;
use Delight\Auth\Auth;
use App\controllers\QueryBuilder;
use PDO;

class EditUserAvatarController {
	private $plug_avatar;
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

	public function page_media($var) {
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
		$plug_avatar = $this->plug_avatar();
		echo $this->templates->render('page_media', ['user' => $user, 'plug_avatar' => $plug_avatar]);
	}

	public function editAvatar() {
		$avatar = $_FILES['avatar'];
		$id = $_POST['editId'];

		$filename = $_FILES['avatar']['name'];
		$tmp_name = $_FILES['avatar']['tmp_name'];
		$result = pathinfo($filename);

		$filename = uniqid() . "." . $result['extension'];


		move_uploaded_file($tmp_name, '../img/demo/avatars/' . $filename);

		$avatar = '../img/demo/avatars/' . $filename;

		$pdo = new PDO("mysql:host=localhost;dbname=marlincom;", "root", "");
		$sql = "UPDATE users SET avatar = :avatar WHERE id = :id";
		$statement = $pdo->prepare($sql);
		$statement->execute(['avatar' => $avatar, 'id' => $id]);

		flash()->message("Профиль успешно обновлен", 'success');
		header("Location: /page_profile" . $_POST['editId']);
	}


	/**
	 * Устанавливает путь на заглушку 
	 * 
	*/
	public function plug_avatar() {
		return 'img/demo/avatars/avatar-m.png';
	}
}