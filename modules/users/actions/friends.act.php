<?

//--------------------------------------------------------------------------------------------------
//	display a users friends ( and their grade?)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	which users friends?
	//----------------------------------------------------------------------------------------------

	// by default show users own friends
	$authorised = true;
	$userUID = $user->data['UID'];
	$userName = $user->getName;

	// if a user was specified
	if ($request['ref'] != '') {
		$reqUID = raGetOwner($request['ref'], 'users');
		if ($reqUID != 'false') { 

			$userUID = $reqUID; 
			$userName = expandBlocks('[[:users::name::userUID=' . $userUID . ':]]', '');

			//--------------------------------------------------------------------------------------
			//	show some other users friends - authorised
			//--------------------------------------------------------------------------------------

			// TODO: consider this
			if ($user->data['ofGroup'] == 'public') { $authorised = false; }

		}
	}

	//----------------------------------------------------------------------------------------------
	//	show the page
	//----------------------------------------------------------------------------------------------

	if ($authorised == false) { do403(); }
	$page->load($installPath . 'modules/users/actions/friends.page.php');
	$page->blockArgs['userUID'] = $userUID;
	$page->blockArgs['userRa'] = $request['ref'];
	$page->blockArgs['userName'] = $userName;
	$page->data['title'] = 'awareNet - friends of ' . $userName;
	$page->render();

?>
