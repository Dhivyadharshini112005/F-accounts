<?php
// F-Taxi Accounts - framework-free PHP runtime
require_once __DIR__ . "/core/Carbon.php";
session_start();

function db(): PDO {
    static $pdo;

    if ($pdo) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_DATABASE') ?: 'f_taxi_accounts';
    $user = getenv('DB_USERNAME') ?: 'root';
    $pass = getenv('DB_PASSWORD') ?: '';

    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false,

        // TiDB Cloud TLS
        PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
    ]);

    return $pdo;
}

class Collection implements Countable, IteratorAggregate {
    public array $items;

    function __construct($items = []) {
        $this->items = array_values($items);
    }

    function count(): int {
        return count($this->items);
    }

    function getIterator(): Traversable {
        return new ArrayIterator($this->items);
    }

    function first() {
        return $this->items[0] ?? null;
    }

    function values() {
        return $this;
    }

    function unique() {
        $seen = [];
        $out = [];

        foreach ($this->items as $x) {
            $k = (string)$x;

            if (!isset($seen[$k])) {
                $seen[$k] = 1;
                $out[] = $x;
            }
        }

        return new self($out);
    }

    function pluck($field) {
        return new self(
            array_map(
                fn($x) => $x->$field ?? null,
                $this->items
            )
        );
    }
}

class Errors {
    public array $e = [];

    function any() {
        return !empty($this->e);
    }

    function has($k) {
        return isset($this->e[$k]);
    }

    function first($k = null) {
        if ($k !== null) {
            return $this->e[$k][0] ?? null;
        }

        foreach ($this->e as $a) {
            return $a[0] ?? null;
        }

        return null;
    }

    function all() {
        return array_merge(...array_values($this->e ?: [[]]));
    }
}

$errors = new Errors();

function req($k = null, $default = null) {
    if ($k === null) {
        return $_REQUEST;
    }

    return $_REQUEST[$k] ?? $default;
}

function old($k, $default = '') {
    return $_SESSION['_old'][$k] ?? $default;
}

function flash($key, $value = null) {
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return;
    }

    $v = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);

    return $v;
}

