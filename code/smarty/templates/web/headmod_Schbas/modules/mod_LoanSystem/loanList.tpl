{include file='web/header.tpl' title='Schulbuchausleihe'}

<div align="center"><h2>Ausgeliehene Lernmittel</h2></div>

<div id="booklist">	
	<ul>
	{foreach $data as $retourbook}
	<li  style="list-style:outside; list-style-type:disc;">	{$retourbook.title}, {$retourbook.author}, {$retourbook.publisher}. Inv.-Nr.: {$retourbook.subject} {$retourbook.year_of_purchase} {$retourbook.class} {$retourbook.bundle} / {$retourbook.exemplar}</li>
	{/foreach}
	</ul>
</div>


{include file='web/footer.tpl'}