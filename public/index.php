<?php
declare(strict_types=1);

require __DIR__ . '/../core/bootstrap.php';
require __DIR__ . '/../core/app.php';

$blade = new BladeLite(__DIR__ . '/../resources/views');

$uri=parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH) ?: '/';
$base=rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''),'/');
if($base && $base!=='/' && str_starts_with($uri,$base)) $uri=substr($uri,strlen($base));
$uri='/'.trim($uri,'/');
if($uri==='//')$uri='/';
$method=strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
if($method==='POST' && !empty($_POST['_method'])) $method=strtoupper($_POST['_method']);
verify_csrf();

try {
    // Public
    if($uri==='/' && $method==='GET'){
        if(Auth::check()) redirect_to(route('dashboard'));
        redirect_to(route('login'));
    }
    if($uri==='/login' && $method==='GET') login_action();
    if($uri==='/login' && $method==='POST') login_action();
    if($uri==='/logout' && $method==='POST') logout_action();

    require_auth();

    // Dashboard
    if(($uri==='/dashboard'||$uri==='/accounts-dashboard') && $method==='GET') dashboard_action();

    // Drivers (keeps original /drivers behaviour; extra CRUD routes use existing views)
    if($uri==='/drivers' && $method==='GET') redirect_to(route('dashboard'));
    if($uri==='/drivers/create' && $method==='GET') drivers_create();
    if($uri==='/drivers' && $method==='POST') drivers_store();
    if(preg_match('#^/drivers/(\\d+)/edit$#',$uri,$m) && $method==='GET') drivers_edit((int)$m[1]);
    if(preg_match('#^/drivers/(\\d+)$#',$uri,$m) && in_array($method,['PUT','PATCH'],true)) drivers_update((int)$m[1]);
    if(preg_match('#^/drivers/(\\d+)$#',$uri,$m) && $method==='DELETE') drivers_delete((int)$m[1]);
    if(preg_match('#^/drivers/(\\d+)/ledger$#',$uri,$m) && $method==='GET') drivers_ledger((int)$m[1]);
    if(preg_match('#^/drivers/(\\d+)/payment$#',$uri,$m) && $method==='POST') drivers_payment((int)$m[1]);

    // Income
    if($uri==='/income' && $method==='GET') income_index();
    if($uri==='/income/create' && $method==='GET') income_create();
    if($uri==='/income' && $method==='POST') income_store();
    if(preg_match('#^/income/(\\d+)/edit$#',$uri,$m) && $method==='GET') income_edit((int)$m[1]);
    if(preg_match('#^/income/(\\d+)$#',$uri,$m) && in_array($method,['PUT','PATCH'],true)) income_update((int)$m[1]);
    if(preg_match('#^/income/(\\d+)$#',$uri,$m) && $method==='DELETE') income_delete((int)$m[1]);

    // Expenses
    if($uri==='/expenses' && $method==='GET') expense_index();
    if($uri==='/expenses/create' && $method==='GET') expense_create();
    if($uri==='/expenses' && $method==='POST') expense_store();
    if(preg_match('#^/expenses/(\\d+)/edit$#',$uri,$m) && $method==='GET') expense_edit((int)$m[1]);
    if(preg_match('#^/expenses/(\\d+)$#',$uri,$m) && in_array($method,['PUT','PATCH'],true)) expense_update((int)$m[1]);
    if(preg_match('#^/expenses/(\\d+)$#',$uri,$m) && $method==='DELETE') expense_delete((int)$m[1]);
    if(preg_match('#^/expenses/(\\d+)/voucher$#',$uri,$m) && $method==='GET') expense_voucher((int)$m[1]);

    // Salary advances — put these before generic expense ID routes
    if($uri==='/expenses/advances' && $method==='GET') advance_index();
    if($uri==='/expenses/advances/create' && $method==='GET') advance_create();
    if($uri==='/expenses/advances' && $method==='POST') advance_store();
    if(preg_match('#^/expenses/advances/(\\d+)/voucher$#',$uri,$m) && $method==='GET') advance_voucher((int)$m[1]);
    if(preg_match('#^/expenses/advances/(\\d+)/edit$#',$uri,$m) && $method==='GET') advance_edit((int)$m[1]);
    if(preg_match('#^/expenses/advances/(\\d+)$#',$uri,$m) && in_array($method,['PUT','PATCH'],true)) advance_update((int)$m[1]);
    if(preg_match('#^/expenses/advances/(\\d+)$#',$uri,$m) && $method==='DELETE') advance_delete((int)$m[1]);

    // Reports / search
    if($uri==='/reports' && $method==='GET') report_index();
    if($uri==='/search' && $method==='GET') search_index();
    if($uri==='/daily-account' && $method==='GET') daily_account();

    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
} catch (Throwable $e) {
    http_response_code(500);
    if((getenv('APP_DEBUG') ?: 'true') === 'true') {
        echo '<h1>Application Error</h1><pre>'.e($e->getMessage())."\n\n".e($e->getTraceAsString()).'</pre>';
    } else echo '<h1>Application Error</h1>';
}
