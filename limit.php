<?php 

$input = $_POST['input'];
$filename = 'limit.json';
$input_json = json_encode(array('limit' => $input));

// Записываем данные в файл
file_put_contents($filename, $input_json);

echo json_encode(array('result' => 'ok'));
?>