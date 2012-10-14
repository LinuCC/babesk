<?php 
if (isset($_SERVER['HTTP_REFERER']) && strstr($_SERVER['HTTP_REFERER'], "section=Fits|Zeugnis")) {
	$fullname = $_GET['forename']." ".$_GET['name'];

	header("Content-type: image/png");
//$picture = imagecreatefrompng( "http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,-strlen(basename($_SERVER['PHP_SELF'])))."../../../../smarty/templates/web/images/FITS.png");
	
	$curl = curl_init("http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,-strlen(basename($_SERVER['PHP_SELF'])))."../../../../smarty/templates/web/images/FITS.png");
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_VERBOSE, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($curl);
	
	file_put_contents("image.png", $output);
	
	$picture=imageCreateFrompng ('image.png');
	
	$black = ImageColorAllocate ($picture, 0, 0, 0);
imagestring($picture, 5, 50, 30, $fullname, $black);
imagestring($picture, 5, 120, 77, $_GET['year'], $black);
ImagePng ($picture); 
imageDestroy ($picture);
unlink("image.png");
} else {
	print("Kein Direktzugriff erlaubt!");
}


?>
