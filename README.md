# Tic-Tac-Toe

A real-time, web-based Tic-Tac-Toe game for two players, built with **PHP**, **WebSockets** (Ratchet), and **jQuery**. The game allows players to create and join games remotely, play with real-time updates, and restart the game as needed. It supports reconnection handling for dropped players.

Access online [here](http://16.16.207.100:8081)

This exercise was done as a test for **The Skills Network**, while applying for the role as **Software Engineer**.

---

## Features

- Real-time multiplayer Tic-Tac-Toe using WebSockets.
- Shareable game link.
- Automatic reconnection for dropped players.
- Fully responsive UI built with Material Design.
- Easy deployment with Docker and Docker Compose.
- Unit tests for core game logic using PHPUnit.

---

## To Include

- Persistent game state with SQL database (MySql, MariaDB or PostgreSQL).
- Animated UI/UX.
- Gameplay sounds.
- QR code share link.

## Requirements

- **PHP 8.2 or later**
- **Composer** for dependency management
- **MySQL** or another supported database
- **Node.js** (optional, for additional frontend tooling)
- **Docker & Docker Compose** (optional, for containerized deployment)

---

## Installation

### Clone the Repository
```bash
git clone https://github.com/srgpaulino/tic-tac-toe.git
cd tic-tac-toe
```

### Install dependencies
```bash
composer install
```

## Usage

### Run the WebSocket Server
Start the WebSocket server to handle real-time updates:

```bash
php app/GameServer.php
```

### Serve the Frontend
Use PHP's built-in web server or any HTTP server to serve the frontend:

```bash
php -S localhost:8081 -t public
```

### Serve with Docker
Using Docker, run docker compose
```bash
docker-compose up -d
```
Your project should now be running and accessible on http://localhost:8081

### Access the Game
Open your browser and navigate to Frontend:

Frontend: http://localhost:8081
WebSocket Server: ws://localhost:8080


## Testing

### Run Unit Tests
Execute the PHPUnit tests:

```bash
vendor/bin/phpunit --configuration phpunit.xml
```

### Coverage Report
After running the tests, view the coverage report in build/coverage/index.html.


# License
This project is licensed under the MIT License. See the LICENSE file for details.