function csrf_token() {
    if (empty($_SESSION['_token'])) {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_token'];
}

function csrf_field() {
    return '<input type="hidden" name="_token" value="' .
        htmlspecialchars(csrf_token(), ENT_QUOTES) .
        '">';
}

function method_field($m) {
    return '<input type="hidden" name="_method" value="' .
        htmlspecialchars($m, ENT_QUOTES) .
        '">';
}

function route($name, $params = []) {
    $map = [
        'login' => '/login',
        'login.submit' => '/login',
        'logout' => '/logout',
        'dashboard' => '/dashboard',
        'accounts.dashboard' => '/accounts-dashboard',

        'drivers.index' => '/drivers',

        'income.index' => '/income',
        'income.create' => '/income/create',
        'income.store' => '/income',
        'income.edit' => '/income/' . $params,
        'income.update' => '/income/' . $params,
        'income.destroy' => '/income/' . $params,

        'expenses.index' => '/expenses',
        'expenses.create' => '/expenses/create',
        'expenses.store' => '/expenses',
        'expenses.edit' => '/expenses/' . $params,
        'expenses.update' => '/expenses/' . $params,
        'expenses.destroy' => '/expenses/' . $params,
        'expenses.voucher' => '/expenses/' . $params . '/voucher',

        'expenses.advances.index' => '/expenses/advances',
        'expenses.advances.create' => '/expenses/advances/create',
        'expenses.advances.store' => '/expenses/advances',
        'expenses.advances.edit' => '/expenses/advances/' . $params . '/edit',
        'expenses.advances.update' => '/expenses/advances/' . $params,
        'expenses.advances.destroy' => '/expenses/advances/' . $params,
        'expenses.advances.voucher' => '/expenses/advances/' . $params . '/voucher',

        'reports.index' => '/reports',
        'search.index' => '/search'
    ];

    $u = $map[$name] ?? '/';

    if (is_array($params)) {
        $q = http_build_query($params);

        if ($q) {
            $u .= '?' . $q;
        }
    }

    return $u;
}

function asset($p) {
    return '/' . ltrim($p, '/');
}

function auth() {
    return new class {
        function check() {
            return isset($_SESSION['user']);
        }

        function user() {
            return $_SESSION['user'] ?? null;
        }
    };
}

function redirect_to($url) {
    header('Location: ' . $url);
    exit;
}

function back() {
    redirect_to($_SERVER['HTTP_REFERER'] ?? '/');
}

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function money($v) {
    return number_format((float)$v, 2);
}

function parseDate($v) {
    return new DateTime($v ?: 'now');
}

function abortx($code) {
    http_response_code($code);
    exit("<h1>{$code}</h1>");
}

function now() {
    return new \Carbon\Carbon('now');
}

function row($sql, $args = []) {
    $s = db()->prepare($sql);
    $s->execute($args);

    return $s->fetch();
}

function rows($sql, $args = []) {
    $s = db()->prepare($sql);
    $s->execute($args);

    return new Collection($s->fetchAll());
}

function val($sql, $args = []) {
    $s = db()->prepare($sql);
    $s->execute($args);

    return $s->fetchColumn();
}

function execq($sql, $args = []) {
    $s = db()->prepare($sql);

    return $s->execute($args);
}

function tableExists($t) {
    try {
        val("SELECT 1 FROM `{$t}` LIMIT 1");
        return true;
    } catch (Throwable $e) {
        return false;
    }
}

function validate(array $rules) {
    global $errors;

    $errors = new Errors();

    foreach ($rules as $k => $rule) {
        $v = req($k);

        foreach (explode('|', $rule) as $r) {

            if (
                $r === 'required' &&
                ($v === null || $v === '')
            ) {
                $errors->e[$k][] =
                    'The ' . $k . ' field is required.';
            }

            if (
                str_starts_with($r, 'max:') &&
                strlen((string)$v) > (int)substr($r, 4)
            ) {
                $errors->e[$k][] =
                    'The ' . $k . ' may not be greater than ' .
                    substr($r, 4) . ' characters.';
            }

            if (
                $r === 'numeric' &&
                $v !== '' &&
                !is_numeric($v)
            ) {
                $errors->e[$k][] =
                    'The ' . $k . ' must be a number.';
            }

            if (
                str_starts_with($r, 'in:') &&
                !in_array(
                    $v,
                    explode(',', substr($r, 3)),
                    true
                )
            ) {
                $errors->e[$k][] =
                    'Invalid ' . $k . '.';
            }
        }
    }

    return !$errors->any();
}

function saveOld() {
    $_SESSION['_old'] = $_POST;
}

function render($view, $data = []) {
    global $errors;

    extract($data);

    $file = __DIR__ .
        '/resources/views/' .
        str_replace('.', '/', $view) .
        '.blade.php';

    if (!is_file($file)) {
        abortx(500);
    }

    $raw = file_get_contents($file);
    $layout = null;

    if (
        preg_match(
            "/@extends\(['\"]([^'\"]+)['\"]\)/",
            $raw,
            $m
        )
    ) {
        $layout = $m[1];

        if (
            preg_match(
                "/@section\(['\"]content['\"]\)(.*?)@endsection/s",
                $raw,
                $sm
            )
        ) {
            $content = $sm[1];
        } else {
            $content = $raw;
        }

        $raw = $content;
    }

    $php = blade_compile($raw);

    ob_start();
    eval('?>' . $php);
    $out = ob_get_clean();

    if ($layout) {
        $lf = __DIR__ .
            '/resources/views/' .
            str_replace('.', '/', $layout) .
            '.blade.php';

        $lr = file_get_contents($lf);

        $lr = preg_replace(
            "/@yield\(['\"]content['\"]\)/",
            '<?= $GLOBALS["__content"] ?>',
            $lr
        );

        $GLOBALS['__content'] = $out;

        $php2 = blade_compile($lr);

        ob_start();
        eval('?>' . $php2);
        $out = ob_get_clean();
    }

    return $out;
}

function blade_compile($s) {

    $s = preg_replace(
        '/@extends\([^\)]*\)/',
        '',
        $s
    );

    $s = preg_replace(
        "/@section\\(['\"]content['\"]\\)/",
        '',
        $s
    );

    $s = str_replace(
        '@endsection',
        '',
        $s
    );

    $s = preg_replace(
        '/@yield\([\'\"]([^\'\"]+)[\'\"]\)/',
        '<?= $GLOBALS["__content"] ?? "" ?>',
        $s
    );

    $s = str_replace(
        '@csrf',
        '<?= csrf_field() ?>',
        $s
    );

    $s = preg_replace(
        '/@method\([\'\"]([^\'\"]+)[\'\"]\)/',
        '<?= method_field("$1") ?>',
        $s
    );

    $s = preg_replace_callback(
        '/@json\(([^\n]*)\)/',
        fn($m) => '<?= json_encode(' . $m[1] . ') ?>',
        $s
    );

    $s = str_replace(
        '@auth',
        '<?php if(auth()->check()): ?>',
        $s
    );

    $s = str_replace(
        '@endauth',
        '<?php endif; ?>',
        $s
    );

    $replace = function ($text, $directive, $prefix) {

        $needle = '@' . $directive;
        $pos = 0;

        while (($p = strpos($text, $needle, $pos)) !== false) {

            $q = strpos(
                $text,
                '(',
                $p + strlen($needle)
            );

            if ($q === false) {
                break;
            }

            $depth = 0;
            $i = $q;
            $len = strlen($text);

            for (; $i < $len; $i++) {

                $c = $text[$i];

                if ($c === '(') {
                    $depth++;
                } elseif ($c === ')') {
                    $depth--;

                    if ($depth === 0) {
                        break;
                    }
                }
            }

            if ($depth !== 0) {
                break;
            }

            $expr = substr(
                $text,
                $q + 1,
                $i - $q - 1
            );

            $text =
                substr($text, 0, $p) .
                $prefix .
                $expr .
                '): ?>' .
                substr($text, $i + 1);

            $pos =
                $p +
                strlen($prefix) +
                strlen($expr) +
                6;
        }

        return $text;
    };

    $s = $replace(
        $s,
        'if',
        '<?php if('
    );

    $s = $replace(
        $s,
        'elseif',
        '<?php elseif('
    );

    $s = $replace(
        $s,
        'foreach',
        '<?php foreach('
    );

    $s = $replace(
        $s,
        'forelse',
        '<?php $__empty=true; foreach('
    );

    $s = $replace(
        $s,
        'error',
        '<?php if($errors->has('
    );

    $s = str_replace(
        '@else',
        '<?php else: ?>',
        $s
    );

    $s = str_replace(
        '@endif',
        '<?php endif; ?>',
        $s
    );

    $s = str_replace(
        '@endforeach',
        '<?php endforeach; ?>',
        $s
    );

    $s = str_replace(
        '@empty',
        '<?php endforeach; if($__empty): ?>',
        $s
    );

    $s = str_replace(
        '@endforelse',
        '<?php endif; ?>',
        $s
    );

    $s = str_replace(
        '@enderror',
        '<?php endif; ?>',
        $s
    );

    $s = preg_replace(
        '/@php\s*/',
        '<?php ',
        $s
    );

    $s = str_replace(
        '@endphp',
        ' ?>',
        $s
    );

    $s = preg_replace(
        '/\{\{\-?\s*(.*?)\s*\-?\}\}/s',
        '<?= e($1) ?>',
        $s
    );

    return $s;
}

function requireAuth() {
    if (!auth()->check()) {
        redirect_to('/login');
    }
}

function login() {

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return render('auth.login');
    }

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    $u = trim((string)req('username'));
    $p = (string)req('password');

    $user = row(
        'SELECT * FROM users WHERE username=? LIMIT 1',
        [$u]
    );

    if (
        $user &&
        password_verify($p, $user->password)
    ) {
        $_SESSION['user'] = $user;

        redirect_to('/dashboard');
    }

    $_SESSION['_flash']['error'] =
        'Invalid username or password';

    saveOld();

    redirect_to('/login');
}

