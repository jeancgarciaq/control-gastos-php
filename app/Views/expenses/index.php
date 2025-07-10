<?php
/**
 * @var string $title The title of the page.
 * @var array $expenses An array of expense data.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="/output.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Expenses</h1>

        <div class="mb-4">
            <a href="/expenses/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Expense
            </a>
        </div>

        <?php if (empty($expenses)): ?>
            <p class="text-gray-700">No expenses found.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 rounded">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4 border-b">Date</th>
                        <th class="py-2 px-4 border-b">Description</th>
                        <th class="py-2 px-4 border-b">Amount</th>
                        <th class="py-2 px-4 border-b">Type</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenses as $expense): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($expense['date']) ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($expense['description']) ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($expense['amount']) ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($expense['type']) ?></td>
                            <td class="py-2 px-4 border-b">
                                <a href="/expenses/<?= htmlspecialchars($expense['id']) ?>" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                                <a href="/expenses/<?= htmlspecialchars($expense['id']) ?>/edit" class="text-green-500 hover:text-green-700 mr-2">Edit</a>
                                <form action="/expenses/<?= htmlspecialchars($expense['id']) ?>/delete" method="post" class="inline">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this expense?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>