<?php
declare(strict_types=1);
namespace Carbon;
class Carbon {
    public const MONDAY=1;
    private \DateTime $d;
    public function __construct(string $time='now', ?\DateTimeZone $tz=null) { $this->d=new \DateTime($time,$tz); }
    public static function parse(string|int|self|\DateTimeInterface $time): self {
        if($time instanceof self) return $time->copy();
        if($time instanceof \DateTimeInterface) return new self($time->format('Y-m-d H:i:s'));
        return new self((string)$time);
    }
    public static function today(): self { return new self('today'); }
    public static function createFromFormat(string $format,string $time,?\DateTimeZone $timezone=null): self|false {
        $d=\DateTime::createFromFormat($format,$time,$timezone);
        return $d ? new self($d->format('Y-m-d H:i:s')) : false;
    }
    public function format(string $format): string { return $this->d->format($format); }
    public function copy(): self { return new self($this->format('Y-m-d H:i:s')); }
    public function startOfWeek(int $day=self::MONDAY): self { $current=(int)$this->format('N'); $diff=($current-$day+7)%7; $this->d->modify("-{$diff} days")->setTime(0,0,0); return $this; }
    public function endOfWeek(int $day=self::MONDAY): self { $this->startOfWeek($day); $this->d->modify('+6 days')->setTime(23,59,59); return $this; }
    public function startOfMonth(): self { $this->d->modify('first day of this month')->setTime(0,0,0); return $this; }
    public function endOfMonth(): self { $this->d->modify('last day of this month')->setTime(23,59,59); return $this; }
    public function addDays(int $n): self { $this->d->modify(($n>=0?'+':'').$n.' days'); return $this; }
    public function addDay(): self { return $this->addDays(1); }
    public function lte(mixed $other): bool { return $this->d->getTimestamp() <= ($other instanceof self ? $other->d->getTimestamp() : (new \DateTime((string)$other))->getTimestamp()); }
    public function getTimestamp(): int { return $this->d->getTimestamp(); }
    public function __toString(): string { return $this->format('Y-m-d H:i:s'); }
}
