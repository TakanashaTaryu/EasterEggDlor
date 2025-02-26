<?php
session_start();

// Check if the player name is set
if (isset($_POST['playerName'])) {
    $_SESSION['playerName'] = htmlspecialchars($_POST['playerName']);
} else if (!isset($_SESSION['playerName'])) {
    // Redirect to index if no player name
    header("Location: index.php");
    exit();
}

$playerName = $_SESSION['playerName'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dino Game</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }
        
        .player-info {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }
        
        canvas {
            border: 1px solid grey;
            background-color: white;
            max-width: 100%;
        }
        
        .back-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }
        
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="player-info">
        Player: <span id="playerNameDisplay"><?php echo $playerName; ?></span>
    </div>
    
    <canvas id="game"></canvas>
    
    <a href="index.php" class="back-button">Back to Menu</a>
    
    <script>
        // Pass player name to JavaScript
        const playerName = "<?php echo $playerName; ?>";
    </script>
    
    <!-- Import your game scripts -->
    <script type="module" src="index.js"></script>
</body>
</html>