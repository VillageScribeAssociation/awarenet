<?

//--------------------------------------------------------------------------------------------------
//|	displays contact information for software distributor
//--------------------------------------------------------------------------------------------------

//opt: tb - title box (yes|nav|no) [string]
//opt: title - title of block [string]

function admin_contactinfo($args) {
	global $theme;

	$html = '';							//%	return value [string]
	$tb = 'nav';
	$title = 'Contact';					//%	title of this block [string]

	//----------------------------------------------------------------------------------------------
	//	no permissions checks as yet, any one can view this
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('tb', $args)) { $tb = $args['tb']; }
	if (true == array_key_exists('title', $args)) { $title = $args['title']; }

	$html = $theme->loadBlock('modules/admin/views/contactinfo.block.php');

	if ('yes' == $tb) { $html = $theme->tb($html, $title, 'divContactDetail', 'show'); }
	if ('nav' == $tb) { $html = $theme->ntb($html, $title, 'divContactDetail', 'show'); }

	return $html;
}


?>
