<?php
require 'inc/auth_check.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - SaaS Bot</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container-fluid">
      <span class="navbar-brand">Bem-vindo, <?= $_SESSION['user_name']; ?></span>
      <a href="auth/logout.php" class="btn btn-light">Sair</a>
    </div>
  </nav>

  <div class="container">
    <h3 class="mb-4">Dashboard de Relatórios</h3>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body text-center">
            <h5 class="card-title">Mensagens Enviadas</h5>
            <p class="card-text display-6">123</p>
            <a href="#" class="btn btn-primary w-100">Ver Detalhes</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body text-center">
            <h5 class="card-title">Respostas do Bot</h5>
            <p class="card-text display-6">97</p>
            <a href="#" class="btn btn-primary w-100">Ver Respostas</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body text-center">
            <h5 class="card-title">Último Acesso</h5>
            <p class="card-text display-6"><?= date('d/m/Y H:i'); ?></p>
            <a href="#" class="btn btn-primary w-100">Ver Histórico</a>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-3">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h5 class="card-title">Relatório de Atividades</h5>
            <p class="card-text">Visualize um resumo das suas interações com o bot nos últimos dias.</p>
            <a href="#" class="btn btn-outline-primary">Acessar Relatório</a>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h5 class="card-title">Estatísticas Gerais</h5>
            <p class="card-text">Veja gráficos e métricas de uso do sistema.</p>
            <a href="#" class="btn btn-outline-primary">Ver Estatísticas</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</body>
</html>
