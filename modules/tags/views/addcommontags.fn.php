<?

//--------------------------------------------------------------------------------------------------
//|	widget to add most common tags to something
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may have tags [string]
//arg: refUID - UID of object to which tags will be attached [string]
//opt: num - max number of tags to show (int) [string]

function tags_addcommontags($args) {
	global $kapenta, $theme, $db, $user;
	$html = '';					//%	return value [string]
	$num = 100;					//%	max number of tags to show [int]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: permissions check here

	if (false == array_key_exists('refModule', $args)) { return '(no refModule)'; }
	if (false == array_key_exists('refModel', $args)) { return '(no refModel)'; }
	if (false == array_key_exists('refUID', $args)) { return '(no refUID)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];	

	if (false == $kapenta->moduleExists($refModule)) { return '(unknown module)'; }
	if (false == $db->objectExists($refModel, $refUID)) { return '(no such object)'; }

	if (true == array_key_exists('num', $args)) { $num = (int)$args['num']; }

	//----------------------------------------------------------------------------------------------
	//	load n tags from database
	//----------------------------------------------------------------------------------------------
	$range = $db->loadRange('tags_tag', '*', '', 'objectCount DESC', $num);
	$tags = array();
	foreach($range as $item) {
		$link = '%%serverPath%%tags/add'
			. '/refModule_' . $refModule
			. '/refModel_' . $refModel
			. '/refUID_' . $refUID
			. '/tagName_' . $item['name'];

		$tags[$item['name']] = ''
		 . "<span class='tag'>"
		 . "<a href='$link' class='tag'>"
		 . '&nbsp;&nbsp;' . $item['name'] . '&nbsp;'
		 . "</a>"
		 . "</span>\n";
	}

	asort($tags);

	$html = ''
	 . "<div class='indent' style='line-height: 2.0;'>\n"
	 . "<div class='spacer'></div>"
	 . implode(' ', $tags) . "<br/>\n"
	 . "<small>Click to attach common tags.</small>\n"
	 . "</div>\n";
	return $html;
}

?>
