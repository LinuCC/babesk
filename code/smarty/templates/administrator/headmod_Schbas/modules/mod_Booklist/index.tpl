{extends file=$booklistParent}{block name=content}

<form action="index.php?section=Schbas|Booklist&action={$action['show_booklist']}" method="post">
	<input type="submit" value="B&uuml;cherliste">
</form><br>
<form action="index.php?section=Schbas|Booklist&action={$action['add_book']}" method="post">
	<input type="submit" value="B&uuml;cher hinzuf&uuml;gen">
</form><br>





{/block}