<?php
require 'inc/auth_check.php';
require 'inc/db.php';

// Consulta combinando ganhos, gastos e investimentos
$query = "
  SELECT u.numero, g.valor, g.descricao, g.data, 'Ganho' as tipo
  FROM ganhos g
  JOIN usuarios u ON u.id = g.usuario_id
  UNION ALL
  SELECT u.numero, g.valor, g.descricao, g.data, 'Gasto' as tipo
  FROM gastos g
  JOIN usuarios u ON u.id = g.usuario_id
  UNION ALL
  SELECT u.numero, i.valor, i.descricao, NOW() as data, 'Investimento' as tipo
  FROM investimentos i
  JOIN usuarios u ON u.id = i.usuario_id
  ORDER BY data DESC
";

$stmt = $pdo->query($query);
$transacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Relatório de Transações</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-4">
    <h3>Relatório de Transações</h3>

    <table class="table table-bordered table-striped mt-3">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Tipo</th>
          <th>Usuário (WhatsApp)</th>
          <th>Valor</th>
          <th>Descrição</th>
          <th>Data</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($transacoes as $i => $t): ?>
          <tr>
            <td><?= $i + 1; ?></td>
            <td>
              <span class="badge bg-<?= 
                $t['tipo'] === 'Ganho' ? 'success' : 
                ($t['tipo'] === 'Gasto' ? 'danger' : 'primary') ?>">
                <?= $t['tipo']; ?>
              </span>
            </td>
            <td><?= htmlspecialchars($t['numero']); ?></td>
            <td>R$ <?= number_format($t['valor'], 2, ',', '.'); ?></td>
            <td><?= htmlspecialchars($t['descricao']); ?></td>
            <td><?= date('d/m/Y H:i', strtotime($t['data'])); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary">Voltar ao Dashboard</a>
  </div>
  <?php
// Cálculo dos totais para gráfico
$totalGanhos = 0;
$totalGastos = 0;
$totalInvest = 0;

foreach ($transacoes as $t) {
  if ($t['tipo'] === 'Ganho') $totalGanhos += $t['valor'];
  elseif ($t['tipo'] === 'Gasto') $totalGastos += $t['valor'];
  elseif ($t['tipo'] === 'Investimento') $totalInvest += $t['valor'];
}
?>
<h4 class="mt-5">Gráfico Geral de Transações</h4>
<canvas id="graficoTransacoes" height="120"></canvas>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('graficoTransacoes').getContext('2d');
  const grafico = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Ganhos', 'Gastos', 'Investimentos'],
      datasets: [{
        data: [<?= $totalGanhos; ?>, <?= $totalGastos; ?>, <?= $totalInvest; ?>],
        backgroundColor: ['#198754', '#dc3545', '#0d6efd'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'bottom'
        },
        title: {
          display: true,
          text: 'Distribuição Financeira Total'
        }
      }
    }
  });
</script>

</body>
</html>