function logout() {
    session_destroy();
    redirect_to('/login');
}

function dashboard() {
    requireAuth();

    $totalIncome = (float)val(
        'SELECT COALESCE(SUM(amount),0) FROM incomes'
    );

    $totalExpenseOnly = (float)val(
        'SELECT COALESCE(SUM(amount),0) FROM expenses'
    );

    $totalAdvance = (float)val(
        'SELECT COALESCE(SUM(amount),0) FROM advances'
    );

    $totalExpense =
        $totalExpenseOnly +
        $totalAdvance;

    $balance =
        $totalIncome -
        $totalExpense;

    $totalDrivers = (int)val(
        'SELECT COUNT(*) FROM drivers'
    );

    $cashIncome =
        (float)val(
            "SELECT COALESCE(SUM(fees_amount),0)
             FROM incomes
             WHERE fees_payment_mode='cash'"
        )
        +
        (float)val(
            "SELECT COALESCE(SUM(gst_amount),0)
             FROM incomes
             WHERE gst_payment_mode='cash'"
        );

    $accountIncome =
        (float)val(
            "SELECT COALESCE(SUM(fees_amount),0)
             FROM incomes
             WHERE fees_payment_mode='account'"
        )
        +
        (float)val(
            "SELECT COALESCE(SUM(gst_amount),0)
             FROM incomes
             WHERE gst_payment_mode='account'"
        );

    $cashExpense = (float)val(
        "SELECT COALESCE(SUM(amount),0)
         FROM expenses
         WHERE payment_mode='cash'"
    );

    $accountExpense = (float)val(
        "SELECT COALESCE(SUM(amount),0)
         FROM expenses
         WHERE payment_mode='account'"
    );

    $cashAdvance = (float)val(
        "SELECT COALESCE(SUM(amount),0)
         FROM advances
         WHERE payment_mode='cash'"
    );

    $accountAdvance = (float)val(
        "SELECT COALESCE(SUM(amount),0)
         FROM advances
         WHERE payment_mode='account'"
    );

    $cashBalance =
        $cashIncome -
        $cashExpense -
        $cashAdvance;

    $accountBalance =
        $accountIncome -
        $accountExpense -
        $accountAdvance;

    $recentIncome = rows(
        'SELECT i.*,d.name AS driver_name
         FROM incomes i
         LEFT JOIN drivers d ON d.id=i.driver_id
         ORDER BY i.created_at DESC
         LIMIT 5'
    );

    $recentExpense = rows(
        'SELECT * FROM expenses
         ORDER BY created_at DESC
         LIMIT 5'
    );

    $month = req(
        'month',
        date('Y-m')
    );

    $dt =
        DateTime::createFromFormat('Y-m', $month)
        ?: new DateTime();

    $monthDisplay =
        $dt->format('F Y');

    $availableMonths =
        new Collection(
            array_map(
                fn($r) => date(
                    'M-y',
                    strtotime($r->income_date)
                ),
                rows(
                    'SELECT income_date
                     FROM incomes
                     WHERE income_date IS NOT NULL
                     ORDER BY income_date DESC'
                )->items
            )
        );

    $availableMonths =
        $availableMonths->unique();

    $selectedMonth =
        $availableMonths->first()
        ?: date('M-y');

    $monthly = [];
    $monthlyTrend = [];

    for ($i = 5; $i >= 0; $i--) {

        $d = new DateTime();
        $d->modify("-{$i} month");

        $y = $d->format('Y');
        $m = $d->format('m');

        $inc = (float)val(
            'SELECT COALESCE(SUM(amount),0)
             FROM incomes
             WHERE YEAR(income_date)=?
             AND MONTH(income_date)=?',
            [$y, $m]
        );

        $exp = (float)val(
            'SELECT COALESCE(SUM(amount),0)
             FROM expenses
             WHERE YEAR(expense_date)=?
             AND MONTH(expense_date)=?',
            [$y, $m]
        );

        $adv = (float)val(
            'SELECT COALESCE(SUM(amount),0)
             FROM advances
             WHERE YEAR(advance_date)=?
             AND MONTH(advance_date)=?',
            [$y, $m]
        );

        $monthlyTrend[] = [
            'label' => $d->format('M'),
            'income' => $inc,
            'expense' => $exp + $adv
        ];
    }

    $trendMax = max(
        1,
        ...array_merge(
            array_column($monthlyTrend, 'income'),
            array_column($monthlyTrend, 'expense')
        )
    );

    $monthly = [
        'driver_payout' => 0,
        'driver_amt' => 0,
        'company_amt' => 0,
        'commission' => 0,
        'gst' => 0,
        'business_commission' => 0,
        'pending_amt' => 0,
        'amount_paid' => 0,
        'advance_amount' => 0,
        'toll_fee' => 0,
        'service_charge' => 0
    ];

    return render(
        'dashboard.index',
        compact(
            'totalIncome',
            'totalExpense',
            'totalExpenseOnly',
            'totalAdvance',
            'balance',
            'totalDrivers',
            'cashIncome',
            'accountIncome',
            'cashExpense',
            'accountExpense',
            'cashAdvance',
            'accountAdvance',
            'cashBalance',
            'accountBalance',
            'recentIncome',
            'recentExpense',
            'month',
            'monthDisplay',
            'availableMonths',
            'selectedMonth',
            'monthly',
            'monthlyTrend',
            'trendMax'
        )
    );
}

