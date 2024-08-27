<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $resultado = mysqli_query($conn, $query);
    $usuario = mysqli_fetch_assoc($resultado);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        header("Location: ../views/dashboard.php"); // Redireciona para o painel principal
    } else {
        echo "Email ou senha incorretos.";
    }
}
