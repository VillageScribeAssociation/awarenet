<?

//--------------------------------------------------------------------------------------------------
//|	create a display of all users who like something
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object [string]
//arg: refUID - UID of liked object [string]

function like_show($args) {
	global $kapenta;
	global $kapenta;
	
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { return ''; }

	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($args['refModule']) . "'";
	$conditions[] = "refModel='" . $kapenta->db->addMarkup($args['refModel']) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($args['refUID']) . "'";
	$conditions[] = "cancelled='no'";

	$range = $kapenta->db->loadRange('like_something', '*', $conditions);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	if (0 == count($range)) { return ''; }

	if (1 == count($range)) {
		$item = array_pop($range);
		$html .= ''
		 . "<br/><div class='inlinequote'>"
		 . "[[:users::namelink::userUID=" . $item['createdBy'] . ":]] likes this."
		 . "</div>";

		return $html;
	}
	
	$pictures = '';
	$names = array();

	foreach($range as $item) {
		$names[] = "[[:users::namelink::userUID=" . $item['createdBy'] . ":]]";
		$pictures .= "[[:users::avatar::userUID=" . $item['createdBy'] . "::size=thumbsm:]]";
	}
	
	$html = '<br/>'
	 . "[[:theme::navtitlebox::label=Likes (" . count($range) . ")::toggle=divLS" . $args['refUID'] . ":]]\n"
	 . "<div id='divLS" . $args['refUID'] . "' class='sessionmsg'>\n"
	 . "<table noborder width='100%'>\n"
	 . "  <tr>"
	 . "    <td valign='top'>\n"
	 . implode(', ', $names) . "<br/>\n"
	 . $pictures . "</td>\n"
	 . "  </tr>\n"
	 . "</table>\n"
	 . "</div><br/>\n";

	return $html;
}

?>
