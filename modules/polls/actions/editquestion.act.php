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
	if (false == array_key_exists('refModule', $kapenta->request->args)) { $kapenta->page->do404('no refModule', true); }
	if (false == array_key_exists('refModel', $kapenta->request->args)) { $kapenta->page->do404('no refModel', true); }
	if (false == array_key_exists('refUID', $kapenta->request->args)) { $kapenta->page->do404('no refUID', true); }

	$refModule = $kapenta->request->args['refModule'];
	$refModel = $kapenta->request->args['refModel'];
	$refUID = $kapenta->request->args['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $kapenta->page->do404('unknown module', true); }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { $kapenta->page->do404('unknown owner', true); }

	if (false == $user->authHas($refModule, $refModel, 'polls-add', $refUID)) { 
		$kapenta->page->do403('You are not authorized to add polls to this item.', true);
	}

	//----------------------------------------------------------------------------------------------
	//	discover if this object already has a poll question or not
	//----------------------------------------------------------------------------------------------
	$conditions = array();
	$conditions[] = "refModule='" . $kapenta->db->addMarkup($refModule) . "'";
	$conditions[] = "refModel='" . $kapenta->db->addMarkup($refModel) . "'";
	$conditions[] = "refUID='" . $kapenta->db->addMarkup($refUID) . "'";

	$range = $kapenta->db->loadRange('polls_question', '*', $conditions);
	if (0 == count($range)) { $pageFile = 'modules/polls/actions/newquestion.if.page.php'; }

	foreach($range as $item) { $questionUID = $item['UID']; }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load($pageFile);
	$kapenta->page->blockArgs['refModule'] = $refModule;
	$kapenta->page->blockArgs['refModel'] = $refModel;
	$kapenta->page->blockArgs['refUID'] = $refUID;
	$kapenta->page->blockArgs['UID'] = $questionUID;
	$kapenta->page->blockArgs['questionUID'] = $questionUID;
	$kapenta->page->render();

?>
