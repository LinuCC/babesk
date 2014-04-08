$(document).ready(function() {

	bootbox.setDefaults({locale: 'de'});

	$('#resetAssignment').on('click', function(event) {
		event.preventDefault();
		bootbox.confirm(
			'Wenn sie bereits einen Zuweisungsprozess gestartet haben, werden die \
			Daten des Prozesses unwiederbringlich verloren gehen. Sind sie sicher?',
			function(res) {
				if(res) {
					window.location.href = 'index.php?module=administrator|Kuwasys|\
						KuwasysUsers|AssignUsersToClasses|Reset';
				}
			}
		);
	});

	$('#applyAssignment').on('click', function(event) {
		event.preventDefault();
		bootbox.confirm(
			'Wenn sie die Zuweisungen durchführen, werden die in diesem Modul \
			Temporär durchgeführten Veränderungen auf die Nutzer angewendet und \
			die Nutzer sind dann offiziell in den hier zugewiesenen Kursen. Kleine \
			Veränderungen können aber auch im Kurs-Modul nachher noch durchgeführt \
			werden.',
			function(res) {
				if(res) {
					window.location.href = 'index.php?module=administrator|Kuwasys|\
					KuwasysUsers|AssignUsersToClasses|ApplyChanges';
				}
			}
		);
		$('#confirmAssignment').dialog('open');
	});
});
