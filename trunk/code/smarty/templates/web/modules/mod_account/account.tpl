{include file='web/header.tpl' title='Konto sperren'}

<h3>Konto sperren</h3> 
<form action="index.php?section=account" method="post">
    <fieldset>
      <select name="kontoSperren">
      <option value="lockAccount">Konto sperren</option>
      <option value="dontLockAccount" selected>Konto NICHT sperren</option>
      <input type="submit" value="Best&auml;tigen" />
      </select>
    </fieldset>
</form>                                                                          
{include file='web/footer.tpl'}
