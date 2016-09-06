<?	// http://shtraf.biz/api.php
    // (с) 2015 Федорук Александр fedl@mail.ru
	// The MIT License (MIT)
	session_start();
	require_once('api.php');
	require('db.php');
	if(isset($_SESSION['l'])) 	unset($_SESSION['l']);
    if(isset($_SESSION['acc'])) unset($_SESSION['acc']);
    if(isset($_SESSION['zn'])) 	unset($_SESSION['zn']);
    if(isset($_SESSION['pin'])) unset($_SESSION['pin']);
    if(isset($_SESSION['type'])) unset($_SESSION['type']);
?>
<table id="tblselect">
  <td>
    <input type="radio" name="selsrch" id="selshtraf" checked>
  </td>
  <td><div class="quest"></div></td>
  <td><label for="selshtraf">Искать штрафы ГИБДД</label>
  </td>
  </tr>

</table>
<br>
<span id="msgstr" class="err"></span>
<br><br>
<?php session_start(); ?>
<table id="tblshtraf">
  <tr>
    <td>Номер ВУ</td>
    <td>
	<input type="text" id="vu" size="12" maxlength="12" value="<?php echo $_SESSION['dlic']; ?>">
    </td>
  </tr>
  <tr>
    <td>Номер СТС</td>
    <td>
      <input type="text" id="sts" size="12" maxlength="12">
    </td>
  </tr>
  <tr>
    <td>или</td>
    <td><br></td>
  </tr>
  <tr>
    <td>
	Номер Постановления</td>
    <td>
      <input type="text" id="ps" size="22" maxlength="25">
    </td>
  </tr>
</table>

<br>

<button id="buttsearch" action="dbget.php" method="POST">Поиск >></button>
<script language="javascript" type="text/javascript">
$(document).ready(function()
{   if(intID!=0) { clearTimeout(intID); intID=0; }

	var selshtraf=$('#selshtraf');
    var selnalog=$('#selnalog');
    var tblshtraf=currtbl=$('#tblshtraf');
	var tblnalog=$('#tblnalog');
	var msgstr=$('#msgstr');
	var type="<?echo _ZKZ_SHTRAF;?>";

	selshtraf.on('click',function(){
        tblshtraf.show();
        tblnalog.hide();
        currtbl=tblshtraf;
        msgstr.html("");
        type="<?echo _ZKZ_SHTRAF;?>";
	});

    selnalog.on('click',function(){
        tblshtraf.hide();
        tblnalog.show();
        currtbl=tblnalog;
        msgstr.html("");
        type="<?echo _ZKZ_NALOG;?>";
	});

    $('#buttsearch').on('click',function(){
		fldArr={};
  		currtbl.add('input').each(function() { if($(this).val()!="") fldArr[this.id]=$(this).val(); });
		if(type=="<?echo _ZKZ_SHTRAF;?>"&&!fldArr['vu']&&!fldArr['sts']&&!fldArr['ps']) {
            msgstr.html("Введите номер ВУ и/или СТС или номер Постановления<br>");
		}
		
		else {
            msgstr.attr('class','ok').html("Пожалуйста, подождите. Производится поиск...<br><br>");
            $(this).hide();
            $('#loader').show();
			fldArr['type']=type;
			$.ajax(
			{	url: 'checkpay.php',
			    type: 'post',
				cache: false,
				data: {fld:fldArr},
                error: function(data) { $('#loader').hide(); },
				success: function(data)	{
					$('#loader').hide();
					$('#content').html(data);
				}
			});
		}
    	return false;
	});
});
</script>
