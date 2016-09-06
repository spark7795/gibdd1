<?	
    session_start();
	require_once('api.php');
	require('db.php');
	$dat=array();
	foreach($_POST['fld'] as ($fieldID=>$val))
	{   switch($fieldID)
		{   case 'vu':
            case 'sts':
            case 'ps':
            case 'inn':
            case 'snils':
            case 'ind': $_SESSION['acc'][$fieldID]=$dat[$fieldID]=$val; break;
            case 'type':$_SESSION['type']=$dat[$fieldID]=$val; break;
 			default: break;
		}
	}

    $name1= isset($_SESSION['pdat']['name1'])?$_SESSION['pdat']['name1']:"";
    $name2= isset($_SESSION['pdat']['name2'])?$_SESSION['pdat']['name2']:"";
    $name3= isset($_SESSION['pdat']['name3'])?$_SESSION['pdat']['name3']:"";
    $email= isset($_SESSION['pdat']['email'])?$_SESSION['pdat']['email']:"";
    $flmonSt= isset($_SESSION['pdat']['flmon'])&&$_SESSION['pdat']['flmon']==1?
		" checked":"";

    $api=new _API();
    $dat['top']=API_CHECK_PAY;
	
	
    $ret=$api->sendQUERY($dat);
	unset($api);
	if($ret['err']==0&&count($ret['l'])>0)
	{   $_SESSION['l']=$ret['l']; // сохраним в сессии список найденных платежей
?>
	<table id="paystbl">
		<thead>
		<tr>
    		<th class="sel">К оплате</th>
    		<th class="sum">Сумма</th>
	    	<th class="doc">УИН</th>
            <th class="dat">Дата</th>
            <th class="rem">Дополнительно</th>
    	</tr>
		</thead>
		<tbody>
<?
		$tPay= $_SESSION['type']==_ZKZ_SHTRAF?"Штраф":"Налог";
		$numPay=$sumPay=$sumFee=0;
		foreach($_SESSION['l'] as $IDpay=>$elem)
		{	$UIN=substr($IDpay,strpos($IDpay,'_')+1);
    		$numPay++;
    		$sumPay+=$elem['sum'];
            $sumFee+=$elem['feesrv'];

?>
	    <tr>
    	    <td>
    	    <input data-sum="<?echo $elem['sum'];?>"
                data-feesrv="<?echo $elem['feesrv'];?>"
				id="<?echo $IDpay;?>" type="checkbox" checked>
    	    </td>
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
<br>
<span id="uinstr" class="ok">УИН к оплате: <?echo $numPay;?>, на сумму: <?echo $sumPay;?> руб. Комиссия: <?echo $sumFee;?> руб.</span>
<br><br>
<!--
<table id="tblclient">
  <tr>
    <td>Фамилия</td>
    <td>
	<input type="text" id="name1" size="25" maxlength="50" value="<?echo $name1;?>">
    </td>
  </tr>
  <tr>
    <td>Имя</td>
    <td>
      <input type="text" id="name2" size="25" maxlength="50" value="<?echo $name2;?>">
    </td>
  </tr>
  <tr>
    <td>Отчество</td>
    <td>
      <input type="text" id="name3" size="25" maxlength="50" value="<?echo $name3;?>">
    </td>
  </tr>
  <tr>
    <td>email</td>
    <td>
      <input type="text" id="email" size="20" maxlength="45" value="<?echo $email;?>">
    </td>
  </tr>
</table> 
-->

<br><br>

<input id="docsign" type="checkbox"> Я принимаю условия
<a href="http://shtraf.biz/doc_sogl.pdf" target="_blank">"Соглашения о предоставлении услуг"</a>
<br>
<?
		if($_SESSION['type']==_ZKZ_SHTRAF)
		{
?>
<br><input id="flmon" type="checkbox"<?echo $flmonSt;?>> Подписаться на сервис мониторинга штрафов
<?
		}
?>
<br><br>
<button id="buttnewsearch"><< Новый поиск</button>
<button id="buttnext">Дальше >></button>
<button id="buttedit" class="invisible"><< Редактировать Заказ</button>
<button id="buttpay" class="invisible">Перейти на оплату >></button>
<form name="fgp" id="fgp" method="post" action="createzkz.php"></form>
<br><br>
<span id="msgstr" class="ok"></span>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	var inputs=$('#paystbl input'),
    	sumpay,numpay="<?echo $numPay;?>",feesrv,
    	msgstr=$('#msgstr'),
        uinstr=$('#uinstr'),
    	client=$('#tblclient input'),
    	docsign=$('#docsign'),
    	flMon=$('#flmon'),
    	buttedit=$('#buttedit'),
    	buttpay=$('#buttpay'),
    	buttnext=$('#buttnext'),
    	buttnewsearch=$('#buttnewsearch'),
		loader=$('#loader'),
    	content=$('#content');

	// обработка клика по чекбоксу платежа из списка найденных платежей
	inputs.on('click',function(){
        var sumpay=numpay=feesrv=0;
        inputs.each(function() {
			if($(this).prop('checked')) {
    			sumpay+=($(this).attr('data-sum'))*1;
                feesrv+=($(this).attr('data-feesrv'))*1;
                numpay++;
			}
        	uinstr.html('УИН к оплате: '+numpay+', на сумму: '+Math.round(sumpay*100)/100+' руб.'+' Комиссия: '+Math.round(feesrv*100)/100+' руб.');
		});
	});

    // обработка нажатия кнопки "Дальше >>"
	buttnext.on('click',function(){
		var name1=$('#name1'),
        	name2=$('#name2'),
            email=$('#email');

        msgstr.attr('class','err');

		if(numpay==0) {
			msgstr.html('Выберите хотя бы один УИН для оплаты');
		}
		else
		if(name1.val()=="") {
			msgstr.html('Введите Фамилию Плательщика');
			name1.focus();
		}
		else
		if(name2.val()=="") {
			msgstr.html('Введите Имя Плательщика');
            name2.focus();
		}
		else
		if(email.val()=="") {
			msgstr.html('Введите адрес электронной почты Плательщика');
            email.focus();
		}
		else
		if(!docsign.prop('checked')) {
			msgstr.html('Вам необходимо прочитать и подписать (установить галочку) "Соглашение о предоставлении услуг"');
		}
		else {
            fldArr={};
            inputs.each(function(){
				if(!$(this).prop('checked')){
					// передаем информацию о НЕвыбранных платежах
                    fldArr[this.id]=1;
    			}
                $(this).prop('disabled',true);
			});
            client.each(function(){
				if($(this).val()!="") {
                    fldArr[this.id]=$(this).val();
    			}
                $(this).prop('disabled',true);
			});

			docsign.prop('disabled',true);
            flMon.prop('disabled',true);

            fldArr['flmon']=flMon.prop('checked')?1:0;
			fldArr['onlysave']=1;
   			$.ajax({
			   	url: 'createzkz.php',
		    	type: 'post',
				cache: false,
				data: {fld:fldArr},
                error: function(data) {},
				success: function(data){
                    buttnext.hide();
                    buttnewsearch.hide();
                    buttedit.show();
                    buttpay.show();
                    msgstr.attr('class','ok').html('Данные успешно проверены. Нажмите кнопку "Перейти на оплату >>" для того чтобы оплатить Ваш Заказ.');
				}
			});
		}
		return false;
	});

    // обработка нажатия кнопки "<< Новый поиск"
	buttnewsearch.on('click',function(){
  		if(confirm('При нажатии кнопки "Ок" все найденные платежи и введенные данные не сохранятся.\n\nВы уверены что хотите провести новый поиск ?')){
   	    	content.load('main.php');
		}
	});

	// обработка нажатия кнопки "<< Редактировать Заказ"
    buttedit.on('click',function(){
        msgstr.html("");
        inputs.each(function() {
	        $(this).prop('disabled',false);
		});
        client.each(function() {
		    $(this).prop('disabled',false);
		});
		docsign.prop('disabled',false);
        flMon.prop('disabled',false);
        $(this).hide();
        buttpay.hide();
        buttnext.show();
        buttnewsearch.show();
	});

    // обработка нажатия кнопки "Перейти на оплату >>"
	buttpay.on('click',function(){
		if(confirm('При нажатии "Ок" - редактирование Заказа станет невозможным. Вы перейдете на страницу оплаты, где сможете оплатить Заказ удобным Вам способом (банковской картой, WebMoney, Яндекс.Деньги, QIWI, Монета.ру, Сбербанк-онлайн, Альфа-клик, МСТ, Билайн и другими способами.\n\nВы хотите перейти на оплату Заказа ?')){
        	buttedit.hide();
           	$(this).hide();
	        msgstr.attr('class','ok').html('Создание Заказа. Пожалуйста, подождите...');
  			$("#fgp").attr('target','_blank').submit();
            setTimeout(function upd(){
				content.load('loadzkz.php');
			},5000);
        }
	});
});
</script>
<?
	}
	else
	{
?>
<button id="buttnewsearch"><< Новый поиск</button>
<br><br>
<span class="err">Ничего не найдено ...<br>Рекомендуем повторить поиск через некоторое время.</span>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$('#buttnewsearch').on('click',function(){
    	$('#content').load('main.php');
	});
});
</script>
<?
	}
?>
