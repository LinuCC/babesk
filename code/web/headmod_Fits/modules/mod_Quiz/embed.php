<?php 
if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], "section=Fits|Quiz")) {
	$uid=$_GET['uid'];
	include 'quizzy/quizzyHeader.php';
	print("</head><body>");
	include 'quizzy/quizzy.php';
	print("</body></html>");
} else {
	print("Kein Direktzugriff erlaubt!");
}

?>
