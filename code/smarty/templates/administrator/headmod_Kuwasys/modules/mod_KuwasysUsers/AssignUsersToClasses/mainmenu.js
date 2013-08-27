$(document).ready(function() {

	translations = {
		"buttonNewProcessName": "{_g('Start a new Process')}",
		"buttonCancelName": "{_g('Cancel')}",
	};

	$('#confirmReset').dialog({
		modal: true,
		autoOpen: false,
		width:400,
		buttons: {
			"Zuweisungsprozess neu starten": function() {
				window.location.href = 'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Reset';
			},
			"Abbrechen": function() {

				$(this).dialog('close');
			}
		}
	});

	$('#resetAssignment').on('click', function(event) {
		event.preventDefault();
		$('#confirmReset').dialog('open');
	});
});
