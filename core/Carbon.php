<?php
namespace Carbon;
class Carbon {
 public \DateTime $d;
 function __construct($v='now'){ $this->d=new \DateTime($v); }
 static function parse($v){return new self($v);}
 static function createFromFormat($f,$v){$x=new self('now');$x->d=\DateTime::createFromFormat($f,$v);if(!$x->d)throw new \Exception('Invalid date');return $x;}
 function format($f){return $this->d->format($f);}
 function __get($k){return (int)$this->d->format($k==='year'?'Y':($k==='month'?'m':'d'));}
 function subMonths($n){$this->d->modify("-{$n} month");return $this;}
}
