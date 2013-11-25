<?

//--------------------------------------------------------------------------------------------------
//*	move learners from one school to another
//--------------------------------------------------------------------------------------------------
//postarg: from - UID of a Schools_School object [string]
//postarg: to - UID of a Schools_School object [string]
//postarg: user - UID or alias of a Users_User object, '*' for all users [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }			//	admins only

	if (false == array_key_exists('from', $args)) { $page->do404("Source school not given."); }
	if (false == array_key_exists('to', $args)) { $page->do404("Destination school not given."); }
	if (false == array_key_exists('user', $args)) { $page->do404("User(s) not spcified."); }

	$fromSchool = new Schools_School($_POST['from']);
	if (false == $fromSchool->loaded) { $page->do404('Source school not found.'); }

	$toSchool = new Schools_School($_POST['to']);
	if (false == $toSchool->loaded) { $page->do404('Destination school not found.'); }

	//----------------------------------------------------------------------------------------------
	//	move all users from a school
	//----------------------------------------------------------------------------------------------

	if ('*' == $_POST['user']) { 
		$conditions = array("school='" . $db->addMarkup($fromSchool->UID) . "'");
		$range = $db->loadRange('users_user', '*', $conditions);
		foreach($range as $item) {
			
			$model = new Users_User($item['UID']);
			$model->school = $toSchool->UID;
			$report = $model->save();
			
			if ('' == $report) { 
				$msg = ''
				 . "Changed school for user [[:users::namelink::userUID=" . $model->UID . ":]] "
				 . "from [[:school::name::schoolUID=" . $fromSchool->UID . "::link=yes:]] "
				 . "to [[:school::name::schoolUID=" . $toSchool->UID . "::link=yes:]]";
				$session->msg($msg, 'ok');
			} else {
				$msg = "Could not move [[:users::namelink::userUID=" . $model->UID . ":]].";
				$session->msg($msg, 'bad');
			}

		}
	}

	//----------------------------------------------------------------------------------------------
	//	TODO: handle other cases
	//----------------------------------------------------------------------------------------------

	$page->do404("Not yet implemented.");

?>
