<div>
    <p id="last_login">Letzter Login: {$last_login}</p>
</div>

<div id="background">
{if isset($smarty.get.section)}
  <img src="../smarty/templates/web/images/{$smarty.get.section|replace:"|":"_"}_background.png" class="center" />
{else}
   <img src="../smarty/templates/web/images/welcome_background.png" class="center" />
{/if}
   <div id="footer">
    <p>BaBeSK {$babesk_version} &copy; 2011 Lessing Gymnasium Uelzen</p>
</div>
</div>

</body>
</html>
