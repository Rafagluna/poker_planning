document.querySelectorAll('.carta').forEach(function (button) {
    button.addEventListener('click', function () {
        const valorVoto = this.textContent;
        // Aqui você pode fazer uma requisição AJAX para salvar o voto no backend
        alert("Você votou: " + valorVoto);
    });
});

const socket = new WebSocket('ws://localhost:8080');

socket.onopen = function() {
    console.log("Conectado ao servidor WebSocket!");
};

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    console.log("Nova mensagem recebida:", data);
    // Exibe a carta selecionada por outro jogador no console
    console.log(`Usuário escolheu a carta: ${data.carta}`);
};

// Enviando uma mensagem (voto) ao servidor WebSocket
document.querySelectorAll('.carta').forEach(function(button) {
    button.addEventListener('click', function() {
        const valorVoto = this.textContent;
        // Enviando o voto como um objeto JSON
        if (socket.readyState === WebSocket.OPEN) {
            socket.send(JSON.stringify({ carta: valorVoto }));
        } else {
            console.log("WebSocket não está aberto. Estado atual: " + socket.readyState);
        }
        console.log("Você votou: " + valorVoto);
    });
    
});