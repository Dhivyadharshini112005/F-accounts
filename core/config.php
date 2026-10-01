<?php
declare(strict_types=1);

$envFile=dirname(__DIR__).'/.env';
if (is_file($envFile)) {
    foreach(file($envFile, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $line){
        $line=trim($line);
        if($line===''||$line[0]==='#'||!str_contains($line,'='))continue;
        [$k,$v]=explode('=',$line,2);$k=trim($k);$v=trim($v);
        if(strlen($v)>=2 && (($v[0]==='"'&&substr($v,-1)==='"')||($v[0]==="'"&&substr($v,-1)==="'"))) $v=substr($v,1,-1);
        if(getenv($k)===false) putenv($k.'='.$v);
    }
}

return [
    'app_url' => getenv('APP_URL') ?: 'http://127.0.0.1:8000',
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_DATABASE') ?: 'f_taxi_accounts',
        'user' => getenv('DB_USERNAME') ?: 'root',
        'pass' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ],
];
