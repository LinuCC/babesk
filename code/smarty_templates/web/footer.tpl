<div>
	{if $last_login}
		<p id="last_login">Letzter Login: {$last_login}</p>
	{/if}
</div>

<div id="background">

{if isset($footerBackground)}
	<img src="{$footerBackground}" class="center" />
{/if}

</div>

</div>
</div>
<div id="footer">
	<p>BaBeSK {$babesk_version}</p>
</div>
</body>
</html>
