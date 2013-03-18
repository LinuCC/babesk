{include file='web/header.tpl' title='Vorlagen'}

{if $editor}<a href="index.php?section=Messages|MAdmin&action=newcontract">Neue Vorlage erstellen</a>{/if}
{if !($error)} 
<p><b>Post:</b></p>

<table><tr>{if $editor}<th>Klasse</th>{/if}<th>Beschreibung</th>{if $editor}<th>g&uuml;ltig von</th><th>g&uuml;ltig bis</th>{/if}<th>Aktion</th></tr>

{foreach $contracts as $contract}
<tr>
{if $editor}<td>{$contract.class}</td>{/if}<td>{$contract.title}</td>{if $editor}<td>{$valid_from} </td><td>{$valid_to}</td>{/if}
<td>
{if $BaBeSkTerminal} Hinweis: Post kann nicht am BaBeSK-Terminal <br>ge&ouml;ffnet werden! 
{else}
<a href="index.php?section=Messages|MAdmin&action=showcontract&id={$contract.id}"><img src="../smarty/templates/web/images/page_white_acrobat.png"></a>
{/if}
{if $editor}<a href="index.php?section=Messages|MAdmin&action=deletecontract&id={$contract.id}"><img src="../smarty/templates/web/images/delete.png"></a>{/if}</td>
</tr>
{/foreach}
</table>
{/if}
{include file='web/footer.tpl'}