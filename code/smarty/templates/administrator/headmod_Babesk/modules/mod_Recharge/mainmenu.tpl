{extends file=$inh_path}{block name="content"}

<h2 class="moduleHeader">
	Kartenaufladung Menü
</h2>

<fieldset class="smallContainer">
	<legend>
		{_g('Standard Actions')}
	</legend>
	<ul class="submodulelinkList">
		<li>
			<a href="index.php?module=administrator|Babesk|Recharge|RechargeCard">
				{_g('Recharge a Card')}
			</a>
		</li>
		<li>
			<a href="index.php?module=administrator|Babesk|Recharge|PrintRechargeBalance">
				{_g('Print Recharge Balance')}
			</a>
		</li>
	</ul>
</fieldset>

{/block}
