<?

	require_once($installPath . 'modules/live/models/mailbox.mod.php');
	require_once($installPath . 'modules/live/models/trigger.mod.php');

//--------------------------------------------------------------------------------------------------
//*	object for reading, writing and rendering pages and other response documents
//--------------------------------------------------------------------------------------------------

class KPage {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	var $fileName;			//_ the .page.php file being loaded [string]
	var $loaded = false;	//_ is set to true when a page is loaded [bool]

	var $UID;				//_ UID of the current response document [string]

	var $data;				//_ page components [array]
	var $blockArgs;			//_ block arguments [array]

	var $debug;				//_	array of debug notices [array]
	var $logDebug = false;	//_	set to true to capture and display page debug log [bool]

	// the parts of a page -------------------------------------------------------------------------

	var $template = '';		//_	document template - supplies stylesheet, column layout, etc [string]
	var $title = '';		//_	title of the page/document, if format supports this [string]
	var $content = '';		//_	main content section of the document [string]
	var $nav1 = '';			//_	second column of page [string]
	var $nav2 = '';			//_	third column of page, if any [string]

	var $script = '';		//_	javascript section (in header) [string]
	var $jsinit = '';		//_	javascript to be run on page load [string]

	var $banner = '';		//_	page banner graphic (not implemented in awarenet) [string]
	var $head = '';			//_	optional items in document header (css, linked js, etc) [string]

	var $menu1 = '';		//_	first (top) level menu [string]
	var $menu2 = '';		//_	second level menu [string]
	var $section = '';		//_	for menu highlighting, not used in awarenet [string]
	var $subsection = '';	//_ for menu highlighting, not used in awarenet [string]
	var $breadcrumb = '';	//_	le sigh [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------
	//opt: fileName - fileName relative to installPath [string]

	function KPage($fileName = '') {
		global $kapenta;
		$this->UID = $kapenta->createUID();					// current render ID
		$this->blockArgs = array();							// default block environment variables
		$this->debug = array();
		if ($fileName != '') { $this->load($fileName); }
	}

	//----------------------------------------------------------------------------------------------
	//.	load page template from a file
	//----------------------------------------------------------------------------------------------
	//arg: fileName - fileName relative to installPath [string]
	//returns: true on success, false on failure [bool]

