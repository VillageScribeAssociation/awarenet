<?php

//--------------------------------------------------------------------------------------------------
//|	list first n members of a project, starting with admins
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of a Projects_Project object [string]
//opt: projectUID - overrides UID if present [string]
//opt: num - number of members to show, default is 12 (int) [string]

function projects_summarybyline($args) {
	global $user;
	global $db;
	global $theme;

	$num = 12;					//%	max members to display [int]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('projectUID', $args)) { $args['UID'] = $args['projectUID']; }
	if (false == array_key_exists('UID', $args)) { return '(project UID not given)'; }

	if (false == $db->objectExists('projects_project', $args['UID'])) { return '(unkown project)'; }

	if (true == array_key_exists('num', $args)) { $args['']; }

	//----------------------------------------------------------------------------------------------
	//	query memebrship table
	//----------------------------------------------------------------------------------------------

	$conditions = array();
	$conditions[] = "projectUID='" . $db->addMarkup($args['UID']) . "'";
	
	$totalMembers = $db->countRange('projects_membership', $conditions);

	$range = $db->loadRange('projects_membership', '*', $conditions, 'role, joined', $num);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$namelist = array();
	$imglist = array();

	foreach($range as $item) {
		$namelist[] = '[[:users::namelink::userUID=' . $item['userUID'] . ':]]';

		$imgBlock = ''
		 . '[[:images::default'
		 . '::refModule=users'
		 . '::refModel=users_user'
		 . '::refUID=' . $item['userUID']
		 . '::display=inline'
		 . '::size=thumbsm' 
		 . '::link=no' 
		 . ':]]';

		$imgBlock = $theme->expandBlocks($imgBlock, 'nav1');

		if (false == strpos($imgBlock, 'unavailable')) { $imglist[] = $imgBlock; }

	}

	$html = ''
	 . implode('', $imglist)
	 . "<br/>"
	 . "<b>By:</b> " . implode(', ', $namelist);

	if ($totalMembers > $num) { $html .= " and " . ($totalMembers - $num) . " others."; }

	return $html;
}

?>
