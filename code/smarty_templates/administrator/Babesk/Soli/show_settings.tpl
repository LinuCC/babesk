{extends file=$base_path}{block name=content}

<h2 class="module-header">{t}Soli-Settings{/t}</h2>

<form action="index.php?module=administrator|Babesk|Soli|Settings"
	class="simpleForm" method="post">

	<!--
	<fieldset class="smallContainer">
		<legend>{t}Enable Soli{/t}</legend>
		<div class="simpleForm">
			<label for="soliEnabled">
				{t}Is the Soli-Module enabled:{/t}
			</label>
			<input id="soliEnabled" class="inpuItem" type="checkbox"
				name="soliEnabled" {if $soliEnabled}checked="checked"{/if} />
		</div>
	</fieldset>
	-->

	<fieldset class="smallContainer">
		<legend>
			{t}Soliprice{/t}
		</legend>

		<div class="simpleForm">
			<label for="solipriceEnabled">
				{t}Is Soliprice Enabled:{/t}
			</label>
			<input id="solipriceEnabled" class="inputItem" type="checkbox"
				name="solipriceEnabled"
				{if $solipriceEnabled}checked="checked"{/if} />
		</div>

		<div class="simpleForm">
			<label for="soliprice">
				{t}Amount to Pay:{/t}
			</label>
			<input id="soliprice" class="inputItem" type="text" maxlength="5"
				size="5" name="soliprice" value="{$soliprice|number_format:2}" /> €
		</div>
	</fieldset>
	<input type="submit" value="{t}Commit Changes{/t}" />
</form>

{/block}
