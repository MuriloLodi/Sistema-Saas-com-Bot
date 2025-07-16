<?php
require 'inc/auth_check.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Chat com o Bot - SaaS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
</head>
<body>
  <div class="container mt-4">
    <h3>Chat com o Bot</h3>

    <div id="chat-box" class="border rounded p-3 mb-3" style="height: 400px; overflow-y: auto; background: #f8f9fa;">
      <!-- Mensagens serão carregadas aqui -->
    </div>

    <form id="chat-form" class="input-group mb-3">
      <input type="text" id="message" class="form-control" placeholder="Digite sua mensagem" required>
      <button class="btn btn-primary">Enviar</button>
    </form>

    <a href="dashboard.php" class="btn btn-secondary">Voltar ao Dashboard</a>
  </div>

<script>
$(function(){
  // Carrega histórico ao abrir
  carregarHistorico();

  $('#chat-form').on('submit', function(e){
    e.preventDefault();
    const msg = $('#message').val();
    $('#chat-box').append('<div><strong>Você:</strong> ' + msg + '</div>');
    $('#message').val('');
    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);

    $.post('api/sendMessage.php', { message: msg }, function(res){
      $('#chat-box').append('<div><strong>Bot:</strong> ' + res + '</div>');
      $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    }).fail(function(){
      $('#chat-box').append('<div class="text-danger"><strong>Erro:</strong> Não foi possível enviar a mensagem</div>');
    });
  });

  function carregarHistorico() {
    $.get('api/getHistorico.php', function(mensagens){
      mensagens.forEach(function(m){
        $('#chat-box').append('<div><strong>Você:</strong> ' + m.message + '</div>');
        $('#chat-box').append('<div><strong>Bot:</strong> ' + (m.response || '<em>Pendente</em>') + '</div>');
      });
      $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
    }, 'json');
  }
});
</script>
</body>
</html>
