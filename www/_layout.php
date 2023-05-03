<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PV011 - <?= $_CONTEXT['page_title'] ?? '' ?> </title>
    <link  href="/css/style.css" rel="stylesheet" >
</head>
<body>

<h1>PHP</h1>
<div  class="menu">
    <img src="/img/image.png" alt="logo" class="logo"/>
    <a   href="/basics">Введение PHP</a>
    <a  href="/fundamentals">Основы PHP</a>
    <a href="/layout">Шаблонизация</a>
    <a href="/formdata">Данные форм</a>
    <a href="/db">Работа с БД</a>
    <a  style="color:maroon" href="/email_test">E-mail</a>
     
    <?php include "_auth.php" ?>
    
</div>
    
    <?php 
    if($path_parts[1] === '') 
    $path_parts[1] = 'index';
        switch( $path_parts[1]){
          
            case 'index'        :
            case 'basics'       :
            case 'fundamentals' :
            case 'layout'   :
            case 'formdata'     :
            case 'db':
            case 'email_test':
            case 'register' :
                include "{$path_parts[1]}.php";
                break;
            case 'shop'   : 
            case 'profile': include "views/{$path_parts[1]}.php";break;
            default:
           include "error404.php";
        }
    ?>
    <?php  $x = 10;
     $i = 20;
     //include "footer.php" ?>
        
</body>
</html>