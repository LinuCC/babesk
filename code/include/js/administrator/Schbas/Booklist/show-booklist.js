$(document).ready(function() {

	(function(){
		var activePage = 1;
		var amountPages = 0;

		$('[title]').tooltip();
		newDataFetch();

		// When searching or entering a new row-count, refresh on enter
		$('#books-per-page, #filter').on('keyup', function(ev) {
			activePage = 1;   //Reset pagenumber
			ev.preventDefault();
			if(ev.which == 13) {
				newDataFetch();
			}
		});

		function newDataFetch() {

			var pagenumber = activePage;

			$.postJSON(
				"index.php?module=administrator|Schbas|Booklist&action=fetchBooklist",
				{
					"pagenumber": pagenumber - 1,
					"booksPerPage": $('#books-per-page').val()
				},
				function(res) {
					console.log(res);
					tableRefresh(res.books);
					paginatorRefresh(res.pagecount);
					$('[title]').tooltip();
				}
			);
		};

		function tableRefresh(books) {

			function td(el) {
				return "<td>" + el + "</td>";
			}

			var tbody = $('#booklist tbody');
			$('#booklist tbody').html('');
			$.each(books, function(ind, book) {
				console.log(book);

				var row = microTmpl($('#booklist-row-template').html(), book)
				tbody.append(row);
			});
		};

		function paginatorRefresh(pagecount) {

			var amountPagesDisplayed = 9;
			var startPage = activePage - Math.floor(amountPagesDisplayed / 2);
			if(startPage < 1) {
				startPage = 1;
			}
			var html = microTmpl(
				$('#paginator-template').html(),
				{
					"startPage": startPage,
					"pagecount": pagecount,
					"amountDisplayed": amountPagesDisplayed
				}
			);
			$('#page-select').html(html);
		};

	})();

});
