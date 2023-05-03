<?php
//@ - это игнорирование предупреждения
@session_start(); //сессия - перед обращением к сессии обязательно
switch( strtoupper($_SERVER['REQUEST_METHOD'])){
    case 'GET'  :     
        $view_data = [];
        if(isset($_SESSION['reg_error'])){
            $view_data['reg_error'] = $_SESSION['reg_error'];
            unset($_SESSION['reg_error']);  
            $view_data['login'] = $_SESSION['login'];
            $view_data['email'] = $_SESSION['email'];
            $view_data['name'] = $_SESSION['name'];
        }
        if(isset($_SESSION['reg_ok'])){
            $view_data['reg_ok'] = $_SESSION['reg_ok'];
            unset($_SESSION['reg_ok']);  
        }
        include "_layout.php";
        break;

    case 'POST' :  
        // echo "$_POST"; exit;      
        //данные формы регистрации - обрабатываем
        //echo "<pre>"; print_r($_FILES); exit;
        if(empty($_POST['login'])){
            $_SESSION['reg_error'] = "Empty login";
        }
        else if(empty($_POST['name'])){
            $_SESSION['reg_error'] = "Empty USer NAme";
        }
        else if(empty($_POST['password'])){
            $_SESSION['reg_error'] = "Empty password";
        }
        else if(empty($_POST['email'])){
            $_SESSION['reg_error'] = "Empty email";
        }
        else if($_POST['password'] !== $_POST['confirm']){
            $_SESSION['reg_error'] = "Passwords mismatch";
        }
        else {         
           
            try{
               $prep = $connection->prepare(
                "SELECT COUNT(Id) FROM Users u WHERE u.`login` = ?");
                $prep->execute([$_POST['login']]);
                $cnt = $prep->fetch(PDO::FETCH_NUM)[0];
            }
            catch (PDOException $ex){
                $_SESSION['reg_error'] = $ex->getMessage();
            }
            if($cnt > 0){
                $_SESSION['reg_error'] = "Login in use";
            }
             // Проверяем наличие аватара и загружаем файл
            // Берем имя файла, отделяем разширение, проверяем на допустимые (изображения)
            //сохраняем разширение, но меняем имя файла
            //использовать переданные имена (опасно)
            //Возможные ДТ - атаки со спецсимволами в именах
            // Файлы храним в отдельной папке, их имена
            if( isset( $_FILES['avatar'] ) ) {  // наличие файлового поля на форме
                if( $_FILES['avatar']['error'] == 0 && $_FILES['avatar']['size'] != 0 ) {
                    // есть переданный файл
                    $dot_position = strrpos( $_FILES['avatar']['name'], '.' ) ;  // strRpos ~ lastIndexOf
                    if( $dot_position == -1 ) {  // нет расширения у файла
                        $_SESSION[ 'reg_error' ] = "File without type not supported" ;
                    }
                    else {
                        $extension = substr( $_FILES['avatar']['name'], $dot_position ) ;  // расширение файла (с точкой ".png")
                        /* Загрузка аватарки:
                            v проверить расширение файла на допустимый перечень
                            v сгенерировать случайное имя файла, сохранить расширение
                            v загрузить файл в папку www/avatars
                            его имя добавить в параметры SQL-запроса и передать в БД
                        */
                        // echo $extension ; exit ;
                        if( ! in_array( $extension, ['.jpg', '.png', '.jpeg', '.svg'] ) ) {
                            $_SESSION[ 'reg_error' ] = "File extension '{$extension}' not supported" ;
                        }
                        else {
                            $avatar_path = 'avatars/' ;
                            do {
                                $avatar_saved_name = bin2hex( random_bytes(8) ) . $extension ;
                            } while( file_exists( $avatar_path . $avatar_saved_name ) ) ;
                            if( ! move_uploaded_file( $_FILES['avatar']['tmp_name'], $avatar_path . $avatar_saved_name ) ) {
                                $_SESSION[ 'reg_error' ] = "File (avatar) uploading error" ;
                            }
                        
                        }
                    }
                }
            }
            

        }

        if( empty( $_SESSION[ 'reg_error' ] ) ) {
            // подключаем фукнцию отправки почты
            @include_once "helper/send_email.php" ;
            if( ! function_exists( "send_email" ) ) {
                $_SESSION[ 'reg_error' ] = "Inner error" ;
            }
        }

        if(empty($_SESSION['reg_error'])){
            $salt = md5(random_bytes(16));
            $pass = md5($_POST['confirm'] . $salt);
            $confirm_code = bin2hex(random_bytes(3));
            send_email( $_POST['email'], 
            "pv011.local Email verification", 
            "<b>Hello, {$_POST['name']}</b><br/>
            Type code <strong>$confirm_code</strong> to confirm email<br/>
            Or follow next <a href='https://php.local/confirm?code={$confirm_code}&email={$_POST['email']}'>link</a>" ) ;

        $sql = "INSERT INTO Users(`id`, `login`,`name`, `salt`, `pass`, `email`,`confirm`,      `avatar`) 
                VALUES(            UUID(),?,      ?,    '$salt','$pass',   ?,   '$confirm_code',    ?)" ;
        try {
            $prep = $connection->prepare( $sql ) ;
            $prep->execute( [ 
                $_POST['login'], 
                $_POST['name'], 
                $_POST['email'],
                isset( $avatar_saved_name ) ? $avatar_saved_name : null
            ] ) ;
            $_SESSION[ 'reg_ok' ] = "Reg ok" ;
        }
        catch( PDOException $ex ) {
            $_SESSION[ 'reg_error' ] = $ex->getMessage() ;
        }
    }
        else {
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['email'] = $_POST['email'];
            $_SESSION['name'] = $_POST['name'];
        }
        header("Location: /" .$path_parts[1]);
        
        // echo "<pre>"; print_r($_POST);
        break;

}