<?php
/**
 * @var string $title The title of the page.
 * @var array $user An array containing the authenticated user's data.
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
<body class="bg-gray-100 h-screen flex antialiased">

    <aside class="w-64 bg-gray-800 text-white flex-none">
        <div class="p-4">
            <h1 class="text-2xl font-semibold"><?= htmlspecialchars($user['username']) ?> Dashboard</h1>
        </div>
        <nav class="py-4">
            <ul>
                <li class="px-4 py-2 hover:bg-gray-700">
                    <a href="/profiles" class="block">Profiles</a>
                </li>
                <li class="px-4 py-2 hover:bg-gray-700">
                    <a href="/expenses" class="block">Expenses</a>
                </li>
                <li class="px-4 py-2 hover:bg-gray-700">
                    <a href="/income" class="block">Income</a>
                </li>
                <li class="px-4 py-2 hover:bg-gray-700">
                    <a href="/logout" class="block">Logout</a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="flex-1 p-4">
        <h2 class="text-2xl font-semibold mb-4">Overview</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white shadow-md rounded-md p-4">
                <h3 class="text-lg font-semibold mb-2">Total Expenses</h3>
                <p class="text-gray-700">$1,200.00</p>
            </div>
            <div class="bg-white shadow-md rounded-md p-4">
                <h3 class="text-lg font-semibold mb-2">Total Income</h3>
                <p class="text-gray-700">$2,500.00</p>
            </div>
            <div class="bg-white shadow-md rounded-md p-4">
                <h3 class="text-lg font-semibold mb-2">Current Balance</h3>
                <p class="text-gray-700">$1,300.00</p>
            </div>
            <!-- Add more cards as needed -->
        </div>
    </main>

</body>
</html>