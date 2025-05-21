


<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header('Content-Type: application/json');
include 'config.php'; // Make sure this file sets up $pdo

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(['error' => 'Username and password are required']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && hash('sha256', $password) === $user['password']) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username']
        ]
    ]);
} else {
    echo json_encode(['error' => 'Invalid credentials']);
}

// if (!$user) {
//     echo json_encode(['error' => 'User not found']);
//     exit;
// }
// if ($user && $password !== $user['password']) {
//     echo json_encode(['error' => 'Password incorrect', 'input' => $password, 'db' => $user['password']]);
//     exit;
// }

?>