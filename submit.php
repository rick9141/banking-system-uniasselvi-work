<?php
include 'db.php';

function generateAccountNumber() {
    return rand(10000000, 99999999); // Gera um número de conta aleatório
}

function generateBranchNumber() {
    return rand(1000, 9999); // Gera um número de agência aleatório
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $cpf = $_POST["cpf"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash da senha
    $accountNumber = generateAccountNumber();
    $branchNumber = generateBranchNumber();
    $balance = 0.0; // Saldo inicial

    $sql = "INSERT INTO clientes (name, cpf, email, phone, account_number, branch_number, balance, password) VALUES (:name, :cpf, :email, :phone, :account_number, :branch_number, :balance, :password)";
    $stmt = $conn->prepare($sql);

    $params = array(
        ':name' => $name,
        ':cpf' => $cpf,
        ':email' => $email,
        ':phone' => $phone,
        ':account_number' => $accountNumber,
        ':branch_number' => $branchNumber,
        ':balance' => $balance,
        ':password' => $password,
    );

    try {
        $stmt->execute($params);
        echo "Cliente cadastrado com sucesso!<br>";
        echo "Número da Conta: " . $accountNumber . "<br>";
        echo "Número da Agência: " . $branchNumber . "<br>";
        // Redirecionamento automático após 5 segundos
        header("refresh:5;url=login.php");
        echo "Você será redirecionado para a tela de login em 5 segundos.";
    } catch (PDOException $e) {
        die("Erro ao executar a query: " . $e->getMessage());
    }
}
?>
