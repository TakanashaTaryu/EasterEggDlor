<?php
header('Content-Type: application/json');

// Get the posted data
if (!isset($_POST['playerName']) || !isset($_POST['score'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$playerName = $_POST['playerName'];
$score = intval($_POST['score']);

// Basic validation
if (empty($playerName) || $score <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Connect to database
include 'db_connect.php';

// Check if this score is higher than player's previous scores
$stmt = $conn->prepare("SELECT MAX(score) as highest_score FROM highscores WHERE player_name = ?");
$stmt->bind_param("s", $playerName);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$highestScore = $row['highest_score'] ?? 0;

$newHighScore = false;

// If this score is higher, update it
if ($score > $highestScore) {
    // Delete previous scores for this player
    $stmt = $conn->prepare("DELETE FROM highscores WHERE player_name = ?");
    $stmt->bind_param("s", $playerName);
    $stmt->execute();
    
    // Insert new high score
    $stmt = $conn->prepare("INSERT INTO highscores (player_name, score) VALUES (?, ?)");
    $stmt->bind_param("si", $playerName, $score);
    
    if ($stmt->execute()) {
        $newHighScore = true;
    }
}

$conn->close();

// Return response
echo json_encode([
    'success' => true,
    'score' => $score,
    'playerName' => $playerName,
    'newHighScore' => $newHighScore
]);
?>