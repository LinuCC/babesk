{extends file=$inh_path}{block name='content'}

<h2 class="moduleHeader">Vorschau Kurs-Importierung</h2>

{if count($classes)}
<form action="index.php?module=administrator|Kuwasys|Classes|CsvImport|ImportExecute"
	method="post">
	<table class="dataTable">
		{$tempId = 1}
		{foreach $classes as $class}
		<tr>
			<th>
				{t}Name{/t}
			</th>
			<th>{$class.name}</th>
			<input type="hidden" name="classes[{$tempId}][name]"
				value="{$class.name|escape}">
			<input type="hidden" name="classes[{$tempId}][description]"
				value="{$class.description|escape}">
			<input type="hidden" name="classes[{$tempId}][maxRegistration]"
				value="{$class.maxRegistration}">
		</tr>

		<tr>
			<td>
				{t}Classteacher{/t}
			</td>
			<td>
				{* For every Classteacher *}
				{foreach name=cts from=$class.classteacher  key=ctKey item=ct}
					{if $ct.displayOptions == 1}
						{* Classteacher was not found, show alternative Options to User *}
						{if !empty($ct.origName)}
							(Eingabe: "{$ct.origName}")<br />
							<input type="hidden" value="{$ct.origName|escape}"
									name="classes[{$tempId}][classteacher][{$ctKey}][name]" >
						{/if}
						<input type="radio"
							name="classes[{$tempId}][classteacher][{$ctKey}][ID]"
							value="CREATE_NEW" checked >Kursleiter neu erstellen<br />
						<input type="radio"
							name="classes[{$tempId}][classteacher][{$ctKey}][ID]"
							value="0" >Kein Kursleiter<br />
						{if $ct.name}
							<input type="radio"
								name="classes[{$tempId}][classteacher][{$ctKey}][ID]"
								value="{$ct.ID}">
								{$ct.name}
						{/if}
					{else}
						{* Classteacher was found, just show him *}
						{$ct.name}
						<input type="hidden"
							name="classes[{$tempId}][classteacher][{$ctKey}][ID]"
							value="{$ct.ID}">
					{/if}
					{if not $smarty.foreach.cts.last}
						<hr>
					{/if}
				{/foreach}
			</td>
		</tr>
		<tr>
			<td>
				{t}Day{/t}
				{if $class.classUnitOption.origName}
				<br />(Eingabe: "{$class.classUnitOption.origName}")
				{/if}
			</th>
			<td>
				{if $class.classUnit}
					{$class.classUnit.name}
					<input type="hidden" name="classes[{$tempId}][classUnit]"
						value="{$class.classUnit.ID}">
				{else}
					<input type="radio" name="classes[{$tempId}][classUnit]"
						value="0" checked >Kein Tag<br />
					{if $class.classUnitOption.ID}
					<input type="radio" name="classes[{$tempId}][classUnit]"
						value="{$class.classUnitOption.ID}">
						{$class.classUnitOption.name}
					{/if}
				{/if}
			</td>
		</tr>
		{$tempId = $tempId + 1}
		{/foreach}
	</table>
	<input type="submit" value="{t}execute Changes{/t}">
</form>
{else}
	<p>
		{t}The uploaded file did not contain any usable data.{/t}
		<a href="index.php?administrator|Kuwasys|Classes|CsvImport">
			{t}click here to go back{/t}
		</a>
	</p>
{/if}

{/block}