function incomeIndex() {
    requireAuth();

    $s = trim((string)req('search', ''));

    $sql =
        'SELECT i.*,d.name AS driver_name
         FROM incomes i
         LEFT JOIN drivers d ON d.id=i.driver_id';

    $args = [];

    if ($s !== '') {
        $sql .=
            ' WHERE (
                i.driver_id LIKE ?
                OR i.description LIKE ?
                OR i.fees_amount LIKE ?
                OR i.gst_amount LIKE ?
                OR i.total_amount LIKE ?
                OR i.amount LIKE ?
                OR i.income_date LIKE ?
            )';

        $x = "%{$s}%";

        $args = [
            $x,
            $x,
            $x,
            $x,
            $x,
            $x,
            $x
        ];
    }

    $sql .=
        ' ORDER BY i.income_date DESC,i.id DESC';

    $incomes = rows($sql, $args);

    return render(
        'income.index',
        compact('incomes')
    );
}

function incomeCreate() {
    requireAuth();

    $drivers =
        rows(
            'SELECT * FROM drivers ORDER BY name'
        );

    return render(
        'income.create',
        compact('drivers')
    );
}

function incomeStore() {
    requireAuth();

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    if (
        !validate([
            'driver_id' => 'required',
            'fees_amount' => 'required|numeric',
            'gst_amount' => 'nullable|numeric',
            'fees_payment_mode' => 'required|in:cash,account',
            'gst_payment_mode' => 'required|in:cash,account',
            'income_date' => 'required',
            'description' => 'nullable|max:255'
        ])
    ) {
        saveOld();
        redirect_to('/income/create');
    }

    $fees = (float)req('fees_amount');
    $gst = (float)(req('gst_amount') ?: 0);
    $total = $fees + $gst;

    execq(
        'INSERT INTO incomes(
            driver_id,
            fees_amount,
            gst_amount,
            amount,
            total_amount,
            fees_payment_mode,
            gst_payment_mode,
            fees_upi_id,
            gst_upi_id,
            income_date,
            description,
            created_at,
            updated_at
        )
        VALUES(?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())',
        [
            req('driver_id'),
            $fees,
            $gst,
            $total,
            $total,
            req('fees_payment_mode'),
            req('gst_payment_mode'),
            req('fees_upi_id'),
            req('gst_upi_id'),
            req('income_date'),
            req('description')
        ]
    );

    $_SESSION['_flash']['success'] =
        'Income added successfully.';

    redirect_to('/income');
}

