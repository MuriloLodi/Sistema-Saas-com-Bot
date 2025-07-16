<?php
require 'inc/auth_check.php';
require 'inc/db.php';

// Buscar total de usuários
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();

// Buscar totais financeiros
$totalGanhos = $pdo->query("SELECT IFNULL(SUM(valor), 0) FROM ganhos")->fetchColumn();
$totalGastos = $pdo->query("SELECT IFNULL(SUM(valor), 0) FROM gastos")->fetchColumn();
$totalInvest = $pdo->query("SELECT IFNULL(SUM(valor), 0) FROM investimentos")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Financeiro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container-fluid">
      <span class="navbar-brand">Dashboard Financeiro</span>
      <a href="auth/logout.php" class="btn btn-light">Sair</a>
    </div>
  </nav>

  <div class="container">
    <div class="row g-3">
      <div class="col-md-3">
        <div class="card shadow text-center">
          <div class="card-body">
            <h5 class="card-title">Usuários</h5>
            <p class="display-6"><?= $totalUsuarios; ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card shadow text-center">
          <div class="card-body">
            <h5 class="card-title">Ganhos</h5>
            <p class="display-6">R$ <?= number_format($totalGanhos, 2, ',', '.'); ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card shadow text-center">
          <div class="card-body">
            <h5 class="card-title">Gastos</h5>
            <p class="display-6">R$ <?= number_format($totalGastos, 2, ',', '.'); ?></p>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card shadow text-center">
          <div class="card-body">
            <h5 class="card-title">Investimentos</h5>
            <p class="display-6">R$ <?= number_format($totalInvest, 2, ',', '.'); ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <a href="relatorio_usuarios.php" class="btn btn-primary">Ver Relatório de Usuários</a>
      <a href="relatorio_transacoes.php" class="btn btn-success">Ver Relatório de Transações</a>
    </div>
  </div>
</body>
</html>
