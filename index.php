<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dino Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .leaderboard {
            margin-top: 20px;
            text-align: left;
        }
        .leaderboard h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dino Game</h1>
        <form id="playerForm" method="post" action="game.php">
            <input type="text" id="playerName" name="playerName" placeholder="Enter your name" required>
            <button type="submit">Start Game</button>
        </form>
        
        <div class="leaderboard">
            <h2>Top Players</h2>
            <table>
                <tr>
                    <th>Rank</th>
                    <th>Player</th>
                    <th>Score</th>
                </tr>
                <?php
                // Connect to database and get top scores
                include 'db_connect.php';
                
                $sql = "SELECT player_name, score FROM highscores ORDER BY score DESC LIMIT 5";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    $rank = 1;
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>$rank</td>
                                <td>" . htmlspecialchars($row["player_name"]) . "</td>
                                <td>" . htmlspecialchars($row["score"]) . "</td>
                              </tr>";
                        $rank++;
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center'>No scores yet</td></tr>";
                }
                $conn->close();
                ?>
            </table>
        </div>
    </div>
</body>
</html>