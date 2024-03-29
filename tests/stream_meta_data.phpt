--TEST--
stream_get_meta_data() on zip stream
--SKIPIF--
<?php
/* $Id: oo_stream.phpt 260091 2008-05-21 09:27:41Z pajoye $ */
if(!extension_loaded('zip')) die('skip');
?>
--FILE--
<?php
$dirname = dirname(__FILE__) . '/';
$file = $dirname . 'test_with_comment.zip';
include $dirname . 'utils.inc';
$zip = new ZipArchive;
if (!$zip->open($file)) {
	exit('failed');
}
$fp = $zip->getStream('foo');

if(!$fp) exit("\n");

var_dump(stream_get_meta_data($fp));

fclose($fp);
$zip->close();


$fp = fopen('zip://' . dirname(__FILE__) . '/test_with_comment.zip#foo', 'rb');
if (!$fp) {
  exit("cannot open\n");
}

var_dump(stream_get_meta_data($fp));
fclose($fp);

?>
--EXPECTF--
array(8) {
  ["timed_out"]=>
  bool(false)
  ["blocked"]=>
  bool(true)
  ["eof"]=>
  bool(false)
  ["stream_type"]=>
  string(3) "zip"
  ["mode"]=>
  string(2) "rb"
  ["unread_bytes"]=>
  int(0)
  ["seekable"]=>
  bool(%s)
  ["uri"]=>
  string(3) "foo"
}
array(9) {
  ["timed_out"]=>
  bool(false)
  ["blocked"]=>
  bool(true)
  ["eof"]=>
  bool(false)
  ["wrapper_type"]=>
  string(11) "zip wrapper"
  ["stream_type"]=>
  string(3) "zip"
  ["mode"]=>
  string(2) "rb"
  ["unread_bytes"]=>
  int(0)
  ["seekable"]=>
  bool(%s)
  ["uri"]=>
  string(%d) "zip://%stest_with_comment.zip#foo"
}
