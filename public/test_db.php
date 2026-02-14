<?php
// public/test_db.php

echo "<h1>Database Connection Test</h1>";

// 1. Check loaded extensions
echo "<h2>Loaded Extensions</h2>";
$extensions = get_loaded_extensions();
echo "PDO drivers: " . implode(', ', pdo_drivers()) . "<br>";
echo "MySQL loaded: " . (in_array('pdo_mysql', $extensions) ? 'Yes' : 'No') . "<br>";
echo "PgSQL loaded: " . (in_array('pdo_pgsql', $extensions) ? 'Yes' : 'No') . "<br>";

// 2. Check Environment Variables (Masked)
echo "<h2>Environment Variables</h2>";
$vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_PORT', 'DB_CONNECTION'];

foreach ($vars as $var) {
    $val = getenv($var) !== false ? getenv($var) : ($_ENV[$var] ?? 'NOT SET');
    if ($var === 'DB_PASS' && $val !== 'NOT SET') {
        $val = substr($val, 0, 3) . '***' . substr($val, -3);
    }
    echo "$var: $val<br>";
}

// 3. Attempt Connection
echo "<h2>Connection Attempt</h2>";
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? '127.0.0.1';
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'bancodadosteste';
$user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '';
$port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
$driver = $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION') ?? 'mysql';

echo "Attempting to connect to <strong>$driver://$host:$port/$dbname</strong>...<br>";

try {
    $dsn = "$driver:host=$host;port=$port;dbname=$dbname";

    // Options for SSL if needed (Render often requires this for public connections, but internal might be fine)
    // For debugging, we just try basic first.
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h3 style='color: green'>SUCCESS! Connected to database.</h3>";
} catch (PDOException $e) {
    echo "<h3 style='color: red'>FAILURE</h3>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Code: " . $e->getCode() . "<br>";
}
