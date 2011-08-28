<h2>Preisklassen einrichten</h2>
<h3>SETUP STEP: <?php echo $_SESSION['setup_step']; ?></h3>
<p>In diesem Schritt richten sie die Preisklassen ein.</p>
<legend>Bitte Daten für eine Preisklasse angeben</legend>
<form action="index.php?&amp;<?php echo htmlspecialchars(SID); ?>" method="post">
	<fieldset>
	   <legend>Preisklasse</legend>
	   <label>Name der Preisklasse</label>
		  <input type="text" name="Name" />
	   <?php for ($i = 0; $i < $_SESSION['nr_groups']; $i++) {
		  echo '<label>Preis für Gruppe '.$_SESSION['groupnames'][$i].'</label>
                    <input type="text" name="Price[]" />';		//TODO: hidden group ID
		  }
            echo '</fieldset> ?>
	<input type="submit" value="weiteren Datensatz hinzufügen" />
</form>
<form action="index.php?&amp;<?php echo htmlspecialchars(SID); ?>">
	<input type="submit" value="Weiter zum nächsten Schritt">
</form>