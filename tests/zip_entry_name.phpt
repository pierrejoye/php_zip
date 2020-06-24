--TEST--
zip_entry_name() function
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
if (!is_resource($zip)) die("Failure");
$entries = 0;
while ($entry = zip_read($zip)) {
  echo zip_entry_name($entry)."\n";
}
zip_close($zip);

?>
--EXPECT--
foo
bar
foobar/
foobar/baz
