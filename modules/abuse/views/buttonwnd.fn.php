<?

//--------------------------------------------------------------------------------------------------
//|	shows button for reporting abuse
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - object type [string]
//arg: refUID - UID of object to report [string]

function abuse_buttonwnd($args) {
	global $user, $theme;
	$html = '';			//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(no module)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no model)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no UID)'; }
	//TODO: permissions check here

	$labels['refModule'] = $args['refModule'];		// TODO: clean strings
	$labels['refModel'] = $args['refModel'];
	$labels['refUID'] = $args['refUID'];

	//----------------------------------------------------------------------------------------------
	//	make the button
	//----------------------------------------------------------------------------------------------
	$windowUrl = ''
	 . '%%serverPath%%abuse/abusewindow'
	 . '/refUID_' . $args['refUID']
	 . '/refModule_' . $args['refModule']
	 . '/refModel_' . $args['refModel'] . '/';
		

	$block = "
	<img src='%%serverPath%%themes/%%defaultTheme%%/images/icons/abuse3.png' 
		onClick=\"kwindowmanager.createWindow("
		 . "'Report Abuse', "
		 . "'" . $windowUrl . "', "
		 . "550, "
		 . "450, "
		 . "'%%serverPath%%modules/live/icons/abuse.png' "
		 . "); scroll(0,0);\" 
		style='float: right;' width='20px'
		border='0' alt='report abuse' title='report abuse' />
	";
	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
