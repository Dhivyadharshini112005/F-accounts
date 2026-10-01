<?php
declare(strict_types=1);
function url(string $path=''): string { return base_path(ltrim($path,'/')); }
function now(): \Carbon\Carbon { return \Carbon\Carbon::today(); }
function app(): object { return new class { public function getLocale(): string { return 'en'; } }; }
