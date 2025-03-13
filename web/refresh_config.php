<?php

include('flash_config.php'); // Ensure this file contains the fetchData function and other necessary utilities

// Define the URL for the configuration source
$configUrl = 'https://www.cyklistickey.cz/flash_config.php';

// Fetching the latest configuration data
$configData = fetchData($configUrl);

// Path to the configuration JSON file
$configJsonFile = 'flash_config.json';

// Encode the fetched configuration into JSON with pretty print and preserve Unicode characters
$finalConfigJson = json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// Attempt to save the JSON data to the file and log the outcome
if (file_put_contents($configJsonFile, $finalConfigJson)) {
    echo "Configuration data refreshed and saved successfully.";
    file_put_contents('log.txt', date('[Y-m-d H:i:s] ') . "Configuration data refreshed and saved successfully.\n", FILE_APPEND);
} else {
    $error_msg = "Failed to refresh and save configuration data.";
    echo $error_msg;
    file_put_contents('log.txt', date('[Y-m-d H:i:s] ') . $error_msg . "\n", FILE_APPEND);
}

?>