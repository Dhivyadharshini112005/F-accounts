<?php
declare(strict_types=1);

class ValidationErrors {
    private array $items;
    public function __construct(array $items=[]) { $this->items=$items; }
    public function any(): bool { return !empty($this->items); }
    public function all(): array { return $this->items; }
    public function get(string $key): array { return $this->items[$key] ?? []; }
    public function has(string $key): bool { return !empty($this->items[$key]); }
    public function first(string $key=null): string { if($key!==null) return (string)($this->items[$key][0] ?? ''); foreach($this->items as $v) return (string)($v[0] ?? ''); return ''; }
}

function e(mixed $value): string {
    if (is_array($value) || is_object($value)) $value = is_scalar($value) ? $value : '';
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function asset(string $path): string { return base_path($path); }
function base_path(string $path=''): string {
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/');
    if ($base === '/' || $base === '\\') $base='';
    return $base . '/' . ltrim($path,'/');
}
function route(string $name, mixed $id=null): string {
    $map=[
        'login'=>'/login','login.submit'=>'/login','logout'=>'/logout',
        'register'=>'/','dashboard'=>'/dashboard','accounts-dashboard'=>'/accounts-dashboard',
        'drivers.index'=>'/drivers','drivers.create'=>'/drivers/create','drivers.store'=>'/drivers',
        'drivers.edit'=>'/drivers/%s','drivers.update'=>'/drivers/%s','drivers.destroy'=>'/drivers/%s',
        'drivers.ledger'=>'/drivers/%s/ledger','drivers.payment'=>'/drivers/%s/payment','drivers.sync'=>'/drivers/sync',
        'income.index'=>'/income','income.create'=>'/income/create','income.store'=>'/income',
        'income.edit'=>'/income/%s/edit','income.update'=>'/income/%s','income.destroy'=>'/income/%s',
        'expenses.index'=>'/expenses','expenses.create'=>'/expenses/create','expenses.store'=>'/expenses',
        'expenses.edit'=>'/expenses/%s/edit','expenses.update'=>'/expenses/%s','expenses.destroy'=>'/expenses/%s',
        'expenses.voucher'=>'/expenses/%s/voucher',
        'expenses.advances.index'=>'/expenses/advances','expenses.advances.create'=>'/expenses/advances/create',
        'expenses.advances.store'=>'/expenses/advances','expenses.advances.voucher'=>'/expenses/advances/%s/voucher',
        'expenses.advances.edit'=>'/expenses/advances/%s/edit','expenses.advances.update'=>'/expenses/advances/%s',
        'expenses.advances.destroy'=>'/expenses/advances/%s',
        'advances.index'=>'/expenses/advances','advances.create'=>'/expenses/advances/create',
        'advances.update'=>'/expenses/advances/%s','reports.index'=>'/reports','daily.account'=>'/daily-account',
        'search'=>'/search','search.index'=>'/search',
    ];
    $url=$map[$name] ?? '/';
    if (str_contains($url,'%s')) $url=sprintf($url,(string)$id);
    return base_path(ltrim($url,'/'));
}
function old(string $key, mixed $default=null): mixed {
    global $oldInput;
    return $oldInput[$key] ?? $default;
}
function session(string $key, mixed $default=null): mixed {
    return $_SESSION[$key] ?? $default;
}
function flash(string $key, mixed $value): void { $_SESSION[$key]=$value; }
function redirect_to(string $url): void {
    header('Location: '.$url); exit;
}
function back(): void {
    redirect_to($_SERVER['HTTP_REFERER'] ?? route('dashboard'));
}
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) $_SESSION['_csrf']=bin2hex(random_bytes(32));
    return $_SESSION['_csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="_token" value="'.e(csrf_token()).'">';
}
function method_field(string $method): string {
    return '<input type="hidden" name="_method" value="'.e(strtoupper($method)).'">';
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD']==='GET') return;
    $ok=isset($_POST['_token'],$_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'],(string)$_POST['_token']);
    if (!$ok) { http_response_code(419); exit('Page expired. Please go back and try again.'); }
}
function input(string $key, mixed $default=null): mixed { return $_POST[$key] ?? $_GET[$key] ?? $default; }
function set_old(array $data): void { $_SESSION['_old']=$data; }
function fail_validation(array $errors): void {
    $_SESSION['_errors']=$errors; set_old($_POST); back();
}

function request(): object {
    return new class {
        public function get(string $key, mixed $default=null): mixed { return $_GET[$key] ?? $_POST[$key] ?? $default; }
        public function input(string $key, mixed $default=null): mixed { return $_POST[$key] ?? $_GET[$key] ?? $default; }
        public function filled(string $key): bool { return isset($_POST[$key]) || (isset($_GET[$key]) && trim((string)($_POST[$key] ?? $_GET[$key])) !== ''); }
        public function is(string $pattern): bool { $path=trim(parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH),'/'); $p=trim($pattern,'/'); if(str_ends_with($p,'*')) return str_starts_with($path,rtrim($p,'*')); return $path===$p; }
        public function routeIs(string $name): bool { $map=['dashboard'=>'dashboard','income.index'=>'income','expenses.index'=>'expenses','reports.index'=>'reports','search.index'=>'search']; $path=trim(parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH),'/'); return isset($map[$name]) && str_starts_with($path,$map[$name]); }
    };
}
function auth(): object {
    return new class {
        public function user(): ?object { return Auth::user(); }
        public function check(): bool { return Auth::check(); }
    };
}


