{include file='web/header.tpl' title='Pers&ouml;nlicher Vertretungsplan'}

<div align="center"><h3>Pers&ouml;nlicher Vertretungsplan*</h3></div>
<br> 
{$planheute}<br>
{$planmorgen}<br>
*ohne Gew&auml;hr!<br>
<form action="index.php?section=Pvau|Pvp" method="post">
    <fieldset>
      <label for="search">Suchfilter:</label>
      <input type="text" name="search"value="{$searchterm}" />
       <input type="submit" value="Best&auml;tigen" />
    </fieldset>
</form>    
Es k&ouml;nnen Lehrerk&uuml;rzel sowie Klassen- und Kursbezeichnungen eingegeben werden, nach denen gesucht werden soll. Gro&szlig;- und Kleinschreibung ist zu beachten! Mehrere Suchbegriffe k&ouml;nnen durch Leerzeichen getrennt eingegeben werden.                                                                     
{include file='web/footer.tpl'}