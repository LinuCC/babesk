{extends file=$inh_path}{block name='content'}

<h2 class="moduleHeader">Vorschau Kurs-Importierung</h2>

{if count($classes)}
<table class="dataTable">
	{$tempId = 1}
	{foreach $classes as $class}
	<tr>
		<th align='center'>
			{_g('Name')}
		</th>
		<th align='center'>{$class.name}</th>
		<input type="hidden" name="class_{$tempId}_name" value="{$class.name}">
	</tr>
	<tr>
		<td align='center'>
			{_g('Classteacher')}
			{if $class.classteacherOption.origName}
			<br />(Eingabe: "{$class.classteacherOption.origName}")
			{/if}
		</th>

		<td align='center'>
			{if $class.classteacher}
				{$class.classteacher.name}
				<input type="hidden" name="class_{$tempId}_classteacher"
					value="{$class.classteacher.ID}">
			{else}
				<input type="radio" name="class_{$tempId}_classteacher"
					value="0" checked >Kein Kursleiter<br />
				{if $class.classteacherOption.ID}
					<input type="radio" name="class_{$tempId}_classteacher"
						value="{$class.classteacherOption.ID}">
						{$class.classteacherOption.name}
				{/if}
			{/if}
		</td>
	</tr>
	<tr>
		<td align='center'>
			{_g('Day')}
			{if $class.classUnitOption.origName}
			<br />(Eingabe: "{$class.classUnitOption.origName}")
			{/if}
		</th>
		<td align='center'>
			{if $class.classUnit}
				{$class.classUnit.name}
				<input type="hidden" name="class_{$tempId}_classUnit"
					value="{$class.classUnit.ID}">
			{else}
				<input type="radio" name="class_{$tempId}_classUnit"
					value="0" checked >Kein Tag<br />
				{if $class.classUnitOption.ID}
				<input type="radio" name="class_{$tempId}_classUnit"
					value="{$class.classUnitOption.ID}">
					{$class.classUnitOption.name}
				{/if}
			{/if}
		</td>
	</tr>
	{$tempId = $tempId + 1}
	{/foreach}
</table>
{else}
	<p>{_g('No changes will be executed.')}</p>
{/if}

<form action="" method="post">
	<input type="submit" value="{_g('execute Changes')}">
</form>

{/block}