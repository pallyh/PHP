<?php
//контроллер подтверждения почты
// сюда можно попасть по ссылке из письма, тошда в параметрах есть email
//со страницы ввода кода, тогда в параметрах только код, но должна быть авторизация
if(empty($_GET['code'])){//несанкционированный вход
    echo "Не правильный код";
    exit;
}
if(empty($_CONTEXT)){ //массив создаваемый в диспетчере доступа, если его нет - непправильный запуск
    echo "Не правильный запуск";
    exit;
}

if(isset($_GET['email'])){ //переход по ссылке из письма
    $sql = "SELECT COUNT(u.id) FROM Users u WHERE u.email = ? AND u.confirm = ?";
    try{
        $_CONTEXT['connection'];
        $prep = $_CONTEXT['connection']->prepare($sql);
        $prep->execute([$_GET['email'], $_GET['code']]);
        $cnt = $prep->fetch(PDO::FETCH_NUM)[0];
            if($cnt == 0){
                echo "Не правильный код или почта";
                exit;
            }
            else{
                $sql = "UPDATE Users u SET u.confirm = NULL WHERE u.email = ? AND u.confirm = ? ";
                $prep->execute([$_GET['email'], $_GET['code']]);
                echo "почта подтверждена";
                exit;
            }
    }
    catch(PDOException $ex){
        echo $ex->getMessage();
        exit;
    }
}
else {      // б) со страницы ввода кода - должна быть авторизация и $_CONTEXT[ 'auth_user' ]
             // но возможен прямой переход по ссылке - нужно проверять факт авторизации
    if( empty( $_CONTEXT[ 'auth_user' ] ) ) {
    echo "Авторизуйтесь для подтверждения почты";
    exit ;
    }
        // Извлекаем код подтверждения по id пользователя
     $sql = "SELECT u.confirm FROM Users u WHERE u.id = '{$_CONTEXT['auth_user']['id']}' " ;
    try {
        $db_code = $_CONTEXT[ 'connection' ]->query( $sql )->fetch( PDO::FETCH_NUM )[0] ;
            if( $db_code == NULL ) {
                echo "Почта подтверждена, действий не требуется" ;
                exit ;
            }
            else if( $db_code == $_GET[ 'code' ] ) {
                // Код подтвержден - сбрасываем в БД
                $sql = "UPDATE Users u SET u.confirm = NULL WHERE u.id = '{$_CONTEXT['auth_user']['id']}' ";
                $_CONTEXT[ 'connection' ]->query( $sql ) ;
                echo "Почта подтверждена" ;
                exit ;
            }
   else {
       echo "Неправильный код подтверждения" ;
       exit ;
   }
    }
    catch( PDOException $ex ) {
    echo $ex->getMessage() ;
    exit ;
    }
}
//print_r($_GET);
