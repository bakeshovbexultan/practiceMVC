<?php

namespace App\controllers;

use League\Plates\Engine;
use PDO;
use App\controllers\QueryBuilder;
use Delight\Auth\Auth;

class CreateUserController {
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

	public function create_user() {
	    $isAdmin = $this->auth->hasRole(\Delight\Auth\Role::ADMIN);

	    if (!$this->auth->isLoggedIn() || !$isAdmin) {
	        header('Location: /page_login');
	    }

		echo $this->templates->render('create_user');
		}

	public function add_user() {
		try {
		    $userId = $this->auth->admin()->createUser($_POST['email'], $_POST['password'], $_POST['username']);

		    //echo 'We have signed up a new user with the ID ' . $userId;
		}
		catch (\Delight\Auth\InvalidEmailException $e) {
			flash()->message('Некорректный эл. адрес', 'error');
			header('Location: /create_user');
		    die();
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
			flash()->message('Некорректный пароль', 'error');
			header('Location: /create_user');
		    die();
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
			flash()->message('Пользователь уже существует', 'error');
			header('Location: /create_user');
		    die();
		}
		$this->update($userId);
	}

	public function update($userId) {
		$status_condition = $_POST['status_condition'];

		$username = $_POST['username'];
		$profession = $_POST['profession'];
		$phone_number = $_POST['phone_number'];
		$address = $_POST['address'];

		$avatar = $_FILES['avatar'];/*--*/

		$vkontakte = $_POST['vkontakte'];
		$telegram = $_POST['telegram'];
		$instagram = $_POST['instagram'];

		$this->qb->update([
			'username' => $username,
			'profession' => $profession,
			'status_condition' => $status_condition,
			'address' => $address,
			'vkontakte' => $vkontakte,
			'telegram' => $telegram,
			'instagram' => $instagram,
			'phone_number' => $phone_number],
			$userId,
			'users');
		
		$this->upload_avatar($avatar, $userId);

		flash()->message('Пользователь добавлен', 'success');
		header('Location: /users');
	}

	public function upload_avatar($avatar, $id) {
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
	}
}