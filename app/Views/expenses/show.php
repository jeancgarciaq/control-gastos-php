<?php
/**
 * @var string $title The title of the page.
 * @var array $expense The expense data to be displayed.
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
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Expense Details</h1>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <strong class="font-bold">Date:</strong>
                <span><?= htmlspecialchars($expense['date']) ?></span>
            </div>
            <div class="mb-4">
                <strong class="font-bold">Description:</strong>
                <span><?= htmlspecialchars($expense['description']) ?></span>
            </div>
            <div class="mb-4">
                <strong class="font-bold">Amount:</strong>
                <span><?= htmlspecialchars($expense['amount']) ?></span>
            </div>
            <div class="mb-4">
                <strong class="font-bold">Type:</strong>
                <span><?= htmlspecialchars($expense['type']) ?></span>
            </div>

            <div class="flex items-center justify-between">
                <a href="/expenses" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Back to Expenses
                </a>
                <div>
                    <a href="/expenses/<?= htmlspecialchars($expense['id']) ?>/edit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Edit
                    </a>
                    <form action="/expenses/<?= htmlspecialchars($expense['id']) ?>/delete" method="post" class="inline">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this expense?')">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>