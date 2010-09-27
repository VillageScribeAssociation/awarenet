<?

//--------------------------------------------------------------------------------------------------
//|	public-facing contact form, to send an email to admins
//--------------------------------------------------------------------------------------------------

function home_contactform($args) {
	global $theme;

	$html = $theme->loadBlock('modules/home/views/contactform.block.php');
	return $html;
}


?>