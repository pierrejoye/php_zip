--TEST--
GH-17139 - zip_entry_name() crash
--SKIPIF--
<?php
if(!extension_loaded('zip')) die('skip');
?>
--INI--
error_reporting=24575
--FILE--
<?php
$zip = zip_open(__DIR__."/test_procedural.zip");
if (!is_resource($zip)) die("Failure");
// no need to bother looping over, the entry name should point to a dangling address from the first iteration
$zip = zip_read($zip);
var_dump(zip_entry_name($zip));
?>
--EXPECTF--
bool(false)
