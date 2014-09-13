{extends file=$base_path}
{block name=content}

<h3 class="module-header">Ausleihliste</h3>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Ausleihliste für: {$user->getForename()} {$user->getName()}
		</h3>
	</div>
	<ul class="list-group checklist">
		{if $formSubmitted}
			<li class="list-group-item list-group-item-success">
				<span class="icon icon-checkmark pull-left"></span>
				Das Formular zur Buchausleihe wurde abgegeben.
			</li>
			{*
			 * userPaid and userSelfpayer can only be checked if form was submitted
			 *}
			{if $userPaid}
				<li class="list-group-item list-group-item-success">
					<span class="icon icon-checkmark pull-left"></span>
					Der Benutzer hat für die Buchausleihe bezahlt.
				</li>
			{else}
				{if $loanChoice == 'ls'}
					<li class="list-group-item list-group-item-warning">
						<span class="icon icon-info pull-left"></span>
						Der Benutzer ist Selbsteinkäufer!
					</li>
				{else if $loanChoice == 'nl'}
					<li class="list-group-item list-group-item-warning">
						<span class="icon icon-info pull-left"></span>
						Keine Teilnahme des Benutzers!
					</li>
				{else}
					<li class="list-group-item list-group-item-danger">
						<span class="icon icon-error pull-left"></span>
						Der Benutzer hat nicht genug für die Bücher bezahlt!
					</li>
				{/if}
			{/if}
		{else}
			<li class="list-group-item list-group-item-danger">
				<span class="icon icon-error pull-left"></span>
				Das Formular zur Buchausleihe wurde noch nicht abgegeben!
			</li>
		{/if}

		{if count($booksLent) == 0}
			<li class="list-group-item list-group-item-success">
				<span class="icon icon-checkmark pull-left"></span>
				Der Benutzer besitzt keine der ausgeliehenen Bücher mehr.
			</li>
		{else}
			<li class="list-group-item list-group-item-warning">
				<span class="icon icon-info pull-left"></span>
				Dem Benutzer sind noch folgende Bücher ausgeliehen:
				<ul class="lent-books-list">
					{foreach $booksLent as $book}
						<li>
							{$book->getTitle()}
						</li>
					{/foreach}
				</ul>
			</li>
		{/if}
		{if count($booksSelfpaid) == 0}
			<li class="list-group-item list-group-item-success">
				<span class="icon icon-checkmark pull-left"></span>
				Der Benutzer kauft keine Bücher selber ein.
			</li>
		{else}
			<li class="list-group-item list-group-item-info">
				<span class="icon icon-info pull-left"></span>
				Der Benutzer kauft folgende Bücher selber ein:
				<ul class="selfbuy-books-list">
					{foreach $booksSelfpaid as $book}
						<li>
							{$book->getTitle()}
						</li>
					{/foreach}
				</ul>
			</li>
		{/if}
	</ul>
	<div class="panel-body">
		<h5 class="books-to-loan-table">Auszugebende Bücher</h5>
	</div>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>Titel</th>
				<th>Publisher</th>
			</tr>
		</thead>
		<tbody>
			{foreach $booksToLoan as $book}
				<tr data-book-id="{$book.id}">
					<td>{$book.title}</td>
					<td>{$book.publisher}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<div class="panel-footer">
		<div class="input-group">
			<span class="input-group-addon">
				<span class="icon icon-Schbas"></span>
			</span>
			<input type="text" id="book-barcode" class="form-control"
				placeholder="Buchcode hier einscannen" autofocus />
			<span class="input-group-btn">
				<button id="book-barcode-submit" class="btn btn-default">
					Buch ausgeben
				</button>
			</span>
		</div>
		</div>
</div>

<a class="btn btn-primary pull-right"
	href="index.php?module=administrator|Schbas|Loan">
	Nächster Benutzer
</a>

<input type="hidden" id="user-id" value="{$user->getId()}" />

{/block}


{block name=style_include append}

<style type="text/css">

	ul.checklist span.icon {
		font-size: 24px;
		margin-top: -3px;
		margin-right: 10px;
	}

	table tbody span.icon {
		margin-right: 10px;
	}

	ul.lent-books-list, ul.selfbuy-books-list {
		margin-left: 10px;
		list-style: disc;
	}

	h5.books-to-loan-table {
		position: relative;
		top: 7px;
		right: 5px;
		padding: 0;
		margin: 0;
		font-weight: 600;
	}

</style>

{/block}

{block name=js_include append}

<script type="text/javascript">

$(document).ready(function(){

	$('#book-barcode').enterKey(barcodeSubmit);
	$('#book-barcode-submit').on('click', barcodeSubmit);

	function barcodeSubmit() {
		var barcode = $('input#book-barcode').val();
		console.log(barcode);
		var userId = $('#user-id').val();
		$.ajax({
			'type': 'POST',
			'url': 'index.php?module=administrator|Schbas|Loan&wacken',
			'data': {
				'barcode': barcode,
				'userId': userId
			},
			'dataType': 'json',
			'success': success,
			'error': error
		});

		function success(res) {

			console.log(res);
			var $row = $('table tr[data-book-id="' + res.bookId + '"]');
			$row.addClass('bg-success text-success');
			$row.children('td')
				.first()
				.prepend('<span class="icon icon-success"></span>');
			$('#book-barcode').focus().select();
		}

		function error(jqXHR) {

			console.log(jqXHR.responseText);
			if(jqXHR.status == 500) {
				if(typeof jqXHR.responseJSON !== 'undefined' &&
					typeof jqXHR.responseJSON.message !== 'undefined') {
					toastr.error(jqXHR.responseJSON.message, 'Fehler');
				}
				else {
					toastr.error('Ein Fehler ist beim Ausleihen aufgetreten');
				}
			}
			else {
				toastr.error('Konnte das Buch nicht ausleihen. Ein genereller Fehler \
					ist aufgetreten', 'Fehler (' + jqXHR.status + ') beim Ausleihen');
			}
			$('#book-barcode').focus().select();
		}
	};
});

</script>

{/block}