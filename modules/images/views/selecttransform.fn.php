<?

	require_once($kapenta->installPath . 'modules/images/models/transforms.set.php');

//--------------------------------------------------------------------------------------------------
//|	make an html select element to choose an image transform / preset size
//--------------------------------------------------------------------------------------------------
//opt: varname - html form field name [string]
//opt: default - preselected item [string]

function images_selecttransform($args) {
	global $user;

	$varname = 'transform';		//%	default html form field [string]
	$default = 'full';			//%	default to original image [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('varname', $args)) { $varname = $args['varname']; }
	if (true == array_key_exists('default', $args)) { $default = $args['default']; }
	//TODO: add permissions here if needed, and sanitize varname

	$set = new Images_Transforms();

	//----------------------------------------------------------------------------------------------
	//	make the select element
	//----------------------------------------------------------------------------------------------

	$html .= "<select name='$varname'>\n";
	foreach($set->presets as $key => $value) {
		$selected = '';
		$label = $value['size'];
		if ($key == $default) { $selected = "selected='selected'"; }
		$html .= "\t<option value='$key'$selected>$key ($label)</option>\n";
	}
	$html .= "</select>\n";

	return $html;
}


?>
