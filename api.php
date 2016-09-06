<?
if(!defined("_ZKZ_SHTRAF"))	define("_ZKZ_SHTRAF", "10");	// оплата штрафов ГИБДД
if(!defined("_ZKZ_NALOG"))	define("_ZKZ_NALOG", "21");		// оплата налогов

if(!defined("API_CHECK_PAY"))	define("API_CHECK_PAY", "1");	// провести поиск начислений
if(!defined("API_CREATE_ZKZ"))	define("API_CREATE_ZKZ", "2");	// создать заказ
if(!defined("API_GO_PAY"))		define("API_GO_PAY", "3");		// отправить Клиента на оплату
if(!defined("API_LOAD_ZKZ"))	define("API_LOAD_ZKZ", "4");	// проверить состояние заказа

ini_set('max_execution_time',210);  // не устанавливайте меньше! Запросы в систему ГИС ГМП отрабатывают долго! Ваш скрипт может "не дождаться".

////////////////////////////////////////////
// Класс "API shtraf.biz"  верс.1.01
// http://shtraf.biz/api.php
// (с) 2015 Федорук Александр fedl@mail.ru
// The MIT License (MIT)
////////////////////////////////////////////
class _API
{   private $url='https://www.elpas.ru/api.php';	// url для запроса
	private $id='R405031394139';	// id Партнера (номер R-кошелька WebMoney)
	private $api_key='Vbgs778ddHbg67h';	// ключ для подписи запросов (получите в техподдержке support@shtraf.biz)

	// создать подпись запроса
    function createHash($top)
	{   return md5($this->id.$top.$this->api_key);
	}

	// получить url для запроса
    function getUrl()
	{   return $this->url;
	}

	// Отправить запрос
	// На выходе: массив с результатом запроса
	function sendQUERY($dat)
	{	// добавляем к массиву с данными идентификационную информацию
        $dat['hash']=$this->createHash($dat['top']);
		if($dat['top']==API_CHECK_PAY||$dat['top']==API_CREATE_ZKZ)
		{   $dat['id']=$this->id;
		}
		if(isset($dat['l'])) $dat['l']=urlencode($dat['l']);
		$ch=curl_init($this->url);
        curl_setopt($ch,CURLOPT_TIMEOUT,210);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch,CURLOPT_HEADER,0);	// результат не включает полученные заголовки
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);	// возврат, а не вывод результата
		curl_setopt($ch,CURLOPT_POST,1);	// отправка http запроса методом POST
		curl_setopt($ch,CURLOPT_POSTFIELDS,$dat);	// массив, содержащий данные для HTTP POST запроса

		$res=curl_exec($ch);
        $curlErr=curl_errno($ch);
		curl_close($ch);
		if($curlErr!=0||$res=="")
		{	$ret['err']=-1;
			$ret['msg']='Ошибка. Нет связи с сервисом. Попробуйте повторить операцию позже';
		}
        else
		{   if($res)
			{   $ret= $dat['top']!=API_GO_PAY?json_decode($res,true):$res;
			}
        }

        return $ret;
	}
}
?>