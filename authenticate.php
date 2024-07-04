<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = $_POST["cpf"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM clientes WHERE cpf = :cpf";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cpf', $cpf);

    try {
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            header('Location: dashboard.php');
            exit();
        } else {
            echo "CPF ou senha incorretos.";
        }
    } catch (PDOException $e) {
        die("Erro ao executar a query: " . $e->getMessage());
    }
}
?>
