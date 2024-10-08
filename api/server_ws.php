<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/vendor/autoload.php';

class PokerServer implements MessageComponentInterface
{
    protected $clients;
    protected $votes;

    public function __construct()
    {
        // $this->clients = new \SplObjectStorage;
        $this->clients = [];
        $this->votes = []; 
    }

    // public function onOpen(ConnectionInterface $conn)
    // {
    //     $this->clients->attach($conn);
    //     echo "Nova conexão! ({$conn->resourceId})\n";
    // }

    public function onOpen(ConnectionInterface $conn)
    {
        // Gerar identificador único para o usuário
        $userId = uniqid('user_', true);

        // Armazenar a conexão e o identificador
        $this->clients[$conn->resourceId] = ['connection' => $conn, 'userId' => $userId];

        // Enviar a mensagem para todos os clientes conectados sobre o novo usuário
        // foreach ($this->clients as $client) {
        //     $client['connection']->send(json_encode([
        //         'type' => 'new_user',
        //         'userId' => $userId,
        //         'name' => '' // Inicialmente, sem nome
        //     ]));
        // } 
        foreach ($this->clients as $client) {
            if ($client['userId'] !== $this->clients[$conn->resourceId]['userId']) {
                $conn->send(json_encode([
                    'type' => 'new_user',
                    'userId' => $client['userId'],
                    'name' => $client['name']
                ]));
            }
        }
    }

    // public function onOpen(ConnectionInterface $conn)
    // {
    //     // Gerar identificador único para o usuário
    //     $userId = uniqid('user_', true);

    //     // Armazenar a conexão e o identificador
    //     $this->clients[$conn->resourceId] = ['connection' => $conn, 'userId' => $userId, 'name' => ''];

    //     // Enviar a mensagem para o novo cliente com os usuários existentes
    //     foreach ($this->clients as $client) {
    //         if ($client['userId'] !== $userId) {
    //             $conn->send(json_encode([
    //                 'type' => 'new_user',
    //                 'userId' => $client['userId'],
    //                 'name' => $client['name']
    //             ]));
    //         }
    //     }
    // }

    // public function onMessage(ConnectionInterface $from, $msg)
    // {
    //     foreach ($this->clients as $client) {
    //         if ($from !== $client) {
    //             $client->send($msg);
    //         }
    //     }
    // }

    // public function onMessage(ConnectionInterface $from, $msg)
    // {
    //     // Processamento de mensagens se necessário
    // }

    // public function onMessage(ConnectionInterface $from, $msg)
    // {
    //     $messageData = json_decode($msg, true);

    //     if ($messageData['type'] === 'set_name') {
    //         $this->clients[$from->resourceId]['name'] = $messageData['name'];

    //         // Notificar todos os clientes sobre o novo usuário com o nome
    //         foreach ($this->clients as $client) {
    //             $client['connection']->send(json_encode([
    //                 'type' => 'new_user',
    //                 'userId' => $this->clients[$from->resourceId]['userId'],
    //                 'name' => $messageData['name']
    //             ]));
    //         }
    //     }
    // }

    // public function onMessage(ConnectionInterface $from, $msg)
    // {
    //     $messageData = json_decode($msg, true);

    //     if ($messageData['type'] === 'set_name') {
    //         $this->clients[$from->resourceId]['name'] = $messageData['name'];

    //         foreach ($this->clients as $client) {
    //             $client['connection']->send(json_encode([
    //                 'type' => 'new_user',
    //                 'userId' => $this->clients[$from->resourceId]['userId'],
    //                 'name' => $messageData['name']
    //             ]));
    //         }
    //     } elseif ($messageData['type'] === 'vote') {
    //         $card = $messageData['card'];
    //         if (!isset($this->votes[$card])) {
    //             $this->votes[$card] = 0;
    //         }
    //         $this->votes[$card] += 1;
    //     } elseif ($messageData['type'] === 'reveal_votes') {
    //         $result = [];
    //         foreach ($this->votes as $card => $count) {
    //             $result[] = ['card' => $card, 'count' => $count];
    //         }

    //         foreach ($this->clients as $client) {
    //             $client['connection']->send(json_encode([
    //                 'type' => 'vote_result',
    //                 'votes' => $result
    //             ]));
    //         }
    //     }
    // }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $messageData = json_decode($msg, true);

        switch ($messageData['type']) {
            case 'set_name':
                $this->clients[$from->resourceId]['name'] = $messageData['name'];
                $userId = $this->clients[$from->resourceId]['userId'];

                // Notificar todos os clientes sobre o novo usuário com o nome
                foreach ($this->clients as $client) {
                    $client['connection']->send(json_encode([
                        'type' => 'new_user',
                        'userId' => $userId,
                        'name' => $messageData['name']
                    ]));
                }
                break;

            case 'vote':
                $card = $messageData['card'];
                if (!isset($this->votes[$card])) {
                    $this->votes[$card] = 0;
                }
                $this->votes[$card] += 1;
                break;

            case 'reveal_votes':
                $result = [];
                foreach ($this->votes as $card => $count) {
                    $result[] = ['card' => $card, 'count' => $count];
                }

                // Enviar os resultados para todos os clientes
                foreach ($this->clients as $client) {
                    $client['connection']->send(json_encode([
                        'type' => 'vote_result',
                        'votes' => $result
                    ]));
                }
                break;
        }
    }


    // public function onClose(ConnectionInterface $conn)
    // {
    //     $this->clients->detach($conn);
    //     echo "Conexão {$conn->resourceId} fechada\n";
    // }

    // public function onClose(ConnectionInterface $conn)
    // {
    //     // Remover a conexão quando o usuário desconectar
    //     unset($this->clients[$conn->resourceId]);

    //     // Notificar os outros clientes que o usuário saiu
    //     foreach ($this->clients as $client) {
    //         $client['connection']->send(json_encode([
    //             'type' => 'user_left',
    //             'userId' => $this->clients[$conn->resourceId]['userId'],
    //             'name' => $messageData['name']
    //         ]));
    //     }
    // }

    public function onClose(ConnectionInterface $conn)
    {
        $userId = $this->clients[$conn->resourceId]['userId'];
        unset($this->clients[$conn->resourceId]);

        foreach ($this->clients as $client) {
            $client['connection']->send(json_encode([
                'type' => 'user_left',
                'userId' => $userId
            ]));
        }
    }

    // public function onError(ConnectionInterface $conn, \Exception $e)
    // {
    //     echo "Erro: {$e->getMessage()}\n";
    //     $conn->close();
    // }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new PokerServer()
        )
    ),
    8080 // Porta onde o WebSocket estará escutando
);

$server->run();
