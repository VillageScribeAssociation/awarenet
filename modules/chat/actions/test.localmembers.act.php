<?

	require_once($kapenta->installPath . 'modules/chat/models/room.mod.php');

//--------------------------------------------------------------------------------------------------
//*	test/develeopment action to display local members of a specified room
//--------------------------------------------------------------------------------------------------
//ref: UID of a Chat_Room object [string]

	//----------------------------------------------------------------------------------------------
	//	check reference and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	if ('' == $req->ref) { $page->doXmlError("No room UID given."); }
	$model = new Chat_Room($req->ref);
	if (false == $model->loaded) { $page->doXmlError("Unknown room."); }

	header('Content-type: application/xml');
	echo $theme->expandBlocks('[[:chat::localmembersxml:]]');

?>
