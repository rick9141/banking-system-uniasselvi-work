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
    $account_number = $_SESSION['account_number'];
    $amount = $_POST["amount"];

    try {
        $conn->beginTransaction();

        // Atualizar saldo da conta
        $sql = "UPDATE clientes SET balance = balance + :amount WHERE account_number = :account_number";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':account_number', $account_number, PDO::PARAM_STR);
        $stmt->execute();

        // Inserir transação na tabela de transações
        $sql = "INSERT INTO transacoes (account_number_from, account_number_to, amount, type) VALUES (:account_number_from, :account_number_to, :amount, :type)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':account_number_from', $account_number, PDO::PARAM_STR);
        $stmt->bindParam(':account_number_to', $account_number, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $type = 'deposito';
        $stmt->execute();

        $conn->commit();

        header('Location: dashboard.php');
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        die("Erro ao processar o depósito: " . $e->getMessage());
    }
}
?>
