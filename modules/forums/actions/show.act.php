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
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('no such forum'); }					// check ref
	$UID = $aliases->findRedirect('forums_board'); 							// check correct ref
	if (false == $kapenta->user->authHas('forums', 'forums_board', 'show', $UID)) 
		{ $kapenta->page->do403('You are not authorized to view this forum.'); }

	//----------------------------------------------------------------------------------------------	
	// load the model
	//----------------------------------------------------------------------------------------------	
	$model = new Forums_Board($kapenta->request->ref);									

	if (array_key_exists('page', $kapenta->request->args) == true) 
		{ $pageNo = floor($kapenta->request->args['page']); }

	//----------------------------------------------------------------------------------------------	
	// render the page
	//----------------------------------------------------------------------------------------------	
	$kapenta->page->load('modules/forums/actions/show.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['raUID'] = $kapenta->request->ref;
	$kapenta->page->blockArgs['pageno'] = $pageNo;
	$kapenta->page->blockArgs['school'] = $model->school;
	$kapenta->page->blockArgs['forumTitle'] = $model->title;
	$kapenta->page->render();

?>
