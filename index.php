<?php

	require_once(dirname(__FILE__) . '/core/ksystem.class.php');
	require_once(dirname(__FILE__) . '/core/klegacy.class.php');

//--------------------------------------------------------------------------------------------------
//		 _                          _                                _    
//		| | ____ _ _ __   ___ _ __ | |_ __ _   ___  _ __ __ _  _   _| | __
//		| |/ / _` | '_ \ / _ \ '_ \| __/ _` | / _ \| '__/ _` || | | | |/ /
//		|   < (_| | |_) |  __/ | | | || (_| || (_) | | | (_| || |_| |   < 
//		|_|\_\__,_| .__/ \___|_| |_|\__\__,_(_)___/|_|  \__, (_)__,_|_|\_\
//		          |_|                                   |___/     
//                                                                           	Version 3.0 Beta
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//*	initialize the kapenta framework in CMS mode
//--------------------------------------------------------------------------------------------------

	set_time_limit(900);

	$kapenta = new KSystem(
		dirname(__FILE__) . '/',					//	install path
		'cms,session,user,role,recovery'			//	franework options
	);

//--------------------------------------------------------------------------------------------------
//	direct aliases of core components
//--------------------------------------------------------------------------------------------------

	$registry = new KLegacy_registry();
	$db = new KLegacy_db();
	$cache = new KLegacy_blockcache();					//	view/block cache
	$utils = new KLegacy_utils();					//	miscellaneous
	$revisions = new KLegacy_revisions();			//	object revision history and recycle bin

	$session = new KLegacy_session();
	$user = new KLegacy_user();
	$role = new KLegacy_role();

	$theme = new KLegacy_theme();
	$req = new KLegacy_request();
	$page = new KLegacy_page();


	$aliases = new KLegacy_aliases;					//	handles object aliases
	$notifications = new KLegacy_notifications;		//	user notification of events

	$kapenta->init();

//--------------------------------------------------------------------------------------------------
//	begin performance profiling if enabled
//--------------------------------------------------------------------------------------------------

/*
	if ('yes' == $kapenta->registry->get('xhprof.enabled')) {
		if (mt_rand(1, (int)$kapenta->registry->get('xhprof.samplesize')) == 1) {
			include_once __DIR__ . '/gui/xhprof/xhprof_lib/utils/xhprof_lib.php';
			include_once __DIR__ . '/gui/xhprof/xhprof_lib/utils/xhprof_runs.php';
			//xhprof_enable(XHPROF_FLAGS_NO_BUILTINS);
			xhprof_enable(XHPROF_FLAGS_MEMORY + XHPROF_FLAGS_CPU);  
		}
	}
*/

//--------------------------------------------------------------------------------------------------
//	include and instantiate the core global objects (database access, templating system, etc)
//--------------------------------------------------------------------------------------------------


	$request = $kapenta->request->toArray();						//	(DEPRECATED)
	$ref = $req->ref;								//	(DEPRECATED)

	if (false !== strpos($kapenta->request->raw,'static/css') 
		or false !== strpos($kapenta->request->raw,'static/images')
		or false !== strpos($kapenta->request->raw,'static/data')
		or false !== strpos($kapenta->request->raw,'static/js')
		or false !== strpos($kapenta->request->raw,'static/video-js')
		or false !== strpos($kapenta->request->raw,'content/')
		or false !== strpos($kapenta->request->raw,'jsi18n/')
		or false !== strpos($kapenta->request->raw,'api/info')
		or false !== strpos($kapenta->request->raw,'api/status')
		or false !== strpos($kapenta->request->raw,'api/get')
		or false !== strpos($kapenta->request->raw,'api/start')
		or false !== strpos($kapenta->request->raw,'api/delete')
		or false !== strpos($kapenta->request->raw,'api/check_video')
		or false !== strpos($kapenta->request->raw,'api/check_subtitle')
		or false !== strpos($kapenta->request->raw,'api/save')
		or false !== strpos($kapenta->request->raw,'api/cancel')
		or false !== strpos($kapenta->request->raw,'math')
		or false !== strpos($kapenta->request->raw,'science')
		or false !== strpos($kapenta->request->raw,'humanities')
		or false !== strpos($kapenta->request->raw,'test-prep')
		or false !== strpos($kapenta->request->raw,'discovery-lab')
		or false !== strpos($kapenta->request->raw,'exercisedashboard')
		or false !== strpos($kapenta->request->raw,'coachreports/table')
		or false !== strpos($kapenta->request->raw,'securesync/api/status')
		or false !== strpos($kapenta->request->raw,'coachreports/scatter')
		or false !== strpos($kapenta->request->raw,'coachreports/api')
		or false !== strpos($kapenta->request->raw,'coachreports/timeline')
		or false !== strpos($kapenta->request->raw,'coachreports/student')
	) {
		$rawdata = file_get_contents('php://input'); //for POSTS
		
		$requestURI = $_SERVER['REQUEST_URI'];
		$requestQuery = $_SERVER['QUERY_STRING'];
		$remoteAddr = $_SERVER['REMOTE_ADDR'];
		$remotePort = $_SERVER['REMOTE_PORT'];
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		$postArgs	= $rawdata;
		$args = array(
			'method' => $requestMethod,
			'uri' => $requestURI,
			'query' => $requestQuery,
			'remoteAddr' => $remoteAddr,
			'remotePort' => $remotePort,
			'postArgs' => $postArgs
		);

		$kapenta->raiseEvent('lessons', 'khanlite_request', $args);
	}

