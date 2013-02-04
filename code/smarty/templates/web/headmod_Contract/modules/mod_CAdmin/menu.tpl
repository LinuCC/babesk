{include file='web/header.tpl' title='Vorlagen'}


<p><b>Vorlagen:</b></p>
{$error}
{if $editor}<a href="index.php?section=Contract|CAdmin&action=newcontract">Neue Vorlage erstellen</a>{/if}
<table><tr><th>Klasse</th><th>Beschreibung</th><th>Aktion</th></tr>
{foreach $contracts as $contract}
<tr>
<td>{$contract.class}</td><td>{$contract.title}</td><td><a href="index.php?section=Contract|CAdmin&action=showcontract&id={$contract.id}"><img src="../smarty/templates/web/images/page_white_acrobat.png"></a>
{if $editor}<a href="index.php?section=Contract|CAdmin&action=deletecontract&id={$contract.id}"><img src="../smarty/templates/web/images/delete.png"></a>{/if}</td>
</tr>
{/foreach}
</table>
{include file='web/footer.tpl'}