function incomeEdit($id) {
    requireAuth();

    if (
        strtoupper($_SESSION['user']->role ?? '') !== 'OWNER'
    ) {
        abortx(403);
    }

    $income =
        row(
            'SELECT * FROM incomes WHERE id=?',
            [$id]
        );

    if (!$income) {
        abortx(404);
    }

    $drivers =
        rows(
            'SELECT * FROM drivers ORDER BY name'
        );

    return render(
        'income.edit',
        compact('income', 'drivers')
    );
}

function incomeUpdate($id) {
    requireAuth();

    if (
        strtoupper($_SESSION['user']->role ?? '') !== 'OWNER'
    ) {
        abortx(403);
    }

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    if (
        !validate([
            'driver_id' => 'required',
            'fees_amount' => 'required|numeric',
            'gst_amount' => 'nullable|numeric',
            'fees_payment_mode' => 'required|in:cash,account',
            'gst_payment_mode' => 'required|in:cash,account',
            'income_date' => 'required'
        ])
    ) {
        saveOld();
        redirect_to('/income/' . $id . '/edit');
    }

    $fees = (float)req('fees_amount');
    $gst = (float)(req('gst_amount') ?: 0);
    $total = $fees + $gst;

    execq(
        'UPDATE incomes SET
            driver_id=?,
            fees_amount=?,
            gst_amount=?,
            amount=?,
            total_amount=?,
            fees_payment_mode=?,
            gst_payment_mode=?,
            income_date=?,
            description=?,
            updated_at=NOW()
         WHERE id=?',
        [
            req('driver_id'),
            $fees,
            $gst,
            $total,
            $total,
            req('fees_payment_mode'),
            req('gst_payment_mode'),
            req('income_date'),
            req('description'),
            $id
        ]
    );

    $_SESSION['_flash']['success'] =
        'Income updated successfully.';

    redirect_to('/income');
}

function incomeDelete($id) {
    requireAuth();

    if (
        strtoupper($_SESSION['user']->role ?? '') !== 'OWNER'
    ) {
        abortx(403);
    }

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    execq(
        'DELETE FROM incomes WHERE id=?',
        [$id]
    );

    $_SESSION['_flash']['success'] =
        'Income deleted successfully.';

    redirect_to('/income');
}

function expensesIndex() {
    requireAuth();

    $s = trim((string)req('search', ''));

    $sql = 'SELECT * FROM expenses';
    $args = [];

    if ($s !== '') {

        $sql .=
            ' WHERE (
                expense_name LIKE ?
                OR expense_type LIKE ?
                OR category LIKE ?
                OR description LIKE ?
                OR amount LIKE ?
                OR payment_mode LIKE ?
                OR expense_date LIKE ?
            )';

        $x = "%{$s}%";

        $args = [
            $x,
            $x,
            $x,
            $x,
            $x,
            $x,
            $x
        ];
    }

    $sql .=
        ' ORDER BY expense_date DESC,id DESC';

    $expenses = rows($sql, $args);

    return render(
        'expenses.index',
        compact('expenses')
    );
}

function expenseCreate() {
    requireAuth();

    return render('expenses.create');
}

