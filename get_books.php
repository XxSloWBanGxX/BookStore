<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$host = 'localhost';
$user = 'root';
<<<<<<< HEAD
$password = '';
=======
$password = 'smaik1322';
>>>>>>> bb325550c9229aaa4ba21cf919905c8fa94a2afb
$dbname = 'shop';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection error']);
    exit;
}

$sql = "SELECT id, title, author, description, image FROM books";
$result = $conn->query($sql);

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

header('Content-Type: application/json');
echo json_encode($books);

$conn->close();
?>
