<?

//--------------------------------------------------------------------------------------------------
//*	iframe for editing a poll question
//--------------------------------------------------------------------------------------------------

	$refModule = '';		//%	name of a kapenta module [string]
	$refModel = '';			//%	type of object to which poll is attached [string]
	$refUID = '';			//%	UID of object to which poll is attached [string]
	$questionUID = '';		//%	UID of the question, if any [string]

	$pageFile = 'modules/polls/actions/editquestion.if.page.php';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $req->args)) { $page->do404('no refModule', true); }
	if (false == array_key_exists('refModel', $req->args)) { $page->do404('no refModel', true); }
	if (false == array_key_exists('refUID', $req->args)) { $page->do404('no refUID', true); }

	$refModule = $req->args['refModule'];
	$refModel = $req->args['refModel'];
	$refUID = $req->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $page->do404('unknown module', true); }
	if (false == $db->objectExists($refModel, $refUID)) { $page->do404('unknown owner', true); }

	if (false == $user->authHas($refModule, $refModel, 'polls-add', $refUID)) { 
		$page->do403('You are not authorized to add polls to this item.', true);
	}

	//----------------------------------------------------------------------------------------------
	//	discover if this object already has a poll question or not
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $db->addMarkup($refModule) . "'";
	$conditions[] = "refModel='" . $db->addMarkup($refModel) . "'";
	$conditions[] = "refUID='" . $db->addMarkup($refUID) . "'";

	$range = $db->loadRange('polls_question', '*', $conditions);
	if (0 == count($range)) { $pageFile = 'modules/polls/actions/newquestion.if.page.php'; }

	foreach($range as $item) { $questionUID = $item['UID']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load($pageFile);
	$page->blockArgs['refModule'] = $refModule;
	$page->blockArgs['refModel'] = $refModel;
	$page->blockArgs['refUID'] = $refUID;
	$page->blockArgs['UID'] = $questionUID;
	$page->blockArgs['questionUID'] = $questionUID;
	$page->render();

?>
