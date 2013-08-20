{extends file=$inh_path}{block name="content"}

<h2 class="moduleHeader">
	{_g('Print Recharge Balance')}
</h2>

<form action="index.php?module=administrator|Babesk|Recharge|PrintRechargeBalance">
	<div class="simpleForm">
		<p>{_g('Zeitspanne:')}</p>
		<select name="interval" class="inputItem">
			{foreach $intervals as $intervalId => $intervalname}
				<option value="{$intervalId}">{$intervalname}</option>
			{/foreach}
		</select>
	</div>

	<div class="simpleForm">
		<p>gett</p>
		<input class="inputItem" type="text" name="date" size="10"
				value="{date('Y-m-d')}" />
	</div>
</form>


<script type="text/javascript">

$(document).ready(function() {

	$('input.inputItem[name=date]').datepicker({
		dateFormat: 'dd.mm.yy',
		changeMonth: true,
		changeYear: true,
		yearRange: "2013:+10"
	});
});

</script>

{/block}
