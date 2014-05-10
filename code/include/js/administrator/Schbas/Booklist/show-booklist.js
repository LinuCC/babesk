$(document).ready(function() {

	(function(){
		var activePage = 1;
		var amountPages = 0;

		newDataFetch();

		$('a').click(function(ev) {
			newDataFetch();
		});

		function newDataFetch() {

			$.postJSON(
				"index.php?module=administrator|Schbas|Booklist&action=fetchBooklist",
				{
					"pagenumber": 0,
					"booksPerPage": 10
				},
				function(res) {
					console.log(res);
					tableRefresh(res);
				}
			);
			// $.ajax({
			// 	"type": "POST",
			// 	url: "index.php?module=administrator|Schbas|Booklist&action=fetchBooklist",
			// 	data: {
			// 		"pagenumber": 0,
			// 		"booksPerPage": 10
			// 	},
			// 	success: function(res) {
			// 		console.log(res);
			// 		// tableRefresh();
			// 	}
			// });
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
		}

	})();

});
