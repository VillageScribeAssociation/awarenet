<?

//--------------------------------------------------------------------------------------------------
//*	display number of users active over a given period
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }
	$month = (60 * 60 * 24 * 30);	// roughly (TODO: improve)

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/users/actions/loginstats.page.php');
	$kapenta->page->blockArgs['dateNow'] = $kapenta->db->datetime();
	$kapenta->page->blockArgs['dateOneMonth'] = $kapenta->db->datetime(time() - ($month * 1));
	$kapenta->page->blockArgs['dateThreeMonth'] = $kapenta->db->datetime(time() - ($month * 3));
	$kapenta->page->blockArgs['dateSixMonth'] = $kapenta->db->datetime(time() - ($month * 6));
	$kapenta->page->render();

?>
