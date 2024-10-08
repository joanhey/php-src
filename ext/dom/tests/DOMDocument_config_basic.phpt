--TEST--
Tests DOMDocument::config read
--CREDITS--
Chris Snyder <chsnyder@gmail.com>
# TestFest 2009 NYPHP
--EXTENSIONS--
dom
--FILE--
<?php
// create dom document
$dom = new DOMDocument;
echo "DOMDocument created\n";

$test = $dom->config;
echo "Read config:\n";
var_dump( $test );

// note -- will always be null as DOMConfiguration is not implemented in PHP

echo "Done\n";
?>
--EXPECTF--
DOMDocument created

Deprecated: Property DOMDocument::$config is deprecated in %s on line %d
Read config:
NULL
Done
