<h2>Формы. Данные формы</h2>

<form method="get">
    <label>Введите строку: <input name="str"/></label>
    <br/>
    <button>Послать GET</button>

</form>

<form method="post">
    <label>Введите строку: <input name="strp"/></label>
    <br/>
    <button>Послать POST</button>

</form>
<br/>
<form method="post" enctype="multipart/form-data">
    <label>файл: <input type="file" name="formfile"/></label>
    <br/>
    <label>Введите описание: <input  name=" descr" value="A file"/></label>
    <br/>
    <label>Введите описание: <input disabled name=" bescr" value="B file"/></label>
    <br/>
    <button>Послать файл</button>
</form>


<p>
    Все GET-параметры (передаваемые в адресной строке ?)
    собираются в глобальный массив $_GET, доступный в любой части кода 
    <br/>
    $_GET: <?php print_r($_GET)?>
</p>

<p>
    POST- данные передаются в теле запроса, в адресной строкеих не видно
    . Значения попадают в массив
    <br/>
    $_POST: <?php print_r( $_POST)?>
    <br/>
    POST и GET, могут приходить одновременно, но только 
    не GET запросом (не должен иметь тела)
</p>

<p>
    Массив $_REQUEST является объединением GET / POST данных
    но для использования не рекомендуется.
    <br/>
    $_REQUEST: <?php print_r(  $_REQUEST)?>
</p>
<p><pre>
    Данные о загруженны (переданных формой) файлах 
    собираются в отдельном глобальном массиве
    <br/>
    $_FILES: <?php print_r($_FILES) ?>
</pre>
</p>
<?php if( isset( $_FILES['formfile'] ) ) {        // передача есть
    if( $_FILES['formfile']['error'] === 0 ) {    // нет ошибки
        if( $_FILES['formfile']['size'] > 0 ) {   // есть данные
            move_uploaded_file( 
                $_FILES['formfile']['tmp_name'],
                './uploads/' . $_FILES['formfile']['name'] 
            ) ;
        }
    }
} ?>