<?php

use PHPUnit\Framework\TestCase;
use App\GameHandler;

class GameHandlerTest extends TestCase
{
    public function testAlternatingTurns()
    {
        $game = new GameHandler();

        $game->makeMove(0); // X
        $this->assertEquals('O', $game->currentPlayer);

        $game->makeMove(1); // O
        $this->assertEquals('X', $game->currentPlayer);
    }

    public function testInvalidMove()
    {
        $game = new GameHandler();

        $game->makeMove(0); // X plays
        $this->assertFalse($game->makeMove(0)); // Invalid move (cell already occupied)
        $this->assertEquals('O', $game->currentPlayer); // Turn does not change
    }

    public function testWinningConditionForRows()
    {
        $game = new GameHandler();

        $game->makeMove(0); // X
        $game->makeMove(3); // O
        $game->makeMove(1); // X
        $game->makeMove(4); // O
        $game->makeMove(2); // X wins
        $this->assertEquals('X', $game->checkWinner());
    }

    public function testWinningConditionForColumns()
    {
        $game = new GameHandler();

        $game->makeMove(0); // X
        $game->makeMove(1); // O
        $game->makeMove(3); // X
        $game->makeMove(2); // O
        $game->makeMove(6); // X wins
        $this->assertEquals('X', $game->checkWinner());
    }

    public function testWinningConditionForDiagonals()
    {
        $game = new GameHandler();

        $game->makeMove(0); // X
        $game->makeMove(1); // O
        $game->makeMove(4); // X
        $game->makeMove(2); // O
        $game->makeMove(8); // X wins
        $this->assertEquals('X', $game->checkWinner());
    }

    public function testDrawCondition()
    {
        $game = new GameHandler();

        // Fill the board with no winner
        $game->board = [
            'X', 'O', 'X',
            'X', 'X', 'O',
            'O', 'X', 'O'
        ];

        $this->assertTrue($game->isDraw());
        $this->assertNull($game->checkWinner());
    }
    
}
