<?

	require_once($kapenta->installPath . 'core/khtml.class.php');

//--------------------------------------------------------------------------------------------------
//*	test of HTML parser
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	admins only
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$html = '';
	$testResult = '';
	$allowedTags = $kapenta->registry->get('kapenta.htmlparser.allowtags');

	//----------------------------------------------------------------------------------------------
	//	set allowed tags
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('setAllowedTags' == $_POST['action'])) {
		$tags = explode('|', $_POST['tags']);
		foreach($tags as $idx => $tag) { $tags[$idx] = trim($tag); }
		$allowedTags = implode('|', $tags);
		$kapenta->registry->set('kapenta.htmlparser.allowtags', $allowedTags);
		$session->msg('<b>Updated tag list:</b><br/>' . $allowedTags, 'ok');

	}

	//----------------------------------------------------------------------------------------------
	//	test parser
	//----------------------------------------------------------------------------------------------

	if ((true == array_key_exists('action', $_POST)) && ('testParser' == $_POST['action'])) {
		$html = $_POST['raw'];
		$mq = strtolower(ini_get('magic_quotes_gpc'));
		if (('on' == $mq) || ('1' == $mq)) { $html = stripslashes($html); }

		$parser = new KHTMLParser($html, true);

		$testResult = ''
			 . "[[:theme::navtitlebox::label=Result:]]\n"
			 . "<b>Parser output:</b><br/>\n"
			 . "<textarea rows='20' cols='80' name='result'>"
			 . str_replace('[[:', '[%%delme%%[:', $utils->trimHtml($parser->output))
			 . "</textarea><br/>\n"
			 . "<hr/><br/>\n"
			 . "[[:theme::navtitlebox::label=Parser Debug Log::toggle=divParserDebug:]]\n"
			 . "<div id='divParserDebug' style='visibility: hidden; display: none;'>\n"
			 . str_replace('[[:', '[%%delme%%[:', $parser->log)
			 . "</div>\n";

	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/admin/actions/parser.page.php');
	$kapenta->page->blockArgs['sampleHtml'] = $html;
	$kapenta->page->blockArgs['allowedTags'] = $allowedTags;
	$kapenta->page->blockArgs['testResult'] = $testResult;
	$kapenta->page->render();


?>
