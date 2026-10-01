<?php
declare(strict_types=1);
namespace App\Models;
class ModelQueryCompat {
    private string $table; private string $column; private mixed $value;
    public function __construct(string $table,string $column,mixed $value){$this->table=$table;$this->column=$column;$this->value=$value;}
    public function sum(string $field): float {
        global $db;
        $v=(is_object($this->value) && method_exists($this->value,'format')) ? $this->value->format('Y-m-d') : (string)$this->value;
        return (float)$db->scalar("SELECT COALESCE(SUM(`$field`),0) FROM `{$this->table}` WHERE DATE(`{$this->column}`)=?",[$v]);
    }
}
class Income { public static function whereDate(string $column,mixed $value): ModelQueryCompat { return new ModelQueryCompat('incomes',$column,$value); } }
class Expense { public static function whereDate(string $column,mixed $value): ModelQueryCompat { return new ModelQueryCompat('expenses',$column,$value); } }
