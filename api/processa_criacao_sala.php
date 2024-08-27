<?php
include('db_connection.php'); // Arquivo para conectar ao banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_sala = $_POST['nome_sala'];

    $query = "INSERT INTO salas (nome) VALUES ('$nome_sala')";
    $resultado = mysqli_query($conn, $query);

    if ($resultado) {
        header("Location: ../views/sala.php?id=" . mysqli_insert_id($conn)); // Redireciona para a sala criada
    } else {
        echo "Erro ao criar sala";
    }
}