function expenseStore() {
    requireAuth();

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    if (
        !validate([
            'expense_name' => 'required|max:255',
            'amount' => 'required|numeric',
            'payment_mode' => 'required|in:cash,account',
            'expense_date' => 'required',
            'category' => 'nullable|max:255',
            'description' => 'nullable|max:255'
        ])
    ) {
        saveOld();
        redirect_to('/expenses/create');
    }

    $cat = req('category');

    execq(
        'INSERT INTO expenses(
            expense_name,
            expense_type,
            amount,
            payment_mode,
            upi_id,
            date,
            expense_date,
            category,
            description,
            created_at,
            updated_at
        )
        VALUES(?,?,?,?,?,?,?,?,?,NOW(),NOW())',
        [
            req('expense_name'),
            $cat ?: req('expense_name'),
            req('amount'),
            req('payment_mode'),
            req('upi_id'),
            req('expense_date'),
            req('expense_date'),
            $cat,
            req('description')
        ]
    );

    $_SESSION['_flash']['success'] =
        'Expense added successfully.';

    redirect_to('/expenses');
}

function expenseEdit($id) {
    requireAuth();

    if (
        strtoupper($_SESSION['user']->role ?? '') !== 'OWNER'
    ) {
        abortx(403);
    }

    $expense =
        row(
            'SELECT * FROM expenses WHERE id=?',
            [$id]
        );

    if (!$expense) {
        abortx(404);
    }

    return render(
        'expenses.edit',
        compact('expense')
    );
}

function expenseUpdate($id) {
    requireAuth();

    if (
        strtoupper($_SESSION['user']->role ?? '') !== 'OWNER'
    ) {
        abortx(403);
    }

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    $cat = req('category');

    execq(
        'UPDATE expenses SET
            expense_name=?,
            expense_type=?,
            amount=?,
            payment_mode=?,
            upi_id=?,
            date=?,
            expense_date=?,
            category=?,
            description=?,
            updated_at=NOW()
         WHERE id=?',
        [
            req('expense_name'),
            $cat ?: req('expense_name'),
            req('amount'),
            req('payment_mode'),
            req('upi_id'),
            req('expense_date'),
            req('expense_date'),
            $cat,
            req('description'),
            $id
        ]
    );

    $_SESSION['_flash']['success'] =
        'Expense updated successfully.';

    redirect_to('/expenses');
}

function expenseDelete($id) {
    requireAuth();

    if (
        strtoupper($_SESSION['user']->role ?? '') !== 'OWNER'
    ) {
        abortx(403);
    }

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    execq(
        'DELETE FROM expenses WHERE id=?',
        [$id]
    );

    $_SESSION['_flash']['success'] =
        'Expense deleted successfully.';

    redirect_to('/expenses');
}

function expenseVoucher($id) {
    requireAuth();

    $expense =
        row(
            'SELECT * FROM expenses WHERE id=?',
            [$id]
        );

    if (!$expense) {
        abortx(404);
    }

    $n = (int)val(
        'SELECT COUNT(*) FROM expenses WHERE id<=?',
        [$id]
    );

    $displayVoucherNumber =
        str_pad($n, 3, '0', STR_PAD_LEFT);

    return render(
        'expenses.voucher',
        compact(
            'expense',
            'displayVoucherNumber'
        )
    );
}

function advancesIndex() {
    requireAuth();

    $advances =
        rows(
            'SELECT * FROM advances
             ORDER BY advance_date DESC,id DESC'
        );

    return render(
        'advances.index',
        compact('advances')
    );
}

function advanceCreate() {
    requireAuth();

    return render('advances.create');
}

function advanceStore() {
    requireAuth();

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    execq(
        'INSERT INTO advances(
            employee_name,
            category,
            amount,
            payment_mode,
            upi_id,
            advance_date,
            description,
            created_at,
            updated_at
        )
        VALUES(?,?,?,?,?,?,?,NOW(),NOW())',
        [
            req('employee_name'),
            req('category'),
            req('amount'),
            req('payment_mode'),
            req('upi_id'),
            req('advance_date'),
            req('description')
        ]
    );

    $_SESSION['_flash']['success'] =
        'Advance added successfully.';

    redirect_to('/expenses/advances');
}

function advanceEdit($id) {
    requireAuth();

    if (
        strtoupper($_SESSION['user']->role ?? '') !== 'OWNER'
    ) {
        abortx(403);
    }

    $advance =
        row(
            'SELECT * FROM advances WHERE id=?',
            [$id]
        );

    if (!$advance) {
        abortx(404);
    }

    return render(
        'advances.edit',
        compact('advance')
    );
}

