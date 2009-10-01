<?

//--------------------------------------------------------------------------------------------------
//	chat bot to translate text using the google AJAX API 
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	language codes as used by google languages API  // TODO: find out which of these work
//--------------------------------------------------------------------------------------------------

function chat_bot_translate_submit($msg, $recipient) {

	$langFrom = '';
	$langTo = '';
	$quietMode = false;
	$error = false;

	$retVal = array();
	$retVal['sender'] = $msg;
	$retVal['recipient'] = $msg;

	//----------------------------------------------------------------------------------------------
	//	Supported by Google Language API // TODO: find out which of these translate
	//----------------------------------------------------------------------------------------------

	$chat_bot_langs = array(
		'ARABIC' => 'ar',		'ARMENIAN' => 'hy',		'AZERBAIJANI' => 'az',	'BASQUE' => 'eu',
		'BELARUSIAN' => 'be',	'BENGALI' => 'bn',		'BIHARI' => 'bh',		'BULGARIAN' => 'bg',
		'BURMESE' => 'my',		'CATALAN' => 'ca',		'CHEROKEE' => 'chr',	'CHINESE' => 'zh',
		'CROATIAN' => 'hr',		'CZECH' => 'cs',		'DANISH' => 'da',		'DHIVEHI' => 'dv',
		'DUTCH'=> 'nl',			'ENGLISH' => 'en',		'ESPERANTO' => 'eo',	'ESTONIAN' => 'et',
		'FILIPINO' => 'tl',		'FINNISH' => 'fi',		'FRENCH' => 'fr',		'GALICIAN' => 'gl',
		'GEORGIAN' => 'ka',		'GERMAN' => 'de',		'GREEK' => 'el',		'GUARANI' => 'gn',
		'GUJARATI' => 'gu',		'HEBREW' => 'iw',		'HINDI' => 'hi',		'HUNGARIAN' => 'hu',
		'ICELANDIC' => 'is',	'INDONESIAN' => 'id',	'INUKTITUT' => 'iu',	'ITALIAN' => 'it',
		'JAPANESE' => 'ja',		'KANNADA' => 'kn',		'KAZAKH' => 'kk',		'KHMER' => 'km',
		'KOREAN' => 'ko',		'KURDISH'=> 'ku',		'KYRGYZ'=> 'ky',		'LAOTHIAN'=> 'lo',
		'LATVIAN' => 'lv',		'LITHUANIAN' => 'lt',	'MACEDONIAN' => 'mk',	'MALAY' => 'ms',
		'MALAYALAM' => 'ml',	'MALTESE' => 'mt',		'MARATHI' => 'mr',		'MONGOLIAN' => 'mn',
		'NEPALI' => 'ne',		'NORWEGIAN' => 'no',	'ORIYA' => 'or',		'PASHTO' => 'ps',
		'PERSIAN' => 'fa',		'POLISH' => 'pl',		'PORTUGUESE' => 'pt-PT','PUNJABI' => 'pa',
		'ROMANIAN' => 'ro',		'RUSSIAN' => 'ru',		'SANSKRIT' => 'sa',		'SERBIAN' => 'sr',
		'SINDHI' => 'sd',		'SINHALESE' => 'si',	'SLOVAK' => 'sk',		'SLOVENIAN' => 'sl',
		'SPANISH' => 'es',		'SWAHILI' => 'sw',		'SWEDISH' => 'sv',		'TAJIK' => 'tg',
		'TAMIL' => 'ta',		'TAGALOG' => 'tl',		'TELUGU' => 'te',		'THAI' => 'th',
		'TIBETAN' => 'bo',		'TURKISH' => 'tr',		'UKRAINIAN' => 'uk',	'URDU' => 'ur',
		'UZBEK' => 'uz',		'UIGHUR' => 'ug',		'VIETNAMESE' => 'vi'
	);

	//----------------------------------------------------------------------------------------------
	//	clean the request, check for quiet mode
	//----------------------------------------------------------------------------------------------

	$msg = trim($msg);
	if (substr($msg, 0, 11) == '/translate ') { $msg = trim(substr($msg, 11)); }
	if (substr($msg, 0, 2) == 'q ') { $quietMode = true; $msg = substr($msg, 2); }

	//----------------------------------------------------------------------------------------------
	//	decide which language pair we're using
	//----------------------------------------------------------------------------------------------

	$spacePos = strpos($msg, ' ');
	if ($spacePos != false) {
		$langPair = substr($msg, 0, $spacePos);
		$dashPos = strpos($langPair, '-');
		if (false != $dashPos) {
			//--------------------------------------------------------------------------------------
			//	laguage pair specified in request, check if it's good
			//--------------------------------------------------------------------------------------
			$langFrom = strtolower(substr($langPair, 0, $dashPos));
			$dashPos++;  // skip hyphen
			$langTo = strtolower(substr($langPair, $dashPos, strlen($langPair) - $dashPos));

			if ( (in_array($langFrom, $chat_bot_langs))	&& (in_array($langTo, $chat_bot_langs)) ) {
				// all good, store this for future requests
				$langPair = $langFrom . '|' . $langTo;
				$_SESSION['sChatBot' . $recipient] = $langPair;

			} else {
				// languages not recognised
				$error = true;
				$retVal['sender'] .= "<br/><i>not translated: language pair not recognised, type "
								   . "/help translate for more information.</i>";
			}

		} else {
			//--------------------------------------------------------------------------------------
			//	no language pair given, perhaps it is already stored in session
			//--------------------------------------------------------------------------------------
			if (true == array_key_exists('sChatBotTranslate' . $recipient, $_SESSION)) {
				// alrighty then
				$langPair = $_SESSION['sChatBotTranslate' . $recipient];
				

			} else {
				// languages not recognised
				$error = true;
				$retVal['sender'] .= "<br/><i>not translated: language pair not set, type "
								   . "/help translate for more information.</i>";

			}

		}
	}

	//----------------------------------------------------------------------------------------------
	//	perform the translation
	//----------------------------------------------------------------------------------------------

	if ($error == false) {		
		//------------------------------------------------------------------------------------------
		//	CURL request to Google AJAX API
		//------------------------------------------------------------------------------------------
		$url = "http://ajax.googleapis.com/ajax/services/language/translate"
			 . "?v=1.0&q=" . urlencode(trim(substr($msg, 5)))
			 . "&langpair=" . $langFrom . "%7C" . $langTo;

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_REFERER, 'http://196.210.143.195/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		$startPos = strpos($result, 'translatedText');
		if ($startPos > 0) {
			$startPos += 17;
			$endPos = strpos($result, "\"}", $startPos);
			$result = substr($result, $startPos, $endPos - $startPos);
			
		} else { $result = "<font color=red><i>translation error.</i></font>"; }

		$retVal['sender'] .= "<i>translated into $langTo: $result</i>";
		$retVal['recipient'] = "$result<i>[translated from $langFrom]</i>";
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	
	return $retVal;	
}

//--------------------------------------------------------------------------------------------------
//	nothing special at this point
//--------------------------------------------------------------------------------------------------

function chat_bot_translate_help($msg) {
	global $chat_bot_langs;

	$chat_bot_langs = array(
		'CATALAN' => 'ca',		'CHINESE' => 'zh',		'DUTCH'=> 'nl',			'ENGLISH' => 'en',
		'FILIPINO' => 'tl',		'FINNISH' => 'fi',		'FRENCH' => 'fr',		'GALICIAN' => 'gl',
		'GEORGIAN' => 'ka',		'GERMAN' => 'de',		'GREEK' => 'el',		'HEBREW' => 'iw',
		'HINDI' => 'hi',		'HUNGARIAN' => 'hu',	'INDONESIAN' => 'id',	'ITALIAN' => 'it',
		'JAPANESE' => 'ja',		'KOREAN' => 'ko',		'LATVIAN' => 'lv',		'LITHUANIAN' => 'lt',
		'MALTESE' => 'mt',		'NORWEGIAN' => 'no',	'POLISH' => 'pl',		'ROMANIAN' => 'ro',
		'RUSSIAN' => 'ru',		'SERBIAN' => 'sr',		'SLOVAK' => 'sk',		'SLOVENIAN' => 'sl',
		'SPANISH' => 'es',		'SWEDISH' => 'sv',		'THAI' => 'th',			'TURKISH' => 'tr',
		'UKRAINIAN' => 'uk',	'VIETNAMESE' => 'vi'
	);

	$html = "<font color=black>To use this bot, type /translate [language pair] [message] and press enter."
		  . "The language pair can be any two of the following, separated by a hyphen, "
		  . "for example: en-de for english-to-german, it-zh for italian to chinese:<br/>";

	foreach($chat_bot_langs as $langName => $langCode) 
		{ $html .= strtolower($langName) . " (<font color=red>$langCode</font>), ";	}

	$html = substr($html, 0, strlen($html) - 2) . "</font>";
	return $html;
}

?>
