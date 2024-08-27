<form method="POST" action="../includes/processa_login.php">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>

    <label for="senha">Senha:</label>
    <input type="password" id="senha" name="senha" required>

    <button type="submit">Login</button>
    <button onclick="window.location.href = 'registro.php'">Cadastrar</button>
</form>