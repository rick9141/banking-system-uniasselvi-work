<?php
session_start();
include 'db.php';

// Função para registrar logs
function log_message($message) {
    echo "<script>console.log('".addslashes($message)."');</script>";
}

log_message("Usuário logado com ID: " . $_SESSION['user_id']);

// Obter os dados do cliente
$user_id = $_SESSION['user_id'];
$account_number = $_SESSION['account_number'];

log_message("Obtendo transações para a conta: " . $account_number);

// Construir a consulta SQL dinamicamente
$sql = "SELECT * FROM transacoes WHERE account_number_from = ? OR account_number_to = ? ORDER BY date DESC";
log_message("Consulta SQL: " . $sql);
$stmt = $conn->prepare($sql);

try {
    log_message("Preparando consulta SQL");
    log_message("Parâmetros vinculados: " . $account_number . ", " . $account_number);

    log_message("Executando consulta SQL");
    $stmt->execute([$account_number, $account_number]);
    log_message("Consulta SQL executada");

    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    log_message("Transações obtidas com sucesso, total de transações: " . count($transactions));
} catch (PDOException $e) {
    log_message("Erro ao obter transações: " . $e->getMessage());
    die("Erro ao obter transações: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato - Banco UniassBank</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.html">Banco UniassBank</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.html#features">Características</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.html#about">Sobre Nós</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.html#contact">Contato</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-custom text-white" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Extrato -->
    <div class="container mt-5">
        <h2 class="text-center">Extrato</h2>
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Histórico de Transações</h5>
                <table class="table table-striped">
                    <thead>
                        <tr>
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

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2024 Banco UniassBank. Todos os direitos reservados.</p>
    </footer>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
