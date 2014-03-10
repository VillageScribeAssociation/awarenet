<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');

//--------------------------------------------------------------------------------------------------
//|	change the owner of some dependant object (eg, move images between galleries)
//--------------------------------------------------------------------------------------------------

function admin_WebShell_reattach($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $theme;
	global $utils;
	global $kapenta;

	$ajw = "<span class='ajaxwarn'>";		//%	tidy [string]
	$html = '';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	$mode = 'reattach';
	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'reattach':
			//--------------------------------------------------------------------------------------
			//	check arguments
			//--------------------------------------------------------------------------------------
			if (false == array_key_exists(0, $args)) { return 'table not given'; }
			if (false == array_key_exists(1, $args)) { return 'UID not given'; }
			if (false == array_key_exists(2, $args)) { return 'owner module not given'; }
			if (false == array_key_exists(3, $args)) { return 'owner model not given'; }
			if (false == array_key_exists(4, $args)) { return 'owner UID not given'; }

			$forModel = $args[0];
			$forUID = $args[1];
			$ownerModule = $args[2];
			$ownerModel = $args[3];
			$ownerUID = $args[4];

			if (false == $kapenta->db->objectExists($forModel, $forUID)) { return '(object not found)'; }
			if (false == $kapenta->db->objectExists($ownerModel, $ownerUID)) { return '(owner not found)'; }

			$objAry = $kapenta->db->getObject($forModel, $forUID);

			if (
				(false == array_key_exists('refModule', $objAry)) ||
				(false == array_key_exists('refModel', $objAry)) ||
				(false == array_key_exists('refUID', $objAry))
			) {
				return "Objects of this type do not belong to anything else.";
			}

			$html .= ''
			 . "Changing ownership of $forModel::$forUID<br/>"
			 . "<tt>from:"
			 . $objAry['refModule'] . '::' . $objAry['refModel'] . '::' . $objAry['refUID']
			 . "</tt><br/>"
			 . "<tt>to: $ownerModule::$ownerModel::$ownerUID</tt><br/>";

			$sql = ''
			 . "UPDATE $forModel SET"
			 . " refModule='" . $kapenta->db->addMarkup($ownerModule) . "',"
			 . " refModel='" . $kapenta->db->addMarkup($ownerModel) . "',"
			 . " refUID='" . $kapenta->db->addMarkup($ownerUID) . "'"
			 . " WHERE UID='" . $kapenta->db->addMarkup($forUID) . "'";

			$result = $kapenta->db->query($sql);
			if (true == $result) { $html .= "<span class='ajaxmsg'>done</span>"; }
			else { $html .= "<span class='ajaxwarn'>database error, could not reset.</span>"; }

			break;	//..............................................................................

		case 'noauth':
			//--------------------------------------------------------------------------------------
			//	user not authorized
			//--------------------------------------------------------------------------------------
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.describe command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function admin_WebShell_reattach_help($short = false) {
	if (true == $short) { return "Change which object something is attached to."; }

	$html = "
	<b>usage: admin.reattach</b> <i>table UID module model UID</i><br/>
	<br/>
	This command changes the ownership of some dependant object (defind by table and UID) to
	the module, model and object specified in the last three arguments.
	<br/>
	";

	return $html;
}


?>
