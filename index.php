<?php
//https://api.telegram.org/bot{TOKEN}/setwebhook?url=URLbot/
class Sms  //класс Sms не знаю почему класс,по сути можно заменить функцией
    // тут отправляем в общем наш запрос в телегу
{
    public $message; // записываем сообщение которое хотим передать
    public $data; //тут храним данные для ответа айди чата и тд
    public $token;
    public function __construct($message,$data,$token)
    {
        $this->message = $message; // запишем данные в свойство name
        $this->data=$data;
# Формируем массив для отправления в телеграм
        $params = [
            'chat_id' => $data['message']['chat']['id'],
            'text'    => $message
        ];
# Отправляем запрос в телеграм
        file_get_contents('https://api.telegram.org/bot'.$token.'/sendMessage?'.http_build_query($params));
    } }
$data = json_decode(file_get_contents('php://input'), TRUE);//декодим Джесон в массив в переменную дата
file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND); // создаем файл куда пишем всю историю наших входящих сообщений
$token = 'TOKEN';

# ниже проверяем команды полученные от бота
//СТАРТ
if($data['message']['text']=='/start') { //проверяем если пришла команда СТАРТ выполняем команду ниже
    unlink('date.txt');
    $date = new Sms('Хотите добавить итог рабочего дня? /DA', $data, $token); // создали объект класса СМС и отправили сообщение
}
elseif ($data['message']['text'] == '/stop') {  //проверяем если пришла команда ДА выполняем команду ниже
    unlink('date.txt');
    $date = new Sms('Бот преркатил работу. Для возобнавления нажмите /start', $data, $token);
}
//ДА
elseif ($data['message']['text'] == '/DA') {  //проверяем если пришла команда ДА выполняем команду ниже
    unlink('date.txt');
    $date = new Sms('Ввведите дату в формате ДД.ММ.ГГ:', $data, $token);
}
//ДАТА
elseif (preg_replace('#..\...\...#','true',$data['message']['text'])=='true'){//проверяем если пришла команда в верном формате выполняем команду ниже
    //тут дописать проверку даты: формат,цифры итд
    $date = new Sms(" Ввведите приход в формате +СУММА,если продаж нет введите +1:\nЕсли допустили ошибку нажмите /stop ", $data, $token);
    $text=$data['message']['text'];
    file_put_contents('date.txt', $text.","."\n", FILE_APPEND); //пишем данные в файл дата
}
//Приход
elseif (preg_replace('#\+.+#','true',$data['message']['text'])=='true'){//проверяем если пришла команда в верном формате выполняем команду ниже
    //тут дописать проверку формата чтоб после + были цифры,первое отличное от 0
    $date = new Sms("Ввведите Расход в формате -СУММА:\n Если допустили ошибку нажмите /stop", $data, $token);
    $text=$data['message']['text'];
    file_put_contents('date.txt', $text.","."\n", FILE_APPEND);//пишем данные в файл дата
}
//РАСХОД
elseif (preg_replace('#-.+#','true',$data['message']['text'])=='true'){//проверяем если пришла команда в верном формате выполняем команду ниже
    //тут дописать проверку формата чтоб после - были цифры,первое отличное от 0
    $date = new Sms("Ввведите комментарий вормата *КОММЕНТАРИЙ:\n Если нет коменда нажмите /*0 \n Если допустили ошибку нажмите /stop", $data, $token);
    $text=$data['message']['text'];
    file_put_contents('date.txt', $text.","."\n", FILE_APPEND);//пишем данные в файл дата
}
//КОММЕНТАРИЙ
elseif (preg_replace('#\*.+#','true',$data['message']['text'])=='true'){//проверяем если пришла команда в верном формате выполняем команду ниже
    $date = new Sms(" Подтвердите верность введеных данных /BEPHO \nЕсли допустили ошибку нажмите /stop ", $data, $token);
    $text=$data['message']['text'];
    file_put_contents('date.txt', $text.","."\n", FILE_APPEND);//пишем данные в файл дата
}
//Подтверждение
elseif($data['message']['text']=='/BEPHO') {//проверяем если пришла команда в верном формате выполняем команду ниже
    $text = $data['message']['from']['username'];// юзер записавший данные и подвердивший отправку
    file_put_contents('date.txt', $text . "," . "\n", FILE_APPEND);//пишем данные в файл дата

    $flag = include 'send.php'; //подкл файл в котором выполним обработку date.txt  для отправки в таблицы в флаг пишем состояние подкл тру и фолс

    If($flag)// если подключился файл успешно и вернул тру выполняем  отправку сообщения и удаление файла тхт
    {
        $date = new Sms("спасибо за проделанную работу...\n Хотите добавить нового рабочего дня? /DA", $data, $token); //
        unlink('date.txt');
    }
    else{
        $date = new Sms('Что-то пошло не так...Попробуйте заполнить снова? /DA', $data, $token);// если подключился подкл вернуло фолс выполняем  отправку сообщения и удаление файла тхт
        unlink('date.txt');
    }
}
else{
    $date = new Sms('Что-то пошло не так...Попробуйте заполнить снова? /DA', $data, $token);// если подключился подкл вернуло фолс выполняем  отправку сообщения и удаление файла тхт
    unlink('date.txt');
}
