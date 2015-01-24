{extends file=$inh_path}{block name=content}

<h3 class="module-header">Sprechtag-Wahlen Menü</h3>

<fieldset>
	<legend>Aktionen</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Elawa|Meetings|GenerateBare">
				Blanke Sprechtag-Wahlen generieren
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Elawa|Meetings|ChangeDisableds">
				Pausen setzen
			</a>
		</li>
	</ul>
</fieldset>

{/block}