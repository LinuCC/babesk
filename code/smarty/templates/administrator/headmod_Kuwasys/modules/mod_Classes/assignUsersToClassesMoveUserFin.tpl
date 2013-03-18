{extends file=$inh_path} {block name='content'}

<h2 class="moduleHeader">Der Benutzer wurde erfolgreich in den anderen Kurs verschoben.</h2>

<br />
<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClasses=true">zurück zu der Kursübersicht</a><br />
<a href="index.php?section=Kuwasys|Classes&amp;action=assignUsersToClasses&amp;showClassDetails={$movedFromClassId}">zurück zu den Kursdetails</a>

{/block}