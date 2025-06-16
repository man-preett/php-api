
<?php
require __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client;

$database = 'employ_management';
$uri = "mongodb://localhost:27017";

try {
    $client = new Client($uri);
    $db = $client->selectDatabase($database);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'MongoDB Connection failed: ' . $e->getMessage()
    ]);
    exit;
}
?>