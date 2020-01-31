// Получаем данные от сервера
function answer_data_server(){
	$('.loading').show();

	$.ajax({
		type: 'post',
		url: 'backend.php',
		success: function(result){
			// Выводим все данные
			resultObj = JSON.parse(result);
			console.log(resultObj);

			if(resultObj.result === 'error'){
				$('.loading').hide();
				$('.odd_data').hide();
				$('.error_data').show();
			}
			else if(resultObj.result === 'ok'){
				$('.loading').hide();
				$('.error_data').hide();
				$('.odd_data').show();
				$('.odd_data').html(resultObj.odd_array);

				// Получаем установленный лимит
				$(".odd_limit_input").val(resultObj.outcome_limit);
			}
		},
		error: function(){
			$('.error_container').show();
		}
	});
}

// Получаем данные от сервера
answer_data_server();

// Таймер для обновления данных
function data_update(){
	date = new Date();

	minutes = date.getMinutes();
	seconds = date.getSeconds();

	if((minutes === 3 && seconds === 0) || (minutes === 8 && seconds === 0) || (minutes === 13 && seconds === 0) || (minutes === 18 && seconds === 0) || (minutes === 23 && seconds === 0) || (minutes === 28 && seconds === 0) || (minutes === 33 && seconds === 0) || (minutes === 38 && seconds === 0) || (minutes === 43 && seconds === 0) || (minutes === 48 && seconds === 0) || (minutes === 53 && seconds === 0) || (minutes === 58 && seconds === 0) || (minutes === 58 && seconds === 30)){
		// Получаем данные от сервера
		answer_data_server();
	}
}
setInterval(data_update, 1000);

// Изменение установленного лимита
$('#form_limit').submit(function(event){
	event.preventDefault();

	// Получаем данные из input
	input_data = $('.odd_limit_input').val();
	// Отправляем данные на сервер
	$.ajax({
		type: 'post',
		url: 'limit.php',
		data: { input: input_data },
		success: function(result){
			resultObj = JSON.parse(result);
			if(resultObj.result === 'ok'){
				$('.save').show();
				$('.save').delay(3000).fadeOut();
			}
			// Получаем данные от сервера
			answer_data_server();
		},
		error: function(){
            alert('Error: Ошибка сохранения данных пользователя');
        }
	});
});