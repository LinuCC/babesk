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

	$('#confirmAssignment').dialog({
		modal: true,
		autoOpen: false,
		width:400,
		buttons: {
			"Zuweisungen durchführen": function() {
				window.location.href = 'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|ApplyChanges';
				$(this).dialog('close');
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

	$('#applyAssignment').on('click', function(event) {
		event.preventDefault();
		$('#confirmAssignment').dialog('open');
	});
});
