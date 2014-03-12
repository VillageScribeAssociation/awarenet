<?

//--------------------------------------------------------------------------------------------------
//|	shows button for reporting abuse
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - object type [string]
//arg: refUID - UID of object to report [string]
//opt: title - title of submission window [string]

function abuse_reportlink($args) {
	global $kapenta;
	global $theme;
	global $kapenta;
	global $kapenta;

	$title = 'Submit Abuse Report';			//%	default windoe banner text [string]
	$html = '';								//%	return value [string]
	
	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (('public' == $kapenta->user->role) || ('banned' == $kapenta->user->role)) { return ''; }
	if (false == array_key_exists('refModule', $args)) { return '(no module)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no model)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no UID)'; }
	//TODO: permissions check here

	$labels['refModule'] = $args['refModule'];			// TODO: clean strings
	$labels['refModel'] = $args['refModel'];
	$labels['refUID'] = $args['refUID'];
	$labels['title'] = $title;

	$kapenta->page->requireJs($kapenta->serverPath . 'modules/abuse/js/report.js');		//	add client-side

	//----------------------------------------------------------------------------------------------
	//	make the button
	//----------------------------------------------------------------------------------------------

	$block = ''
	 . "<a"
	 . " href=\"javascript:Abuse_ReportModal("
		 . "'%%refModule%%', '%%refModel%%', '%%refUID%%', '%%title%%'"
	 . ")\">[report]</a>";

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

?>
