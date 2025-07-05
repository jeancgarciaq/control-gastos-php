<?php

namespace App\Controllers;

use App\Core\View;
use PDO;

/**
 * Class HomeController
 * Handles requests for the home page.
 */
class HomeController
{
    /**
     * @var PDO The database connection object.
     */
    private PDO $pdo;

    /**
     * HomeController constructor.
     *
     * @param PDO $pdo The database connection object.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Displays the home page.
     *
     * @return void
     */
    public function index()
    {
        // No database interaction needed for the home page, but the connection is available if needed.
        View::render('index', ['title' => 'Expense Control']);
    }
}