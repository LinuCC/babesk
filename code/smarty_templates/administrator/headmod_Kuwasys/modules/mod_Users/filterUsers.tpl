<!-- This is a filterbar for the module Users in the Headmodule Kuwasys.
	it is supposed to be used with smarty, and put in the correct places
	of the page.
	It needs the Smarty-Variable $modAction defined, which will be given
	to the next PHP-Script as the GET-Var action -->

<div class="filterBar">
<h3>Filter</h3>
<form action="index.php?section=Kuwasys|Users&amp;action={$modAction}" method="post">
	<input id="filterBarSubmitButton" type="submit" value="Absenden">
Nach
<select name="keyToSortAfter">
	<option value="ID">ID</option>
	<option value="forename">Vorname</option>
	<option value="name">Name</option>
	<option value="username">Benutzername</option>
	<option value="birthday">Geburtstag</option>
	<option value="email">Email-Adresse</option>
	<option value="telephone">Telefon-Nummer</option>
	<option value="gradeLabel">Klasse</option>
	<option value="schoolyearLabel">Schuljahr</option>
</select>
sortieren.<br>
<select name="keyToFilterAfter">
	<option value="ID">ID</option>
	<option value="forename">Vorname</option>
	<option value="name">Name</option>
	<option value="username">Benutzername</option>
	<option value="birthday">Geburtstag</option>
	<option value="email">Email-Adresse</option>
	<option value="telephone">Telefon-Nummer</option>
	<option value="gradeLabel">Klasse</option>
	<option value="schoolyearLabel">Schuljahr</option>
</select>
nach
	<input type="text" name="filterValue" maxlength="12">
filtern.
</form>
</div><br>