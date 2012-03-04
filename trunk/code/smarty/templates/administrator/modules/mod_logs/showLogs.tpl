{extends file=$base_path}{block name=content}
<h3 align="center">Log-Anzeige</h3>
<h5>Die Logs sind bisher nur teilweise in das Projekt eingebaut worden. <br>
Sie bieten noch keine vollständige Übersicht über die Fehler und Hinweise des Programms.</h5>            
<table>
	<tr bgcolor='#33CFF'>
		<th>LogID</th>
		<th>Category</th>
		<th>Severity</th>
		<th>Date</th>
		<th>Message</th>
	</tr>
	{foreach $logs as $log}
	<tr bgcolor="#FFC33">
		<td align="center">{$log['ID']}</td>
		<td align="center">{$log['category']}</td>
		<td align="center">{$log['severity']}</td>
		<td align="center">{$log['time']}</td>
		<td align="center">{$log['message']}</td>
	</tr>	
	{/foreach}
</table>
{/block}