<?php

namespace App\controllers;
use Aura\SqlQuery\QueryFactory;
use PDO;
use Delight\Auth\Auth;

class QueryBuilder {
	private $templates;
	private $pdo;
	private $qb;
	private $auth;
	private $qf;

	public function __construct(PDO $pdo, Auth $auth, QueryFactory $qf) {
		$this->pdo = $pdo;
		$this->queryFactory = $qf;
	}

	public function getAll($table) {
		$select = $this->queryFactory->newSelect();

		$select->cols(['*'])
			->from($table);

		$sth = $this->pdo->prepare($select->getStatement());

		$sth->execute($select->getBindValues());

		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	public function insert($data, $table) {
		$insert = $this->queryFactory->newInsert();

		$insert
		    ->into($table)
		    ->cols($data);

			$sth = $this->pdo->prepare($insert->getStatement());

			$sth->execute($insert->getBindValues());
	}

	public function update($data, $id, $table) {
		$update = $this->queryFactory->newUpdate();

		$update
		    ->table($table)
		    ->cols($data)
		    ->where('id = :id')
		    ->bindValue('id', $id);

			$sth = $this->pdo->prepare($update->getStatement());

			$sth->execute($update->getBindValues());
	}

	public function delete($table, $id) {
		$delete = $this->queryFactory->newDelete();

		$delete
		    ->from($table)
		    ->where('id = :id')
		    ->bindValue('id', $id);

		$sth = $this->pdo->prepare($delete->getStatement());

		$sth->execute($delete->getBindValues());
	}

	public function getOne($table, $id) {
		$select = $this->queryFactory->newSelect();

		$select->cols(['*'])
			->where('id = :id')
			->from($table)
		    ->bindValue('id', $id);

		$sth = $this->pdo->prepare($select->getStatement());

		$sth->execute($select->getBindValues());

		$result = $sth->fetch(PDO::FETCH_ASSOC);

		return $result;
	}
}