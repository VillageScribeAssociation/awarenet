<?

//--------------------------------------------------------------------------------------------------
//*	display number of users active over a given period
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }
	$month = (60 * 60 * 24 * 30);	// roughly (TODO: improve)

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/users/actions/loginstats.page.php');
	$page->blockArgs['dateNow'] = $db->datetime();
	$page->blockArgs['dateOneMonth'] = $db->datetime(time() - ($month * 1));
	$page->blockArgs['dateThreeMonth'] = $db->datetime(time() - ($month * 3));
	$page->blockArgs['dateSixMonth'] = $db->datetime(time() - ($month * 6));
	$page->render();

?>