class OptionalValue {
    private mixed $value;
    public function __construct(mixed $value){$this->value=$value;}
    public function __get(string $key): mixed {
        if($this->value===null)return null;
        if(is_object($this->value)&&isset($this->value->{$key}))return $this->value->{$key};
        return null;
    }
    public function __call(string $method,array $args): mixed {
        if($this->value===null)return null;
        if($method==='format' && is_string($this->value)) return \Carbon\Carbon::parse($this->value)->format($args[0]??'Y-m-d');
        if(is_object($this->value)&&method_exists($this->value,$method)) return $this->value->{$method}(...$args);
        return null;
    }
}
function optional(mixed $value=null): OptionalValue { return new OptionalValue($value); }

function value_or_null(mixed $v): ?string { $v=trim((string)$v); return $v===''?null:$v; }
function json_for_js(mixed $v): string { return json_encode($v, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); }
function now_date(): string { return date('Y-m-d'); }

function numberToWords(int $number): string {
    $ones=[0=>'Zero',1=>'One',2=>'Two',3=>'Three',4=>'Four',5=>'Five',6=>'Six',7=>'Seven',8=>'Eight',9=>'Nine',10=>'Ten',11=>'Eleven',12=>'Twelve',13=>'Thirteen',14=>'Fourteen',15=>'Fifteen',16=>'Sixteen',17=>'Seventeen',18=>'Eighteen',19=>'Nineteen'];
    $tens=[2=>'Twenty',3=>'Thirty',4=>'Forty',5=>'Fifty',6=>'Sixty',7=>'Seventy',8=>'Eighty',9=>'Ninety'];
    if($number<20)return $ones[$number];
    if($number<100)return $tens[intdiv($number,10)].($number%10?' '.$ones[$number%10]:'');
    if($number<1000)return $ones[intdiv($number,100)].' Hundred'.($number%100?' '.numberToWords($number%100):'');
    if($number<100000)return numberToWords(intdiv($number,1000)).' Thousand'.($number%1000?' '.numberToWords($number%1000):'');
    if($number<10000000)return numberToWords(intdiv($number,100000)).' Lakh'.($number%100000?' '.numberToWords($number%100000):'');
    return numberToWords(intdiv($number,10000000)).' Crore'.($number%10000000?' '.numberToWords($number%10000000):'');
}
