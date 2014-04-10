$(document).ready(function() {

	$('button.delete-classteacher').on('click', function(event) {

		event.preventDefault();

		var toDelete = $(this).attr('classteacherId');

		bootbox.confirm(
			'Der Klassenlehrer wird dauerhaft gelöscht! Sind sie sich wirklich \
			sicher?',
			function(res) {
				if(res) {
					window.location = "index.php?module=administrator|Kuwasys\
					|Classteachers|Delete&ID=" + toDelete;
				}
			}
		);
	});
});