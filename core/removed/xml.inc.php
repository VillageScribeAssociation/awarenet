<?

	require_once($installPath . 'core/xmlentity.class.php');

//--------------------------------------------------------------------------------------------------
//*	uses xmlentity.class.php
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//|	convert object heirarchy into array
//--------------------------------------------------------------------------------------------------
//: not yet implemnted, consider if actually useful

function xmlToArray($xml) {
	// TODO
}

//--------------------------------------------------------------------------------------------------
//|	load a file as PHP-escaped XML and return xmlentity object
//--------------------------------------------------------------------------------------------------
//:	<tag>value</tag> to array['tag'] = 'value';

//arg: fileName - absolute fileName [string]
//returns: XmlEntity root object [object]

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
//arg: ary - associative array of entity type => value [array]
//arg: type - entity type of parent [string]
//opt: indent - lines are prefixed with this [string]
//returns: xml [string]

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
