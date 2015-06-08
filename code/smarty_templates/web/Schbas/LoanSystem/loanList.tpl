{extends file=$inh_path}{block name=content}

<div align="center"><h2>Ausgeliehene Lernmittel</h2></div>

<div id="booklist">
	<ul class="list-group">
		{foreach $data as $retourbook}
			<li class="list-group-item">
				{$retourbook.title},
				{$retourbook.author},
				{$retourbook.publisher}.
				Inv.-Nr.: {$retourbook.subject}
					{$retourbook.year_of_purchase}
					{$retourbook.class}
					{$retourbook.bundle} / {$retourbook.exemplar}
			</li>
		{/foreach}
	</ul>
</div>

<a href="index.php?module=web|Schbas|LoanSystem&amp;action=showPdf"
	class="btn btn-default">
	Informationsschreiben herunterladen
</a>


{/block}