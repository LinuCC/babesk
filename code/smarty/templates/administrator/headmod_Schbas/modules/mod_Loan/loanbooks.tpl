{extends file=$loanParent}{block name=content}

<script language="javascript" type="text/javascript">
<!-- 
//influenced by www.tizag.com
function ajaxFunction(){
	var ajax;
	
	try{
		//others
		ajax = new XMLHttpRequest();
	} catch (e){
		// IE
		try{
			ajax = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajax = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				alert("Browser wird nicht unterstuetzt!");
				return false;
			}
		}
	}


	ajax.onreadystatechange = function(){
		if(ajax.readyState == 4){
		
			var barcodeField = document.getElementById('barcode');
			barcodeField.value = '';
			
			var ajaxDisplay = document.getElementById('booklist');
			ajaxDisplay.innerHTML = ajax.responseText;
			
			
		}
	}
	
	var barcode = document.getElementById('barcode').value;
	var queryString = "inventarnr=" + encodeURIComponent(barcode) + "&card_ID={$cardid}&uid={$uid}&ajax=1";
	ajax.open("GET", "http://{$adress}" + queryString, true);
	
	ajax.send(null); 
}

//-->
</script>

<script language="javascript" type="text/javascript">
<!-- 
//influenced by http://tommwilson.com
function enter_pressed(e){
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return false;
return (keycode == 13);
}

//-->
</script>

<div align="center"><h2>Ausleihliste f&uuml;r: {$fullname}</h2></div>
<hr>
<h3>{$alert}</h3>
<hr>
<form name='barcode_scan' onsubmit='return false;'>
<b>Bitte Barcode eingeben:</b> 
<input type='text' id='barcode' onKeyPress='if(enter_pressed(event)) ajaxFunction() '/> <br>
</form>
<hr>
<div align="center"><h3>Auszugebende B&uuml;cher</h3></div>
<div id='booklist'>
{foreach $data as $datatmp}
{$datatmp.title}, {$datatmp.publisher} <br>
{/foreach}
</div>
{/block}