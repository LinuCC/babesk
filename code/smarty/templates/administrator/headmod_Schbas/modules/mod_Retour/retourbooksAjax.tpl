

<div id="booklist">	
	{foreach $data as $retourbook}
		{$retourbook.subject} {$retourbook.year_of_purchase} {$retourbook.class} {$retourbook.bundle} / {$retourbook.exemplar}<br />
	{/foreach}
</div>