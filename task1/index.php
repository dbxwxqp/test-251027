<?php
$file_path = __DIR__ . '/mock_data.json';
$file_data = file_get_contents($file_path);
$aData = json_decode($file_data, true);
//die('#<pre>' . print_r($file_data, true) . '</pre>');
