<?php
/**
 * @var string $title The title of the page.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="./output.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Welcome to <?= htmlspecialchars($title) ?></h1>

        <p class="text-gray-700 mb-4">
            This is a simple personal expense control system built with PHP using the MVC pattern and styled with Tailwind CSS.
        </p>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-2">Key Features:</h2>
        <ul class="list-disc list-inside text-gray-700 mb-4">
            <li>User Registration and Login</li>
            <li>Profile Management</li>
            <li>Expense Tracking</li>
            <li>Income Tracking</li>
            <li>Balance Calculation</li>
            <li>Responsive Design</li>
        </ul>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-2">Technologies Used:</h2>
        <ul class="list-disc list-inside text-gray-700 mb-4">
            <li>PHP (with Composer)</li>
            <li>MySQL (with PDO)</li>
            <li>Tailwind CSS</li>
            <li>HTML</li>
            <li>JavaScript (AJAX)</li>
        </ul>

        <div class="mt-8">
            <a href="/register" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-4">Register</a>
            <a href="/login" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Login</a>
        </div>
    </div>

</body>
</html>