<?	// пример вызова метода createZkz
	// API сервиса поиска и оплаты штрафов и налогов shtraf.biz
	// http://shtraf.biz/api.php
    // (с) 2015 Федорук Александр fedl@mail.ru
	// The MIT License (MIT)
    session_start();
	require_once('api.php');

    if(isset($_POST['fld']['onlysave']))
	{   // сохраняем в:
		// $_SESSION['pdat'][$fieldID] - персональные данные Плательщика
        // $_SESSION['l'][$fieldID] - выбранные платежи
		// для последующей передачи в метод API_CREATE_ZKZ
		foreach($_POST['fld'] as $fieldID=>$val)
		{   switch($fieldID)
			{   case 'name1':
            	case 'name2':
	            case 'name3':
    	        case 'email':
				case 'flmon':	$_SESSION['pdat'][$fieldID]=$dat[$fieldID]=$val; break;
 				default:        // удалить отмененные Клиентом платежи
			 					if(isset($_SESSION['l'][$fieldID]))
								{   unset($_SESSION['l'][$fieldID]);
								}
								break;
			}
		}
	}
	else
	{   $dat=array();
    	$dat=$_SESSION['acc'];
        $dat+=$_SESSION['pdat'];

		$api=new _API();

    	$zn= isset($_SESSION['zn'])?$_SESSION['zn']:0;
    	$pin=isset($_SESSION['pin'])?$_SESSION['pin']:0;

        if($zn==0&&$pin==0) // если заказ ещё не создан
    	{	$dat['top']=API_CREATE_ZKZ;
    		$dat['type']=$_SESSION['type'];
    		$dat['l']=json_encode($_SESSION['l']);  // преобразовать массив платежей в JSON формат
    		$dat['ip']=$_SERVER['REMOTE_ADDR'];
    		$ret=$api->sendQUERY($dat);

 			if($ret['err']==0)	// заказ успешно создан
			{   $_SESSION['zn']=$ret['zn'];
				$_SESSION['pin']=$ret['pin'];
			}
		}

    	$zn= isset($_SESSION['zn'])?$_SESSION['zn']:0;
    	$pin=isset($_SESSION['pin'])?$_SESSION['pin']:0;

		if($zn!=0&&$pin!=0) // перейти на страницу оплаты Заказа
		{   $dat=array();
			$top=API_GO_PAY;
            $dat['zn']=$zn;
			$dat['pin']=$pin;
        	$dat['top']=API_GO_PAY;
        	$ret=$api->sendQUERY($dat); // перейти на оплату Заказа
			echo $ret;
		}
		else
		{
?>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="no-store, no-cache, must-revalidate">
<META HTTP-EQUIV="PRAGMA" CONTENT="no-store, no-cache, must-revalidate">
<style type="text/css">
body {
	background-color:#E3E3E3;
}
</style>
</head>
<body>

<span class="err">Ошибка при создании Заказа:<br>
<? echo $ret['msg'];?>
<br><br>
Вернитесь во вкладку с Заказом и нажмите кнопку "Перейти на оплату >>" ещё раз.
</span>
</body>
</html>
<?
		}
   		unset($api);
	}
?>