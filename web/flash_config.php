<?php

function fetchData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = 'Curl error: ' . curl_error($ch);
        file_put_contents('log.txt', date('[Y-m-d H:i:s] ') . $error_msg . "\n", FILE_APPEND);
        echo $error_msg;
        return null;
    }

    curl_close($ch);
    return json_decode($response, true);
}

$newsUrl = 'https://api.cyklistickey.cz/api/news';
$techUrl = 'https://api.cyklistickey.cz/api/tech';

// Fetching the latest news and technology data
$newsData = fetchData($newsUrl);
$techData = fetchData($techUrl);

// Combining fetched data into a single array
$combinedData = [
    'news' => $newsData,
    'tech' => $techData
];

// Encoding the combined data into JSON with pretty print and preserving Unicode characters
$finalJson = json_encode($combinedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// File where the JSON data will be saved
$jsonFile = 'flash.json';

// Attempt to save the JSON data to file and log the outcome
if (file_put_contents($jsonFile, $finalJson)) {
    echo "Combined JSON data saved to file successfully.";
    file_put_contents('log.txt', date('[Y-m-d H:i:s] ') . "Combined JSON data saved to file successfully.\n", FILE_APPEND);
} else {
    $error_msg = "Failed to save combined JSON data to file.";
    echo $error_msg;
    file_put_contents('log.txt', date('[Y-m-d H:i:s] ') . $error_msg . "\n", FILE_APPEND);
}

?>