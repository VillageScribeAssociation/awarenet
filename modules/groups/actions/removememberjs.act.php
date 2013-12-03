<?

	require_once($kapenta->installPath . 'modules/groups/models/group.mod.php');

//--------------------------------------------------------------------------------------------------
//*	remove a member from a group, return HTML snippet to browser JS client
//--------------------------------------------------------------------------------------------------
//+	a groupUID and userUID should be posted along with 'removeMember as the form action

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) 
		{ echo "<span class='ajaxerror'>Action not given.</span>"; die(); }

	if ('removeMember' != $_POST['action']) 
		{ echo "<span class='ajaxerror'>Action not recognized.</span>"; die(); }

	if (false == array_key_exists('userUID', $_POST)) 
		{ echo "<span class='ajaxerror'>User UID not given.</span>"; die(); }

	if (false == array_key_exists('groupUID', $_POST)) 
		{ echo "<span class='ajaxerror'>Group UID not given.</span>"; die(); }

	$userUID = $_POST['userUID'];					//%	UID of a Users_User object [string]
	$groupUID = $_POST['groupUID'];					//%	UID of a Groups_Group object [string]
	$position = 'member';							//%	placeholder for event arg [string]
	$isAdmin = 'no';								//%	placeholder for event arg [string]

	$model = new Groups_Group($_POST['groupUID']);
	if (false == $model->loaded) { echo "<span class='ajaxerror'>Unknown group.</span>"; die(); }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	remove the member
	//----------------------------------------------------------------------------------------------
	if (false == $model->hasMember($userUID)) {
		echo "<span class='ajaxwarn'>This person is not a group member.</span>"; die();
	}

	$members = $model->getMembers();				//%	set of all group memberships [array]
	foreach($members as $membership) {					
		if ($userUID == $membership['userUID']) {
			$position = $membership['position'];	
			$isAdmin = $membership['admin'];
		}
	}

	$model->removeMember($userUID);					//	note that this will also update school index

	//----------------------------------------------------------------------------------------------
	//	raise event
	//----------------------------------------------------------------------------------------------
	$args = array(
		'module' => 'groups',
		'groupUID' => $model->UID,
		'userUID' => $userUID,
		'position' => $position,
		'admin' => $isAdmin
	);

	$kapenta->raiseEvent('groups', 'member_removed', $args);
	echo "<span class='ajaxmsg'>Removed from group.</span>";

?>