//--------------------------------------------------------------------------------------------------
//	check if user originates in our subnet, may redirect others to a central instance
//--------------------------------------------------------------------------------------------------

	if ((false == $req->local) && ('p2p' != $req->module)) {
		$altInstance = $kapenta->registry->get('kapenta.alternate');
		if (true == array_key_exists('alternate', $req->args)) {
			$kapenta->session->set('usealternate', $req->args['alternate']);
		}

		if (('' != $altInstance) && ('no' != $session->get('usealternate'))) {
			$URI = str_replace('//', '/', $altInstance . $_SERVER['REQUEST_URI']);
			$URI = str_replace('http:/', 'http://', $URI);
	 		header("HTTP/1.1 301 Moved Permanently");
	 		header("Location: " . $URI); 
			echo "The page you requested moved <a href='" . $URI  . "'>here</a>.";
			die();
		}
	}

//--------------------------------------------------------------------------------------------------
//	set up the debugger
//--------------------------------------------------------------------------------------------------

	if (true == array_key_exists('debug', $req->args)) {
		$auth = false;
		if ('admin' == $user->role) { $auth = true; }
		if (
			(array_key_exists('password', $req->args)) && 
			(sha1($kapenta->request->args['password']) == $kapenta->registry->get('kapenta.recoverypassword'))
		) { $auth = true; }

		if ((true == $auth) && ('on' == $kapenta->request->args['debug'])) { $kapenta->session->debug = true; }
		else { $session->debug = false; }
	}

	$kapenta->page->logDebug = $kapenta->session->debug;
	
//--------------------------------------------------------------------------------------------------
//	kapenta environment is set up, load the action requested by the user and pass control
//--------------------------------------------------------------------------------------------------
//		$date = new DateTime();
//		$kapenta->fs->put("data/lessons/scraper/test" . $date->format('U') . ".txt", $kapenta->request->raw);

	$actionFile = ''
	 . 'modules/' . $kapenta->request->module
	 . '/actions/' . $kapenta->request->action . '.act.php';

	if (false == $kapenta->fs->exists($actionFile)) { $kapenta->page->do404('Unknown action'); }

	require_once($actionFile);

//--------------------------------------------------------------------------------------------------
//	record profile
//--------------------------------------------------------------------------------------------------

/*
	if ('yes' == $kapenta->registry->get('xhprof.enabled')) {
		$skip = false;
		//TODO: registry key

		if (
			(('live' == $req->module) && ('getmessages' == $req->action)) ||
			(('images' == $req->module) && ('full' == $req->action)) ||
			(('images' == $req->module) && ('default' == $req->action)) ||
			(('home' == $req->module) && ('css' == $req->action)) 
		) { $skip = true; }

		if (false == $skip) {
			$xHProfData = xhprof_disable();
			$xHProfRuns = new XHProfRuns_Default();
			$xHProfRuns->save_run($xHProfData, $kapenta->websiteName);
		}
	}
*/

?>
