<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="/output.css?v=<?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/output.css') ?>" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-4">Login</h2>

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

        <form method="post" action="/login" id="loginForm">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="username" type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($data['username'] ?? '') ?>">
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       id="password" type="password" name="password" placeholder="Password">
            </div>

            <!-- Google reCAPTCHA -->
            <div class="mb-6">
                <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars(getenv('RECAPTCHA_SITE_KEY')) ?>" data-callback="handleRecaptcha"></div>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Login
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="/register">
                    Don't have an account? Register!
                </a>
            </div>
        </form>
    </div>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="/js/recaptcha.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>