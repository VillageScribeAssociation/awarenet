<?

//--------------------------------------------------------------------------------------------------
//*	test loading SWF object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$testVideo = $kapenta->serverPath . 'modules/videos/temp/RSA-Animate-The-Empathic-Civilisation.flv';

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/videos/actions/testflv.page.php');
	$kapenta->page->blockArgs['swfFile'] = $testVideo;
	$kapenta->page->blockArgs['swfWidth'] = 570;
	$kapenta->page->blockArgs['swfHeight'] = 423;
	$kapenta->page->render();

?>
