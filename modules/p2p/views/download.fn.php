<?

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//|	displays details and progress of an active download
//--------------------------------------------------------------------------------------------------
//arg: path - path of file to be downloaded [string]

function p2p_download($args) {
	global $user;
	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	$klf = new KLargeFile($args['path']);
	if (false == $klf->loaded) { return '(unknown download)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$cpc = $klf->percentComplete();
	$progress = ''
	 . "<table noborder width='100%'>"
	 . "<tr height='10px'>"
	 . "<td width='" . $cpc . "%' bgcolor='00ff00'></td>"
	 . "<td width='" . (100 - $cpc) . "%' bgcolor='aaaaaa'></td>"
	 . "</tr>"
	 . "</table>";

	$html .= ''
	 . "<table noborder width='100%'>\n"
	 . "\t<tr>\n"
	 . "\t\t<td width='40px' valign='top'>"
		 . "<img src='%%serverPath%%themes/%%defaultTheme%%/icons/arrow_down_green.png' />"
		 . "</td>\n"
	 . "\t\t<td>\n"
	 . "\t\t\t$progress\n"
	 . "\t\t\t<b>File:</b> " . $klf->path . "<br/>"
	 . "\t\t\t<small>"
		. "<b>Size:</b> " . $klf->size . " (" . $cpc . "%)"
	 	. "</small>"
	 . "\t\t</td>"
	 . "\t<tr>\n"
	 . "</table>\n";


	return $html;
}

?>
