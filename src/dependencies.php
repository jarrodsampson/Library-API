<?php
use App\Controllers\Books;
use App\Controllers\Categories;
use App\Controllers\Rates;

use App\Database\DataAccess;

$container = $app->getContainer();

// Database
$container['pdo'] = function ($c)
{
    $settings = $c->get('settings') ['pdo'];
    return new PDO($settings['dsn'], $settings['username'], $settings['password']);
};

// monolog
$container['logger'] = function ($c)
{
    $settings = $c->get('settings') ['logger'];
    $logger = new \Monolog\Logger($settings['name']);
    $file_handler = new \Monolog\Handler\StreamHandler(__DIR__ . '/../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['cache'] = function ()
{
    return new \Slim\HttpCache\CacheProvider();
};

// database
$container['App\Database\DataAccess'] = function ($c)
{
    $localtable = $c->get('settings') ['localtable'] != '' ? $c->get('settings') ['localtable'] : '';
    return new DataAccess($c->get('logger') , $c->get('pdo') , $localtable);
};

$container['App\Controllers\Books'] = function ($c)
{
    return new Books($c->get('logger') , $c->get('App\Database\DataAccess'));
};

$container['App\Controllers\Categories'] = function ($c)
{
    return new Categories($c->get('logger') , $c->get('App\Database\DataAccess'));
};

$container['App\Controllers\Rates'] = function ($c)
{
    return new Rates($c->get('logger') , $c->get('App\Database\DataAccess'));
};