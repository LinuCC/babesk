{extends file=$base_path}{block name=content}

<h2 class="moduleHeader">{_g('Soli-Settings')}</h2>

<form action="index.php?module=administrator|Babesk|Soli|Settings"
	class="simpleForm" method="post">

	<!--
	<fieldset class="smallContainer">
		<legend>{_g('Enable Soli')}</legend>
		<div class="simpleForm">
			<label for="soliEnabled">
				{_g('Is the Soli-Module enabled:')}
			</label>
			<input id="soliEnabled" class="inpuItem" type="checkbox"
				name="soliEnabled" {if $soliEnabled}checked="checked"{/if} />
		</div>
	</fieldset>
	-->

	<fieldset class="smallContainer">
		<legend>
			{_g("Soliprice")}
		</legend>

		<div class="simpleForm">
			<label for="solipriceEnabled">
				{_g("Is Soliprice Enabled:")}
			</label>
			<input id="solipriceEnabled" class="inputItem" type="checkbox"
				name="solipriceEnabled"
				{if $solipriceEnabled}checked="checked"{/if} />
		</div>

		<div class="simpleForm">
			<label for="soliprice">
				{_g("Amount to Pay:")}
			</label>
			<input id="soliprice" class="inputItem" type="text" maxlength="5"
				size="5" name="soliprice" value="{$soliprice|number_format:2}" /> €
		</div>
	</fieldset>
	<input type="submit" value="{_g('Commit Changes')}" />
</form>

{/block}
