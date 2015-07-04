<html>

<h3 style="text-align: center">{$title}</h3>

<div style="page-break-inside:avoid">
	<table border="1" cellpadding="1mm">
		<tr style="font-weight:bold; text-align:center;font-size: 0.7em;">
			<th style="width: 75mm">Name</th>
			<th style="width: 20mm">Klasse</th>
			<th style="width: 20mm">Bezahlt</th>
			<th style="width: 20mm">Soll</th>
			<th style="width: 42mm">Typ</th>
		</tr>
		{foreach $users as $user}
			<tr style="text-align: center; font-size: 0.6em;">
				<td style="width: 75mm">
					{$user.forename} {$user.name}
				</td>
				<td style="width: 20mm">
					{$user.activeGrade}
				</td>
				<td style="width: 20mm">
					{$user.payedAmount}
				</td>
				<td style="width: 20mm">
					{$user.amountToPay}
				</td>
				<td style="width: 42mm">
					{if $user.loanChoice}
						{$user.loanChoice}
					{else}
						Antrag nicht erfasst
					{/if}
				</td>
			</tr>
		{/foreach}
	</table>
</div>

</html>