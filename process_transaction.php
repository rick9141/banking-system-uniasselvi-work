<?php
session_start();
include 'db.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $account_from = $_SESSION['account_number'];
    $account_to = $_POST["account_to"];
    $amount = $_POST["amount"];
    $type = $_POST["type"];

    try {
        $conn->beginTransaction();

        // Verificar saldo suficiente
        $sql = "SELECT balance FROM clientes WHERE account_number = :account_number";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':account_number' => $account_from]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user['balance'] < $amount) {
            throw new Exception("Saldo insuficiente.");
        }

        // Atualizar saldo da conta de origem
        $sql = "UPDATE clientes SET balance = balance - :amount WHERE account_number = :account_number";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':amount' => $amount, ':account_number' => $account_from]);

        // Atualizar saldo da conta de destino
        $sql = "UPDATE clientes SET balance = balance + :amount WHERE account_number = :account_number";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':amount' => $amount, ':account_number' => $account_to]);

        // Inserir transação na tabela de transações
        $sql = "INSERT INTO transacoes (account_number_from, account_number_to, amount, type) VALUES (:account_from, :account_to, :amount, :type)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':account_from' => $account_from, ':account_to' => $account_to, ':amount' => $amount, ':type' => $type]);

        $conn->commit();

        $_SESSION['success_message'] = "Transação de $type concluída com sucesso!";
        header('Location: dashboard.php');
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        die("Erro ao processar a transação: " . $e->getMessage());
    }
}
?>
