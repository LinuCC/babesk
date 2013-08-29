$(document).ready(function() {

	var userIdToMove = 0;


	$('div#moveStatusDialog').dialog({
		autoOpen: false,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			"Status verändern": function() {
				changeStatus(userIdToMove, classId,
					$('#moveStatusDialog select[name=status] option:selected')
						.val());
				$(this).dialog("close");
			},
			"Abbrechen": function() {
				$(this).dialog("close");
			}
		}
	});


	$('div#moveClassDialog').dialog({
		autoOpen: false,
		height: 300,
		width: 800,
		modal: true,
		buttons: {
			"Kurs verändern": function() {
				changeClass(userIdToMove, classId,
					$('select[name=class] option:selected').val());
				$(this).dialog("close");
			},
			"Abbrechen": function() {
				$(this).dialog("close");
			}
		}
	});


	$('div#addUserDialog').dialog({
		autoOpen: false,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			"Benutzer hinzufügen": function() {
				addUser($('input[name=username]').val(),
					classId,
					$('#addUserDialog select[name=status] option:selected')
						.val());
				$(this).dialog("close");
			},
			"Abbrechen": function() {
				$(this).dialog("close");
			}
		}
	});


	$('#username').autocomplete({
		source: "index.php?module=administrator|System|User|JsSearchForUsername",
	});


	var fetch = function(classId) {
		$.ajax({
			type: 'POST',
			url: 'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|ClassdetailsGet',
			data: {
				'classId': classId
			},
			success: function(res) {
				console.log(res);
				try {
					var data = JSON.parse(res);
				} catch(e) {
					adminInterface.errorShow('Konnte die Serverantwort nicht verarbeiten!');
				}
				tablesClear();
				tablesFill(data);
			},
			error: function(res) {
				adminInterface.errorShow('Konnte den Server nicht erreichen!');
			}
		});
	};


	var tablesFill = function(data) {
		for(var statusIndex in data) {
			var container = $('table#' + statusIndex + 'Users');
			for(var requestIndex in data[statusIndex]) {
				request = data[statusIndex][requestIndex];
				container.append(
					'<tr><td>' + request.username + '</td><td>' +
					request.grade + '</td><td>' + request.origStatusname +
					'</td><td><a href="index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|Classdetails&classId='+
					request.otherClassId +'">' +
					request.otherClassLabel + '</a></td><td>' +
					'<a class="moveStatus" userId="' + request.userId +
					'" href="#">Status verändern</a>\
					<a class="moveClass" userId="' + request.userId +
					'" href="#">Kurs verändern</a>\
					</td></tr>'
					);
			}
		}
	};


	var tablesClear = function() {
		$('table.dataTable').html(
			'<tr>\
			<th>Name</th>\
			<th>Klasse</th>\
			<th>Wahlstatus</th>\
			<th>Anderer Kurs desselben Tages</th>\
			<th>Optionen</th>\
			</tr>'
			);
	};


	$('table').on('click', 'a.moveStatus', function(event) {

		userIdToMove = $(this).attr('userId');
		$('div#moveStatusDialog').dialog('open');

		event.preventDefault();

	});


	$('table').on('click', 'a.moveClass', function(event) {

		userIdToMove = $(this).attr('userId');
		$('div#moveClassDialog').dialog('open');

		event.preventDefault();

	});

	$('a#addUserToClass').on('click', function(event) {

		$('div#addUserDialog').dialog('open');
		event.preventDefault();

	});


	var changeStatus = function(userId, classId, statusname) {

		$.ajax({
			type: 'POST',
			url: 'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|ChangeStatusOfUser',
			data: {
				'userId': userId,
				'classId': classId,
				'statusname': statusname,
			},

			success: function(res) {

				try {
					data = JSON.parse(res);
				} catch(e) {
					adminInterface.errorShow(
						'Konnte die Serverantwort nicht verarbeiten');
					return false;
				}

				if(data.value == 'success') {
					adminInterface.successShow(data.message);
					fetch(classId);
				}
				else if(data.value == 'error') {
					adminInterface.errorShow(data.message);
				}
				else {
					adminInterface.errorShow(
						'Konnte die Serverantwort nicht verarbeiten');
				}
			},

			error: function(res) {
				adminInterface.errorShow(
					'Konnte nicht zur Datenbank verbinden');
			}
		});
	};


	var changeClass = function(userId, classId, newClassId) {

		$.ajax({
			type: 'POST',
			url: 'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|ChangeClassOfUser',
			data: {
				'userId': userId,
				'classId': classId,
				'newClassId': newClassId,
			},

			success: function(res) {

				try {
					data = JSON.parse(res);
				} catch(e) {
					adminInterface.errorShow(
						'Konnte die Serverantwort nicht verarbeiten');
					return false;
				}

				if(data.value == 'success') {
					adminInterface.successShow(data.message);
					fetch(classId);
				}
				else if(data.value == 'error') {
					adminInterface.errorShow(data.message);
				}
				else {
					adminInterface.errorShow(
						'Konnte die Serverantwort nicht verarbeiten');
				}
			},

			error: function(res) {
				adminInterface.errorShow(
					'Konnte nicht zur Datenbank verbinden');
			}
		});
	};


	var addUser = function(username, classId, statusname) {

		$.ajax({
			type: 'POST',
			url: 'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|AddUserToClass',
			data: {
				'username': username,
				'classId': classId,
				'statusname': statusname,
			},

			success: function(res) {

				console.log(res);

				try {
					data = JSON.parse(res);
				} catch(e) {
					adminInterface.errorShow(
						'Konnte die Serverantwort nicht verarbeiten');
					return false;
				}

				if(data.value == 'success') {
					adminInterface.successShow(data.message);
					fetch(classId);
				}
				else if(data.value == 'error') {
					adminInterface.errorShow(data.message);
				}
				else {
					adminInterface.errorShow(
						'Konnte die Serverantwort nicht verarbeiten');
				}
			},

			error: function(res) {
				adminInterface.errorShow(
					'Konnte nicht zur Datenbank verbinden');
			}
		});
	};


	fetch(classId);
});
