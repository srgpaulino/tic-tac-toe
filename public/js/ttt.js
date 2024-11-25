$(document).ready(function () {
    const socket = new WebSocket('ws://localhost:8080');
    let gameId = null;
    let currentPlayer = 'X';
    let board = ['', '', '', '', '', '', '', '', ''];

    // Function to update the Game ID title
    function updateGameIdTitle(id) {
        $('#game-id-title').text(`Game ID: ${id}`);
    }

    // Render the game board dynamically
    function renderBoard() {
        $('#game-board').empty();
        board.forEach((cell, index) => {
            const cellDiv = $('<div>')
                .addClass('cell')
                .attr('data-index', index)
                .text(cell)
                .click(function () {
                    if (cell === '' && currentPlayer === 'X') {
                        socket.send(JSON.stringify({
                            action: 'move',
                            gameId: gameId,
                            index: index
                        }));
                    }
                });
            $('#game-board').append(cellDiv);
        });
    }

    // Highlight winning cells
    function highlightWinner(combo) {
        combo.forEach(index => {
            $(`.cell[data-index="${index}"]`).addClass('winner');
        });
    }

    // Update game status text
    function updateStatus(message) {
        $('#game-status').html(message);
    }

    // WebSocket Event Handlers
    socket.onopen = function () {
        if (gameId) {
            // Attempt to reconnect to the existing game
            socket.send(JSON.stringify({ action: 'reconnect', gameId: gameId }));
        } else {
            console.log('WebSocket connection established.');
        }
    };
    

    socket.onmessage = function (event) {
        const data = JSON.parse(event.data);

        console.log(data);

        switch (data.action) {
            case 'created':
                gameId = data.gameId;
                const shareableLink = data.shareableLink;
                updateGameIdTitle(gameId);
                updateStatus(`Game created! Share this <a href="${shareableLink}" target="_blank">link</a>`);
                break;

            case 'joined':
                gameId = data.gameId;
                updateGameIdTitle(gameId);
                updateStatus(`Joined game: ${gameId}`);
                break;

            case 'update':
                board = data.board;
                currentPlayer = data.currentPlayer;
                renderBoard();
                if (data.winner) {
                    highlightWinner(data.winnerCombo);
                    updateStatus(`${data.winner} wins!`);
                    $('#restart-game').removeClass('hidden');
                } else if (data.isDraw) {
                    updateStatus("It's a draw!");
                    $('#restart-game').removeClass('hidden');
                } else {
                    updateStatus(`Current Player: ${currentPlayer}`);
                }
                break;

            case 'error':
                updateStatus(data.message);
                break;
        }
    };

    socket.onerror = function (error) {
        console.error('WebSocket error:', error);
    };

    // Create a new game
    $('#create-game').click(function () {
        socket.send(JSON.stringify({ action: 'create' }));
        updateStatus('Creating game...');
    });

    // Join an existing game
    $('#join-game').click(function () {
        const id = $('#game-id-input').val().trim();
        if (id) {
            socket.send(JSON.stringify({ action: 'join', gameId: id }));
            updateStatus(`Joining game ${id}...`);
        } else {
            updateStatus('Please enter a valid Game ID.');
        }
    });

    // Restart the game
    $('#restart-game').click(function () {
        board.fill('');
        currentPlayer = 'X';
        renderBoard();
        updateStatus('Game restarted. Waiting for Player X.');
        $(this).addClass('hidden');
        socket.send(JSON.stringify({ action: 'restart', gameId: gameId }));
    });

    // Initial render of the game board
    renderBoard();
});