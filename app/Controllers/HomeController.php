<?php

namespace App\Controllers;

use App\Core\View;

/**
 * Class HomeController
 * Handles requests for the home page.
 */
class HomeController
{
    /**
     * Displays the home page.
     *
     * @return void
     */
    public function index()
    {
        View::render('index', ['title' => 'Expense Control']);
    }
}