function advanceUpdate($id) {
    requireAuth();

    if (
        strtoupper($_SESSION['user']->role ?? '') !== 'OWNER'
    ) {
        abortx(403);
    }

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    execq(
        'UPDATE advances SET
            employee_name=?,
            category=?,
            amount=?,
            payment_mode=?,
            upi_id=?,
            advance_date=?,
            description=?,
            updated_at=NOW()
         WHERE id=?',
        [
            req('employee_name'),
            req('category'),
            req('amount'),
            req('payment_mode'),
            req('upi_id'),
            req('advance_date'),
            req('description'),
            $id
        ]
    );

    $_SESSION['_flash']['success'] =
        'Advance updated successfully.';

    redirect_to('/expenses/advances');
}

function advanceDelete($id) {
    requireAuth();

    if (
        strtoupper($_SESSION['user']->role ?? '') !== 'OWNER'
    ) {
        abortx(403);
    }

    if (
        !hash_equals(
            csrf_token(),
            (string)req('_token')
        )
    ) {
        abortx(419);
    }

    execq(
        'DELETE FROM advances WHERE id=?',
        [$id]
    );

    $_SESSION['_flash']['success'] =
        'Advance deleted successfully.';

    redirect_to('/expenses/advances');
}

function advanceVoucher($id) {
    requireAuth();

    $advance =
        row(
            'SELECT * FROM advances WHERE id=?',
            [$id]
        );

    if (!$advance) {
        abortx(404);
    }

    $n = (int)val(
        'SELECT COUNT(*) FROM advances WHERE id<=?',
        [$id]
    );

    $displayVoucherNumber =
        str_pad($n, 3, '0', STR_PAD_LEFT);

    $amountInWords =
        numberToWords(
            (int)floor((float)$advance->amount)
        );

    return render(
        'advances.voucher',
        compact(
            'advance',
            'displayVoucherNumber',
            'amountInWords'
        )
    );
}

function numberToWords($n) {

    $ones = [
        'Zero',
        'One',
        'Two',
        'Three',
        'Four',
        'Five',
        'Six',
        'Seven',
        'Eight',
        'Nine',
        'Ten',
        'Eleven',
        'Twelve',
        'Thirteen',
        'Fourteen',
        'Fifteen',
        'Sixteen',
        'Seventeen',
        'Eighteen',
        'Nineteen'
    ];

    $tens = [
        '',
        '',
        'Twenty',
        'Thirty',
        'Forty',
        'Fifty',
        'Sixty',
        'Seventy',
        'Eighty',
        'Ninety'
    ];

    if ($n < 20) {
        return $ones[$n];
    }

    if ($n < 100) {
        return $tens[intdiv($n, 10)] .
            ($n % 10 ? ' ' . $ones[$n % 10] : '');
    }

    if ($n < 1000) {
        return $ones[intdiv($n, 100)] .
            ' Hundred' .
            ($n % 100
                ? ' ' . numberToWords($n % 100)
                : '');
    }

    if ($n < 100000) {
        return numberToWords(intdiv($n, 1000)) .
            ' Thousand' .
            ($n % 1000
                ? ' ' . numberToWords($n % 1000)
                : '');
    }

    if ($n < 10000000) {
        return numberToWords(intdiv($n, 100000)) .
            ' Lakh' .
            ($n % 100000
                ? ' ' . numberToWords($n % 100000)
                : '');
    }

    return numberToWords(intdiv($n, 10000000)) .
        ' Crore' .
        ($n % 10000000
            ? ' ' . numberToWords($n % 10000000)
            : '');
}

function reports() {
    requireAuth();

    $incomes =
        rows(
            'SELECT i.*,d.name AS driver_name
             FROM incomes i
             LEFT JOIN drivers d ON d.id=i.driver_id
             ORDER BY i.income_date DESC'
        );

    $expenses =
        rows(
            'SELECT * FROM expenses
             ORDER BY expense_date DESC'
        );

    $advances =
        rows(
            'SELECT * FROM advances
             ORDER BY advance_date DESC'
        );

    $totalIncome =
        (float)val(
            'SELECT COALESCE(SUM(amount),0)
             FROM incomes'
        );

    $totalExpense =
        (float)val(
            'SELECT COALESCE(SUM(amount),0)
             FROM expenses'
        );

    $totalAdvance =
        (float)val(
            'SELECT COALESCE(SUM(amount),0)
             FROM advances'
        );

    $balance =
        $totalIncome -
        $totalExpense -
        $totalAdvance;

    return render(
        'reports.index',
        compact(
            'incomes',
            'expenses',
            'advances',
            'totalIncome',
            'totalExpense',
            'totalAdvance',
            'balance'
        )
    );
}

