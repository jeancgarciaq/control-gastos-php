<?php
/**
 * @var string $title The title of the page.
 * @var array|null $errors An array of validation errors, or null if there are no errors.
 * @var array $profile The profile data to be edited.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="../output.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-4">Edit Profile</h2>

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

        <form method="post" action="/profile/<?= htmlspecialchars($profile['id']) ?>/edit">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Name
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="name" type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($profile['name'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                    Phone
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="phone" type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="position_or_company">
                    Position or Company
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="position_or_company" type="text" name="position_or_company" placeholder="Position or Company" value="<?= htmlspecialchars($profile['position_or_company'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="marital_status">
                    Marital Status
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="marital_status" type="text" name="marital_status" placeholder="Marital Status" value="<?= htmlspecialchars($profile['marital_status'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="children">
                    Children
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="children" type="number" name="children" placeholder="Children" value="<?= htmlspecialchars($profile['children'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="assets">
                    Assets
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="assets" type="number" step="0.01" name="assets" placeholder="Assets" value="<?= htmlspecialchars($profile['assets'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="initial_balance">
                    Initial Balance
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="initial_balance" type="number" step="0.01" name="initial_balance" placeholder="Initial Balance" value="<?= htmlspecialchars($profile['initial_balance'] ?? '') ?>">
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Update
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="/profile/<?= htmlspecialchars($profile['id']) ?>">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</body>
</html>