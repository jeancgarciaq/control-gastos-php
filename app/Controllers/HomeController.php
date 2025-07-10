<?php
/**
 * @file HomeController.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Controlador para gestionar la vista home.
*/

namespace App\Controllers;

use App\Core\View;

/**
 * Class HomeController
 * Handles requests for the home page.
 */
class HomeController extends Controller
{
    /**
     * Displays the home page.
     *
     * @return void
     */
    public function index()
    {
        // No database interaction needed for the home page, but the connection is available if needed.
        View::render('home', ['title' => 'Expense Control']);
    }
}