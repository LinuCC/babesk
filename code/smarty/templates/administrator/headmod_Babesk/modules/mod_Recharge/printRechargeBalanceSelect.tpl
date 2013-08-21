{extends file=$inh_path}{block name="content"}

<h2 class="moduleHeader">
	{_g('Print Recharge Balance')}
</h2>

<form action="index.php?module=administrator|Babesk|Recharge|PrintRechargeBalance" method="post">
	<div class="simpleForm">
		<p>{_g('Interval:')}</p>
		<select name="interval" class="inputItem">
			{foreach $intervals as $intervalId => $intervalname}
				<option value="{$intervalId}">{$intervalname}</option>
			{/foreach}
		</select>
	</div>

	<div class="simpleForm">
		<p>{_g('At:')}</p>
		<input class="inputItem" type="text" name="date" size="10"
				value="{date('d.m.Y')}" />
	</div>
	<br />
	<input type="submit" value="{_g('Download Pdf')}" />

</form>


<script type="text/javascript">

$(document).ready(function() {

	$('input.inputItem[name=date]').datepicker({
		dateFormat: 'dd.mm.yy',
		changeMonth: true,
		changeYear: true,
		yearRange: "2013:now"
	});
});

</script>

{/block}
