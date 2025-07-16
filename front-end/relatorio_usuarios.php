<?php
require 'inc/auth_check.php';
require 'inc/db.php';

// Buscar todos os usuários
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY criado_em DESC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatório de Usuários</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">
    <h3>Relatório de Usuários</h3>

    <table class="table table-striped mt-3">
      <thead>
        <tr>
          <th>#</th>
          <th>Número WhatsApp</th>
          <th>Data de Cadastro</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $i => $user): ?>
          <tr>
            <td><?= $i + 1; ?></td>
            <td><?= htmlspecialchars($user['numero']); ?></td>
            <td><?= date('d/m/Y H:i', strtotime($user['criado_em'])); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">Voltar ao Dashboard</a>
  </div>
</body>
</html>
