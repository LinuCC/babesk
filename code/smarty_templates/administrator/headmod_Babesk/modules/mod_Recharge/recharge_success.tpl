{extends file=$base_path}{block name=content}
<p>Geldaufladung erfolgreich!</p>
<p>Dem Benutzer "{$username}" wurden {$amount}&euro; gutgeschrieben</p>
<a href="index.php?module=administrator|Babesk|Recharge|RechargeCard">weiter zur n&auml;chsten Geldaufladung</a>

<script type="text/javascript">
var ref = 'index.php?module=administrator|Babesk|Recharge|RechargeCard';

$(document).ready(function() {
	$('body').on('keypress', function(event) {
		if(event.which == '13') {
			location.href = ref;
		}
	});
});
</script>
{/block}
