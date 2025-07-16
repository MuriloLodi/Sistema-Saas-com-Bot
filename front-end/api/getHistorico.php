<?php
session_start();
require '../inc/db.php';

// Verifica se o usuário está logado
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
  http_response_code(401);
  exit('Usuário não autenticado');
}

// Busca as últimas 50 mensagens do usuário
$stmt = $pdo->prepare("SELECT message, response FROM messages WHERE user_id = ? ORDER BY created_at ASC LIMIT 50");
$stmt->execute([$user_id]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retorna como JSON
header('Content-Type: application/json');
echo json_encode($mensagens);
