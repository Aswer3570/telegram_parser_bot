<?php
// Вывод ошибок
/*ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/

$i = 0;
$a = 0;
$key = 0;

// Задаём UserAgent
$context  = stream_context_create(array('http' => array('user_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.53 Safari/525.19')));

// Парсер сайта
$str = file_get_contents('https://t.me/s/statistika_fifa_penalty_fast', false, $context);
preg_match_all('#<div class=.?tgme_widget_message_text[^>]+>(.+?)</div>#su', $str, $score);

foreach($score[0] as $key => $value){
	// Чистим от HTML тегов
	$valueClearHTML = strip_tags($value);

	// Замена
	$pattern = '/(#\w+)\.\s+(#\w+)\s+(\w+\s*\w*)\s+\((\d+):(\d+)\)\s+(\w+)(\s*\w*)?/iu';
	$replacement = '$4:$5';
	$score_replace = preg_replace($pattern, $replacement, $valueClearHTML);

	// Чистим выборку от рекламного мусора
	if(strlen($score_replace) == 3){
		// Считаем
		$first_number = preg_replace('/(\d+):(\d+)/', '$1', $score_replace);
		$second_number = preg_replace('/(\d+):(\d+)/', '$2', $score_replace);
		$score_odd = $first_number + $second_number;

		// Записываем все исходы в массив
		$scoreArray[$i] = $score_odd;
    	$i++;
	}
}

// Получаем лимит исходов
$limit_data_json = file_get_contents('limit.json');
$limit_data = json_decode($limit_data_json, true);
$limit_data_mat =  count($scoreArray) - $limit_data['limit'];

// Проверяем количество нечётных результатов
for($n = $limit_data_mat; $n < count($scoreArray); $n++){
	// Фильтруем только нечётные результаты
	if(!($scoreArray[$n] % 2) == 0){
		$scoreArrayOdd[$a] = $scoreArray[$n];
        $a++;
	}
}

// Формируем JSON ответ для клиента
if(empty($scoreArray) || empty($scoreArrayOdd)){
	echo json_encode(array('result' => 'error'));
}
else{
	$date = date("d.m.Y H:i:s");
	$scoreArrayOdd_length = count($scoreArrayOdd);
	$json_data = [
		'result' => 'ok',
  		'odd_array' => $scoreArrayOdd_length,
  		'outcome_limit' => $limit_data['limit'],
  		'time' => $date,
  		'all_data' => $scoreArray,
  		'odd_data' => $scoreArrayOdd
	];
	echo json_encode($json_data);
}

// Проверяем количество нечётных результатов и отправляем уведомление через Telegram
if(count($scoreArrayOdd) == $limit_data['limit']){
	$answer = 'Последние ' . $limit_data['limit'] . ' результатов были нечётными! (' . $date . ')';
	// Отправляем запрос на сервер Telegram
	$token = '1054735993:AAHmJezhcGOsqohmkf7w1cHiLEYR5d2H9D8';
	$inquiry = file_get_contents('https://api.telegram.org/bot' . $token . '/getupdates?offset=-1', false, $context);
	$results = json_decode($inquiry, true);
	// Получения данных из JSON ответа от Telegram
	$chat_id = $results['result']['0']['message']['from']['id'];
	// Отправляем данные боту
	fopen('https://api.telegram.org/bot' . $token . '/sendmessage?chat_id=' . $chat_id . '&text=' . $answer . '', 'r');
}
?>