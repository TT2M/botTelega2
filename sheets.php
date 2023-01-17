<?php
session_start();
// Подключаем клиент Google таблиц
require_once __DIR__ . '/vendor/autoload.php';
// Наш ключ доступа к сервисному аккаунту
$googleAccountKeyFilePath = __DIR__ . '/service_key.json';
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath);
// Создаем новый клиент
$client = new Google_Client();
// Устанавливаем полномочия
$client->useApplicationDefaultCredentials();
// Добавляем область доступа к чтению, редактированию, созданию и удалению таблиц
$client->addScope(['https://www.googleapis.com/auth/drive', 'https://www.googleapis.com/auth/spreadsheets']);
$service = new Google_Service_Sheets($client);
// ID таблицы
$spreadsheetId = '1Ommp_D8PIvsU_Kr3JDW3wXoyNHubkM_JibPvn3J8Mh0';



// Данные для обновления
// Диапазон, в котором мы определяем заполненные данные. Например, если указать диапазон A1:A10
// и если ячейка A2 ячейка будет пустая, то новое значение запишется в строку, начиная с A2.
// Поэтому лучше перестраховаться и указать диапазон побольше:
$range = 'list!A1:F';
// Данные для добавления
$values = [
    $_SESSION['data']
];
// Объект - диапазон значений
$ValueRange = new Google_Service_Sheets_ValueRange();
// Устанавливаем наши данные
$ValueRange->setValues($values);
// Указываем в опциях обрабатывать пользовательские данные
$options = ['valueInputOption' => 'USER_ENTERED'];
// Добавляем наши значения в последнюю строку (где в диапазоне A1:Z все ячейки пустые)
$service->spreadsheets_values->append($spreadsheetId, $range, $ValueRange, $options);
