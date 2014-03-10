<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	creates a tables of teacher contact details for a specified school
//--------------------------------------------------------------------------------------------------
//returns: html table [string:html]
//arg: schoolUID - UID of a Schools_School object [string]

function users_teachercontacts($args) {
		global $user;
		global $db;
		global $theme;

	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: contactdetails permission
	if (('admin' != $user->role) && ('teacher' != $user->role)) { return "[[:users::pleaslogin:]]";}
	if (false == array_key_exists('schoolUID', $args)) { return '(err: no schoolUID)'; }

	$sUID = $args['schoolUID'];			//%	shortcut [string]

	if (false == $db->objectExists('schools_school', $sUID)) { return '(err: bad schoolUID)'; }

	//----------------------------------------------------------------------------------------------
	//	query database
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "(role='teacher' OR role='admin')";
	$conditions[] = "school='" . $db->addMarkup($sUID) . "'";
	
	$range = $db->loadRange('users_user', '*', $conditions, 'surname');
	if (0 == count($range)) { return ''; }		

	//----------------------------------------------------------------------------------------------
	//	make table of teacher contact details
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('Name', 'Contact');
	foreach($range as $item) {
		$model = new Users_User();
		$model->loadArray($item);

		$contact = '';
		if ((true == array_key_exists('tel', $model->profile)) && ('' != $model->profile['tel']))
			{ $contact .= "<b>Phone:</b> " . $model->profile['tel'] . "<br/>\n"; }

		if ((true == array_key_exists('email', $model->profile)) && ('' != $model->profile['email']))
			{ $contact .= "<b>Email:</b> " . $model->profile['email'] . "<br/>\n"; }

		if ('' == $contact) { $contact = "<span class='ajaxerror'>no contact details</span>"; }

		$table[] = array($model->getNameLink(), $contact);
	}

	$html .= "<b>Teacher contact details:</b><br/>\n";
	$html .= $theme->arrayToHtmlTable($table, true, true);

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $html;
}

?>
