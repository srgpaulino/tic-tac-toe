<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class GameServer implements MessageComponentInterface
{
    private $clients;       // All connected clients
    private $games;         // Game state
    private $playerMap;     // Map connection resourceId to gameId

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->games = []; // Format: ['gameId' => GameHandler instance]
        $this->playerMap = []; // Format: ['resourceId' => 'gameId']
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection established ({$conn->resourceId}).\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        switch ($data['action']) {
            case 'create':
                //generate unique game id
                $gameId = uniqid();

                $this->games[$gameId] = new GameHandler();
                $this->playerMap[$from->resourceId] = $gameId;

                $serverHost = $this->getServerHost();
                //get a shareable link
                $shareableLink = "$serverHost/public/index.html?gameId=$gameId";

                $from->send(json_encode([
                    'action' => 'created',
                    'gameId' => $gameId,
                    'shareableLink' => $shareableLink
                ]));

                echo "Game created: $gameId | Shareable Link: $shareableLink\n";
                break;

            case 'join':
                $gameId = $data['gameId'] ?? null;
                if ($gameId && isset($this->games[$gameId])) {
                    $this->playerMap[$from->resourceId] = $gameId;
                    $from->send(json_encode(['action' => 'joined', 'gameId' => $gameId]));
                } else {
                    $from->send(json_encode(['action' => 'error', 'message' => 'Game not found']));
                }
                break;

            case 'reconnect':
                $gameId = $data['gameId'] ?? null;
                if ($gameId && isset($this->games[$gameId])) {
                    $this->playerMap[$from->resourceId] = $gameId;

                    $game = $this->games[$gameId];
                    $from->send(json_encode([
                        'action' => 'update',
                        'gameId' => $gameId,
                        'board' => $game->board,
                        'currentPlayer' => $game->currentPlayer
                    ]));
                } else {
                    $from->send(json_encode(['action' => 'error', 'message' => 'Game not found']));
                }
                break;

            case 'move':
                $gameId = $data['gameId'] ?? null;
                $index = $data['index'] ?? null;

                if ($gameId && isset($this->games[$gameId])) {
                    $game = $this->games[$gameId];

                    if ($game->makeMove($index)) {
                        $winner = $game->checkWinner();
                        $isDraw = $game->isDraw();

                        foreach ($this->clients as $client) {
                            $client->send(json_encode([
                                'action' => 'update',
                                'gameId' => $gameId,
                                'board' => $game->board,
                                'currentPlayer' => $game->currentPlayer,
                                'winner' => $winner,
                                'isDraw' => $isDraw
                            ]));
                        }
                    } else {
                        $from->send(json_encode(['action' => 'error', 'message' => 'Invalid move']));
                    }
                } else {
                    $from->send(json_encode(['action' => 'error', 'message' => 'Game not found']));
                }
                break;

            case 'restart':
                $gameId = $data['gameId'] ?? null;

                if ($gameId && isset($this->games[$gameId])) {
                    $this->games[$gameId]->resetGame();

                    foreach ($this->clients as $client) {
                        $client->send(json_encode([
                            'action' => 'update',
                            'gameId' => $gameId,
                            'board' => $this->games[$gameId]->board,
                            'currentPlayer' => $this->games[$gameId]->currentPlayer
                        ]));
                    }
                } else {
                    $from->send(json_encode(['action' => 'error', 'message' => 'Game not found']));
                }
                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->playerMap[$conn->resourceId]);
        echo "Connection {$conn->resourceId} has disconnected.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Retrieve the server host URL (adjust for production as needed).
     */
    private function getServerHost()
    {
        return "https://srgpaulino.github.io"; // Adjust as per your frontend server address
    }
}

// Start the WebSocket server
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$port = getenv('PORT') ?: 8080; // Use Heroku's assigned port or default to 8080

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new GameServer()
        )
    ),
    $port
);

echo "WebSocket server started on wss://spaulino-tictactoe-30674857a67d.herokuapp.com\n";
$server->run();
