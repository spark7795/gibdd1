<?	// пример вызова метода loadZkz
	// API сервиса поиска и оплаты штрафов и налогов shtraf.biz
	// http://shtraf.biz/api.php
    // (с) 2015 Федорук Александр fedl@mail.ru
	// The MIT License (MIT)
    session_start();
	require_once('api.php');
    $dat=array();
    $dat['zn']= isset($_SESSION['zn'])?$_SESSION['zn']:0;
    $dat['pin']=isset($_SESSION['pin'])?$_SESSION['pin']:0;
	$flUpd=0;
    $api=new _API();
	if($dat['zn']!=0&&$dat['pin']!=0)
    {   $dat['top']=API_LOAD_ZKZ;
		$ret=$api->sendQUERY($dat);
		if($ret['err']==0&&count($ret['l'])>0)
		{   switch($ret['stat'])
			{   case 2: $state='ОПЛАЧЕН'; $flUpd=1; break;
                case 3: $state='ВЫПОЛНЕН'; break;
                case 5: $state='ВЫПОЛНЕН ЧАСТИЧНО'; break;
                case 8: $state='ОТМЕНЕН'; break;
                case 6:
                case 9: $state='ЖДЕТ ОПЛАТУ'; $flUpd=1; break;
                case 14: $state='ВЫПОЛНЯЕТСЯ'; $flUpd=1; break;
                case 15: $state='ОТМЕНЕН ПЛАТЕЖНОЙ СИСТЕМОЙ'; break;
				default:$state='СОЗДАН'; break;
			}

?>
<span id="msgstr" class="ok">Заказ N: <?echo substr(strrchr($ret['zn'], "-"),1);?> от <?echo date("d-m-y H:i:s",$ret['mkt']);?> на сумму: <?echo $ret['sumpay'];?> руб. <?echo $state;?></span>
<br><br>
	<table id="paystbl">
		<thead>
		<tr>
    		<th class="sel">Состояние</th>
    		<th class="sum">Сумма</th>
	    	<th class="doc">УИН</th>
            <th class="doc">Дата</th>
            <th class="rem">Дополнительно</th>
    	</tr>
		</thead>
		<tbody>
<?
			$tPay= $_SESSION['type']==_ZKZ_SHTRAF?"Штраф":"Налог";
			foreach($ret['l'] as $IDpay=>$elem)
			{	$UIN=substr($IDpay,strpos($IDpay,'_')+1);
				switch($elem['stat'])
				{   case 3: $statePay='выполнен'; break;
    	            case 15: $statePay='отменен'; break;
					default: $statePay='выполняется'; break;
				}
?>
	    <tr>
    	    <td><?echo $statePay;?></td>
	        <td><?echo number_format($elem['sum'],2,'.','');?></td>
    	    <td><?echo $UIN;?></td>
            <td><?echo $elem['dat'];?></td>
            <td><a class="tooltip" href="#">подробнее<span class="custom info"><em><?echo $tPay;?></em><?echo $elem['addinfo'];?></span></a></td>
	    </tr>
<?
			}
?>
		</tbody>
</table>
<?
		}
        unset($api);
	}
	else
	{
?>
<span id="msgstr" class="ok">Создание Заказа. Пожалуйста подождите ...</span>
<?
	}
?>
<br><br>
<button id="buttnewsearch"><< Новый поиск</button>
<?
    if($flUpd)
	{
?>
<button id="buttpay">Перейти на оплату >></button>
<?
	}
?>
<form name="fgp" id="fgp" method="post" action="createzkz.php"></form>

<script language="javascript" type="text/javascript">
$(document).ready(function(){
    $('#loader').hide();

	// обработка клика по кнопке "<< Новый поиск"
	$('#buttnewsearch').on('click',function(){
        if(confirm('При нажатии "Ок" Вы перейдете в форму поиска новых штрафов и налогов. Если Вы уже оплатили Заказ - информация о его выполнении будет отправлена на Ваш e-mail\n\nВы подтверждаете переход ?')){
            if(intID!=0) { clearTimeout(intID); intID=0; }
            $('#content').load('main.php');
		}
	});

<?
    if($flUpd)
	{
?>
    // обработка клика по кнопке "Перейти на оплату >>"
    $('#buttpay').on('click',function(){
        if(confirm("Вы уже переходили на страницу оплаты. Если оплата прошла успешно - пожалуйста ничего не предпринимайте - информация о состоянии Вашего Заказа обновится автоматически, а также будет отправлена на Ваш e-mail. Если же произошел сбой - Вы можете перейти на страницу оплаты ещё раз.\n\nВы уверены что хотите перейти на страницу оплаты ещё раз ?")) {
            $("#fgp").attr('target','_blank').submit();
		}
	});

	intID=setTimeout(function upd(){
        $('#loader').show();
		$('#content').load('loadzkz.php');
	},30000);
<?
	}
?>
});
</script>

