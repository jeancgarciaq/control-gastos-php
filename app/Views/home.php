<?php
/**
 * @var string $title The title of the page.
 */
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title) ?></title>
  <link href="/output.css?v=<?= time() ?>" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <script src="https://kit.fontawesome.com/394d785d07.js" crossorigin="anonymous"></script>
</head>
<body class="flex flex-col min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

  <!-- Hero Section -->
  <section class="relative bg-blue-600 dark:bg-blue-700 overflow-hidden">
    <div class="absolute inset-0 opacity-20 bg-[url('/images/finance-bg.jpg')] bg-cover bg-center"></div>
    <div class="relative container mx-auto px-6 py-32 text-center">
      <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white mb-4">
        Take Control of Your Expenses
      </h1>
      <p class="text-lg sm:text-xl text-blue-100 mb-8">
        Track, analyze and optimize your spending with Expense Control
      </p>
      <div class="space-x-4">
        <a href="/register"
           class="inline-block bg-white text-blue-600 font-semibold py-3 px-6 rounded-lg shadow-lg hover:bg-blue-50 transition">
          Get Started
        </a>
        <a href="/login"
           class="inline-block bg-transparent border-2 border-white text-white font-semibold py-3 px-6 rounded-lg hover:bg-white hover:text-blue-600 transition">
          Log In
        </a>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold text-center mb-12">Key Features</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Feature Card -->
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-start space-y-4">
        <div class="text-blue-500 text-4xl"><i class="fas fa-user-plus"></i></div>
        <h3 class="text-xl font-semibold">Easy Signup & Login</h3>
        <p class="text-gray-600 dark:text-gray-300">Secure authentication with email/password and reCAPTCHA.</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-start space-y-4">
        <div class="text-green-500 text-4xl"><i class="fas fa-list-alt"></i></div>
        <h3 class="text-xl font-semibold">Profile Management</h3>
        <p class="text-gray-600 dark:text-gray-300">Manage multiple profiles with custom settings.</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-start space-y-4">
        <div class="text-red-500 text-4xl"><i class="fas fa-money-bill-wave"></i></div>
        <h3 class="text-xl font-semibold">Expense Tracker</h3>
        <p class="text-gray-600 dark:text-gray-300">Log expenses, categorize and search them easily.</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-start space-y-4">
        <div class="text-yellow-500 text-4xl"><i class="fas fa-wallet"></i></div>
        <h3 class="text-xl font-semibold">Income Tracker</h3>
        <p class="text-gray-600 dark:text-gray-300">Record all your incomes and compare with expenses.</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-start space-y-4">
        <div class="text-purple-500 text-4xl"><i class="fas fa-balance-scale"></i></div>
        <h3 class="text-xl font-semibold">Net Balance</h3>
        <p class="text-gray-600 dark:text-gray-300">See your financial health at a glance.</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col items-start space-y-4">
        <div class="text-indigo-500 text-4xl"><i class="fas fa-mobile-alt"></i></div>
        <h3 class="text-xl font-semibold">Responsive Design</h3>
        <p class="text-gray-600 dark:text-gray-300">Works great on desktop, tablet, and mobile.</p>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="bg-gray-50 dark:bg-gray-800 py-16">
    <div class="container mx-auto px-6">
      <h2 class="text-3xl font-bold text-center mb-12">How It Works</h2>
      <div class="flex flex-col md:flex-row justify-between items-start space-y-8 md:space-y-0 md:space-x-8">
        <div class="flex-1 text-center">
          <div class="mx-auto w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-user-plus text-blue-600"></i>
          </div>
          <h3 class="font-semibold mb-2">1. Sign Up</h3>
          <p class="text-gray-600 dark:text-gray-300">Create your free account.</p>
        </div>
        <div class="flex-1 text-center">
          <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-pencil-alt text-green-600"></i>
          </div>
          <h3 class="font-semibold mb-2">2. Add Entries</h3>
          <p class="text-gray-600 dark:text-gray-300">Log expenses & incomes.</p>
        </div>
        <div class="flex-1 text-center">
          <div class="mx-auto w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-chart-pie text-purple-600"></i>
          </div>
          <h3 class="font-semibold mb-2">3. Analyze</h3>
          <p class="text-gray-600 dark:text-gray-300">Review reports & balance.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Testimonials (mock) -->
  <section class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold text-center mb-12">What Our Users Say</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <blockquote class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <p class="italic text-gray-700 dark:text-gray-300 mb-4">“Expense Control me ayudó a poner orden en mis finanzas ¡Lo recomiendo!”</p>
        <footer class="flex items-center space-x-4">
          <img src="/images/user1.jpg" alt="User 1" class="w-12 h-12 rounded-full">
          <div>
            <p class="font-semibold">María López</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Emprendedora</p>
          </div>
        </footer>
      </blockquote>
      <blockquote class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <p class="italic text-gray-700 dark:text-gray-300 mb-4">“Ahora controlo mis gastos mensuales y ahorro más que nunca.”</p>
        <footer class="flex items-center space-x-4">
          <img src="/images/user2.jpg" alt="User 2" class="w-12 h-12 rounded-full">
          <div>
            <p class="font-semibold">Carlos Pérez</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">Desarrollador</p>
          </div>
        </footer>
      </blockquote>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 text-gray-400 py-8">
    <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
      <p class="text-sm">&copy; <?= date('Y') ?> Expense Control. All rights reserved.</p>
      <div class="flex space-x-4">
        <a href="https://github.com/jeancgarciaq/control-gastos-php" target="_blank" class="hover:text-white">
          <i class="fab fa-github fa-lg"></i>
        </a>
        <a href="https://x.com/jeancgarciaq" target="_blank" class="hover:text-blue-400">
          <i class="fab fa-x-twitter fa-lg"></i>
        </a>
        <a href="https://linkedin.com/in/jean-carlo-garcia-quinones" target="_blank" class="hover:text-blue-600">
          <i class="fab fa-linkedin fa-lg"></i>
        </a>
      </div>
    </div>
  </footer>

</body>
</html>