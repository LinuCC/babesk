{extends file=$inh_path} {block name='content'}

<h2>
	Schuljahreswechsel
</h2>

<p>
	Bitte wählen sie ein Schuljahr aus:
</p>

<form action="index.php?module=administrator|System|Schoolyear|SwitchSchoolyear|Upload"
	method="POST">

	<select name="schoolyearId">
		{foreach $schoolyears as $schoolyear}
		<option value="{$schoolyear.ID}">
			{$schoolyear.label}
		</option>
		{/foreach}
	</select>
	<input type="submit" value="Schuljahr verändern" />
</form>

{/block}
