{extends file=$checkoutParent}{block name=content}


<h3>Antrag erfassen</h3>



		Barcode:<input id="barcodeInput" type="text" /><br />
		<small>Enter drücken, wenn Barcode eingescannt ist.</small>
	

<script type="text/javascript" language="JavaScript">

	
	$('#barcodeInput').on('keyup', function(event) {
	if(event.keyCode == 13) {
		sendUserReturnedBarcode($(this).val());
	}
	});
	
	
	function sendUserReturnedBarcode(barcode) {
	$.ajax({
		'type': 'POST',
		'url': 'index.php?section=Schbas|SchbasAccounting&action=userSetReturnedFormByBarcodeAjax',
		data: {
			'barcode': barcode
		},
		success: function(data) {
			if(data == 'error') {
				alert('Der Barcode ist nicht vollständig');
			}
			else if(data == 'entryNotFound') {
				alert('Der Link zwischen Nachricht und Benutzer konnte nicht gefunden werden');
			}
			else if(data == 'notValid') {
				alert('Der Barcode enthält inkorrekte Zeichen');
			}
			else if(data == 'dupe') {
				alert(unescape('Formular wurde bereits eingescannt. Bei %C4nderungen bitte zuerst l%F6schen!'));
			}
			else {
				alert('Einscannen erfolgreich!');
				location.reload();
			}
		},
		error: function(data) {
			alert('Ein Fehler ist beim Senden des Barcodes aufgetreten!');
		}
	});
}
	
</script>





{/block}