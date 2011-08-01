<?

	require_once($kapenta->installPath . 'core/khtml.class.php');

//--------------------------------------------------------------------------------------------------
//*	test of HTML parser
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$html = '';
	$testResult = '';
	$allowedTags = $registry->get('kapenta.htmlparser.allowtags');

	//----------------------------------------------------------------------------------------------
	//	set allowed tags
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('setAllowedTags' == $_POST['action'])) {
		$tags = explode('|', $_POST['tags']);
		foreach($tags as $idx => $tag) { $tags[$idx] = trim($tag); }
		$allowedTags = implode('|', $tags);
		$registry->set('kapenta.htmlparser.allowtags', $allowedTags);
		$session->msg('<b>Updated tag list:</b><br/>' . $allowedTags, 'ok');

	}

	//----------------------------------------------------------------------------------------------
	//	test parser
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('testParser' == $_POST['action'])) {
		$html = $_POST['raw'];
		$parser = new KHTMLParser($html, true);

		$testResult = ''
			 . "[[:theme::navtitlebox::label=Result:]]\n"
			 . "<b>Parser output:</b><br/>\n"
			 . "<textarea rows='20' cols='80' name='result'>"
			 . $parser->output
			 . "</textarea><br/>\n"
			 . "<hr/><br/>\n"
			 . "[[:theme::navtitlebox::label=Parser Debug Log::toggle=divParserDebug:]]\n"
			 . "<div id='divParserDebug' style='visibility: hidden; display: none;'>\n"
			 . $parser->log
			 . "</div>\n";

	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/admin/actions/parser.page.php');
	$page->blockArgs['sampleHtml'] = $html;
	$page->blockArgs['allowedTags'] = $allowedTags;
	$page->blockArgs['testResult'] = $testResult;
	$page->render();


?>
