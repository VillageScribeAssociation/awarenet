<?
	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//-------------------------------------------------------------------------------------------------
//|	fired when a KA Lite request is identified by index.php
//-------------------------------------------------------------------------------------------------
//arg: method - http POST or GET [string]
//arg: uri - uri of http call [string]
//arg: query - parameters in http call [string]
//arg: remoteAddr - address of caller [string]
//arg: remotePort - port of caller [string]
//arg: remotePort - args of POST [string]

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
	
	$kalite = $kapenta->registry->get('kalite.installation');

	//----------------------------------------------------------------------------------------------
	//	get sessionid and csrftoken for KA Lite interaction
	//----------------------------------------------------------------------------------------------	
	if (true == $kapenta->session->has('c_sessionid')) {
		$sessionid = $kapenta->session->get('c_sessionid');
	}
	if (true == $kapenta->session->has('c_csrftoken')) {
		$csrftoken = $kapenta->session->get('c_csrftoken');
	}
		
	$reply = '';

	//----------------------------------------------------------------------------------------------
	//	redirect requests for css files to KA Lite
	//----------------------------------------------------------------------------------------------	
	if (false !== strpos($request, 'static/css')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}

		header('Content-Type: text/css');
	} 
	//----------------------------------------------------------------------------------------------
	//	redirect request for images files to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, 'static/images')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}
	
		if (false !== strpos($request, 'png')) {
			header('Content-Type: image/png');
		} else if (false !== strpos($request, 'gif')) {
			header('Content-Type: image/gif');
		}else {
			header('Content-Type: text/image');
		}
	}
	//----------------------------------------------------------------------------------------------
	//	redirect request for data to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, 'static/data')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}
	
		header('Content-Type: application/json');
	}
	//----------------------------------------------------------------------------------------------
	//	redirect requests for video and image content to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, 'content/')) {
		$cookies = '';
		if (false !== strpos($request, 'mp4')) {
			if (false == function_exists('curl_init')) { return false; }	// is cURL installed?
			$ch = curl_init($kalite . $request);
			$interface = $kapenta->hostInterface;
			if ('' != $interface) { curl_setopt($ch, CURLOPT_INTERFACE, $interface); }
			curl_setopt($ch, CURLOPT_HEADER, true);
			if ('' != $cookies) { curl_setopt($ch, CURLOPT_COOKIE, $cookie); }
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if ('yes' == $kapenta->proxyEnabled) {
				$credentials = $kapenta->proxyUser . ':' . $kapenta->proxyPass;
				curl_setopt($ch, CURLOPT_PROXY, $kapenta->proxyAddress);
				curl_setopt($ch, CURLOPT_PROXYPORT, $kapenta->proxyPort);
				curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
				if (trim($credentials) != ':') {
					curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, $credentials);
				}
			}
			$response = curl_exec($ch);
			$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
			$header = substr($response, 0, $header_size);
			$reply = substr($response, $header_size);			
			curl_close($ch);
		} else {
		
			if ('GET' == $requestMethod) {
				$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
			}
			
		}	
		
		if (false !== strpos($request, 'mp4')) {
			$pos = strpos($header, 'Content-Length:');
			$pos1 = strpos($header, 'Content-Type:');
			$pos2 = strpos($header, 'Accept-Ranges:');
			$len = strlen('Content-Length: ');
			$length = substr ( $header, ($pos + $len), $pos2 - ($pos + $len)  );
			header('Content-Type: video/mp4');
			header('Accept-Ranges: bytes');
			header('Content-Length: '.$length);
		} else if (false !== strpos($request, 'png')) {
			header('Content-Type: image/png');
		} else {
			header('Content-Type: text/html');
		}
	}
	//----------------------------------------------------------------------------------------------
	//	redirect requests for java script code to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, 'static/js')) 
	{
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}
	
		if (false !== strpos($request, 'css')) {
			header('Content-Type: text/css');
		} else if (false !== strpos($request, 'html')){
			header('Content-Type: text/html');
		} else {
			header('Content-Type: application/javascript');
		}
	}
	//----------------------------------------------------------------------------------------------
	//	redirect requests for jsi18n to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, 'jsi18n')) 
	{
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}
	
		header('Content-Type: text/html');
	}
	//----------------------------------------------------------------------------------------------
	//	redirect requests for specific video functionality to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, 'static/video-js')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}
		
		if (false !== strpos($request, 'css')) {
			header('Content-Type: text/css');
		} else if (false !== strpos($request, 'png')) {
			header('Content-Type: image/png');
		} else {
			header('Content-Type: application/javascript');
		}
	}
	//----------------------------------------------------------------------------------------------
	//	redirect requests for api calls to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, 'api/info')) {
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}

		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'api/updates/progress')) {
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}

		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'api/status')) {
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}
	
		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'api/get')
		) {
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;
		$reply = $kapenta->utils->curlPost($kalite . $request, $postArgs, false, $cookies, 
			array('X-CSRFToken: '.$csrftoken));

		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'api/start')
			or false !== strpos($request, 'api/check_video')
			or false !== strpos($request, 'api/check_subtitle')
			or false !== strpos($request, 'api/delete')
			or false !== strpos($request, 'api/cancel')
			or false !== strpos($request, 'api/retry')
			or false !== strpos($request, 'api/save')
			or false !== strpos($request, 'api/videos')
			or false !== strpos($request,'coachreports/api')
			or false !== strpos($request,'securesync/api/status')
		) {
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;
		$reply = $kapenta->utils->curlPost($kalite . $request, $postArgs, false, $cookies, 
			array('X-CSRFToken: '.$csrftoken));

		header('Content-Type: application/json');
	}
	//----------------------------------------------------------------------------------------------
	//	redirect requests for sub items to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, 'math')
		or false !== strpos($request, 'science')
		or false !== strpos($request, 'economics-finance-domain')	
		or false !== strpos($request, 'humanities')	
		or false !== strpos($request, 'test-prep')	
		or false !== strpos($request, 'discovery-lab')	
		or false !== strpos($request, 'exercisedashboard')	
	)
	{
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;

		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}

		header('Content-Type: text/html');
		$reply = removeLinksFromKhanLitePage($reply);

		$kapenta->page->load('modules/lessons/actions/khansub.page.php');
		$kapenta->page->blockArgs['kalisting'] = $reply;
		$kapenta->page->render();	

		die();	
		return true;
	}
	else if (false !== strpos($request, 'coachreports/table')	
		or false !== strpos($request,'coachreports/scatter')
		or false !== strpos($request,'coachreports/timeline')
		or false !== strpos($request,'coachreports/student')
	)
	{
		$cookies = 'sessionid='.$sessionid.';csrftoken='.$csrftoken;
		
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}

		header('Content-Type: text/html');
		
		if (!strlen($reply)){
			$request = str_replace('/video', '/video/', $request);
			$request = str_replace('/exercise', '/exercise/', $request);
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}
	
		$reply = removeLinksFromKhanLitePage($reply);

		$kapenta->page->load('modules/lessons/actions/khansub.page.php');
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
