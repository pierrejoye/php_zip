--TEST--
zip_open() function
--SKIPIF--
<?php
/* $Id$ */
if(!extension_loaded('zip')) die('skip');
?>
--INI--
error_reporting=24575
--FILE--
<?php
$zip = zip_open(dirname(__FILE__)."/test_procedural.zip");

echo is_resource($zip) ? "OK" : "Failure";

?>
--EXPECT--
OK
