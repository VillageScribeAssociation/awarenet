<?

//--------------------------------------------------------------------------------------------------
//*	action to search for friends
//--------------------------------------------------------------------------------------------------

	$search = '';		//%	search query, if any [string]

	//----------------------------------------------------------------------------------------------
	//	check permissions and arguments
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('users', 'users_user', 'show'))
		{ $kapenta->page->do403('Please log in.', true); }

	if (true == array_key_exists('q', $_POST)) { $search = $utils->cleanString($_POST['q']); }

	//----------------------------------------------------------------------------------------------
	//	add var for search?
	//----------------------------------------------------------------------------------------------

	$add = '';
	if (true == array_key_exists('add', $kapenta->request->args)) {
		$add = "<br/>[[:users::friendrequestprofilenav::"
					 . "userUID=" . $kapenta->request->args['add'] . "::notitle=yes:]]";
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/find.page.php');
	$kapenta->page->blockArgs['fsearch'] = $search;
	$kapenta->page->blockArgs['fadd'] = $add;
	$kapenta->page->render();

?>
