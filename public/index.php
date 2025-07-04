<?php

use App\Core\Router;
use App\Core\Request;
use App\Core\DotEnv;

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
(new DotEnv(__DIR__ . '/../.env'))->load();

// Instantiate the Router
$router = new Router(new Request());

// Define routes (example - adjust as needed)
$router->get('/', 'HomeController@index');
$router->get('/register', 'AuthController@register');
$router->post('/register', 'AuthController@processRegister');
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@processLogin');
$router->get('/logout', 'AuthController@logout');

//Profile Routes
$router->get('/profile/create', 'ProfileController@create');
$router->post('/profile/create', 'ProfileController@store');
$router->get('/profile/{id}/edit', 'ProfileController@edit');
$router->post('/profile/{id}/edit', 'ProfileController@update');
$router->get('/profile/{id}', 'ProfileController@show');
$router->post('/profile/{id}/delete', 'ProfileController@destroy'); //Simulate DELETE request


// Expense Routes
$router->get('/expenses', 'ExpenseController@index');
$router->get('/expenses/create', 'ExpenseController@create');
$router->post('/expenses/create', 'ExpenseController@store');
$router->get('/expenses/{id}/edit', 'ExpenseController@edit');
$router->post('/expenses/{id}/edit', 'ExpenseController@update');
$router->get('/expenses/{id}', 'ExpenseController@show');
$router->post('/expenses/{id}/delete', 'ExpenseController@destroy'); //Simulate DELETE request

// Income Routes
$router->get('/income', 'IncomeController@index');
$router->get('/income/create', 'IncomeController@create');
$router->post('/income/create', 'IncomeController@store');
$router->get('/income/{id}/edit', 'IncomeController@edit');
$router->post('/income/{id}/edit', 'IncomeController@update');
$router->get('/income/{id}', 'IncomeController@show');
$router->post('/income/{id}/delete', 'IncomeController@destroy'); //Simulate DELETE request

// Run the router
$router->resolve();