$(document).ready(function() {

	var fetch = function(classId) {
		$.ajax({
			type: 'POST',
			url: 'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|ClassdetailsGet',
			data: {
				'classId': classId
			},
			success: function(res) {
				// console.log(res);
				try {
					var data = JSON.parse(res);
				} catch(e) {
					adminInterface.errorShow('Konnte die Serverantwort nicht verarbeiten!');
				}
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
				console.log(container);
				container.append(
					'<tr><td>' + request.username + '</td><td>' +
					request.grade + '</td><td>' + request.origStatusname +
					'</td><td>\
					<a class="moveStatus" userId="' + request.userId +
					'" href="#">Status verändern</a>\
					<a class="moveClass" userId="' + request.userId +
					'" href="#">Kurs verändern</a>\
					</td></tr>'
				);
			}
		}
	}

	var tablesClear = function(data) {
		for(var index in data) {
			var container = $('table#' + index + 'Users');
			container.html(
				'<tr>\
					<th>Name</th>\
					<th>Klasse</th>\
					<th>Wahlstatus</th>\
					<th>Optionen</th>\
				</tr>'
			);
		}
	}
	fetch(classId);
});
