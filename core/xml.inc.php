<?

//--------------------------------------------------------------------------------------------------
//	uses xmlentity.class.php
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'core/xmlentity.class.php');

//--------------------------------------------------------------------------------------------------
//	convert object heirarchy into array
//--------------------------------------------------------------------------------------------------

function xmlToArray($xml) {
	// TODO
}

//--------------------------------------------------------------------------------------------------
//	load a file as PHP-escaped XML and return xmlentity object
//--------------------------------------------------------------------------------------------------
//	<tag>value</tag> to array['tag'] = 'value';

function xmlLoad($fileName) {
	if (file_exists($fileName) == false) { return false; }
	$raw = implode(file($fileName));
	$raw = phpUnComment($raw);
	$xe = new XmlEntity($raw);
	return $xe;
}

//--------------------------------------------------------------------------------------------------
//	convert a 2d array to xml
//--------------------------------------------------------------------------------------------------

function arrayToXml2d($ary, $type, $indent = '') {
	$retVal = $indent . "<$type>\n";
	foreach($ary as $key => $value) {
		$value = htmlentities($value);
		$key = strtolower($key);
		$retVal .= $indent . "  <$key>$value</$key>\n";
	}
	$retVal .= $indent . "</$type>\n";
	return $retVal;
}

?>
