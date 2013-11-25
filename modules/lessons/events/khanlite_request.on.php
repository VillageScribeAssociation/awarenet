<?
	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

// require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//-------------------------------------------------------------------------------------------------
//|	fired when a comment is added
//-------------------------------------------------------------------------------------------------
//arg: refModule - name of module to which a comment was attached [string]
//arg: refModel - type of object to which comment was attached [string]
//arg: refUID - UID of object to which comment was attached [string]
//arg: commentUID - UID of the new comment [string]
//arg: comment - text/html of comment [string]

function lessons__cb_khanlite_request($args) {
	global $kapenta;
	global $db;
	global $user;
	global $notifications;

	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------	
	if (false == array_key_exists('method', $args)) 	{ return false; }
	if (false == array_key_exists('uri', $args)) 		{ return false; }
	if (false == array_key_exists('query', $args)) 		{ return false; }
	if (false == array_key_exists('remoteAddr', $args)) { return false; }
	if (false == array_key_exists('remotePort', $args)) { return false; }
	if (false == array_key_exists('postArgs', $args)) 	{ return false; }

	$requestMethod = $args['method'];
	$uri = $args['uri'];
	$query = $args['query'];
	$request = $uri;
	$remoteAddr = $args['remoteAddr'];
	$remotePort = $args['remotePort'];
	$postArgs = $args['postArgs'];

	$csrftoken = '';
	$sessionid = '';
	
	if (true == $kapenta->session->has('c_sessionid')) {
		$sessionid = $kapenta->session->get('c_sessionid');
	}
	if (true == $kapenta->session->has('c_csrftoken')) {
		$csrftoken = $kapenta->session->get('c_csrftoken');
	}
		
	$reply = '';

	if (false !== strpos($request, 'static/css')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}

		header('Content-Type: text/css');
	} 
	else if (false !== strpos($request, 'static/images')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}
	
		header('Content-Type: text/image');
	}
	else if (false !== strpos($request, 'static/data')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}
	
		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'content/')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}
		
		if (false !== strpos($request, 'mp4')) {
			header('Content-Type: video/mp4');
		} else if (false !== strpos($request, 'png')) {
			header('Content-Type: image/png');
		}
	}
	else if (false !== strpos($request, 'static/js')) 
	{
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}
	
		if (false !== strpos($request, 'css')) {
			header('Content-Type: text/css');
		} else if (false !== strpos($request, 'html')){
			header('Content-Type: text/html');
		} else {
			header('Content-Type: application/javascript');
		}
	}
	else if (false !== strpos($request, 'jsi18n')) 
	{
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}
	
		header('Content-Type: text/html');
	}
	else if (false !== strpos($request, 'static/video-js')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}
		
		if (false !== strpos($request, 'css')) {
			header('Content-Type: text/css');
		} else {
			header('Content-Type: application/javascript');
		}
	}
	else if (false !== strpos($request, 'api/info')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}

		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'api/status')) {
		$cookies = 'sessionid='.$sessionid;
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}
	
		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'api/get')
		) {
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;
		$reply = $kapenta->utils->curlPost('http://localhost:8008' . $request, $postArgs, false, $cookies, 
			array('X-CSRFToken: '.$csrftoken));

		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'api/start')
			or false !== strpos($request, 'api/check_video')
			or false !== strpos($request, 'api/check_subtitle')
			or false !== strpos($request, 'api/delete')
			or false !== strpos($request, 'api/cancel')
			or false !== strpos($request, 'api/retry')
		) {
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;
		$reply = $kapenta->utils->curlPost('http://localhost:8008' . $request, $postArgs, false, $cookies, 
			array('X-CSRFToken: '.$csrftoken));

		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'math')
		or false !== strpos($request, 'science')
		or false !== strpos($request, 'humanities')	
		or false !== strpos($request, 'test-prep')	
		or false !== strpos($request, 'discovery-lab')	
		or false !== strpos($request, 'exercisedashboard')	
	)
	{
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;

		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet('http://localhost:8008' . $request, '', false, $cookies);
		}

		header('Content-Type: text/html');
		$reply = removeLinksFromKhanLitePage($reply);

		$kapenta->page->load('modules/lessons/actions/khan.page.php');
		$kapenta->page->blockArgs['kalisting'] = $reply;
		$kapenta->page->render();	

		die();	
		return true;
	}

	echo $reply;
	die();
	//----------------------------------------------------------------------------------------------
	//	ok, done
	//----------------------------------------------------------------------------------------------	
	return true;
}

//-------------------------------------------------------------------------------------------------
?>
