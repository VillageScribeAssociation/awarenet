<?

//--------------------------------------------------------------------------------------------------
//|	makes and iframe for the /editreply/ inline action
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Forums_Reply object [string]
//opt: replyUID - overrides UID if present [string]

function forums_editreplyif($args) {
	global $kapenta;
	$html = "";

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('replyUID', $args)) { $args['UID'] = $args['replyUID']; }
	if (false == array_key_exists('UID', $args)) { return '(Reply UID not given)'; }

	//TODO: check user and timing

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html = ''
	 . "<iframe "
	 . " src='" . $kapenta->serverPath . "forums/editreply/" . $args['UID'] . "'"
	 . " name='ifEdit" . $args['UID'] . "'"
	 . " id='ifEditReply" . $args['UID'] . "'"
	 . " width='570' height='5' frameborder='0' scrolling='no'"
	 . "></iframe>";
	
	return $html;
}


?>
