<?php
$test = realpath(dirname("../../index.php"));

$test2 = $_SERVER['DOCUMENT_ROOT'];
echo $test." (".strlen($test).")<br />\n";
echo $test2." (".strlen($test2).")<br />\n";
echo "subfolder:<br />\n";
if(strlen($test) > strlen($test2)) {
	$test3 = substr($test,strlen($test2));
	echo $test3."<br />\n";
}
phpinfo();
?>
