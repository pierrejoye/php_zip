--TEST--
zip_entry_read() function
--SKIPIF--
<?php
/* $Id$ */
if(!extension_loaded('zip')) die('skip');
?>
--INI--
error_reporting=24575
--FILE--
<?php
$zip    = zip_open(dirname(__FILE__)."/test_procedural.zip");
$entry  = zip_read($zip);
if (!zip_entry_open($zip, $entry, "r")) die("Failure");
echo zip_entry_read($entry);
zip_entry_close($entry);
zip_close($zip);

?>
--EXPECT--
foo