	function load($fileName) {
		global $db, $utils, $kapenta;

		$this->fileName = $fileName;
		$xmlDoc = new KXmlDocument($fileName, true);
		if (false == $xmlDoc->loaded) { 
			$kapenta->logErr('KPage', 'load()', 'could not load: ' . $fileName);
			echo "<br/>could not load $fileName<br/>\n";
			return false; 
		}

		$this->data = $xmlDoc->getChildren2d();

		foreach($this->data as $key => $val) {
			$val = $db->removeMarkup($val); 
			$val = $utils->removeHtmlEntities($val);

			switch($key) {
				case 'template':	$this->template = $val;		break;
				case 'title':		$this->title = $val;		break;
				case 'content':		$this->content = $val; 		break;
				case 'nav1':		$this->nav1 = $val;			break;
				case 'nav2':		$this->nav2 = $val;			break;
				case 'script':		$this->script = $val;		break;
				case 'jsinit':		$this->jsinit = $val;		break;
				case 'banner':		$this->banner = $val;		break;
				case 'head':		$this->head = $val;			break;
				case 'menu1':		$this->menu1 = $val;		break;
				case 'menu2':		$this->menu2 = $val;		break;
				case 'section':		$this->section = $val;		break;
				case 'subsection':	$this->subsection = $val;	break;
				case 'breadcrumb':	$this->breadcrumb = $val;	break;
			}
		}

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	save to file
	//----------------------------------------------------------------------------------------------

	function save() {
		global $kapenta, $db, $utils;

		$clean = $this->toArray();
		foreach($clean as $key => $value) { $clean[$key] = $db->addMarkup(stripslashes($value)); }
		$xml = $utils->arrayToXml2d('page', $clean, true);
		$kapenta->filePutContents($this->fileName, $xml, false, true);
	}

	//----------------------------------------------------------------------------------------------
	//	serialize to array
	//----------------------------------------------------------------------------------------------

	function toArray() {
		$serialize = array(
			'template' => $this->template,
			'title' => $this->title,
			'content' => $this->content,
			'nav1' => $this->nav1,
			'nav2' => $this->nav2,
			'script' => $this->script,
			'jsinit' => $this->jsinit,
			'banner' => $this->banner,
			'head' => $this->head,
			'menu1' => $this->menu1,
			'menu2' => $this->menu2,
			'section' => $this->section,
			'subsection' => $this->subsection,
			'breadcrumb' => $this->breadcrumb
		);
		return $serialize;
	}

	//----------------------------------------------------------------------------------------------
	//	render (all blocks in all template areas)
	//----------------------------------------------------------------------------------------------

	function render() {
		global $kapenta, $session, $theme, $request, $user, $serverPath;
		//------------------------------------------------------------------------------------------
		//	set some universals
		//------------------------------------------------------------------------------------------
		$d = $this->toArray();
		$d['websiteName'] = $kapenta->websiteName;
		$d['serverPath'] = $kapenta->serverPath;
		$d['moduleName'] = $request['module'];
		$d['defaultTheme'] = $kapenta->defaultTheme;
		$d['pageInstanceUID'] = $this->UID;
		$d['jsUserUID'] = $user->UID;
		$d['sMessage'] = '';
		//$d['debug'] = '';

		//------------------------------------------------------------------------------------------
		//	session messages
		//------------------------------------------------------------------------------------------

		if ('' != $session->message) {

			$d['sMessage'] = "[[:theme::navtitlebox::label=Notice::toggle=divSMessage:]]"
						   . "<div id='divSMessage'>"
						   . $session->message . "</div><br/>\n";

			$session->message = ''; // send message to user once only
		}

		//------------------------------------------------------------------------------------------
		//	load the template
		//------------------------------------------------------------------------------------------
		$templateFile = 'themes/' . $theme->name . '/' . $d['template'];
		$template = $kapenta->fileGetContents($templateFile, false, true);

		//------------------------------------------------------------------------------------------
		//	decide on a menu
		//------------------------------------------------------------------------------------------

		$adminCl = '';
		if ('admin' == $user->role) 
			{ $adminCl = "<a href='%%serverPath%%admin/' class='menu'>Admin</a>"; }

		$this->blockArgs['adminConsoleLink'] = $adminCl;

		$menuFile = '/views/usermenu.block.php';
		if ('public' == $user->role) { $menuFile = '/views/publicmenu.block.php'; }
		$d['menu1'] = $theme->loadBlock('/themes/' . $kapenta->defaultTheme . $menuFile);

		//------------------------------------------------------------------------------------------
		//	perform the replacements
		//------------------------------------------------------------------------------------------
		foreach($d as $f => $r)	// replace an all page components except template
			{ if ($f != 'template') { $template = str_replace('%%' . $f . '%%', $r, $template); } }

		$template = $this->replaceLabels($this->blockArgs, $template);

		//------------------------------------------------------------------------------------------
		//	execute blocks
		//------------------------------------------------------------------------------------------
		$template = $theme->expandBlocks($template, '');
		//------------------------------------------------------------------------------------------
		//	style 
		//------------------------------------------------------------------------------------------		
		if (false == $theme->styleLoaded) { $theme->readStyle(); }
		$template = $this->replaceLabels($theme->style, $template);

		//------------------------------------------------------------------------------------------
		//	special admin option
		//------------------------------------------------------------------------------------------
		if ('admin' == $user->role) {
			//$fileName = str_replace($installPath, '', $this->fileName);
			//$parts = explode('/', $fileName);
			//$editLink = "<a href='/pages/edit/module_" . $parts[1] . "/" . $parts[2] 
			//	  . "'>[edit this page]</a>";

			//$template = str_replace('</body>', $editLink . "\n</body>", $template);
		}

		//------------------------------------------------------------------------------------------
		//	include debug report if enabled
		//------------------------------------------------------------------------------------------
		
		if (true == $this->logDebug) { $d['debug'] = $this->debugToHtml(); }
		else { $d['debug'] = ''; }

		//------------------------------------------------------------------------------------------
		//	perform replacements again after blocks
		//------------------------------------------------------------------------------------------
		foreach($d as $f => $r) // replace an all page components except template
			{ if ($f != 'template') { $template = str_replace('%%' . $f . '%%', $r, $template); } }

		$template = $this->replaceLabels($this->blockArgs, $template);
		$template = str_replace('%%' . 'delme%%', '', $template);

		//------------------------------------------------------------------------------------------
		//	convert relative URLs to absolute URLs
		//------------------------------------------------------------------------------------------
		$replaceset = explode('|', 'href|src|background|action');
		foreach($replaceset as $toAbs) {
			$template = str_replace($toAbs . "='/", $toAbs . "='" . $serverPath, $template);
			$template = str_replace($toAbs . "=/", $toAbs . "=" . $serverPath, $template);
			$template = str_replace($toAbs . "=\"/", $toAbs . "=\"" . $serverPath, $template);
		}

		//------------------------------------------------------------------------------------------
		//	log the page view and send the page
		//------------------------------------------------------------------------------------------
		$kapenta->logPageView();	// log this page view
		$session->store();			// store session state
		echo $template;
	}

	//----------------------------------------------------------------------------------------------
	//.	substitute an array of values for labels in text
	//----------------------------------------------------------------------------------------------
	//arg: labels - array of 'label' => 'value' pairs  [array]
	//arg: txt - string to perform replacement on [string]
	//opt: marker - string delimiting labels, default is %% [string]
	//returns: string with labels replaced [string]

	function replaceLabels($labels, $txt, $marker = '%%') {
		global $kapenta;

		$labels['serverPath'] = $kapenta->serverPath;
		$labels['websiteName'] = $kapenta->websiteName;
		$labels['defaultTheme'] = $kapenta->defaultTheme;

		foreach($labels as $label => $value) 
			{$txt = str_replace($marker . $label . $marker, $value, $txt); }

		return $txt;
	}

	//----------------------------------------------------------------------------------------------
	//.	copy arguments from $request to $this->blockArgs ($argNames is a comma separated list)
	//----------------------------------------------------------------------------------------------

	function allowBlockArgs($argNames) {
		global $req;
		$argNames = explode(',', $argNames);  
		foreach($argNames as $argName) {
			$argName = trim($argName);
			if (array_key_exists($argName, $req->args)) {
				$this->blockArgs[$argName] = $req->args[$argName];
			}
		}
	}

	//==============================================================================================
	//	errors and redirects
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	redirect browser (and search engines) to latest alias for a record
	//----------------------------------------------------------------------------------------------
	//arg: URI - relative to serverPath [string]	

	function do301($URI) {
		global $kapenta, $session;
		$URI = $kapenta->serverPath . $URI;	
 		header( "HTTP/1.1 301 Moved Permanently" );
 		header( "Location: " . $URI ); 
		echo "The page you requested moved <a href='" . $URI  . "'>here</a>.";
		$session->store();		// store session state
		die();
	}

	//----------------------------------------------------------------------------------------------
	//.	forbidden
	//----------------------------------------------------------------------------------------------

	function do403($message = '', $iframe = false) {
		header( "HTTP/1.1 403 Forbidden" );

		$fileName = 'modules/home/actions/403.page.php';
		if (true == $iframe) { 	$fileName = 'modules/home/actions/403if.page.php'; }
		if ('' != $message) { $message = "<span class='ajaxerror'>$message</span>"; }

		$this->load($fileName);
		$this->blockArgs['message'] = $message;
		$this->blockArgs['loginredirect'] = $_SERVER['REQUEST_URI'];
		$this->render();
		die();
	}

	//----------------------------------------------------------------------------------------------
	//.	temporary redirect, for shuffling browsers around
	//----------------------------------------------------------------------------------------------
	//arg: URI - location relative to serverPath;

	function do302($URI) {
		global $kapenta, $session;
		$URI = $kapenta->serverPath . $URI;
 		header( "HTTP/1.1 302 Moved Temporarily" );
 		header( "Location: " . $URI ); 
		echo "The page you requested has moved <a href='" . $URI . "'>here</a>.";
		$session->store();		// store session state
		die();
	}

	//----------------------------------------------------------------------------------------------
	//.	you have died of dysentery
	//----------------------------------------------------------------------------------------------
	//opt: msg - an error message or response code [string]
	//opt: iframe - set to true to use iframe version [bool]

	function do404($message = '', $iframe = false) {
 		header( "HTTP/1.1 404 Not Found" );
	
		// choose which page template to use
		$fileName = 'modules/home/actions/404.page.php';
		if (true == $iframe) { $fileName = 'modules/home/actions/404if.page.php'; }

		if ('' == $message) { 
			// no message specified, show default 404 message
			//TODO: make this is an editable block
			$message = "<h1>404 - Not Found</h1>\n"
				 . "<p>The page you requested could not be found.  It may have been removed or "
				 . "deleted, check for typos in the link you followed to get here.</p>\n";

		} else { $message = "<span class='ajaxerror'>$message</span>"; }

		$this->load($fileName);
		$this->blockArgs['message'] = $message;
		$this->render();
		die();
	}

	//----------------------------------------------------------------------------------------------
	//.	xml error
	//----------------------------------------------------------------------------------------------
	//opt: msg - an error message or response code [string]

	function doXmlError($msg = '') {
		global $session;
	 	header( "HTTP/1.1 404 Not Found" );
		echo "<?xml version=\"1.0\"?>\n";
		echo "<error>$msg</error>\n";
		$session->store();		// store session state
		die();
	}

	//==============================================================================================
	//	AJAX (live) support
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	create a trigger for this page
	//----------------------------------------------------------------------------------------------	
	//arg: module - name of a kapenta module [string]
	//arg: name - name of trigger / channel [string]
	//arg: block - block to refresh when tripped [string]
	//return: true on success, false on failure [bool]

	function setTrigger($module, $channel, $block) {
		$model = new Live_Trigger();
		$model->pageUID = $this->UID;
		$model->module = $module;
		$model->channel = $channel;
		$model->block = $block;
		$report = $model->save();
		if ('' == $report) { return true; }
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	fire a trigger on any pages listening for it
	//----------------------------------------------------------------------------------------------	
	//arg: module - module which provides block [string]
	//arg: channel - name of channel on which there is a message [string]

	function doTrigger($module, $channel) {
		global $db;

		$conditions = array();
		$conditions[] = "module='" . $db->addMarkup($module) . "'";
		$conditions[] = "channel='" . $db->addMarkup($channel) . "'";

		$range = $db->loadRange('Live_Trigger', '*', $conditions);
		foreach($range as $row) {
			$model = new Live_Trigger();
			$model->loadArray($row);
			$model->send();
		}
	}

	//==============================================================================================
	//	live debug
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	set debug notifications
	//----------------------------------------------------------------------------------------------
	//opt: msg - an error message or response code [string]
	//returns true on success, false on failure [bool]

	function logDebug($system, $msg) {
		if (false == $this->logDebug) { return false; }
		if (false == array_key_exists($system, $this->debug)) { $this->debug[$system] = array(); }
		$this->debug[$system][] = $msg;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	make debug report
	//----------------------------------------------------------------------------------------------

	function debugToHtml() {
		global $kapenta, $user, $role, $theme, $page;

		$report = '';
		$block = $theme->loadBlock('themes/' . $kapenta->defaultTheme . '/views/debug.block.php');

		//------------------------------------------------------------------------------------------		
		//	add basic environment stuff
		//------------------------------------------------------------------------------------------
		$report .= "[[:theme::navtitlebox::label=Role Permissions::toggle=debugRolePerms:]]\n"
				. "<div id='debugRolePerms'>" . $role->toHtml() . "</div><br/>\n";

		//------------------------------------------------------------------------------------------		
		//	add dbLoad
		//------------------------------------------------------------------------------------------

		if (true == array_key_exists('dbLoad', $this->debug)) {
			$queries = '';
			foreach($this->debug['dbLoad'] as $query) 
				{ $queries .= "<div class='inlinequote'>$query</div>\n"; }

			$report .= "[[:theme::navtitlebox::label=dbLoad calls::toggle=debugDbLoad:]]\n"
					. "<div id='debugDbLoad'>" . $queries . "</div><br/>\n";
		}

		//------------------------------------------------------------------------------------------		
		//	add auth checks
		//------------------------------------------------------------------------------------------
		if (true == array_key_exists('auth', $this->debug)) {
			$queries = '';
			foreach($this->debug['auth'] as $query) 
				{ $queries .= "<div class='inlinequote'>$query</div>\n"; }

			$report .= "[[:theme::navtitlebox::label=Role Permissions::toggle=debugAuthChecks:]]\n"
					. "<div id='debugAuthChecks'>" . $queries . "</div><br/>\n";
		}

		//------------------------------------------------------------------------------------------		
		//	add database queries
		//------------------------------------------------------------------------------------------

		if (true == array_key_exists('query', $this->debug)) {
			$queries = '';
			foreach($this->debug['query'] as $query) 
				{ $queries .= "<div class='inlinequote'>$query</div>\n"; }

			$report .= "[[:theme::navtitlebox::label=Database Queries::toggle=debugDbQueries:]]\n"
					. "<div id='debugDbQueries'>" . $queries . "</div><br/>\n";
		}

		//------------------------------------------------------------------------------------------		
		//	substitute into block
		//------------------------------------------------------------------------------------------
		$report = str_replace('%%report%%', $report, $block);
		$report = $theme->expandBlocks($report, '');
		return $report;
	}

}

?>
