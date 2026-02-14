<?php
// public/debug_content.php
require __DIR__ . '/../Banco de dados/conexao.php';

echo "<h1>Debug Content</h1>";

// 1. Check for duplicate products
echo "<h2>1. Duplicate Check</h2>";
$sql = "SELECT nome, COUNT(*) as c FROM produtos GROUP BY nome HAVING COUNT(*) > 1";
$stmt = $pdo->query($sql);
$duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($duplicates) > 0) {
    echo "<p style='color:red'>Found duplicates!</p>";
    echo "<pre>" . print_r($duplicates, true) . "</pre>";
} else {
    echo "<p style='color:green'>No duplicates found by name.</p>";
}

// 2. Inspect a sample product description
echo "<h2>2. Description Formatting</h2>";
$stmt = $pdo->query("SELECT id, nome, descricao FROM produtos LIMIT 3");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $p) {
    echo "<h3>" . htmlspecialchars($p['nome']) . "</h3>";
    echo "<strong>Raw DB Content (nl2br applied):</strong><br>";
    echo "<div style='border:1px solid #ccc; padding:10px; background:#f9f9f9'>";
    // Show raw string with replaced \n visible
    $raw = htmlspecialchars($p['descricao']);
    echo "<code>" . str_replace("\n", "<span style='color:red;'>\\n</span>", $raw) . "</code>";
    echo "</div>";

    echo "<strong>Formatted Output (HTML):</strong><br>";
    echo "<div style='border:1px solid #ccc; padding:10px;'>";
    echo nl2br(htmlspecialchars($p['descricao']));
    echo "</div>";
    echo "<hr>";
}

// 3. User Check
echo "<h2>3. Users</h2>";
$users = $pdo->query("SELECT id, email, nome FROM usuarios LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>" . print_r($users, true) . "</pre>";
