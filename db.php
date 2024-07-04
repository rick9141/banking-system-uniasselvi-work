<?php
$dsn = "sqlsrv:Server=localhost,1433;Database=bancoUniassBank";
$username = "sa";
$password = "Rick123456*";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}
?>
