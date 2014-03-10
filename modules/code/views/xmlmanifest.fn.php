<?

//--------------------------------------------------------------------------------------------------
//|	makes an XML document describing a package and the files it contains
//--------------------------------------------------------------------------------------------------
//arg: raUID - alias or UID of a Code_Package object [string]
//opt: UID - overrides raUID if present [string]
//opt: packageUID - overrides raUID if present [string]

function code_xmlmanifest($args) {
	$xml = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('UID', $args)) { $args['raUID'] = $args['UID']; }
	if (true == array_key_exists('packageUID', $args)) { $args['raUID'] = $args['packageUID']; }

	//TODO: implement permissions for private and restricted packages	

	$model = new Code_Package($args['raUID']);
	if (false == $model->loaded) { return '<error>Unkown package</error>'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$xml = $model->toXml();

	return $xml;
}

?>
