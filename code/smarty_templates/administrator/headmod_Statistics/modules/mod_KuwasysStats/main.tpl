{extends file=$inh_path} {block name='content'}

{literal}
<style type="text/css">

#main {
	width: 1000px;
}

</style>
{/literal}

<h2 class="module-header">Statistiken zu Kuwasys</h2>

<p>Wählen sie eine Statistikanzeige aus:</p>

<form action="index.php?section=Statistics|KuwasysStats&amp;action=chooseChart" method="POST">
	<select name="chartName" size="1">
		<option value="gradesChosen">
			Wahlen nach Klassen
		</option>
		<option value="gradelevelsChosen">
			Wahlen nach Jahrgängen
		</option>
		<option value="allUsersInSchooltypes">
			Wahlen nach Schultypen
		</option>
		<option value="usersChosenInSchoolyears">
			Schüler die gewählt haben in Schuljahre aufgeteilt
		</option>
		<option value="classesChosenInSchoolyears">
			Wahlen in Schuljahre aufgeteilt
		</option>
		<option value="byUsersChosenInSchooltypeAndYeargroup">
			Anzahl Wahlen in Schultypen und Jahrgängen
		</option>
	</select>
	<input type="submit" value="Statistik anzeigen" />
</form>

{if isset($chartName)}
<br /><br />
<img src="index.php?section=Statistics|KuwasysStats&amp;action=showChart&amp;chart={$chartName}" />
{/if}
{/block}