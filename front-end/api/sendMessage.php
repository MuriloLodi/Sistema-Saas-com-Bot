<?php
session_start();
require '../inc/db.php';

// Validação básica
if (!isset($_POST['message']) || empty($_POST['message'])) {
  http_response_code(400);
  exit('Mensagem inválida');
}

$message = $_POST['message'];
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
  http_response_code(401);
  exit('Usuário não autenticado');
}

// Envia a mensagem para o bot Node.js
$botResponse = @file_get_contents('http://localhost:3000/api/bot?message=' . urlencode($message));

// Se a API não responder, envia erro
if ($botResponse === false) {
  http_response_code(500);
  exit('Erro ao conectar ao bot');
}

// Salva no banco de dados
$stmt = $pdo->prepare("INSERT INTO messages (user_id, message, response) VALUES (?, ?, ?)");
$stmt->execute([$user_id, $message, $botResponse]);

// Retorna a resposta para o frontend
echo $botResponse;
