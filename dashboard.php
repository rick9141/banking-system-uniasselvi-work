<?php
session_start();
include 'db.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obter os dados do cliente
$user_id = $_SESSION['user_id'];
$sql = "SELECT name, account_number, branch_number, balance, email, phone FROM clientes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $user_id);

try {
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Erro ao obter dados do cliente.";
        exit();
    }
    $_SESSION['account_number'] = $user['account_number'];
} catch (PDOException $e) {
    die("Erro ao executar a query: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Banco UniassBank</title>
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

    <!-- Dashboard -->
    <div class="container mt-5">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <h2 class="text-center">Bem-vindo, <?php echo htmlspecialchars($user['name']); ?></h2>
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Detalhes da Conta</h5>
                <p class="card-text"><strong>Número da Conta:</strong> <?php echo htmlspecialchars($user['account_number']); ?></p>
                <p class="card-text"><strong>Número da Agência:</strong> <?php echo htmlspecialchars($user['branch_number']); ?></p>
                <p class="card-text"><strong>Saldo:</strong> R$ <?php echo number_format($user['balance'], 2, ',', '.'); ?></p>
                <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="card-text"><strong>Telefone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            </div>
        </div>

        <!-- Ações -->
        <div class="mt-4">
            <h5>Ações Disponíveis</h5>
            <div class="list-group">
                <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#transferModal">Transferência Bancária</a>
                <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#pixModal">Pix</a>
                <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#depositModal">Depósito</a>
                <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#updateModal">Alteração Cadastral</a>
                <a href="extrato.php" class="list-group-item list-group-item-action">Extrato</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2024 Banco UniassBank. Todos os direitos reservados.</p>
    </footer>

    <!-- Modals -->
    <!-- Transfer Modal -->
    <div class="modal fade" id="transferModal" tabindex="-1" aria-labelledby="transferModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transferModalLabel">Transferência Bancária</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="process_transaction.php" method="post">
                        <input type="hidden" name="type" value="transferencia">
                        <div class="form-group">
                            <label for="transferAccount">Número da Conta Destino</label>
                            <input type="text" class="form-control" id="transferAccount" name="account_to" required>
                        </div>
                        <div class="form-group">
                            <label for="transferAmount">Valor</label>
                            <input type="number" class="form-control" id="transferAmount" name="amount" required>
                        </div>
                        <button type="submit" class="btn btn-custom">Transferir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pix Modal -->
    <div class="modal fade" id="pixModal" tabindex="-1" aria-labelledby="pixModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pixModalLabel">Pix</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="process_transaction.php" method="post">
                        <input type="hidden" name="type" value="pix">
                        <div class="form-group">
                            <label for="pixKey">Chave Pix</label>
                            <input type="text" class="form-control" id="pixKey" name="account_to" required>
                        </div>
                        <div class="form-group">
                            <label for="pixAmount">Valor</label>
                            <input type="number" class="form-control" id="pixAmount" name="amount" required>
                        </div>
                        <button type="submit" class="btn btn-custom">Enviar Pix</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Deposit Modal -->
    <div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="depositModalLabel">Depósito</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="deposit.php" method="post">
                        <div class="form-group">
                            <label for="depositAmount">Valor</label>
                            <input type="number" class="form-control" id="depositAmount" name="amount" required>
                        </div>
                        <button type="submit" class="btn btn-custom">Depositar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Alteração Cadastral</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="update_profile.php" method="post">
                        <div class="form-group">
                            <label for="updateName">Nome Completo</label>
                            <input type="text" class="form-control" id="updateName" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="updateEmail">Email</label>
                            <input type="email" class="form-control" id="updateEmail" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="updatePhone">Telefone</label>
                            <input type="text" class="form-control" id="updatePhone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>
                        <button type="submit" class="btn btn-custom">Atualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
