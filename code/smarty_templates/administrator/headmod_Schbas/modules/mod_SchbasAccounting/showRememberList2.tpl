{extends file=$inh_path} {block name='content'}

<style type='text/css'  media='all'>
/*Table should not be over the Border of the Line of the main-block*/
#main {
 width:1100px;
}

fieldset.selectiveLink {
 margin-left: 5%;
 margin-right: 5%;
 margin-bottom: 30px;
 border: 2px dashed rgb(100,100,100);
}

a.selectiveLink {
 padding: 5px;
}

.dataTable {
 margin: 0 auto;
}
</style>


<h2 class='module-header'>Liste der ausgeliehenen B&uuml;cher ({$className})</h2>

<p>{$listOfClasses}</p>

<table class="dataTable">
	<tr>
		<th align="center">Name</th>
		<th align="center">Vorname</th>
		<th align="center">Ausgeliehende B&uuml;cher</th>
	</tr>
	
	{for $i=0 to $nr}
	<tr>
		<td align="left">{$name[$i]}</td>
		<td align="left">{$forename[$i]}</td>
		<td align="left">{$books[$i]}</td>
	</tr>
	{/for}
</table>


{/block}
