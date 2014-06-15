{extends file=$retourParent}{block name=content}

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

<script language="javascript" type="text/javascript">
<!-- 
//Browser Support Code
function ajaxFunction(){
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			
			var barcodeField = document.getElementById('barcode');
			barcodeField.value = '';
		
			var ajaxDisplay = document.getElementById('booklist');
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
		}
	}
	
	var barcode = document.getElementById('barcode').value;
	var queryString = "inventarnr=" + encodeURIComponent(barcode) + "&card_ID={$cardid}&uid={$uid}&ajax=1";
	
	ajaxRequest.open("GET", "http://{$adress}" + queryString, true);
	
	ajaxRequest.send(null); 
}

//-->
</script>

<div align="center"><h2>Ausleihliste f&uuml;r: {$fullname}</h2></div>
<hr>
<form name='barcode_scan' onsubmit='return false;' />
	Inventarnummer: <input type='text' id='barcode' onKeyPress='if(enter_pressed(event)){ ajaxFunction(); }'/> <br />
</form>




<div id="booklist">	
	{foreach $data as $retourbook}
		{$retourbook.title}, {$retourbook.author}, {$retourbook.publisher}. Inv.-Nr.: {$retourbook.subject} {$retourbook.year_of_purchase} {$retourbook.class} {$retourbook.bundle} / {$retourbook.exemplar}<br />
	{/foreach}
</div>
{/block}