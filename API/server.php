<?php
header("Content-Type: application/json");

// Load question data from CSV
function loadQuestions() {
    $questions = [];
    
    if (($handle = fopen("question.csv", "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) >= 3) {
                // Add each question as a new array element
                // This preserves all questions, even with duplicate codes
                $questions[] = [
                    'code' => trim($data[0]),
                    'question' => trim($data[1]),
                    'position' => trim($data[2])
                ];
            }
        }
        fclose($handle);
    }
    return $questions;
}

// Load coordinates from JSON
function loadCoordinates() {
    $json = file_get_contents("FullKey.json");
    $data = json_decode($json, true);
    
    // Fix the JSON structure if needed
    $coordinates = [];
    
    // Check if data is valid
    if (!is_array($data)) {
        // Try to fix malformed JSON
        $json = preg_replace('/,\s*}/', '}', $json); // Remove trailing commas
        $json = preg_replace('/,\s*]/', ']', $json);
        $json = preg_replace('/name:/', '"name":', $json); // Fix unquoted keys
        $data = json_decode($json, true);
        
        if (!is_array($data)) {
            // If still not valid, return empty array
            return $coordinates;
        }
    }
    
    foreach ($data as $item) {
        if (isset($item['name'])) {
            $code = $item['name'];
            $coords = [];
            
            foreach ($item as $key => $value) {
                if (is_array($value) && isset($value['x']) && isset($value['y']) && isset($value['letter'])) {
                    $coords[] = [
                        'x' => $value['x'],
                        'y' => $value['y'],
                        'letter' => $value['letter']
                    ];
                }
            }
            
            if (!empty($coords)) {
                $coordinates[$code] = $coords;
            }
        }
    }
    
    return $coordinates;
}

// Handle GET request - return coordinates and questions
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $questions = loadQuestions();
    $coordinates = loadCoordinates();
    
    // Prepare response with coordinates (without revealing letters)
    $response = [
        'coordinates' => [],
        'questions' => []
    ];
    
    $index = 1;
    foreach ($coordinates as $code => $coords) {
        foreach ($coords as $coord) {
            $response['coordinates'][] = [
                'id' => $index++,
                'x' => $coord['x'],
                'y' => $coord['y']
            ];
        }
    }
    
    // Add questions - no need to change this part as we're now iterating through an array
    foreach ($questions as $question) {
        $response['questions'][] = [
            'code' => $question['code'],
            'question' => $question['question'],
            'position' => $question['position']
        ];
    }
    
    echo json_encode($response);
}

// Handle POST request - validate answers
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['code']) || !isset($data['position']) || !isset($data['coordinates'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request format']);
        exit;
    }
    
    $code = $data['code'];
    $position = $data['position'];
    $userCoordinates = $data['coordinates'];
    
    $questions = loadQuestions();
    $coordinates = loadCoordinates();
    
    // Check if the code exists
    if (!isset($coordinates[$code])) {
        echo json_encode([
            'result' => "$code:False:$position",
            'message' => 'Code not found'
        ]);
        exit;
    }
    
    // Check if the position matches
    $correctPosition = false;
    $matchingQuestion = null;
    foreach ($questions as $q) {
        if ($q['code'] === $code && $q['position'] === $position) {
            $correctPosition = true;
            $matchingQuestion = $q;
            break;
        }
    }
    
    if (!$correctPosition) {
        echo json_encode([
            'result' => "$code:False:$position",
            'message' => 'Position mismatch'
        ]);
        exit;
    }
    
    // Check if coordinates match
    $correctCoords = $coordinates[$code];
    $isCorrect = true;
    
    // Check if the number of coordinates matches
    if (count($userCoordinates) !== count($correctCoords)) {
        $isCorrect = false;
    } else {
        // Check each coordinate
        foreach ($userCoordinates as $index => $userCoord) {
            $found = false;
            foreach ($correctCoords as $correctCoord) {
                if ($userCoord['x'] == $correctCoord['x'] && 
                    $userCoord['y'] == $correctCoord['y'] && 
                    strtoupper($userCoord['letter']) == strtoupper($correctCoord['letter'])) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $isCorrect = false;
                break;
            }
        }
    }
    
    $result = $isCorrect ? "True" : "False";
    echo json_encode([
        'result' => "$code:$result:$position",
        'debug' => [
            'userCoordinates' => $userCoordinates,
            'correctCoords' => $correctCoords,
            'matchingQuestion' => $matchingQuestion
        ]
    ]);
}
else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>