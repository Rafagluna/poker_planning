<?php
$servername = "localhost:3306";
$username = "root"; // padrão do MySQL
$password = "202223rg"; // deixe vazio se estiver usando XAMPP
$dbname = "poker_planning";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
?>