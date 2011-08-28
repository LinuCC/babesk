{include file='web/header.tpl' title='Bestellen'}

<p><u>Speiseplan:</u></p>
{foreach $meals as $meal}
<p>{$meal.date}: <a href="index.php?section=order&order={$meal.ID}">{$meal.name}</a></p>    <!-- TODO: Tabelle! Speiseplan soweit vorhanden dieser + nächster Woche jeweils in einer Tabelle mit 2 Spalten:   -->
{/foreach}  
                                                                                <!-- oben die Wochentage, links menü 1, menü 2     -->
{include file='web/footer.tpl'}
