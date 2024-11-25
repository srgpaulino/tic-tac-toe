<?php

namespace App;

class GameHandler
{
    public $board;
    public $currentPlayer;

    public function __construct()
    {
        $this->resetGame();
    }

    /**
     * Resets the game board and sets the first player.
     */
    public function resetGame()
    {
        $this->board = array_fill(0, 9, '');
        $this->currentPlayer = 'X';
    }

    /**
     * Makes a move if it's valid.
     *
     * @param int $index The index of the board (0-8).
     * @return bool True if the move was valid, false otherwise.
     */
    public function makeMove(int $index): bool
    {
        if ($this->board[$index] === '') {
            $this->board[$index] = $this->currentPlayer;
            $this->currentPlayer = $this->currentPlayer === 'X' ? 'O' : 'X';
            return true;
        }
        return false; // Invalid move
    }

    /**
     * Checks if there is a winner.
     *
     * @return string|null The winning player ('X' or 'O') or null if no winner.
     */
    public function checkWinner(): ?string
    {
        $winningCombinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // Rows
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // Columns
            [0, 4, 8], [2, 4, 6]             // Diagonals
        ];

        foreach ($winningCombinations as $combo) {
            [$a, $b, $c] = $combo;
            if (
                $this->board[$a] !== '' &&
                $this->board[$a] === $this->board[$b] &&
                $this->board[$a] === $this->board[$c]
            ) {
                return $this->board[$a]; // Return the winner ('X' or 'O')
            }
        }

        return null; // No winner yet
    }

    /**
     * Checks if the game is a draw.
     *
     * @return bool True if the game is a draw, false otherwise.
     */
    public function isDraw(): bool
    {
        return !in_array('', $this->board, true) && $this->checkWinner() === null;
    }
}
