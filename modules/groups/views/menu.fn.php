<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');
	require_once($kapenta->installPath . 'modules/groups/models/membership.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for groups, no arguments
//--------------------------------------------------------------------------------------------------

function groups_menu($args) {
	global $theme;
	global $user;

	$labels = array(
		'newEntry' => '',
		'editCurrentGroup' => '',
		'viewCurrentGroup' => '',
		'makeAnnouncement' => ''
	);									//%	menu items

	$html = '';							//%	return value [string]
	$action = '';						//%	control context menu items [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments (if any) and permissions
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('action', $args)) { $action = $args['action']; }

	if (true == array_key_exists('UID', $args)) {
		$UID = $args['UID'];

		if (true == $user->authHas('groups', 'groups_group', 'new', $UID)) {
			$labels['newEntry'] = '[[:theme::submenu::label=Add Group::link=/groups/new/:]]';
		}
	
		switch($action) {
			case 'show':
				if (true == $user->authHas('groups', 'groups_group', 'edit', $UID)) {
					$labels['editCurrentGroup'] = ''
					 . "[[:theme::submenu::label=Edit This Group::link=/groups/edit/" . $UID . ":]]";
				}
				break;

			case 'edit':
				$labels['viewCurrentGroup'] = ''
				 . "[[:theme::submenu::label=View This Group::link=/groups/" . $UID . ":]]";
				break;	
		}

			
		if (true == $user->authHas('groups', 'groups_group', 'announcements-add', $UID)) {
			$labels['makeAnnouncement'] = ''
			 . '[[:theme::submenu'
			 . '::label=Make Announcement'
			 . '::link=/announcements/new/refModule_groups/refModel_groups_group/refUID_' . $UID
			 . ':]]';
		}
	}

	//----------------------------------------------------------------------------------------------
	//	make and return the block
	//----------------------------------------------------------------------------------------------	
	$block = $theme->loadBlock('modules/groups/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
