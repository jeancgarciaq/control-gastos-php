<?php
/**
 * @var string $title The title of the page.
 * @var array $profiles An array of profile data.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="/output.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/output.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">My Profiles</h1>

        <div class="mb-4">
            <a href="/profile/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create New Profile
            </a>
        </div>

        <?php if (empty($profiles)): ?>
            <p class="text-gray-700">No profiles found.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($profiles as $profile): ?>
                    <div class="bg-white shadow-md rounded px-4 py-3">
                        <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($profile['name']) ?></h2>
                        <p class="text-gray-700">
                            <?= htmlspecialchars($profile['position_or_company']) ?>
                        </p>
                        <div class="mt-2">
                            <a href="/profile/<?= htmlspecialchars($profile['id']) ?>" class="text-blue-500 hover:text-blue-700 mr-2">View</a>
                            <a href="/profile/<?= htmlspecialchars($profile['id']) ?>/edit" class="text-green-500 hover:text-green-700 mr-2">Edit</a>
                            <form action="/profile/<?= htmlspecialchars($profile['id']) ?>/delete" method="post" class="inline">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this profile?')">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>