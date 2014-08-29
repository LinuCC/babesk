$(document).ready(function() {

	activePage = 1;
	dataFetch();
	$pagination = $('#page-select');

	$pagination.bootpag({
		total: 1,
		page: activePage,
		maxVisible: 10
	}).on('page', activePageChange);


	function dataFetch() {

		var filter = $('#filter').val();
		$.postJSON(
			'index.php?module=administrator|Babesk|Recharge|RechargeCard',
			{
				'filter': filter,
				'activePage': activePage
			},
			success
		);

		function success(res) {
			console.log(res);
			tableFill(res.users);
			pageSelectorUpdate(res.pagecount);
		};
	};

	function tableFill(users) {

		var html = microTmpl(
			$('#user-table-template').html(),
			{'users': users}
		);
		$('#user-table').html(html);
	};

	function pageSelectorUpdate(pagecount) {
		$pagination.bootpag({
			total: pagecount,
			page: activePage,
			maxVisible: 10
		});
	}

	function activePageChange(event, pagenum) {
		activePage = pagenum;
		dataFetch();
	}
});