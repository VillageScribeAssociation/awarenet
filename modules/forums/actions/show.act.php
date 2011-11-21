<?

	require_once($kapenta->installPath . 'modules/forums/models/board.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display a board
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------	
	// control variables
	//----------------------------------------------------------------------------------------------	
	$pageNo = 1; 		//% list page to show (default is 1) [int]

	//----------------------------------------------------------------------------------------------	
	// check permissions and reference
	//----------------------------------------------------------------------------------------------	
	if ('' == $req->ref) { $page->do404('no such forum'); }					// check ref
	$UID = $aliases->findRedirect('forums_board'); 							// check correct ref
	if (false == $user->authHas('forums', 'forums_board', 'show', $UID)) 
		{ $page->do403('You are not authorized to view this forum.'); }

	//----------------------------------------------------------------------------------------------	
	// load the model
	//----------------------------------------------------------------------------------------------	
	$model = new Forums_Board($req->ref);									

	if (array_key_exists('page', $req->args) == true) 
		{ $pageNo = floor($req->args['page']); }

	//----------------------------------------------------------------------------------------------	
	// render the page
	//----------------------------------------------------------------------------------------------	
	$page->load('modules/forums/actions/show.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['pageno'] = $pageNo;
	$page->blockArgs['school'] = $model->school;
	$page->blockArgs['forumTitle'] = $model->title;
	$page->render();

?>
