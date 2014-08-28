$(document).ready(function() {

	users = [
		{
			forename: 'Hans',
			name: 'Peter',
			username: 'schinken',
			cardnumber: '239048723'
		}
	];
	tableFill(users);
	dataFetch();

	function tableFill(users) {
		var html = microTmpl(
			$('#user-table-template').html(),
			users
		);
		$('#user-table').html(html);
	};

	function dataFetch() {

		var filter = $('#filter').val();
		$.postJSON(
			'index.php?module=administrator|Babesk|Recharge|RechargeCard',
			{
				'filter': filter
			},
			success
		);

		function success(res) {
			console.log(res);
			toastr.success('yay!');
		};
	};
});