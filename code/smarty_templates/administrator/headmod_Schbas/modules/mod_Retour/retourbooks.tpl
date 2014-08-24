{extends file=$retourParent}{block name=content}


<h2 class="module-header">Ausleihliste f&uuml;r: {$fullname}</h2>

<form name='barcode_scan' onsubmit='return false;' />
	<div class="form-group">
		<label for="barcode">Inventarnummer</label>
		<input type='text' id='barcode' class="form-control" onKeyPress='if(enter_pressed(event)){ ajaxFunction(); }'/> <br />
	</div>
</form>

<div id="booklist">
	<table class="table table-responsive table-striped table-hover">
		<thead>
			<tr>
				<th>Titel</th>
				<th>Author</th>
				<th>Publisher</th>
				<th>Inventarnummer</th>
			</tr>
		</thead>
		<tbody>
			{foreach $data as $retourbook}
			<tr>
				<td>{$retourbook.title}</td>
				<td>{$retourbook.author}</td>
				<td>{$retourbook.publisher}</td>
				<td>
					{$retourbook.subject} {$retourbook.year_of_purchase} {$retourbook.class} {$retourbook.bundle} / {$retourbook.exemplar}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

	{*
	{foreach $data as $retourbook}
		{$retourbook.title}, {$retourbook.author}, {$retourbook.publisher}. Inv.-Nr.: {$retourbook.subject} {$retourbook.year_of_purchase} {$retourbook.class} {$retourbook.bundle} / {$retourbook.exemplar}<br />
	{/foreach}
	*}
</div>
{/block}

{block name=js_include}

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

{/block}