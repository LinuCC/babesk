{extends file=$retourParent}{block name=content}


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
			var ajaxDisplay = document.getElementById('booklist');
			ajaxDisplay.innerHTML = ajaxRequest.responseText;
		}
	}
	var barcode = document.getElementById('barcode').value;
	var queryString = "inventarnr=" + barcode;
	ajaxRequest.open("GET", "http://localhost/babesk/code/administrator/index.php?section=Schbas|Retour&" + queryString, true);
	ajaxRequest.send(null); 
}

//-->
</script>

<form name='barcode_scan'>
Inventarnummer: <input type='text' id='barcode' /> <br />
<input type='button' onclick='ajaxFunction()' value='Abfragen' />
</form>


<div id="booklist">	
	{foreach $data as $retourbook}
		{$retourbook.subject} {$retourbook.year_of_purchase} {$retourbook.class} {$retourbook.bundle} / {$retourbook.exemplar}<br />
	{/foreach}
{/block}