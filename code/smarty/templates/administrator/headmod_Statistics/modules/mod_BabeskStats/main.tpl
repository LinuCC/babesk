{extends file=$inh_path} {block name='content'}

{literal}
<style type="text/css">

#main {
	width: 1000px;
}

</style>
{/literal}

<h2 class="moduleHeader">Statistiken zu Babesk</h2>

<p>Wählen sie eine Statistikanzeige aus:</p>

<form action="index.php?section=Statistics|BabeskStats&amp;action=chooseChart" method="POST">
	<select name="chartName" size="1">
		
		<option value="gradelevelsChosen">
			Wahlen nach Jahrgängen
		</option>
		
	</select>
	<input type="submit" value="Statistik anzeigen" />
</form>

{if isset($chartName)}
<br /><br />
<img src="index.php?section=Statistics|BabeskStats&amp;action=showChart&amp;chart={$chartName}" />
{/if}
{/block}