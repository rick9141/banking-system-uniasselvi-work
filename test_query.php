<?php
session_start();
include 'db.php';

// Função para registrar logs
function log_message($message) {
    echo "<script>console.log('".addslashes($message)."');</script>";
}

log_message("Teste de consulta simples");

$sql = "SELECT TOP 10 * FROM transacoes";
log_message("Consulta SQL: " . $sql);
$stmt = $conn->prepare($sql);

try {
    log_message("Executando consulta SQL");
    $stmt->execute();
    log_message("Consulta SQL executada");

    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    log_message("Transações obtidas com sucesso, total de transações: " . count($transactions));
} catch (PDOException $e) {
    log_message("Erro ao obter transações: " . $e->getMessage());
    die("Erro ao obter transações: " . $e->getMessage());
}

foreach ($transactions as $transaction) {
    log_message("Transação ID: " . $transaction['id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Consulta</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Teste de Consulta</h2>
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Resultados da Consulta</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>De</th>
                            <th>Para</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($transaction['date']))); ?></td>
                                <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['account_number_from']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['account_number_to']); ?></td>
                                <td>R$ <?php echo number_format($transaction['amount'], 2, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
