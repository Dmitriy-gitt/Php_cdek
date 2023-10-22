<?php header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
require "vendor/autoload.php";

$name_city  = ["Миасс", "Челябинск", "Златоуст", "Москва"];


//Создаем данные для запроса
$myCurl = curl_init();
curl_setopt_array($myCurl, array(
    CURLOPT_URL => 'https://api.edu.cdek.ru/v2/oauth/token?parameters',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_POSTFIELDS => http_build_query(array(
    'grant_type' => 'client_credentials',
    'client_id' => 'EMscd6r9JnFiQ3bLoyjJY6eM78JrJceI',
    'client_secret' => 'PjLZkKBHEiLK3YsjtNrt3TGNG0ahs3kG'))
));
curl_setopt($myCurl, CURLOPT_SSL_VERIFYHOST, false);
//Делаем запрос
$response = curl_exec($myCurl);
//Закрываем сеанс
curl_close($myCurl);

//получаем ответ от СДЕКА, декодируем в массив, что бы забрать токен
$get_token_cdek = get_object_vars(json_decode($response));
$token_cdek = $get_token_cdek["access_token"];
//echo $token_cdek;

$url = 'https://api.edu.cdek.ru/v2/location/cities?city=Миасс';

$headers = ["Authorization: Bearer " . $token_cdek]; // создаем заголовок
$point_arr =[];// массив для точек

foreach ($name_city as $key => $val){
    $url = 'https://api.edu.cdek.ru/v2/location/cities?city='.$val; 
//Делаем запрос для получения кода города
    curl_setopt_array($myCurl, array(
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_VERBOSE => 1, 
        CURLOPT_POST => false, 
        CURLOPT_URL => $url,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0));

    $resul = curl_exec($myCurl);
    $get_code_city = json_decode($resul);//декодируем результат в json формат
    $code_city =  get_object_vars($get_code_city[0]);//создаем массив
    $code = $code_city["code"];//получаем код города

    //Делаем запрос к списку офисов
    curl_setopt_array($myCurl, array(
        CURLOPT_URL => 'https://api.edu.cdek.ru/v2/deliverypoints?city_code='.$code,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array("Authorization: Bearer " . $token_cdek)));

    $lst_office = curl_exec($myCurl);
    $num_1 = json_decode($lst_office);

    foreach($num_1 as $key => $val){
    $a = get_object_vars($num_1[$key]);
    $b = get_object_vars($a["location"]);
    $adress_full = $b["address_full"];//Получаем полный адрес
    $longitude = $b["longitude"];// Получаем долготу
    $latitude = $b["latitude"];//Получаем широту
    $point = ['longitude'=>$longitude, 'latitude'=>$latitude];
    array_push($point_arr, $point);
    };

}
echo json_encode($point_arr);
curl_close($myCurl);

?>