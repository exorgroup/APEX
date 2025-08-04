<?php
// test-cm-direct.php
require __DIR__ . '/vendor/autoload.php';

use CMText\TextClient;

$apiKey = '9b23b0af-20d2-42e7-8438-e83b3576e63e'; // Put your actual CM API key in UUID format

try {
    $client = new TextClient($apiKey);
    $result = $client->SendMessage(
        'Test message',
        'TestSender',
        ['+35699829840'],
        'test-ref'
    );

    echo "Success!\n";
    echo "Status Code: " . $result->statusCode . "\n";
    echo "Status Message: " . $result->statusMessage . "\n";
    print_r($result->details);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
