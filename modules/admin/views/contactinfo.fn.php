<?

//--------------------------------------------------------------------------------------------------
//|	displays contact information for software distributor
//--------------------------------------------------------------------------------------------------

//opt: tb - title box (yes|nav|no) [string]
//opt: title - title of block [string]

function admin_contactinfo($args) {
	global $kapenta;

	$html = '';							//%	return value [string]
	$tb = 'nav';
	$title = 'Contact';					//%	title of this block [string]

	//----------------------------------------------------------------------------------------------
	//	no permissions checks as yet, any one can view this
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('tb', $args)) { $tb = $args['tb']; }
	if (true == array_key_exists('title', $args)) { $title = $args['title']; }

	$html = $kapenta->theme->loadBlock('modules/admin/views/contactinfo.block.php');

	if ('yes' == $tb) { $html = $kapenta->theme->tb($html, $title, 'divContactDetail', 'show'); }
	if ('nav' == $tb) { $html = $kapenta->theme->ntb($html, $title, 'divContactDetail', 'show'); }

	return $html;
}


?>
