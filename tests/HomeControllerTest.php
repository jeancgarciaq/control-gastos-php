<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Controllers\HomeController;
use App\Core\View;

class HomeControllerTest extends TestCase
{
    public function testIndex()
    {
        // Mock the View::render method
        View::$mockRenderedView = null;
        View::$mockRenderedData = null;
        View::$mock = true;

        $controller = new HomeController();
        $controller->index();

        $this->assertEquals('index', View::$mockRenderedView);
        $this->assertArrayHasKey('title', View::$mockRenderedData);
        $this->assertEquals('Expense Control', View::$mockRenderedData['title']);

        // Reset mock state
        View::$mock = false;
    }
}