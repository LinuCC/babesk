{extends file=$schbasSettingsParent}{block name=content}

{if $save == true}
<br><b>Die Einstellungen wurden erfolgreich gespeichert</b><br><br>
{/if}

<b>Geb&uuml;hren</b>

<form action="index.php?section=Schbas|SchbasSettings&action=5" method="post">
	<table border="1" width="400">
	<tr align=center>
		<th/>
		<th>normal</th>
		<th>erm&auml;&szlig;igt</th>
	</tr>
	<tr align=center>
		<td>5.Klasse</td>
		<td><input type="text" name="5norm" maxlength="5" size="5" value={$settings.0.fee_normal}></td>
		<td><input type="text" name="5erm" maxlength="5" size="5"  value={$settings.0.fee_reduced}></td>
	</tr>
	<tr align=center>
		<td>6.Klasse</td>
		<td><input type="text" name="6norm" maxlength="5" size="5"  value={$settings.1.fee_normal}></td>
		<td><input type="text" name="6erm" maxlength="5" size="5"  value={$settings.1.fee_reduced}></td>
	</tr>
	<tr align=center>
		<td>7.Klasse</td>
		<td><input type="text" name="7norm" maxlength="5" size="5"  value={$settings.2.fee_normal}></td>
		<td><input type="text" name="7erm" maxlength="5" size="5"  value={$settings.2.fee_reduced}></td>
	</tr>
	<tr align=center>
		<td>8.Klasse</td>
		<td><input type="text" name="8norm" maxlength="5" size="5"  value={$settings.3.fee_normal}></td>
		<td><input type="text" name="8erm" maxlength="5" size="5"  value={$settings.3.fee_reduced}></td>
	</tr>
	<tr align=center>
		<td>9.Klasse</td>
		<td><input type="text" name="9norm" maxlength="5" size="5"  value={$settings.4.fee_normal}></td>
		<td><input type="text" name="9erm" maxlength="5" size="5"  value={$settings.4.fee_reduced}></td>
	</tr>
	<tr align=center>
		<td>10.Klasse</td>
		<td><input type="text" name="10norm" maxlength="5" size="5"  value={$settings.5.fee_normal}></td>
		<td><input type="text" name="10erm" maxlength="5" size="5"  value={$settings.5.fee_reduced}></td>
	</tr>
	<tr align=center>
		<td>11.Klasse</td>
		<td><input type="text" name="11norm" maxlength="5" size="5"  value={$settings.6.fee_normal}></td>
		<td><input type="text" name="11erm" maxlength="5" size="5"  value={$settings.6.fee_reduced}></td>
	</tr>
	<tr align=center>
		<td>12.Klasse</td>
		<td><input type="text" name="12norm" maxlength="5" size="5"  value={$settings.7.fee_normal}></td>
		<td><input type="text" name="12erm" maxlength="5" size="5"  value={$settings.7.fee_reduced}></td>
	</tr>
	</table><br>
	<input id="submit" type="submit" value="Submit" />
</form>

{/block}