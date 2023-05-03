<?php

include_once "helper/send_email.php" ;

if( ! function_exists( "send_email" ) ) { //функция с отрицанием
    echo "include error" ;
    exit ;
}
send_email( "valeriiahubnytskaya@gmail.com", 
    "Email verification", 
    "<b>Hello</b><br/>Type code XXXXXX to confirm email" ) ;



/*
include         подключить (выполнить). Если файла нет (warning) и идет дальше
include_once    то же самое, + проверка не был ли файл подключен ранее
require         то же самое, но если файла нет - фатальная ошибка
require_once    то же самое, + проверка на подключенее ранее
@ - игнорирует warning
*/