<?php
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Encripta a senha

    $query = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')";
    $resultado = mysqli_query($conn, $query);

    if ($resultado) {
        header("Location: ../views/login.php"); // Redireciona para a página de login
    } else {
        echo "Erro ao registrar usuário.";
    }
}
