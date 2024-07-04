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
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];

    $sql = "UPDATE clientes SET name = :name, email = :email, phone = :phone WHERE id = :id";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute(array(
            ':name' => $name,
            ':email' => $email,
            ':phone' => $phone,
            ':id' => $user_id
        ));
        $_SESSION['name'] = $name;
        header('Location: dashboard.php');
        exit();
    } catch (PDOException $e) {
        die("Erro ao atualizar os dados: " . $e->getMessage());
    }
}
?>
