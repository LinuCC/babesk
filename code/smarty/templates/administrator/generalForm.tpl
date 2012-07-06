{extends file=$inh_path} {block name='content'}

<!-- Template-File for those pesky repeating Forms needed for the modules in Admininstrator 
	If you need more GET-Variables, just extend the action-string with whatever you feel like-->

<h2 class='moduleHeader'>{$headString}</h2>

<form action='index.php?section={$sectionString}&action={$actionString}' method='post'>
	{foreach $inputContainer as $input}
		<label>
			{$input.displayName}
			<input 
				type='{$input.type}' 
				name='{$input.name}' 
				{if isset($input.value)}
					value='{$input.value}'
				{/if}
			>
		</label><br><br>
	{/foreach}
	<input type='submit' value='{$submitString}'>
</form>

{/block}