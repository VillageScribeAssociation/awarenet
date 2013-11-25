<?
	//require_once($kapenta->installPath . 'modules/newsletter/models/subscription.mod.php');
	// ^ sometimes needed for breadcrumbs, etc

//--------------------------------------------------------------------------------------------------
//*	show form to edit a Subscription object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('Subscription not specified'); }
	$UID = $kapenta->request->ref;
	if (false == $db->objectExists('newsletter_subscription', $UID)) { $page->do404(); }
	if (false == $user->authHas('newsletter', 'newsletter_subscription', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Subscriptions.'); }


	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/newsletter/actions/editsubscription.page.php');
	$kapenta->page->blockArgs['UID'] = $UID;
	$kapenta->page->blockArgs['subscriptionUID'] = $UID;
	$kapenta->page->render();

?>
