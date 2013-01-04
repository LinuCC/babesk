

<div id="booklist">	
	{foreach $data as $retourbook}
		{$retourbook.title}, {$retourbook.author}, {$retourbook.publisher}. Inv.-Nr.: {$retourbook.subject} {$retourbook.year_of_purchase} {$retourbook.class} {$retourbook.bundle} / {$retourbook.exemplar}<br />
	{/foreach}
</div>