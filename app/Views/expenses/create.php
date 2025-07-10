<?php
/**
 * @var string $title The title of the page.
 * @var array|null $errors An array of validation errors, or null if there are no errors.
 * @var array|null $data The data submitted by the user (for repopulating the form).
 * @var array $profiles an array containing the user's profiles
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
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-4">Create New Expense</h2>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">Please correct the following errors:</span>
                <ul class="list-disc list-inside mt-2">
                    <?php foreach ($errors as $field => $messages): ?>
                        <?php foreach ($messages as $message): ?>
                            <li><?= htmlspecialchars($message) ?></li>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/expenses/create">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="date">
                    Date
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="date" type="date" name="date" placeholder="Date" value="<?= htmlspecialchars($data['date'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Description
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="description" type="text" name="description" placeholder="Description" value="<?= htmlspecialchars($data['description'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="amount">
                    Amount
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="amount" type="number" step="0.01" name="amount" placeholder="Amount" value="<?= htmlspecialchars($data['amount'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                    Type
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="type" type="text" name="type" placeholder="Type" value="<?= htmlspecialchars($data['type'] ?? '') ?>">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="profile_id">
                    Profile
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="profile_id" name="profile_id">
                    <?php foreach ($profiles as $profile): ?>
                        <option value="<?= htmlspecialchars($profile['id']) ?>" <?= ($data['profile_id'] ?? '') == $profile['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($profile['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Create
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="/expenses">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</body>
</html>