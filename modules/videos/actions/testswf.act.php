<?

//--------------------------------------------------------------------------------------------------
//*	test loading SWF object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$testVideo = $kapenta->serverPath . 'modules/videos/temp/aids_02 08873064-E72A-4B20-ABBC-D2534F155585.swf';
	$testVideo = $kapenta->serverPath . 'modules/videos/temp/456959901178324631.swf';
	//$testVideo = 'http://awarenet.co.za/data/videos/4/5/6/456959901178324631.swf';

	$navReturn = "
		<table noborder>
		  <tr>
			<td>
				<img src='%%serverPath%%themes/%%defaultTheme%%/icons/arrow_left.jpg' />
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
	$page->load('modules/videos/actions/testswf.page.php');
	$page->blockArgs['swfFile'] = $testVideo;
	$page->blockArgs['swfWidth'] = 968;
	$page->blockArgs['swfHeight'] = 672;
	$page->blockArgs['navReturn'] = $navReturn;
	$page->render();

?>
