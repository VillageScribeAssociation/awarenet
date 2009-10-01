<?

//--------------------------------------------------------------------------------------------------
//	show all users in a given year for a given school
//--------------------------------------------------------------------------------------------------

	// TODO: This should really be split into two actions

	//----------------------------------------------------------------------------------------------
	//	show a named grade
	//----------------------------------------------------------------------------------------------

	if (array_key_exists('grade', $request['args']) == true) { 
		if ($request['ref'] == '') { do404(); }
		require_once($installPath . 'modules/schools/models/schools.mod.php');
		$model = new School($request['ref']);

		$page->load($installPath . 'modules/schools/actions/grade.page.php');
		$page->blockArgs['raUID'] = $request['ref'];
		$page->blockArgs['schoolUID'] = $model->data['UID'];
		$page->blockArgs['schoolName'] = $model->data['name'];
		$page->blockArgs['schoolDescription'] = $model->data['description'];
		$page->blockArgs['grade'] = base64_decode($request['args']['grade']);

		$page->data['breadcrumb'] = ""
						  . "[[:theme::breadcrumb::label=Schools - ::link=/schools/:]]\n"
						  . "[[:theme::breadcrumb::label=" . $model->data['name']
						  . " - ::link=/schools/" . $model->data['recordAlias'] . ":]]\n"				
						  . "[[:theme::breadcrumb::label=" . $page->blockArgs['grade'] . ":]]\n";

		$page->render();

	}

	//----------------------------------------------------------------------------------------------
	//	show the grade of a specified user
	//----------------------------------------------------------------------------------------------

	if (array_key_exists('user', $request['args']) == true) {
		require_once($installPath . 'modules/schools/models/schools.mod.php');
	
		$u = new Users($request['args']['user']);
		$model = new School($u->data['school']);

		$page->load($installPath . 'modules/schools/actions/grade.page.php');
		$page->blockArgs['raUID'] = $request['ref'];
		$page->blockArgs['schoolUID'] = $model->data['UID'];
		$page->blockArgs['schoolName'] = $model->data['name'];
		$page->blockArgs['schoolDescription'] = $model->data['description'];
		$page->blockArgs['grade'] = $u->data['grade'];
		$page->blockArgs['userRa'] = $u->data['recordAlias'];
		$page->blockArgs['userUID'] = $u->data['UID'];
		$page->blockArgs['userName'] = $u->getName();

		$page->data['menu2'] = "[[:users::menu::userUID=" . $u->data['UID'] . ":]]";
		$page->data['title'] = ':: awareNet :: people :: ' . $u->getName() . ' :: classmates ::';

		$page->render();		
	}

	//----------------------------------------------------------------------------------------------
	//	just in case
	//----------------------------------------------------------------------------------------------

	if ( (array_key_exists('user', $request['args']) == false) 
	   && (array_key_exists('grade', $request['args']) == false) ) { do404(); }

?>
