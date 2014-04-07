<?
	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//-------------------------------------------------------------------------------------------------
//|	fired by $kapenta->page->do404 // unknown request
//-------------------------------------------------------------------------------------------------
//arg: method - http POST or GET [string]
//arg: uri - uri of http call [string]
//arg: query - parameters in http call [string]
//arg: remoteAddr - address of caller [string]
//arg: remotePort - port of caller [string]
//arg: remotePort - args of POST [string]

function lessons__cb_404($args) {
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

//	$kapenta->logEvent('kalite', 'events', 'any request', print_r($args, true));

	$requestMethod = $args['method'];
	$uri = $args['uri'];
	$query = $args['query'];
	$request = $uri;
	$remoteAddr = $args['remoteAddr'];
	$remotePort = $args['remotePort'];
	$postArgs = $args['postArgs'];

	$kalite = $kapenta->registry->get('kalite.installation');
	//----------------------------------------------------------------------------------------------
	//	get sessionid and csrftoken for KA Lite interaction
	//----------------------------------------------------------------------------------------------	
	$cookies = '';
	$sessionid = '';
	if (true == $kapenta->session->has('kalite_sessionid')) {
		$sessionid = $kapenta->session->get('kalite_sessionid');
		$cookies = 'sessionid='.$sessionid.';';
	}
	if (true == $kapenta->session->has('kalite_csrftoken')) {
		$csrftoken = 	$kapenta->session->get('kalite_csrftoken');
		$cookies = $cookies . 'csrftoken='.$csrftoken;
	}
		
	$reply = '';
	
	//----------------------------------------------------------------------------------------------
	//	redirect requests for status (used to check if we are logged in)
	//----------------------------------------------------------------------------------------------	
	if (false !== strpos($request, '/kalite/api/status')) {
//		$kapenta->logEvent('kalite', 'events', 'api/status', print_r($args, true));
		if ('GET' == $requestMethod) {
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
			if (0 < strpos($reply, '"is_logged_in": false')) {
				//for some reason we are logged out and need to log in again!
				$kapenta->logEvent('kalite', 'login', 'impromptu login', $reply);
				logoutKhanLite();
				$retarg = createAndLoginKhanLite();
				$kapenta->session->set('kalite_sessionid', $retarg['sessionid']);
				$kapenta->session->set('kalite_csrftoken', $retarg['csrftoken']);
				$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
			}
		}	

		header('Content-Type: application/json');
	}

	//----------------------------------------------------------------------------------------------
	//	redirect requests for css files to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, '/kalite/static/css')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
		}

		$reply = replaceLinksFromKhanLitePage($reply);
		header('Content-Type: text/css');
	} 
	//----------------------------------------------------------------------------------------------
	//	redirect request for images files to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, '/kalite/static/images')) {
		$cookies = '';
		if ('GET' == $requestMethod) {
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
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
	else if (false !== strpos($request, '/kalite/static/data')
			or false !== strpos($request, 'kalite/api/info')
			) {
		if ('GET' == $requestMethod) {
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
		}
	
		header('Content-Type: application/json');
	}
	//----------------------------------------------------------------------------------------------
	//	redirect requests for java script code to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, '/kalite/static/js')
			or false !== strpos($request, '/kalite/static/video-js')
			) 
	{
		$cookies = '';
		if ('GET' == $requestMethod) {
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
		}
	
		if (false !== strpos($request, 'css')) {
			header('Content-Type: text/css');
		} else if (false !== strpos($request, 'html')){
			header('Content-Type: text/html');
		} else if (false !== strpos($request, 'png')) {
			header('Content-Type: image/png');
		} else {
			header('Content-Type: application/javascript');
			$reply = replaceLinksFromKhanLitePage($reply);
		}
	}
	//----------------------------------------------------------------------------------------------
	//	redirect requests for video and image content to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, '/kalite/content/')) {
		$cookies = '';
		$request = str_replace("/kalite", "", $request);
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
	//	redirect requests for jsi18n to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, 'kalite/jsi18n')) 
	{
		$cookies = '';
		if ('GET' == $requestMethod) {
			$request = str_replace("kalite/", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}
		
		$reply = replaceLinksFromKhanLitePage($reply);

		header('Content-Type: text/html');
	}
	else if (false !== strpos($request, 'kalite/api/get')) {
		$request = str_replace("kalite/", "", $request);
		$reply = $kapenta->utils->curlPost($kalite . $request, $postArgs, false, $cookies, 
			array('X-CSRFToken: '.$csrftoken));

		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'kalite/api/updates/progress')
			or false !== strpos($request, 'kalite/api/languagepacks/installed')
			or false !== strpos($request, 'kalite/api/languagepacks/refresh')
		) {
		$request = str_replace("kalite/", "", $request);
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}

		header('Content-Type: application/json');
	}
	else if (false !== strpos($request, 'kalite/api/start')
			or false !== strpos($request, 'kalite/api/check_video')
			or false !== strpos($request, 'kalite/api/check_subtitle')
			or false !== strpos($request, 'kalite/api/delete')
			or false !== strpos($request, 'kalite/api/cancel')
			or false !== strpos($request, 'kalite/api/retry')
			or false !== strpos($request, 'kalite/api/save')
			or false !== strpos($request, 'kalite/api/videos')
			or false !== strpos($request, 'kalite/api/languagepacks/start')
			or false !== strpos($request,'kalite/coachreports/api')
			or false !== strpos($request,'kalite/securesync/api/status')
		) {
		$request = str_replace("kalite/", "", $request);
		$reply = $kapenta->utils->curlPost($kalite . $request, $postArgs, false, $cookies, 
			array('X-CSRFToken: '.$csrftoken));

		header('Content-Type: application/json');
	}
	//----------------------------------------------------------------------------------------------
	//	redirect requests for sub items to KA Lite
	//----------------------------------------------------------------------------------------------	
	else if (false !== strpos($request, '/kalite/math')
		or false !== strpos($request, '/kalite/science')
		or false !== strpos($request, '/kalite/economics-finance-domain')	
		or false !== strpos($request, '/kalite/humanities')	
		or false !== strpos($request, '/kalite/test-prep')	
		or false !== strpos($request, '/kalite/discovery-lab')	
		or false !== strpos($request, '/kalite/exercisedashboard')	
		or false !== strpos($request, '/kalite/partner-content')	
		or false !== strpos($request, '/kalite/update/languages')	
	)
	{
		$request = str_replace("/kalite", "", $request);

		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
			if (false !== strpos($request, '/update/languages/?set_default')) {
				$reply = $kapenta->utils->curlGet($kalite . '/update/languages/', '', false, $cookies);
			}
		}

	 	$replaced = trimKAPage($reply);
		$replaced = removeLinksFromKhanLitePage($replaced);
		$replaced = replaceLinksFromKhanLitePage($replaced);
		$replaced = changeLocalLinksFromKhanLitePage($replaced);

		header('Content-Type: text/html');

		$kapenta->page->load('modules/lessons/actions/khansub.page.php');
		$kapenta->page->blockArgs['kalisting'] = $replaced;
		$kapenta->page->render();	

		die();	
		return true;
	}
	else if (false !== strpos($request, 'kalite/coachreports/table')	
		or false !== strpos($request,'kalite/coachreports/scatter')
		or false !== strpos($request,'kalite/coachreports/timeline')
		or false !== strpos($request,'kalite/coachreports/student')
	)
	{
		$request = str_replace("kalite/", "", $request);
		
		if ('GET' == $requestMethod) {
			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
		}

		header('Content-Type: text/html');
		
//		if (!strlen($reply)){
//			$request = str_replace('/video', '/video/', $request);
//			$request = str_replace('/exercise', '/exercise/', $request);
//			$reply = $kapenta->utils->curlGet($kalite . $request, '', false, $cookies);
//		}
	
	 	$replaced = trimKAPage($reply);
		$replaced = removeLinksFromKhanLitePage($replaced);
		$replaced = replaceLinksFromKhanLitePage($replaced);
		$replaced = changeLocalLinksFromKhanLitePage($replaced);

		$kapenta->page->load('modules/lessons/actions/khansub.page.php');
		$kapenta->page->blockArgs['kalisting'] = $replaced;
		$kapenta->page->render();	

		die();	
		return true;
	
	} else {
		if (false == strpos($request, 'images')) {
			$kapenta->logEvent('kalite', 'events', 'not processed do404s', print_r($args, true));
		}
		return false;
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
