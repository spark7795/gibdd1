<?	// пример организации сайта для поиска и оплаты штрафов ГИБДД и налогов
	// на основе API сервиса shtraf.biz
	// http://shtraf.biz/api.php
    // (с) 2015 Федорук Александр fedl@mail.ru
	// The MIT License (MIT)
	session_start();
	require('db.php');
?>
<!DOCTYPE html>
<html>
<head>
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="no-store, no-cache, must-revalidate">
<META HTTP-EQUIV="PRAGMA" CONTENT="no-store, no-cache, must-revalidate">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>Поиск штрафов</title>
<link rel="stylesheet" href="api.css" type="text/css" charset="utf-8">
</head>
<body>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<div id="loader"></div>
<script language="javascript" type="text/javascript">
	var  intID=0;   // ID процедуры setTimeout (файл loadzkz.php)
    $('#loader').hide();
</script>
<div id="content">
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	$('#content').load('main.php');
});
</script>
</div>
</body>
</html>
