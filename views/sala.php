<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poker Planning</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        } */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #eafde6;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        header {
            flex-shrink: 0;
        }

        /* #mesa {
            width: 50%;
            height: 50%;
            border-radius: 5px;
            background-color: #0a8967;
            display: flex;
            justify-content: center;
            align-items: center;
        } */

        #mesa {
            width: 400px;
            /* Defina o tamanho desejado */
            height: 200px;
            /* Mesma altura e largura para formar um quadrado */
            display: flex;
            justify-content: center;
            align-items: center;
            margin: auto;
            /* Centraliza a mesa horizontal e verticalmente */
            background-color: #0a8967;
            /* Cor de fundo opcional */
            border: 2px solid #519548;
            /* Borda opcional */
            border-radius: 5px;
            position: relative;
        }

        .titulo-mesa {
            width: 150px;
            height: 80px;
            border-radius: 5px;
            color: #fff;
            font-size: 14px;
            background-color: #09c184;
            display: flex;
            justify-content: center;
            align-items: center;
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

        .integrante {
            position: absolute;
            width: 60px;
            /* Tamanho do "avatar" do integrante */
            height: 60px;
            background-color: #519548;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            text-align: center;
        }

        #cartas {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .cartas {
            width: 60px;
            height: 90px;
            background-color: #f0f0f0;
            /* Cor neutra */
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            color: black;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cartas.selecionada {
            background-color: #519548;
            /* Cor quando a carta é selecionada */
            color: white;
        }
    </style>
</head>

<body>
    <header style="background-color: #519548; height: 60px;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center">
            <h3>Poker Planning</h3>
            <div>
                <div id="user_conn" style="color: black; font-size: 18px"></div>
                <div style="color: black; font-size: 18px; background-color: yellow">Compartilhar</div>
            </div>
        </div>
    </header>
    <div id="mesa">
        <span class="titulo-mesa">Revelar Cards Votadas</span>
    </div>
    <div id="users"></div>
    <div id="results" style="display: none; margin-top: 20px;"></div>
    <div id="cartas"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        criarCartas();
        adicionarBotaoRevelar();
        let socket;
        let userName = '';
        let selectedCard = null;

        // Função para iniciar a conexão WebSocket
        function initializeWebSocket() {
            socket = new WebSocket('ws://localhost:8080');

            socket.onmessage = function(event) {
                const message = JSON.parse(event.data);

                if (message.type === 'new_user') {
                    addUserToUI(message.userId, message.name);
                } else if (message.type === 'user_left') {
                    removeUserFromUI(message.userId);
                } else if (message.type === 'vote_result') {
                    displayVoteResults(message.votes);
                }
            };
        }

        function handleCardSelection(event) {
            const carta = event.target;
            selectedCard = carta.textContent;

            console.log('selectedCard', selectedCard);
            return;

            // Enviar a carta selecionada para o servidor
            if (selectedCard) {
                socket.send(JSON.stringify({
                    type: 'vote',
                    card: selectedCard
                }));
            }
        }
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', handleCardSelection);
        });

        // Função para exibir os resultados das cartas votadas
        // function displayVoteResults(votes) {
        //     const resultsContainer = document.getElementById('results');
        //     resultsContainer.innerHTML = '';

        //     votes.forEach((vote) => {
        //         const resultDiv = document.createElement('div');
        //         resultDiv.textContent = `Carta ${vote.card} - ${vote.count} votos`;
        //         resultsContainer.appendChild(resultDiv);
        //     });
        // }

        function displayVoteResults(votes) {
            const resultsContainer = document.getElementById('results');
            resultsContainer.innerHTML = '';

            if (votes.length === 0) {
                resultsContainer.innerHTML = 'Nenhum voto registrado ainda.';
                resultsContainer.style.display = 'block';
                return;
            }

            votes.forEach((vote) => {
                const resultDiv = document.createElement('div');
                resultDiv.textContent = `Carta ${vote.card} - ${vote.count} votos`;
                resultsContainer.appendChild(resultDiv);
            });

            resultsContainer.style.display = 'block'; // Mostrar o contêiner de resultados
        }


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
            } else if (message.type === 'vote_result') {
                displayVoteResults(message.votes);
            }
        };

        // function addUserToUI(userId, name) {
        //     const existingUserDiv = document.getElementById(userId);
        //     if (existingUserDiv) {
        //         existingUserDiv.textContent = name;
        //         return;
        //     }
        //     const usersDiv = document.getElementById('users');
        //     const userDiv = document.createElement('div');
        //     userDiv.classList.add('user');
        //     userDiv.id = userId;
        //     userDiv.textContent = name;
        //     usersDiv.appendChild(userDiv);
        // }

        // function addUserToUI(userId, name) {
        //     const existingUserDiv = document.getElementById(userId);
        //     if (existingUserDiv) {
        //         existingUserDiv.textContent = name;
        //         return;
        //     }

        //     const mesa = document.getElementById('mesa');
        //     const usersCount = document.querySelectorAll('.user').length;
        //     const maxUsers = 6; // Defina o número máximo de usuários ao redor da mesa

        //     if (usersCount >= maxUsers) {
        //         console.log("Máximo de usuários atingido.");
        //         return;
        //     }

        //     const angle = (360 / maxUsers) * usersCount; // Calcula o ângulo para posicionar o usuário
        //     const radius = 150; // Raio da mesa em pixels

        //     const x = radius * Math.cos(angle * (Math.PI / 180)) + mesa.offsetWidth / 2 - 30;
        //     const y = radius * Math.sin(angle * (Math.PI / 180)) + mesa.offsetHeight / 2 - 30;

        //     const userDiv = document.createElement('div');
        //     userDiv.classList.add('user');
        //     userDiv.id = userId;
        //     userDiv.textContent = name;
        //     userDiv.style.position = 'absolute';
        //     userDiv.style.left = `${x}px`;
        //     userDiv.style.top = `${y}px`;
        //     userDiv.style.width = '60px';
        //     userDiv.style.height = '60px';
        //     userDiv.style.backgroundColor = '#519548';
        //     userDiv.style.color = 'white';
        //     userDiv.style.display = 'flex';
        //     userDiv.style.justifyContent = 'center';
        //     userDiv.style.alignItems = 'center';
        //     userDiv.style.borderRadius = '50%';

        //     mesa.appendChild(userDiv);
        // }

        // function adicionarBotaoRevelar() {
        //     const mesa = document.getElementById('mesa');
        //     const botaoRevelar = document.createElement('button');
        //     botaoRevelar.textContent = 'Revelar Cards Mais Votados';
        //     botaoRevelar.addEventListener('click', () => {
        //         socket.send(JSON.stringify({
        //             type: 'reveal_votes'
        //         }));
        //     });
        //     mesa.appendChild(botaoRevelar);

        //     const resultsContainer = document.createElement('div');
        //     resultsContainer.id = 'results';
        //     mesa.appendChild(resultsContainer);
        // }

        function adicionarBotaoRevelar() {
            const mesa = document.getElementById('mesa');
            const botaoRevelar = document.createElement('button');
            botaoRevelar.textContent = 'Revelar Cards Mais Votados';
            botaoRevelar.classList.add('btn', 'btn-primary'); // Adiciona estilo do Bootstrap
            botaoRevelar.addEventListener('click', () => {
                socket.send(JSON.stringify({
                    type: 'reveal_votes'
                }));
            });
            mesa.appendChild(botaoRevelar);
        }

        function addUserToUI(userId, name) {
            const existingUserDiv = document.getElementById(userId);
            if (existingUserDiv) {
                existingUserDiv.textContent = name;
                return;
            }

            const usersCount = document.querySelectorAll('.user').length;
            const maxUsers = 6; // Defina o número máximo de usuários ao redor da mesa

            if (usersCount >= maxUsers) {
                console.log("Máximo de usuários atingido.");
                return;
            }

            const userDiv = document.createElement('div');
            userDiv.classList.add('user');
            userDiv.id = userId;
            userDiv.textContent = name;
            userDiv.style.position = 'absolute';
            userDiv.style.width = '60px';
            userDiv.style.height = '60px';
            userDiv.style.backgroundColor = '#519548';
            userDiv.style.color = 'white';
            userDiv.style.display = 'flex';
            userDiv.style.justifyContent = 'center';
            userDiv.style.alignItems = 'center';
            userDiv.style.borderRadius = '50%';

            // Calcula a posição com base no número de usuários conectados
            switch (usersCount) {
                case 0: // Topo à esquerda
                    userDiv.style.top = '-30px';
                    userDiv.style.left = 'calc(25% - 30px)';
                    break;
                case 1: // Topo à direita
                    userDiv.style.top = '-30px';
                    userDiv.style.left = 'calc(75% - 30px)';
                    break;
                case 2: // Base à esquerda
                    userDiv.style.bottom = '-30px';
                    userDiv.style.left = 'calc(25% - 30px)';
                    break;
                case 3: // Base à direita
                    userDiv.style.bottom = '-30px';
                    userDiv.style.left = 'calc(75% - 30px)';
                    break;
                case 4: // Esquerda (Meio)
                    userDiv.style.top = 'calc(50% - 30px)';
                    userDiv.style.left = '-30px';
                    break;
                case 5: // Direita (Meio)
                    userDiv.style.top = 'calc(50% - 30px)';
                    userDiv.style.right = '-30px';
                    break;
            }

            document.getElementById('mesa').appendChild(userDiv);
        }

        // Função para gerar a sequência de Fibonacci até 55
        function gerarFibonacci() {
            const fibonacci = [1, 2];
            while (true) {
                const nextValue = fibonacci[fibonacci.length - 1] + fibonacci[fibonacci.length - 2];
                if (nextValue > 55) break;
                fibonacci.push(nextValue);
            }
            return fibonacci;
        }

        // Função para criar as cartas
        function criarCartas() {
            const fibonacci = gerarFibonacci();
            const cartasContainer = document.getElementById('cartas');

            fibonacci.forEach((numero) => {
                const carta = document.createElement('div');
                carta.classList.add('cartas');
                carta.textContent = numero;

                // Adiciona evento de clique para seleção
                carta.addEventListener('click', function(event) {
                    // Desmarcar todas as cartas
                    const todasCartas = document.querySelectorAll('.cartas');
                    todasCartas.forEach(carta => {
                        carta.classList.remove('selecionada');
                    });

                    let cartaCampo = event.target;
                    selectedCard = cartaCampo.textContent;

                    // Enviar a carta selecionada para o servidor
                    if (selectedCard) {
                        socket.send(JSON.stringify({
                            type: 'vote',
                            card: selectedCard
                        }));
                    }
                    // Marcar a carta clicada como selecionada
                    carta.classList.add('selecionada');
                });

                cartasContainer.appendChild(carta);
            });
        }

        // Chama a função para criar as cartas quando a página for carregada
        window.onload = function() {
            criarCartas();
        };

        function removeUserFromUI(userId) {
            const userDiv = document.getElementById(userId);
            if (userDiv) {
                userDiv.remove();
            }
        }
    </script>
</body>

</html>