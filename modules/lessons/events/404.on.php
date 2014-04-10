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

function lessons__cb_404() {
	global $kapenta;
	global $db;
	global $user;
	global $notifications;
	
	//----------------------------------------------------------------------------------------------
	//	check arguments
	//----------------------------------------------------------------------------------------------	
	$args = $kapenta->request->httpArgs;
	
	if (false == array_key_exists('method', $args)) 	{ return false; }
	if (false == array_key_exists('uri', $args)) 		{ return false; }
	if (false == array_key_exists('query', $args)) 		{ return false; }
	if (false == array_key_exists('remoteAddr', $args)) { return false; }
	if (false == array_key_exists('remotePort', $args)) { return false; }

//	$kapenta->logEvent('kalite', 'events', 'any request', $kapenta->request->raw);

	$requestMethod = $args['method'];
	$uri = $args['uri'];
	$query = $args['query'];
	$request = $uri;
	$remoteAddr = $args['remoteAddr'];
	$remotePort = $args['remotePort'];

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

	if ('GET' == $requestMethod) {

		//----------------------------------------------------------------------------------------------
		//	redirect requests for status (used to check if we are logged in)
		//----------------------------------------------------------------------------------------------	
		if (false !== strpos($request, '/kalite/api/status')) {
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
			if (0 < strpos($reply, '"is_logged_in": false')) {
				//for some reason we are logged out and need to log in again!
				$kapenta->logEvent('kalite', 'login', 'impromptu login:' . $kapenta->user->name, $reply);
				logoutKhanLite();
				$retarg = createAndLoginKhanLite();
				$kapenta->session->set('kalite_sessionid', $retarg['sessionid']);
				$kapenta->session->set('kalite_csrftoken', $retarg['csrftoken']);
				$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
			}
			header('Content-Type: application/json');
		}
		//----------------------------------------------------------------------------------------------
		//	redirect requests for css and image files to KA Lite
		//----------------------------------------------------------------------------------------------	
		else if (false !== strpos($request, '/kalite/static/css')
				or false !== strpos($request, '/kalite/static/images')
				) {
			$cookies = '';
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);

			if (false !== strpos($request, 'png')) {
				header('Content-Type: image/png');
			} else if (false !== strpos($request, 'gif')) {
				header('Content-Type: image/gif');
			} else if (false !== strpos($request, 'css')) {
				$reply = replaceLinksFromKhanLitePage($reply);
				header('Content-Type: text/css');
			}else {
				header('Content-Type: text/image');
			}
		} 
		//----------------------------------------------------------------------------------------------
		//	redirect request for data to KA Lite
		//----------------------------------------------------------------------------------------------	
		else if (false !== strpos($request, '/kalite/static/data')
				or false !== strpos($request, '/kalite/api/info')
				or false !== strpos($request, '/kalite/api/get')
				or false !== strpos($request, '/kalite/coachreports/api/data')
				or false !== strpos($request, '/kalite/api/updates/progress')
				or false !== strpos($request, '/kalite/api/languagepacks/installed')
				or false !== strpos($request, '/kalite/api/languagepacks/refresh')
				or false !== strpos($request, '/kalite/api/videos')
				) {
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
			header('Content-Type: application/json');
		}
		//----------------------------------------------------------------------------------------------
		//	redirect requests for java script code to KA Lite
		//----------------------------------------------------------------------------------------------	
		else if (false !== strpos($request, '/kalite/static/js')
				or false !== strpos($request, '/kalite/static/srt')
				or false !== strpos($request, '/kalite/static/video-js')
				) 
		{
			$cookies = '';
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
	
			if (false !== strpos($request, 'css')) {
				$reply = replaceLinksFromKhanLitePage($reply);
				header('Content-Type: text/css');
			} else if (false !== strpos($request, 'html')){
				$reply = trimKAPage($reply);
				$reply = replaceLinksFromKhanLitePage($reply);
				header('Content-Type: text/html');
			} else if (false !== strpos($request, 'png')) {
				header('Content-Type: image/png');
			} else if (false !== strpos($request, 'srt')) {
				header('Content-Type: text/plain');
			} else {
				$reply = replaceLinksFromKhanLitePage($reply);
				header('Content-Type: application/javascript');
			}
		}
		//----------------------------------------------------------------------------------------------
		//	redirect requests for video and image content to KA Lite
		//----------------------------------------------------------------------------------------------	
		else if (false !== strpos($request, '/kalite/content/')) {
			$cookies = '';
			$replaced = str_replace("/kalite", "", $request);
			if (false !== strpos($replaced, 'mp4')) {
				if (false == function_exists('curl_init')) { return false; }	// is cURL installed?
				$ch = curl_init($kalite . $replaced);
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
				$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
			}	
		
			if (false !== strpos($replaced, 'mp4')) {
				$pos = strpos($header, 'Content-Length:');
				$pos1 = strpos($header, 'Content-Type:');
				$pos2 = strpos($header, 'Accept-Ranges:');
				$len = strlen('Content-Length: ');
				$length = substr ( $header, ($pos + $len), $pos2 - ($pos + $len)  );
				header('Content-Type: video/mp4');
				header('Accept-Ranges: bytes');
				header('Content-Length: '.$length);
			} else if (false !== strpos($replaced, 'png')) {
				header('Content-Type: image/png');
			} else {
				$reply = trimKAPage($reply);
				$reply = replaceLinksFromKhanLitePage($reply);
				header('Content-Type: text/html');
			}
		}
		//----------------------------------------------------------------------------------------------
		//	redirect requests for jsi18n to KA Lite
		//----------------------------------------------------------------------------------------------	
		else if (false !== strpos($request, '/kalite/jsi18n')) 
		{
			$cookies = '';
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
		
			$reply = trimKAPage($reply);
			$reply = replaceLinksFromKhanLitePage($reply);

			header('Content-Type: text/html');
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
			or false !== strpos($request, '/kalite/coachreports/table')	
			or false !== strpos($request, '/kalite/coachreports/scatter')
			or false !== strpos($request, '/kalite/coachreports/timeline')
			or false !== strpos($request, '/kalite/coachreports/student')
		)
		{
			$replaced = str_replace("/kalite", "", $request);

			$reply = $kapenta->utils->curlGet($kalite . $replaced, '', false, $cookies);
			if (false !== strpos($replaced, '/update/languages/?set_default')) {
				$reply = $kapenta->utils->curlGet($kalite . '/update/languages/', '', false, $cookies);
			}

		 	$replaced = trimKAPage($reply);
			$replaced = replaceLinksFromKhanLitePage($replaced);
			$replaced = changeLocalLinksFromKhanLitePage($replaced);
			$replaced = removeLinksFromKhanLitePage($replaced);

			header('Content-Type: text/html');

			$kapenta->page->load('modules/lessons/actions/khansub.page.php');
			$kapenta->page->blockArgs['kalisting'] = $replaced;
			$kapenta->page->render();	

			die();	
			return true;
		}	
		else {
			if (false == strpos($request, 'images')) {
				$kapenta->logEvent('kalite', 'events', 'not processed do404s', print_r($args, true));
			}
			return false;
		}
	} else if ('POST' == $requestMethod) {
		$postArgs = file_get_contents('php://input');;
				
		//----------------------------------------------------------------------------------------------
		//	redirect post requests to do KA Lite functionality
		//----------------------------------------------------------------------------------------------	
		if (false !== strpos($request, '/kalite/api/get')
			or false !== strpos($request, '/kalite/api/start')
			or false !== strpos($request, '/kalite/api/check_video')
			or false !== strpos($request, '/kalite/api/check_subtitle')
			or false !== strpos($request, '/kalite/api/delete')
			or false !== strpos($request, '/kalite/api/cancel')
			or false !== strpos($request, '/kalite/api/retry')
			or false !== strpos($request, '/kalite/api/save')
			or false !== strpos($request, '/kalite/api/videos')
			or false !== strpos($request, '/kalite/api/languagepacks/start')
			or false !== strpos($request, '/kalite/coachreports/api')
			or false !== strpos($request, '/kalite/securesync/api/status')
			) {
			$replaced = str_replace("/kalite", "", $request);
			$reply = $kapenta->utils->curlPost($kalite . $replaced, $postArgs, false, $cookies, 
				array('X-CSRFToken: '.$csrftoken));

			header('Content-Type: application/json');
		}
		else {
			$kapenta->logEvent('kalite', 'events', 'not processed do404s', print_r($args, true));
			return false;
		}
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
