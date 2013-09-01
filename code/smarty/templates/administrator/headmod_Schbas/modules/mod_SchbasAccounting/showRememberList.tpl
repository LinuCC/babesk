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


<h2 class='moduleHeader'>Liste der ausgeliehenden B&uuml;cher</h2>

<table class="dataTable">
	<tr>
		<th align="center">Sch&uuml;ler</th>
		<th align="center">Klasse</th>
		<th align="center">Ausgeliehende B&uuml;cher</th>
		<th align="center">Datum</th>
	</tr>
	
	{for $i=0 to $schuelerTotalNr}
	<tr>
		<td align="left">{$schueler1[$i]}</td>
		<td align="center">{$class[$i]}</td>
		<td align="left">{$title[$i]}</td>
		<td align="center">{$date[$i]}</td>
	</tr>
	{/for}
</table>


{/block}
