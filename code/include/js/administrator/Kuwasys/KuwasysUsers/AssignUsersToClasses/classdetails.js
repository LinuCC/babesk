$(document).ready(function() {

	//classId defined in actual tpl

	$('#moveStatusDialogSubmit').on('click', function(ev) {
		$('#moveStatusDialog').modal('hide');
		changeStatus(
			$('#moveStatusDialog').attr('userid'),
			classId,
			$('#moveStatusDialog select[name=status] option:selected').val());
	}),

	$('#moveClassDialogSubmit').on('click', function(ev) {
		$('#moveClassDialog').modal('hide');
		console.log($('select[name=class] option:selected').val());
		var newClass = JSON.parse($('select[name=class] option:selected').val());
		if(!newClass) {
			toastr.error('Ein Fehler ist beim parsen des neuen Kurses aufgetreten');
			return false;
		}
		changeClass(
			$('#moveClassDialog').attr('userid'),
			classId,
			newClass
		);
	});

	$('#addUserDialogSubmit').on('click', function(ev) {
		addUser(
			$('input[name=username]').val(),
			classId,
			$('#addUserDialog select[name=status] option:selected') .val()
		);
	});


	$('#inputUsername').autocomplete({
		source: "index.php?module=administrator|System|User|JsSearchForUsername",
	});

	/**
	 * Fetches the table-data from the server and refills the tables
	 * @param  {int}    classId The id of the class
	 */
	var fetch = function(classId, categoryId) {

		$.postJSON(
			'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|ClassdetailsGet',
			{
				'classId': classId,
				'categoryId': categoryId
			},
			function(res) {
				console.log(res);
				tablesClear();
				tablesFill(res);
			}
		);
	};


	var tablesFill = function(data) {
		for(var statusIndex in data) {
			var $container = $('table#' + statusIndex + 'Users tbody');
			//Set count of how many users are in each status
			$('.user-count-' + statusIndex).html(data[statusIndex].length + ' ');
			$container.html('');   //Remove void table-entry added in tablesClear
			for(var requestIndex in data[statusIndex]) {
				request = data[statusIndex][requestIndex];
				var rowHtml = microTmpl($('#rowTemplate').html(), request);
				$container.append(rowHtml);
			}
		}
		$('table').tooltip({
			selector: 'button.moveStatus, button.moveClass'
		});
	};


	var tablesClear = function() {
		// Reset the count of the Categories
		$('[class^="user-count-"]').html('0 ');
		// Clear the table
		$('table').html(
			'<thead><tr>\
			<th>Name</th>\
			<th>Klasse</th>\
			<th>Wahlstatus</th>\
			<th>Anderer Kurs desselben Tages</th>\
			<th>Optionen</th>\
			</tr></thead><tbody><td colspan="5">---</td></tbody>'
		);
	};


	$('table').on('click', 'button.moveStatus', function(event) {
		$('#moveStatusDialog').attr('userid', $(this).attr('userid'));
	});


	$('table').on('click', 'button.moveClass', function(event) {
		$('#moveClassDialog').attr('userid', $(this).attr('userid'));
	});

	var changeStatus = function(userId, classId, statusname) {

		$.postJSON(
			'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|ChangeStatusOfUser',
			{
				'userId': userId,
				'classId': classId,
				'statusname': statusname
			},
			function(res) {

				if(res.value == 'success') {
					toastr['success'](res.message);
					fetch(classId, categoryId);
				}
				else if(res.value == 'error') {
					toastr['error'](res.message);
				}
			}
		);
	};


	var changeClass = function(userId, classId, newClass) {

		$.postJSON(
			'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|ChangeClassOfUser',
			{
				'userId': userId,
				'classId': classId,
				'newClassId': newClass.classId,
				'newClassCategoryId': newClass.categoryId
			},
			function(res) {
				if(res.value == 'success') {
					toastr['success'](res.message);
					fetch(classId, categoryId);
				}
				else if(res.value == 'error') {
					toastr['error'](res.message);
				}
			}
		);
	};


	var addUser = function(username, classId, statusname) {

		$.postJSON(
			'index.php?module=administrator|Kuwasys|KuwasysUsers|AssignUsersToClasses|AddUserToClass',
			{
				'username': username,
				'classId': classId,
				'statusname': statusname,
			},
			function(res) {

				if(res.value == 'success') {
					toastr['success'](res.message);
					fetch(classId, categoryId);
				}
				else if(res.value == 'error') {
					toastr['error'](res.message);
				}
			}
		);
	};


	fetch(classId, categoryId);
});
