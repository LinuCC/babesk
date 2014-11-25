<html>

{$block = $data.usercount}

<h3>Teilnehmerzahlen</h3>
<table>
	<thead>
	</thead>
	<tbody>
		{foreach $block as $name => $pairData}
			<tr>
				<td>{$name}</td>
				<td>{$pairData}</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<p></p>

{$block = $data.payedAmount}
<h3>Einnahmen</h3>
<table>
	<thead>
	</thead>
	<tbody>
		{foreach $block as $name => $pairData}
			<tr>
				<td>{$name}</td>
				<td>{$pairData}</td>
			</tr>
		{/foreach}
	</tbody>
</table>

</html>