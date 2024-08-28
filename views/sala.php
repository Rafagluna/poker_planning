<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poker Planning</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        #users {
            display: flex;
            gap: 10px;
        }

        .user {
            width: 50px;
            height: 50px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: capitalize;
        }
    </style>
</head>

<body>
    <div id="users"></div>

    <script>
        let socket;
        let userName = '';

        // Função para iniciar a conexão WebSocket
        function initializeWebSocket() {
            socket = new WebSocket('ws://localhost:8080');

            socket.onmessage = function(event) {
                const message = JSON.parse(event.data);

                if (message.type === 'new_user') {
                    addUserToUI(message.userId, message.name);
                } else if (message.type === 'user_left') {
                    removeUserFromUI(message.userId);
                }
            };
        }
        // const socket = new WebSocket('ws://localhost:8080');
        // let userName = '';

        // Exibe o SweetAlert para pedir o nome do usuário
        Swal.fire({
            title: 'Bem-vindo!',
            text: 'Como você quer ser chamado?',
            input: 'text',
            inputPlaceholder: 'Digite seu nome',
            confirmButtonText: 'Entrar',
            allowOutsideClick: false,
            preConfirm: (name) => {
                if (!name) {
                    Swal.showValidationMessage('Por favor, insira um nome');
                }
                return name;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                userName = result.value;
                // Inicializa a conexão WebSocket após o nome ser definido
                initializeWebSocket();

                // Enviar o nome para o servidor após a conexão ser estabelecida
                socket.onopen = function() {
                    socket.send(JSON.stringify({
                        type: 'set_name',
                        name: userName
                    }));
                };
            }
        });

        socket.onmessage = function(event) {
            const message = JSON.parse(event.data);

            if (message.type === 'new_user') {
                addUserToUI(message.userId, message.name);
            } else if (message.type === 'user_left') {
                removeUserFromUI(message.userId);
            }
        };

        function addUserToUI(userId, name) {
            const existingUserDiv = document.getElementById(userId);
            if (existingUserDiv) {
                existingUserDiv.textContent = name;
                return;
            }
            const usersDiv = document.getElementById('users');
            const userDiv = document.createElement('div');
            userDiv.classList.add('user');
            userDiv.id = userId;
            userDiv.textContent = name;
            usersDiv.appendChild(userDiv);
        }

        function removeUserFromUI(userId) {
            const userDiv = document.getElementById(userId);
            if (userDiv) {
                userDiv.remove();
            }
        }
    </script>
</body>

</html>