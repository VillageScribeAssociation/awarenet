<?

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
		global $db, $utils, $kapenta, $session;

		//------------------------------------------------------------------------------------------
		//	check for a page matching alternative device profiles
		//------------------------------------------------------------------------------------------
		$profile = $kapenta->session->get('deviceprofile');

		if ('desktop' !== $profile) {
			$altFile = str_replace('.page.php', ".{$profile}.page.php", $fileName);
			if (true == $kapenta->fileExists($altFile)) { $fileName = $altFile; }
		}

		$this->fileName = $fileName;

		//------------------------------------------------------------------------------------------
		//	load the page template and parse XML
		//------------------------------------------------------------------------------------------

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

		//------------------------------------------------------------------------------------------
		//	handle mobile browsers
		//------------------------------------------------------------------------------------------
		if ('true' == $kapenta->session->get('mobile')) {
			if ('twocol-rightnav.template.php' == $this->template) {
				$this->template = 'mobile.template.php';
			}
			if ('twocol-leftnav.template.php' == $this->template) {
				$this->template = 'mobile.template.php';
			}
		}

		$this->loaded = true;
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	save to file
	//----------------------------------------------------------------------------------------------

	function save() {
		global $kapenta;

		$clean = $this->toArray();

		foreach($clean as $key => $value) {
			$clean[$key] = $kapenta->db->addMarkup(stripslashes($value));
		}

		$xml = $kapenta->utils->arrayToXml2d('page', $clean, true);
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
		global $kapenta, $session, $theme, $request, $user;

		//------------------------------------------------------------------------------------------
		//	page / environment variables to be replaced in template
		//------------------------------------------------------------------------------------------
		$env = array(
			'%%websiteName%%' => $kapenta->websiteName,
			'%%serverPath%%' => $kapenta->serverPath,
			'%%moduleName%%' => $request['module'],
			'%%defaultTheme%%' => $kapenta->defaultTheme,
			'%%pageInstanceUID%%' => $this->UID,
			'%%jsUserUID%%' => $user->UID,
			'%%jsUserName%%' => $user->getName(),
			'%%jsTheme%%' => $kapenta->defaultTheme,
			'%%jsMobile%%' => $session->get('mobile') ? 'true' : 'false',
			'%%title%%' => $this->title
		);

		foreach($this->blockArgs as $arg => $value) { 
			if (false == array_key_exists($arg, $env)) { $env['%%' . $arg . '%%'] = $value; }
		}

		//------------------------------------------------------------------------------------------
		//	load the template
		//------------------------------------------------------------------------------------------

		if ('desktop' !== $session->get('deviceprofile')) {
			$this->template = str_replace('twocol-rightnav', 'mobile', $this->template);
			$this->template = str_replace('twocol-leftnav', 'mobile', $this->template);
			$this->template = str_replace('onecol', 'mobile', $this->template);
		} else {
			//echo "<textarea rows='2' cols='80'>not mobile</textarea><br/>\n";
		}

		$templateFile = 'themes/' . $theme->name . '/templates/' . $this->template;

		//echo "<h2>" . $this->template . "</h2>\n";

		if (false == $kapenta->fileExists($templateFile)) {
			$templateFile = 'themes/' . $theme->name . '/' . $this->template;
		}

		$template = $kapenta->fs->get($templateFile, false, true);

		$template = str_replace(array_keys($env), array_values($env), $template);

		//------------------------------------------------------------------------------------------
		//	awareNet only
		//------------------------------------------------------------------------------------------
		$this->menu1 = "[[:home::usermenu:]]";

		//------------------------------------------------------------------------------------------
		//	execute blocks in template and page sections
		//------------------------------------------------------------------------------------------

		if ('desktop' == $session->get('deviceprofile')) {

			$template = str_replace(
				array(
					'%%content%%',
					'%%nav1%%',
					'%%nav2%%',
					'%%menu1%%',
					'%%menu2%%',
					'%%breadcrumb%%',
					'%%sMessage%%',
					'%%debug%%',
				),
				array(
					$theme->expandBlocks($this->content, 'content'),
					$theme->expandBlocks($this->nav1, 'nav1'),
					$theme->expandBlocks($this->nav2, 'nav2'),
					$theme->expandBlocks($this->menu1, 'menu1'),
					$theme->expandBlocks($this->menu2, 'menu2'),
					$theme->expandBlocks($this->breadcrumb, 'breadcrumb'),
					$session->messagesToHtml(),
					$this->debugToHtml()
				),
				$template
			);

		} else {

			$template = str_replace(
				array(
					'%%content%%',
					'%%nav1%%',
					'%%nav2%%',
					'%%menu1%%',
					'%%menu2%%',
					'%%breadcrumb%%',
					'%%sMessage%%',
					'%%debug%%',
				),
				array(
					$theme->expandBlocks($this->content, 'mobile'),
					$theme->expandBlocks($this->nav1, 'nav1'),
					$theme->expandBlocks($this->nav2, 'nav2'),
					$theme->expandBlocks($this->menu1, 'menu1'),
					$theme->expandBlocks($this->menu2, 'menu2'),
					$theme->expandBlocks($this->breadcrumb, 'breadcrumb'),
					$session->messagesToHtml(),
					$this->debugToHtml()
				),
				$template
			);

		}

		$session->clearMessages();

		$template = str_replace(array_keys($env), array_values($env), $template);
		$template = $theme->expandBlocks($template, '');

		//------------------------------------------------------------------------------------------
		//	update jsinit, script and head (may have been modified by expanding blocks)
		//------------------------------------------------------------------------------------------
		$template = str_replace('%%head%%', $this->head, $template);
		$template = str_replace('%%script%%', $this->script, $template);
		$template = str_replace('%%jsinit%%', $this->jsinit, $template);

		//------------------------------------------------------------------------------------------
		//	perform env replacements again after blocks
		//------------------------------------------------------------------------------------------
		$template = str_replace(array_keys($env), array_values($env), $template);
		$template = str_replace('%%' . 'delme%%', '', $template);

		//------------------------------------------------------------------------------------------
		//	convert relative URLs to absolute URLs	(DEPRECATED behivior)
		//------------------------------------------------------------------------------------------
		$replaceset = explode('|', 'href|src|background|action');
		foreach($replaceset as $toAbs) {
			$template = str_replace($toAbs . "='/", $toAbs . "='" . $kapenta->serverPath, $template);
			$template = str_replace($toAbs . "=/", $toAbs . "=" . $kapenta->serverPath, $template);
			$template = str_replace($toAbs . "=\"/", $toAbs . "=\"" . $kapenta->serverPath, $template);
		}

		//------------------------------------------------------------------------------------------
		//	output to browser
		//------------------------------------------------------------------------------------------
		echo $template;

		//------------------------------------------------------------------------------------------
		//	log the page view (gethostbyaddr introduces a delay, start sending output first)
		//------------------------------------------------------------------------------------------
		$kapenta->logPageView();	// log this page view
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
	//	conditionally included files
	//==============================================================================================
	//	these are added by blocks as they need them

	//----------------------------------------------------------------------------------------------
	//.	add a CSS file (once)
	//----------------------------------------------------------------------------------------------
	//arg: $url - absolute location of CSS / JS file

	function requireCss($url) {
		global $kapenta;
		$url = str_replace('%%serverPath%%', $kapenta->serverPath, $url);
		$link = "\t<link href='$url' rel='stylesheet' type='text/css' />\n";
		if (false === strpos($this->head, $link)) { $this->head .= $link; }
	}

	//----------------------------------------------------------------------------------------------
	//.	add a Javascript file (once)
	//----------------------------------------------------------------------------------------------
	//arg: $url - absolute location of CSS / JS file

	function requireJs($url) {
		global $kapenta;
		$url = str_replace('%%serverPath%%', $kapenta->serverPath, $url);
		$link = "\t<script src='$url'></script>\n";
		if (false === strpos($this->head, $link)) { $this->head .= $link; }
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
		$URI = str_replace($kapenta->serverPath . '/', $kapenta->serverPath, $URI);
 		header( "HTTP/1.1 301 Moved Permanently" );
 		header( "Location: " . $URI ); 
		echo "The page you requested moved <a href='" . $URI  . "'>here</a>.";
		die();
	}

	//----------------------------------------------------------------------------------------------
	//.	forbidden
	//----------------------------------------------------------------------------------------------

	function do403($message = '', $iframe = false) {
		global $user;
		header( "HTTP/1.1 403 Forbidden" );

		$fileName = 'modules/home/actions/403.page.php';
		if (true == $iframe) { 	$fileName = 'modules/home/actions/403if.page.php'; }
		if ('' != $message) { $message = "<span class='ajaxerror'>$message</span>"; }

		if ('public' == $user->role) {
			$message = ''
			 . "<h1>Your session has expired.  Please log in again.</h1>\n"
			 . $message . "\n"
			 . "<p>You must be logged in to perform the requested action.  This probably just means "
			 . "that your session has expired, in which case you'll just need to log in "
			 . "again.</p>\n"
			 . "<h2>Log in</h2>\n"
			 . "[[:users::loginform::redirectSelf=yes:]]\n";

		} else {
			$message = ''
			 . "<h1>Not allowed.</h1>\n"
			 . $message . "\n"
			 . "<p>Some actions on awareNet need special permissions - only certain people should "
			 . "be able to take them.  For example: only you should be able to edit your profile, "
			 . "and only teachers should be able to use the teacher tools.  Your account is not "
			 . "cleared to perform the requested action.  If you think this is incorrect please "
			 . "contact an admin and we'll check.</p>\n";

		}

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
		$URI = str_replace($kapenta->serverPath . '/', $kapenta->serverPath, $URI);
 		header( "HTTP/1.1 302 Moved Temporarily" );
 		header( "Location: " . $URI ); 
		echo "The page you requested has moved <a href='" . $URI . "'>here</a>.";
		die();
	}

	//----------------------------------------------------------------------------------------------
	//.	you have died of dysentery
	//----------------------------------------------------------------------------------------------
	//opt: msg - an error message or response code [string]
	//opt: iframe - set to true to use iframe version [bool]

	function do404($message = '', $iframe = false) {
		global $user;
 		header( "HTTP/1.1 404 Not Found" );

		// choose which page template to use
		$fileName = 'modules/home/actions/404.page.php';
		if (true == $iframe) { $fileName = 'modules/home/actions/404if.page.php'; }

		if ('' == $message) { 
			// no message specified, show default 404 message
			//TODO: make this is an editable block
			$message = "";
		} else {
			$message = "<span class='ajaxerror'>$message</span>";
		}

		if ('public' == $user->role) {
			$message = ''
			 . "<h1>Oops, something went wrong</h1>"
			 . "<p>Please reload the page and try again.  If you keep experiencing this problem "
			 . "please send a message to an admin.</p>"
			 . "<h2>Log in</h2>"
			 . "You are not logged in, please try logging in again.<br/><br/>"
			 . "[[:users::loginform:]]"
			 . $message;
		} else {
			$message = ''
			 . "<h1>Oops, something went wrong</h1>"
			 . "Please reload the page and try again.  If you keep experiencing this problem "
			 . "please send a message to an admin.<br/><br/>"
			 . $message;
		}

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
		die();
	}

	//==============================================================================================
	//	AJAX (live) support
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	create a trigger for this page - DEPRECATED: page triggers no longer in use
	//----------------------------------------------------------------------------------------------	
	//arg: module - name of a kapenta module [string]
	//arg: channel - name of trigger / channel [string]
	//arg: block - block to refresh when tripped [string]
	//return: true on success, false on failure [bool]

	function setTrigger($module, $channel, $block) {
        /*
		$model = new Live_Trigger();
		$model->pageUID = $this->UID;
		$model->module = $module;
		$model->channel = $channel;
		$model->block = $block;
		$report = $model->save();
		if ('' == $report) { return true; }
        */		
        return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	fire a trigger on any pages listening for it - DEPRECATED: live triggers no longer supported
	//----------------------------------------------------------------------------------------------	
	//arg: module - module which provides block [string]
	//arg: channel - name of channel on which there is a message [string]

	function doTrigger($module, $channel) {
        /*
		global $db;

		$conditions = array();
		$conditions[] = "module='" . $db->addMarkup($module) . "'";
		$conditions[] = "channel='" . $db->addMarkup($channel) . "'";

		$range = $db->loadRange('live_trigger', '*', $conditions);
		foreach($range as $row) {
			$model = new Live_Trigger();
			$model->loadArray($row);
			$model->send();
		}
        */
	}

	//==============================================================================================
	//	live debug
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	set debug notifications
	//----------------------------------------------------------------------------------------------
	//opt: msg - an error message or response code [string]
	//returns true on success, false on failure [bool]

	function logDebugItem($system, $msg) {
		if (false == $this->logDebug) { return false; }
		if (false == array_key_exists($system, $this->debug)) { $this->debug[$system] = array(); }
		$this->debug[$system][] = str_replace('[[:', '[[%%delme%%:', $msg);
		return true;
	}

	//	DEPRECATED: function name is the same as property name, presently need for Kapmed support
	//	strix 2012-08-30

	function logDebug($system, $msg) {
		return $this->logDebugItem($system, $msg);
	}

	//----------------------------------------------------------------------------------------------
	//.	make debug report
	//----------------------------------------------------------------------------------------------

	function debugToHtml() {
		global $kapenta, $user, $role, $theme, $page;

		if (false == $this->logDebug) { return ''; }

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

			foreach($this->debug['query'] as $query) {
				$queries .= "<div class='inlinequote'>$query</div>\n";
			}			

			$report .= ''
			 . "[[:theme::navtitlebox"
			 . "::label=" . count($this->debug['query']) . " Database Queries"
			 . "::toggle=debugDbQueries"
			 . ":]]\n"
			 . "<div id='debugDbQueries'>" . $queries . "</div><br/>\n";
		}

		//------------------------------------------------------------------------------------------		
		//	add memcached activity
		//------------------------------------------------------------------------------------------

		if (true == array_key_exists('memcached', $this->debug)) {
			$mca = '';

			foreach($this->debug['memcached'] as $query) {
				$mca .= "<div class='inlinequote'>$query</div>\n";
			}			

			$report .= ''
			 . "[[:theme::navtitlebox"
			 . "::label=" . count($this->debug['memcached']) . " Memcached Activity "
			 . "::toggle=debugMemcached"
			 . ":]]\n"
			 . "<div id='debugMemcached'>" . $mca . "</div><br/>\n";
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
