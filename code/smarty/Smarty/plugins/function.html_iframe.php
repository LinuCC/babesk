<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_iframe} function plugin
 *
 * Type:     function<br>
 * Name:     html_iframe<br>
 * Date:     10/12/2007<br>
 * Purpose:  displays an iframe<br>
 * Input:<br>
 *
 * Examples: {html_iframe}
 * @author   Tim Golen
 * @version  1.0
 * @param params
 * @param smarty
 * @return string
 */
function smarty_function_html_iframe($params, &$smarty)
{
	$type = '';
	
	// get all of our data from the array
	$src = 			(isset($params['src']))?$params['src']:'';
	$height = 		(isset($params['height']))?$params['height']:'100%';
	$width = 		(isset($params['width']))?$params['width']:'100%';
	$name = 		(isset($params['name']))?$params['name']:'';
	$frame = 		(isset($params['frame']))?$params['frame']:'0';
	
	$result = <<<EOT
<iframe src="$src" width="$width" height="$height" name="$name" frameborder="$frame">
  <p>Your browser does not support iframes.</p>
</iframe>
EOT;
	
	return $result;
}
?>