function searchPage() {
    requireAuth();

    $s = trim((string)req('search', ''));

    $x =
        $s !== ""
            ? "%{$s}%"
            : "%";

    $incomes =
        rows(
            'SELECT i.*,d.name AS driver_name
             FROM incomes i
             LEFT JOIN drivers d ON d.id=i.driver_id
             WHERE i.driver_id LIKE ?
             OR i.description LIKE ?
             OR i.fees_amount LIKE ?
             OR i.gst_amount LIKE ?
             OR i.total_amount LIKE ?
             OR i.amount LIKE ?
             OR i.income_date LIKE ?
             ORDER BY i.income_date DESC,i.id DESC',
            [
                $x,
                $x,
                $x,
                $x,
                $x,
                $x,
                $x
            ]
        );

    $expenses =
        rows(
            'SELECT * FROM expenses
             WHERE description LIKE ?
             OR amount LIKE ?
             OR expense_date LIKE ?
             ORDER BY expense_date DESC,id DESC',
            [
                $x,
                $x,
                $x
            ]
        );

    return render(
        'search.search',
        compact(
            'search',
            'incomes',
            'expenses'
        )
    );
}

$method = $_SERVER['REQUEST_METHOD'];

if (
    $method === 'POST' &&
    isset($_POST['_method'])
) {
    $method = strtoupper($_POST['_method']);
}

$uri =
    parse_url(
        $_SERVER['REQUEST_URI'],
        PHP_URL_PATH
    );

$uri =
    rtrim($uri, '/') ?: '/';

if ($uri === '/login') {
    echo login();
    exit;
}

if (
    $uri === '/logout' &&
    $method === 'POST'
) {
    logout();
}

if (
    $uri === '/dashboard' ||
    $uri === '/accounts-dashboard'
) {
    echo dashboard();
    exit;
}

if ($uri === '/drivers') {
    requireAuth();
    redirect_to('/dashboard');
}

if (
    $uri === '/income' &&
    $method === 'GET'
) {
    echo incomeIndex();
    exit;
}

if ($uri === '/income/create') {
    echo
        $method === 'GET'
            ? incomeCreate()
            : incomeStore();

    exit;
}

if (
    preg_match(
        '#^/income/(\d+)/edit$#',
        $uri,
        $m
    )
) {
    echo incomeEdit($m[1]);
    exit;
}

if (
    preg_match(
        '#^/income/(\d+)$#',
        $uri,
        $m
    )
) {
    if ($method === 'PUT') {
        incomeUpdate($m[1]);
    }

    if ($method === 'DELETE') {
        incomeDelete($m[1]);
    }
}

if (
    $uri === '/expenses' &&
    $method === 'GET'
) {
    echo expensesIndex();
    exit;
}

if ($uri === '/expenses/create') {
    echo
        $method === 'GET'
            ? expenseCreate()
            : expenseStore();

    exit;
}

if (
    preg_match(
        '#^/expenses/(\d+)/edit$#',
        $uri,
        $m
    )
) {
    echo expenseEdit($m[1]);
    exit;
}

if (
    preg_match(
        '#^/expenses/(\d+)/voucher$#',
        $uri,
        $m
    )
) {
    echo expenseVoucher($m[1]);
    exit;
}

if (
    preg_match(
        '#^/expenses/(\d+)$#',
        $uri,
        $m
    )
) {
    if ($method === 'PUT') {
        expenseUpdate($m[1]);
    }

    if ($method === 'DELETE') {
        expenseDelete($m[1]);
    }
}

if (
    $uri === '/expenses/advances' &&
    $method === 'GET'
) {
    echo advancesIndex();
    exit;
}

if ($uri === '/expenses/advances/create') {
    echo
        $method === 'GET'
            ? advanceCreate()
            : advanceStore();

    exit;
}

if (
    preg_match(
        '#^/expenses/advances/(\d+)/edit$#',
        $uri,
        $m
    )
) {
    echo advanceEdit($m[1]);
    exit;
}

if (
    preg_match(
        '#^/expenses/advances/(\d+)/voucher$#',
        $uri,
        $m
    )
) {
    echo advanceVoucher($m[1]);
    exit;
}

if (
    preg_match(
        '#^/expenses/advances/(\d+)$#',
        $uri,
        $m
    )
) {
    if ($method === 'PUT') {
        advanceUpdate($m[1]);
    }

    if ($method === 'DELETE') {
        advanceDelete($m[1]);
    }
}

if ($uri === '/reports') {
    echo reports();
    exit;
}

if ($uri === '/search') {
    echo searchPage();
    exit;
}

http_response_code(404);
echo '<h1>404 Not Found</h1>';