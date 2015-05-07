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
<p></p>

{if isset($assistantsCost) || isset($toolsCost)}
	<h3>Ausgaben</h3>
	<table>
		<thead>
		</thead>
		<tbody>
			{if isset($assistantsCost)}
				<tr>
					<td>Kosten Hilfskräfte</td>
					<td>{$assistantsCost}</td>
				</tr>
			{/if}
			{if isset($toolsCost)}
				<tr>
					<td>Kosten Hilfsmittel</td>
					<td>{$toolsCost}</td>
				</tr>
			{/if}
		</tbody>
	</table>
	<p></p>
{/if}

{if $otherCosts && count($otherCosts)}
	<h3>Sonstige Ausgaben</h3>
	<table>
		<thead>
			<tr>
				<th>Kosten</th>
				<th>Datum</th>
				<th>Empfänger</th>
			</tr>
		</thead>
		<tbody>
			{foreach $otherCosts as $otherCost}
				<tr>
					<td>{$otherCost.amount}</td>
					<td>{$otherCost.date}</td>
					<td>{$otherCost.recipient}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{/if}

</html>