{extends file=$soliParent}{block name=content}
<h3 align=center>{$name} - Essenszuschuss f&uuml;r KW {$ordering_date}</h3><br>
{literal}
<style>
td {
	padding-left: 15px;
	padding-right: 15px;
	padding-bottom: 5px;
	padding-top: 5px;
}

</style>
{/literal}
<table style="text-align: center;">
	<thead>
		<tr bgcolor="#33CFF">
			<th >Datum</th>
			<th>Men&uuml;</th>
			<th>Preis</th>
			<th>Eigenanteil</th>
			<th>Aus Kasse</th>
		</tr>
	</thead>
	
	<tbody>
	{foreach $orders as $order}
		<tr bgcolor="#FFC33">
			<td>{$order.date}</td>
			<td>{$order.mealname}</td>
			<td>{sprintf("%01.2f", $order.mealprice)}€</td>
			<td>{sprintf("%01.2f", $order.soliprice)}€</td>
			<td>{sprintf("%01.2f", ($order.mealprice - $order.soliprice))}€</td>
		</tr>
	{/foreach}
	</tbody>
	
</table>
	<p> Differenz zwischen normalem Preis und Soli-Preis:  <b>{sprintf("%01.2f", $sum)}€</b><p>	
{/block}