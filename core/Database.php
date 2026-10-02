<?php
declare(strict_types=1);

class Database {
    private PDO $pdo;

    public function __construct(array $cfg) {
        $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};dbname={$cfg['name']};charset={$cfg['charset']}";
        $this->pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function pdo(): PDO { return $this->pdo; }

    public function all(string $sql, array $params=[]): array {
        $st=$this->pdo->prepare($sql); $st->execute($params); return $st->fetchAll();
    }
    public function first(string $sql, array $params=[]): ?object {
        $st=$this->pdo->prepare($sql); $st->execute($params); $v=$st->fetch(); return $v ?: null;
    }
    public function scalar(string $sql, array $params=[]): mixed {
        $st=$this->pdo->prepare($sql); $st->execute($params); return $st->fetchColumn();
    }
    public function execute(string $sql, array $params=[]): int {
        $st=$this->pdo->prepare($sql); $st->execute($params); return $st->rowCount();
    }
    public function insert(string $sql, array $params=[]): int {
        $st=$this->pdo->prepare($sql); $st->execute($params); return (int)$this->pdo->lastInsertId();
    }
}
