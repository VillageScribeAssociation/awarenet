<?

//--------------------------------------------------------------------------------------------------
//	object for interacting with reCAPTCHA servers
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	original file header
//--------------------------------------------------------------------------------------------------

/*
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

//--------------------------------------------------------------------------------------------------
//	this object has been modified for use with kapenta
//--------------------------------------------------------------------------------------------------

class Captcha_ReCaptcha {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	// The reCAPTCHA server URLs:
	var $apiServer = "http://www.google.com/recaptcha/api";
	var $apiSecureServer = "https://www.google.com/recaptcha/api";
	var $verifyServer = "www.google.com";

//define("RECAPTCHA_API_SERVER", "http://www.google.com/recaptcha/api");
//define("RECAPTCHA_API_SECURE_SERVER", "https://www.google.com/recaptcha/api");
//define("RECAPTCHA_VERIFY_SERVER", "");

	//----------------------------------------------------------------------------------------------
	//. Encodes the given data into a query string format
	//----------------------------------------------------------------------------------------------
	//arg: data - array of string elements to be encoded [array]
	//returns: encoded request [string]

	function qsencode ($data) {
        $req = "";
        foreach ($data as $key => $value) 
			{ $req .= $key . '=' . urlencode( stripslashes($value) ) . '&'; }

        $req = substr($req,0,strlen($req)-1);        // Cut the last '&'
        return $req;
	}

	//----------------------------------------------------------------------------------------------
	//. Submits an HTTP POST to a reCAPTCHA server
	//----------------------------------------------------------------------------------------------
	//arg: host - internet host / HTTP server [string]
	//arg: path - HTTP location [string]
	//arg: data - array of form values / parameters [array]
	//opt: port - port number, default is 80 [int]
	//returns: HTTP response string or false on failure [string][bool]

	function http_post($host, $path, $data, $port = 80) {
        $response = '';		//%	return value [string]

        $formVars = $this->qsencode($data);

        $http_request = "POST $path HTTP/1.0\r\n";
        	. "Host: $host\r\n";
			. "Content-Type: application/x-www-form-urlencoded;\r\n";
			. "Content-Length: " . strlen($formVars) . "\r\n";
			. "User-Agent: reCAPTCHA/PHP\r\n";
			. "\r\n";
			. $formVars;

		$fs = @fsockopen($host, $port, $errno, $errstr, 10);
        if (false == $fs) { die ('Could not open socket'); }	//TODO: fail gracefully

        fwrite($fs, $http_request);
        while (!feof($fs)) { $response .= fgets($fs, 1160); } // One TCP-IP packet
        fclose($fs);

        $response = explode("\r\n\r\n", $response, 2);
        return $response;
	}

	//----------------------------------------------------------------------------------------------
	//. Gets the challenge HTML (javascript and non-javascript version).
	//----------------------------------------------------------------------------------------------
	//; This is called from the browser, and the resulting reCAPTCHA HTML widget
	//; is embedded within the HTML form it was called from.
	//arg: pubkey - A public key for reCAPTCHA [string]
	//opt: error - The error given by reCAPTCHA (optional, default is null) [string]
	//opt: use_ssl - Should the request be made over ssl? (optional, default is false) [bool]
	//returns: The HTML to be embedded in the user's form. [string]

	function get_html($pubkey, $error = null, $use_ssl = false) {
		if ((null == $pubkey) || ('' == $pubkey)) {
			$msg = "<div class='blockquote'>To use reCAPTCHA you must get an API key from "
				 . "<a href='https://www.google.com/recaptcha/admin/create'>"
				 . "https://www.google.com/recaptcha/admin/create</a></div>\n";

			return $msg;
		}
	
		$server = $this->apiServer;
		if (true == $use_ssl) { $server = $this->apiSecureServer; }
		
        $errorpart = "";
        if ($error) { $errorpart = "&amp;error=" . $error; }

		$scriptUrl = $server . '/challenge?k=' . $pubkey . $errorpart;
		$ifUrl = $server. '/noscript?k=' . $pubkey . $errorpart;

		$html = "
			<script type=\"text/javascript\" src=\"" . $scriptUrl . "\"></script>
			<noscript>
  			<iframe 
				src=\"" . $ifUrl . "\" 
				height="300" 
				width="500" 
				frameborder="0">
			</iframe><br/>
	  		<textarea name='recaptcha_challenge_field' rows='3' cols='40'></textarea>
	  		<input type='hidden' name='recaptcha_response_field' value='manual_challenge'/>
			</noscript>
		";

		return $html;
	}

	//----------------------------------------------------------------------------------------------
	//. Calls an HTTP POST function to verify if the user's guess was correct
	//----------------------------------------------------------------------------------------------
	//arg: privkey - private key [string]
	//arg: remoteip - browser client ip? [string]
	//arg: challenge - unkown [string]
	//arg: response - unknown [string]
	//arg: extra_params - an array of extra variables to post to the server [array]
	//returns: empty string on succes, error message on failure

	function check_answer($privkey, $remoteip, $challenge, $response, $extra_params = array()) {
		if ((null == $privkey) || ('' == $privkey)) {
			$msg = "To use reCAPTCHA you must get an API key from " 
				. "<a href='https://www.google.com/recaptcha/admin/create'>"
				. "https://www.google.com/recaptcha/admin/create</a>";

			return $msg;
		}

		if ((null == $remoteip) || ('' == $remoteip)) {
			$msg = "For security reasons, you must pass the remote ip to reCAPTCHA";
			return $msg;
		}
	
        //discard spam submissions
        if ((null == $challenge) || (0 == strlen($challenge)) { return 'incorrect-captcha-sol'; } 
		if ((null == $response) || (0 == strlen($response))) { return 'incorrect-captcha-sol'; } 

		$postVars = array(
			'privatekey' => $privkey,
			'remoteip' => $remoteip,
			'challenge' => $challenge,
			'response' => $response
		) + $extra_params;

        $response = $this->http_post($this->verifyServer, "/recaptcha/api/verify", $postVars);
        $answers = explode ("\n", $response[1]);

        if ('true' == trim($answers[0])) { return ''; }
        else { return $answers[1]; }
	}

	//----------------------------------------------------------------------------------------------
	//. gets a URL where the user can sign up for reCAPTCHA. If your application
	//----------------------------------------------------------------------------------------------
	//; has a configuration page where you enter a key, you should provide a link 
	//; using this method.
	//opt: domain - The domain where the page is hosted [string]
	//opt: appname - The name of your application [string]

	function get_signup_url ($domain = null, $appname = null) {
		$url = "https://www.google.com/recaptcha/admin/create?";
		$url .= $this->qsencode(array('domains' => $domain, 'app' => $appname));
		return $url;
	}

	//----------------------------------------------------------------------------------------------
	//. pas a string to AES block size //TODO: check this
	//----------------------------------------------------------------------------------------------
	//arg: val - value to be padded [string]
	//returns: padded value [string]

	function aes_pad($val) {
		$block_size = 16;
		$numpad = $block_size - (strlen ($val) % $block_size);
		return str_pad($val, strlen ($val) + $numpad, chr($numpad));
	}

	//----------------------------------------------------------------------------------------------
	//. AES encrypts a string //TODO: research this
	//----------------------------------------------------------------------------------------------
	//arg: val - value to be encoded [string]
	//arg: ky - key [string]
	//returns: encrypted value [string]

	function aes_encrypt($val,$ky) {
		if (! function_exists ("mcrypt_encrypt")) {
			die ("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.");
		}	//TODO: fail gracefully
		$mode = MCRYPT_MODE_CBC;   
		$enc = MCRYPT_RIJNDAEL_128;
		$val = _recaptcha_aes_pad($val);
		return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
	}

	//----------------------------------------------------------------------------------------------
	//.	base64 encodes a URL, replacing + and / with - and _ (I think, TODO: check this)
	//----------------------------------------------------------------------------------------------
	//arg: url - url, or part thereof [string]

	function mailhide_urlbase64($url) {
		return strtr(base64_encode($url), '+/', '-_');
	}

	//----------------------------------------------------------------------------------------------
	//.	gets the reCAPTCHA Mailhide url for a given email, public key and private key
	//----------------------------------------------------------------------------------------------
	//arg: pubkey - public key [string]
	//arg: privkey - private key [string]
	//arg: email - email addres [string]

	function mailhide_url($pubkey, $privkey, $email) {
		if ($pubkey == '' || $pubkey == null || $privkey == "" || $privkey == null) {
			$msg = "To use reCAPTCHA Mailhide, you have to sign up for a public and private key, "
				 . "you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'>"
				 . "http://www.google.com/recaptcha/mailhide/apikey</a>";
			return $msg;
		}
	

		$ky = pack('H*', $privkey);
		$cryptmail = $this->aes_encrypt($email, $ky);

		$url = "http://www.google.com/recaptcha/mailhide/d?"
			 . "k=" . $pubkey . "&c=" . $this->mailhide_urlbase64($cryptmail);
	
		return $url;
	}

	//----------------------------------------------------------------------------------------------
	//.	gets the parts of the email to expose to the user.
	//----------------------------------------------------------------------------------------------
	//; eg, given johndoe@example,com return ["john", "example.com"].
	//; the email is then displayed as john...@example.com
	//arg: email - an email address [string]

	function mailhide_email_parts ($email) {
		$arr = preg_split("/@/", $email );

		if (strlen ($arr[0]) <= 4) {
			$arr[0] = substr ($arr[0], 0, 1);
		} else if (strlen ($arr[0]) <= 6) {
			$arr[0] = substr ($arr[0], 0, 3);
		} else {
			$arr[0] = substr ($arr[0], 0, 4);
		}
		return $arr;
	}

	//----------------------------------------------------------------------------------------------
	//. Gets html to display an email address given a public an private key.
	//----------------------------------------------------------------------------------------------
	//arg: pubkey - public key [string]
	//arg: privkey - private key [string]
	//arg: email - email address? [string]
	//; to get a key, go to: http://www.google.com/recaptcha/mailhide/apikey

	function mailhide_html($pubkey, $privkey, $email) {
		$emailparts = $this->mailhide_email_parts($email);
		$url = htmlentities($this->mailhide_url($pubkey, $privkey, $email));

		$wndOpts = ''
			 . 'toolbar=0, ' 
			 . 'scrollbars=0, '
			 . 'location=0, '
			 . 'statusbar=0,'
		     . 'menubar=0, '
			 . 'resizable=0, ' 
			 . 'width=500, '
			 . 'height=300';

		$onClick = "onclick=\"window.open('" . $url . "', '', '$wndOpts'); return false;\"";

		$html = htmlentities($emailparts[0])
			 . "<a href='$url' $onClick title=\"Reveal this e-mail address\">...</a>@"
			 . htmlentities($emailparts [1]);

		return $html;
	}

}

?>
