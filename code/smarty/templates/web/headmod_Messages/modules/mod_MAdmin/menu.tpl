{include file='web/header.tpl' title='Vorlagen'}

{if $editor}<a href="index.php?section=Messages|MAdmin&action=newcontract">Neue Vorlage erstellen</a>{/if}
{if !($error)} 
<p><b>Post:</b></p>

<table><tr><th>Klasse</th><th>Beschreibung</th><th>Aktion</th></tr>

{foreach $contracts as $contract}
<tr>
<td>{$contract.class}</td><td>{$contract.title}</td><td><a href="index.php?section=Messages|MAdmin&action=showcontract&id={$contract.id}"><img src="../smarty/templates/web/images/page_white_acrobat.png"></a>
{if $editor}<a href="index.php?section=Messages|MAdmin&action=deletecontract&id={$contract.id}"><img src="../smarty/templates/web/images/delete.png"></a>{/if}</td>
</tr>
{/foreach}
</table>
{/if}
{include file='web/footer.tpl'}