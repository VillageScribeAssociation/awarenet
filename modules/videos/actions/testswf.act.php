<?

//--------------------------------------------------------------------------------------------------
//*	test loading SWF object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$testVideo = 'modules/videos/temp/aids_02 08873064-E72A-4B20-ABBC-D2534F155585.swf';
	$testVideo = 'modules/videos/temp/456959901178324631.swf';
	//$testVideo = 'http://awarenet.co.za/data/videos/4/5/6/456959901178324631.swf';

	$navReturn = "
		<table noborder>
		  <tr>
			<td>
				<img src='%%serverPath%%themes/%%defaultTheme%%/images/icons/arrow_left.jpg' />
			</td>
			<td>
			<b>Return to video gallery.</b>
			</td>
		  </tr>
		</table>
	";

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/videos/actions/testswf.page.php');
	$kapenta->page->blockArgs['swfFile'] = $kapenta->serverPath . $testVideo;
	$kapenta->page->blockArgs['swfWidth'] = 968;
	$kapenta->page->blockArgs['swfHeight'] = 672;
	$kapenta->page->blockArgs['navReturn'] = $navReturn;
	$kapenta->page->render();

?>
