{extends file=$inh_path}{block name=content}

<h3>Kursdetails des Kurses {$class.label}</h3>
<br><br>
<div class="classDescription">
{$class.description}
</div>
<br>
Dein Status bei diesem Kurs:<br>
{if $class.status} {$class.status}
{else}<b>Fehler - ein falscher Statuseintrag! Wenden sie sich an den Administrator!</b>
{/if}
<br><br>
{if $class.registrationEnabled}
<form action="index.php?section=Kuwasys|ClassDetails&action=deRegisterClassConfirmation&classId={$class.ID}" method="post">
	<input type="submit" value="Von dem Kurs abmelden">
</form>
{else}
Abmelden von dem Kurs ist nicht mehr möglich.
{/if}

{/block}