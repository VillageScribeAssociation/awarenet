<?
/*
	VersionManager : 0.1 : 07.07.2009
	----------------------------------
	$manager = new VersionManager(Array(
		prototype=>Array( 'CanvasRenderingContext2D', 'CanvasGradient', 'CanvasPattern' ),
		canvas2D=>Array( 'measureText', 'fillText', 'strokeText' )
	));
	$info = $manager->getInfo(); // get all information
	$language = $manager->getLanguage(); // get preferred language
	$manager->updateDB(); // update current browser versions from wikipedia
	
	// version compare in javascript:  http://phpjs.org/functions/version_compare:852
	
	VersionManager will let a user know whether their computer is at risk (based on data provided -- cross-rerferenced with virus databases)
	
*/

class VersionManager {
	public function __construct($features) {
		try { // requires browscap ini directive
			$this->agent = @get_browser(null, true);
		} catch (Exception $e) {
			return;
		}	   	
	   	$agent = $this->agent["browser"];
	   	$ver = $this->agent["version"];
		function includeVM($file, $script = "") {
			global $AGENT;
			$return = "";
			if(gettype($file) != "array")
				$file = Array($file);
			foreach($file as $key=>$value) {
				if(strpos(".html", $value)) {
					if($type == "JS")
						$return .= "</script>" . "\n";
					$return .= file_get_contents("{$AGENT[root]}".$value) . "\n";
					$type = "HTML";
				}
				else {
					if($type != "JS") 
						$return .= '<script type="text/javascript">' . "\n";
					else 
						$return .= "\n";
					$return .= file_get_contents("{$AGENT[root]}".$value) . "\n";
					$type = "JS";
				}
			}
			if($type == "JS")
				$return .= ($script ? "\n" . $script . "\n" : "") . "</script>" . "\n";
			return $return;
		};
		if( $agent == "IE" || 
		   ($agent == "Firefox" && version_compare($ver, "3.0", "<")) ||
		   ($agent == "Safari" && version_compare($ver, "3.1", "<")) ||
		    $agent == "Konqueror")
			$FIX .= includeVM("lib/HTMLElement.getElementsByClassName.js");
		foreach($features as $k=>$v) { // adds features 
			switch($k) {
				case "prototype":
					if($agent == "Safari" && version_compare($ver, "3.0", "<=")) {
						$prototype = json_encode($v); // items to prototype
						$FIX .= <<<EOF
<script type="text/javascript">
(function(proto) { // retrofit prototype Safari 3 (from Kevin Weibell)
	var ctx = document.createElement('Canvas').getContext('2d');
	for(var i in proto) eval(proto[i] + " = window." + proto[i] + " || { prototype: ctx.__proto__ };");
})({$prototype});
</script>
EOF;
					}
					break;
				case "canvas2D":
					if($agent == "IE" && version_compare($ver, "9.0", "<"))
						$FIX .= includeVM("lib/IECanvas.html");
					foreach($v as $key=>$value) {
						switch($value) {
							case "Typeface": 
								if(($agent == "Firefox" && version_compare($ver, "3.5", ">=")) || // these work
								   ($agent == "Safari" && version_compare($ver, "4.0", ">=")) ||
								   ($agent == "Opera" && version_compare($ver, "10.0", ">=")))
									break;
								else if($agent == "Firefox" && version_compare($ver, "3.5", "<") && version_compare($ver, "3.0", ">="))
									$FIX .= includeVM("lib/Canvas2D.Typeface[Firefox3.0].js");
								else // draw manually
									$FIX .= includeVM(
												Array(
													"lib/Canvas2D.LiberationSans.js", // default font
													"lib/Canvas2D.SVG.js", // parse font
													"lib/Canvas2D.Typeface.js" // fillText, strokeText, drawText, measureText
												), 
												"if(!CanvasRenderingContext2D.prototype.fillText) Canvas2D.Typeface(CanvasRenderingContext2D.prototype);"
											);
								break;						
						}
					}
					break;
				default:
					break;
			}
		}
		$this->fix = $FIX ? "<!-- Fixes for browser quirks in {$agent} v{$ver} -->\n{$FIX}" : "";
		$this->urls = json_decode('{"IE":{"wikipedia":"Internet_Explorer","stable":{"ver":"8.0","url":"http:\/\/www.microsoft.com\/windows\/internet-explorer\/default.aspx"},"preview":null},"Chrome":{"wikipedia":"Google_Chrome","stable":{"ver":"2.0.172.33","url":"http:\/\/www.google.com\/landing\/chrome\/beta\/"},"preview":{"ver":{"Mac":"3.0.190.0","Windows":"3.0.191.3","Linux":"3.0.190.2"},"url":"http:\/\/www.google.com\/landing\/chrome\/beta\/"}},"Firefox":{"wikipedia":"Mozilla_Firefox","stable":{"ver":"3.5","url":"http:\/\/www.mozilla.com\/en-US\/firefox\/all-beta.html"},"preview":null},"Safari":{"wikipedia":"Safari_(web_browser)","stable":{"ver":"4.0.1","url":"http:\/\/www.apple.com\/safari\/download\/"},"preview":null},"Opera":{"wikipedia":"Opera_(web_browser)","stable":{"ver":"9.64","url":"http:\/\/www.opera.com\/browser\/next\/"},"preview":{"ver":{"Windows":"10.00b1622","MacOSX":"10.00b6540","Linux":"10.00b4464","FreeBSD":"10.00b4464"},"url":"http:\/\/www.opera.com\/browser\/next\/"}},"Camino":{"wikipedia":"Camino","stable":{"ver":"1.6.8","url":"http:\/\/preview.caminobrowser.org\/"},"preview":{"ver":"2.0b3","url":"http:\/\/preview.caminobrowser.org\/"}},"Konqueror":{"wikipedia":"Konqueror","stable":{"ver":"4.2.4","url":"http:\/\/www.kde.org\/download\/"},"preview":{"ver":"4.2.85","url":"http:\/\/www.kde.org\/download\/"}},"Flock":{"wikipedia":"Flock_(web_browser)","stable":{"ver":"2.5","url":"http:\/\/www.flock.com\/beta\/download"},"preview":null},"SeaMonkey":{"wikipedia":"SeaMonkey","stable":{"ver":"1.1.17","url":"http:\/\/www.seamonkey-project.org\/"},"preview":{"ver":"2.0a3","url":"http:\/\/www.seamonkey-project.org\/"}},"Songbird":{"wikipedia":"Songbird_(software)","stable":{"ver":"1.2","url":"http:\/\/getsongbird.com\/download\/"},"preview":{"ver":"1.3a","url":"http:\/\/getsongbird.com\/download\/"}}}', true);
	}
    public function getFix() {
		return $this->fix;
	}
	public function updateDB() { // updates browser versions (parses data from wikipedia)
		$urls = Array(
			'IE'=>Array('Internet_Explorer', 'http://www.microsoft.com/windows/internet-explorer/default.aspx'),
			'Chrome'=>Array('Google_Chrome', 'http://www.google.com/chrome', 'http://www.google.com/landing/chrome/beta/'),
			'Firefox'=>Array('Mozilla_Firefox', 'http://www.mozilla.com/firefox/', 'http://www.mozilla.com/en-US/firefox/all-beta.html'),
			'Safari'=>Array('Safari_(web_browser)', 'http://www.apple.com/safari/download/'),
			'Opera'=>Array('Opera_(web_browser)', 'http://www.opera.com/download/', 'http://www.opera.com/browser/next/'),
			'Camino'=>Array('Camino', 'http://caminobrowser.org/download/', 'http://preview.caminobrowser.org/'),
			'Konqueror'=>Array('Konqueror', 'http://www.kde.org/download/'),//
			'Flock'=>Array('Flock_(web_browser)', 'http://www.flock.com/', 'http://www.flock.com/beta/download'),
			'SeaMonkey'=>Array('SeaMonkey', 'http://www.seamonkey-project.org/'), 
			'Songbird'=>Array('Songbird_(software)', 'http://getsongbird.com/download/'),
//			'Flash'=>Array('Adobe_Flash', 'http://get.adobe.com/flashplayer/'),
//			'IECanvas'=>Array('IECanvas', 'http://get.jumis.com/iecanvas/', 'http://get.jumis.com/iecanvas/beta/')
		);
		$z = Array();
		function parseVersion($browser, $type) {
			$browser = str_replace(Array("_(web_browser)", "_(software)"), "", $browser);
			$url = "http://en.wikipedia.org/w/index.php?title=Template:Latest_{type}_software_release/{browser}&action=edit";
			$url = str_replace(Array("{type}", "{browser}"), Array($type, $browser), $url);
			$session = curl_init($url);
			curl_setopt($session, CURLOPT_FOLLOWLOCATION, true); 
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$file = curl_exec($session);
			curl_close($session);
			$text = explode($type == "preview" ? "{{LPR" : "{{LSR", $file);
			$os_find = Array("Mac OS X", " dev build");
			$os_replace = Array("MacOSX", "");
			if($text[1] && $text = $text[1]) {
				$text = strip_tags(htmlspecialchars($text));
				$text = substr($text, strpos($text, 'release_version = ') + 18);
				$text = trim(substr($text, 0, strpos($text, "|")));
				if(trim($text) == "none") return "";
				if(strpos($text, "Beta")) { // Opera
					$ver = substr($text, 0, strpos($text, " ")) . "b";
					$tmp = explode("Build ", $text);
					$text = Array();
					foreach($tmp as $key=>$value) {
						$value = preg_replace('/[\[\]]/i', '', $value);
						$value = preg_split('/[()]/i', $value);
						$key = str_replace($os_find, $os_replace, $value[1]); 
						if(strpos($key, ",")) { // stacked object
							$key = explode(",", $key);
							foreach($key as $k=>$v)
								$text[trim($v)] = $ver . trim($value[0]);
						}
						else if($key)
							$text[$key] = $ver . trim($value[0]);
					}
					return $text;
				}
				if(strpos($text, "&"))
					$text = trim(substr($text, 0, strpos($text, "&")));
				if(strpos($text, "["))
					$text = trim(substr($text, 0, strpos($text, "[")));
				if(strpos($text, ",")) { // multiple versions
					$tmp = explode(", ", $text);
					$text = Array();
					foreach($tmp as $key=>$value) {
						$value = preg_split('/[()]/i', $value);
						$text[str_replace($os_find, $os_replace, $value[1])] = trim($value[0]);
					}
				}
				return $text;
			}
			else // no version available
				return "";
		};
		foreach($urls as $key=>$value) {
			// find stable release
			$z[$key] = Array(
				"wikipedia"=>$value[0],
				"stable"=>null,
				"preview"=>null
			);
			if($beta = parseVersion($value[0], "preview")) {
				$z[$key]["preview"]["ver"] = $beta;
				$z[$key]["preview"]["url"] = $value[2] ? $value[2] : $value[1];
			}			
			if($stable = parseVersion($value[0], "stable")) {
				$z[$key]["stable"]["ver"] = $stable;
				$z[$key]["stable"]["url"] = $value[2] ? $value[2] : $value[1];
			}			
		}	
		echo json_encode($z); // gotta copy + paste for now
		print_r($z);
	}
    public function getInfo() {
		$lang = $this->getLanguage(); // guess language
		$agent = $this->agent;
		$urls = $this->urls[$agent["browser"]];
    	$upgrade = Array();
		// find stable upgrades
		$stable = $urls["stable"];
		if(count($stable["ver"])) {
			if($stable["ver"][$agent["platform"]])
				$stable["ver"] = $stable["ver"][$agent["platform"]];
			else //
				$stable = "";
		}
		if(version_compare($agent["version"], $stable["ver"], "<"))
			$upgrade["stable"] = $stable;
		// find beta upgrades
		$beta = $urls["preview"];
		if(count($beta["ver"])) {
			if($beta["ver"][$agent["platform"]])
				$beta["ver"] = $beta["ver"][$agent["platform"]];
			else //
				$beta = "";
		}
		if(version_compare($agent["version"], $beta["ver"], "<"))
			$upgrade["beta"] = $beta;
		return Array( // browser information
			"wikipedia"=>$urls["wikipedia"],
			"upgrade"=>count($upgrade) ? $upgrade : null,
			"platform"=>$agent["platform"],
			"browser"=>$agent["browser"],
			"version"=>$agent["version"],
			"java"=>$agent["javaapplets"],
			"javascript"=>$agent["javascript"],
			"cookies"=>$agent["cookies"],
			"activex"=>$agent["activexcontrols"],
			"lang"=>$lang,
			"langid"=>$lang["id"]
		);
    }
    public function getLanguage() { // parse language information
		$language = Array( // id: { default, english, native, lcid }
			'af'=>Array('default'=>'af-za', 'english'=>'Afrikaans', 'native'=>'Afrikaans', 'lcid'=>'1078' ),
			'af-za'=>Array('default'=>'af-za', 'english'=>'Afrikaans (South Africa)', 'native'=>'Afrikaans (Suid Afrika)', 'lcid'=>'' ),
			'ar'=>Array('default'=>'ar-sa', 'english'=>'Arabic', 'native'=>'العربية', 'lcid'=>'' ),
			'ar-ae'=>Array('default'=>'ar-ae', 'english'=>'Arabic (U.A.E.)', 'native'=>'العربية (الإمارات العربية المتحدة)', 'lcid'=>'14337' ),
			'ar-bh'=>Array('default'=>'ar-bh', 'english'=>'Arabic (Bahrain)', 'native'=>'العربية (البحرين)', 'lcid'=>'15361' ),
			'ar-dz'=>Array('default'=>'ar-dz', 'english'=>'Arabic (Algeria)', 'native'=>'العربية (الجزائر)', 'lcid'=>'5121' ),
			'ar-eg'=>Array('default'=>'ar-eg', 'english'=>'Arabic (Egypt)', 'native'=>'العربية (مصر)', 'lcid'=>'3073' ),
			'ar-iq'=>Array('default'=>'ar-iq', 'english'=>'Arabic (Iraq)', 'native'=>'العربية (العراق)', 'lcid'=>'2049' ),
			'ar-jo'=>Array('default'=>'ar-jo', 'english'=>'Arabic (Jordan)', 'native'=>'العربية (الأردن)', 'lcid'=>'11265' ),
			'ar-kw'=>Array('default'=>'ar-kw', 'english'=>'Arabic (Kuwait)', 'native'=>'العربية (الكويت)', 'lcid'=>'13313' ),
			'ar-lb'=>Array('default'=>'ar-lb', 'english'=>'Arabic (Lebanon)', 'native'=>'العربية (لبنان)', 'lcid'=>'12289' ),
			'ar-ly'=>Array('default'=>'ar-ly', 'english'=>'Arabic (Libya)', 'native'=>'العربية (ليبيا)', 'lcid'=>'4097' ),
			'ar-ma'=>Array('default'=>'ar-ma', 'english'=>'Arabic (Morocco)', 'native'=>'العربية (المملكة المغربية)', 'lcid'=>'6145' ),
			'ar-om'=>Array('default'=>'ar-om', 'english'=>'Arabic (Oman)', 'native'=>'العربية (عمان)', 'lcid'=>'8193' ),
			'ar-qa'=>Array('default'=>'ar-qa', 'english'=>'Arabic (Qatar)', 'native'=>'العربية (قطر)', 'lcid'=>'16385' ),
			'ar-sa'=>Array('default'=>'ar-sa', 'english'=>'Arabic (Saudi Arabia)', 'native'=>'العربية (المملكة العربية السعودية)', 'lcid'=>'1025' ),
			'ar-sy'=>Array('default'=>'ar-sy', 'english'=>'Arabic (Syria)', 'native'=>'العربية (سوريا)', 'lcid'=>'10241' ),
			'ar-tn'=>Array('default'=>'ar-tn', 'english'=>'Arabic (Tunisia)', 'native'=>'العربية (تونس)', 'lcid'=>'7169' ),
			'ar-ye'=>Array('default'=>'ar-ye', 'english'=>'Arabic (Yemen)', 'native'=>'العربية (اليمن)', 'lcid'=>'9217' ),
			'az'=>Array('default'=>'az-latn-az', 'english'=>'Azeri', 'native'=>'Azərbaycan­ılı', 'lcid'=>'' ),
			'az-cyrl-az'=>Array('default'=>'az-cyrl-az', 'english'=>'Azeri (Cyrillic, Azerbaijan)', 'native'=>'Азәрбајҹан (Азәрбајҹан)', 'lcid'=>'' ),
			'az-latn-az'=>Array('default'=>'az-latn-az', 'english'=>'Azeri (Latin, Azerbaijan)', 'native'=>'Azərbaycan­ılı (Azərbaycanca)', 'lcid'=>'' ),
			'be'=>Array('default'=>'be-by', 'english'=>'Belarusian', 'native'=>'Беларускі', 'lcid'=>'1059' ),
			'be-by'=>Array('default'=>'be-by', 'english'=>'Belarusian (Belarus)', 'native'=>'Беларускі (Беларусь)', 'lcid'=>'' ),
			'bg'=>Array('default'=>'bg-bg', 'english'=>'Bulgarian', 'native'=>'български', 'lcid'=>'1026' ),
			'bg-bg'=>Array('default'=>'bg-bg', 'english'=>'Bulgarian (Bulgaria)', 'native'=>'български (България)', 'lcid'=>'' ),
			'bs-latn-ba'=>Array('default'=>'bs-latn-ba', 'english'=>'Bosnian (Bosnia and Herzegovina)', 'native'=>'bosanski (Bosna i Hercegovina)', 'lcid'=>'' ),
			'ca'=>Array('default'=>'ca-es', 'english'=>'Catalan', 'native'=>'Català', 'lcid'=>'1027' ),
			'ca-es'=>Array('default'=>'ca-es', 'english'=>'Catalan (Catalan)', 'native'=>'català (català)', 'lcid'=>'' ),
			'cs'=>Array('default'=>'cs-cz', 'english'=>'Czech', 'native'=>'Čeština', 'lcid'=>'1029' ),
			'cs-cz'=>Array('default'=>'cs-cz', 'english'=>'Czech (Czech Republic)', 'native'=>'Čeština (Česká Republika)', 'lcid'=>'' ),
			'cy-gb'=>Array('default'=>'cy-gb', 'english'=>'Welsh (United Kingdom)', 'native'=>'Cymraeg (y Deyrnas Unedig)', 'lcid'=>'' ),
			'da'=>Array('default'=>'da-dk', 'english'=>'Danish', 'native'=>'Dansk', 'lcid'=>'1030' ),
			'da-dk'=>Array('default'=>'da-dk', 'english'=>'Danish (Denmark)', 'native'=>'dansk (Danmark)', 'lcid'=>'' ),
			'de'=>Array('default'=>'de-de', 'english'=>'German', 'native'=>'Deutsch', 'lcid'=>'1031' ),
			'de-at'=>Array('default'=>'de-at', 'english'=>'German (Austria)', 'native'=>'Deutsch (Österreich)', 'lcid'=>'3079' ),
			'de-ch'=>Array('default'=>'de-ch', 'english'=>'German (Switzerland)', 'native'=>'Deutsch (Schweiz)', 'lcid'=>'2055' ),
			'de-de'=>Array('default'=>'de-de', 'english'=>'German (Germany)', 'native'=>'Deutsch (Deutschland)', 'lcid'=>'' ),
			'de-li'=>Array('default'=>'de-li', 'english'=>'German (Liechtenstein)', 'native'=>'Deutsch (Liechtenstein)', 'lcid'=>'5127' ),
			'de-lu'=>Array('default'=>'de-lu', 'english'=>'German (Luxembourg)', 'native'=>'Deutsch (Luxemburg)', 'lcid'=>'4103' ),
			'dv'=>Array('default'=>'dv-mv', 'english'=>'Divehi', 'native'=>'ދިވެހިބަސް', 'lcid'=>'' ),
			'dv-mv'=>Array('default'=>'dv-mv', 'english'=>'Divehi (Maldives)', 'native'=>'ދިވެހިބަސް (ދިވެހި ރާއްޖެ)', 'lcid'=>'' ),
			'e'=>Array('default'=>'', 'english'=>'Greek', 'native'=>'', 'lcid'=>'1032' ),
			'el'=>Array('default'=>'el-gr', 'english'=>'Greek', 'native'=>'ελληνικά', 'lcid'=>'' ),
			'el-gr'=>Array('default'=>'el-gr', 'english'=>'Greek (Greece)', 'native'=>'ελληνικά (Ελλάδα)', 'lcid'=>'' ),
			'en'=>Array('default'=>'en-us', 'english'=>'English', 'native'=>'English', 'lcid'=>'9' ),
			'en-029'=>Array('default'=>'en-029', 'english'=>'English (Caribbean)', 'native'=>'English (Caribbean)', 'lcid'=>'' ),
			'en-au'=>Array('default'=>'en-au', 'english'=>'English (Australia)', 'native'=>'English (Australia)', 'lcid'=>'3081' ),
			'en-bz'=>Array('default'=>'en-bz', 'english'=>'English (Belize)', 'native'=>'English (Belize)', 'lcid'=>'10249' ),
			'en-ca'=>Array('default'=>'en-ca', 'english'=>'English (Canada)', 'native'=>'English (Canada)', 'lcid'=>'4105' ),
			'en-cb'=>Array('default'=>'', 'english'=>'English (Caribbean)', 'native'=>'', 'lcid'=>'9225' ),
			'en-gb'=>Array('default'=>'en-gb', 'english'=>'English (United Kingdom)', 'native'=>'English (United Kingdom)', 'lcid'=>'2057' ),
			'en-ie'=>Array('default'=>'en-ie', 'english'=>'English (Ireland)', 'native'=>'English (Eire)', 'lcid'=>'6153' ),
			'en-jm'=>Array('default'=>'en-jm', 'english'=>'English (Jamaica)', 'native'=>'English (Jamaica)', 'lcid'=>'8201' ),
			'en-nz'=>Array('default'=>'en-nz', 'english'=>'English (New Zealand)', 'native'=>'English (New Zealand)', 'lcid'=>'5129' ),
			'en-ph'=>Array('default'=>'en-ph', 'english'=>'English (Republic of the Philippines)', 'native'=>'English (Philippines)', 'lcid'=>'' ),
			'en-tt'=>Array('default'=>'en-tt', 'english'=>'English (Trinidad and Tobago)', 'native'=>'English (Trinidad y Tobago)', 'lcid'=>'11273' ),
			'en-us'=>Array('default'=>'en-us', 'english'=>'English (United States)', 'native'=>'English (United States)', 'lcid'=>'1033' ),
			'en-za'=>Array('default'=>'en-za', 'english'=>'English (South Africa)', 'native'=>'English (South Africa)', 'lcid'=>'7177' ),
			'en-zw'=>Array('default'=>'en-zw', 'english'=>'English (Zimbabwe)', 'native'=>'English (Zimbabwe)', 'lcid'=>'' ),
			'es'=>Array('default'=>'es-es', 'english'=>'Spanish', 'native'=>'Español', 'lcid'=>'3082' ),
			'es-ar'=>Array('default'=>'es-ar', 'english'=>'Spanish (Argentina)', 'native'=>'Español (Argentina)', 'lcid'=>'11274' ),
			'es-bo'=>Array('default'=>'es-bo', 'english'=>'Spanish (Bolivia)', 'native'=>'Español (Bolivia)', 'lcid'=>'16394' ),
			'es-c'=>Array('default'=>'', 'english'=>'Spanish (Chile)', 'native'=>'', 'lcid'=>'13322' ),
			'es-cl'=>Array('default'=>'es-cl', 'english'=>'Spanish (Chile)', 'native'=>'Español (Chile)', 'lcid'=>'' ),
			'es-co'=>Array('default'=>'es-co', 'english'=>'Spanish (Colombia)', 'native'=>'Español (Colombia)', 'lcid'=>'9226' ),
			'es-cr'=>Array('default'=>'es-cr', 'english'=>'Spanish (Costa Rica)', 'native'=>'Español (Costa Rica)', 'lcid'=>'5130' ),
			'es-do'=>Array('default'=>'es-do', 'english'=>'Spanish (Dominican Republic)', 'native'=>'Español (República Dominicana)', 'lcid'=>'7178' ),
			'es-ec'=>Array('default'=>'es-ec', 'english'=>'Spanish (Ecuador)', 'native'=>'Español (Ecuador)', 'lcid'=>'12298' ),
			'es-es'=>Array('default'=>'es-es', 'english'=>'Spanish (Spain)', 'native'=>'español (España)', 'lcid'=>'' ),
			'es-gt'=>Array('default'=>'es-gt', 'english'=>'Spanish (Guatemala)', 'native'=>'Español (Guatemala)', 'lcid'=>'4106' ),
			'es-hn'=>Array('default'=>'es-hn', 'english'=>'Spanish (Honduras)', 'native'=>'Español (Honduras)', 'lcid'=>'18442' ),
			'es-mx'=>Array('default'=>'es-mx', 'english'=>'Spanish (Mexico)', 'native'=>'Español (México)', 'lcid'=>'2058' ),
			'es-ni'=>Array('default'=>'es-ni', 'english'=>'Spanish (Nicaragua)', 'native'=>'Español (Nicaragua)', 'lcid'=>'19466' ),
			'es-pa'=>Array('default'=>'es-pa', 'english'=>'Spanish (Panama)', 'native'=>'Español (Panamá)', 'lcid'=>'6154' ),
			'es-pe'=>Array('default'=>'es-pe', 'english'=>'Spanish (Peru)', 'native'=>'Español (Perú)', 'lcid'=>'10250' ),
			'es-pr'=>Array('default'=>'es-pr', 'english'=>'Spanish (Puerto Rico)', 'native'=>'Español (Puerto Rico)', 'lcid'=>'20490' ),
			'es-py'=>Array('default'=>'es-py', 'english'=>'Spanish (Paraguay)', 'native'=>'Español (Paraguay)', 'lcid'=>'15370' ),
			'es-sv'=>Array('default'=>'es-sv', 'english'=>'Spanish (El Salvador)', 'native'=>'Español (El Salvador)', 'lcid'=>'17418' ),
			'es-uy'=>Array('default'=>'es-uy', 'english'=>'Spanish (Uruguay)', 'native'=>'Español (Uruguay)', 'lcid'=>'14346' ),
			'es-ve'=>Array('default'=>'es-ve', 'english'=>'Spanish (Venezuela)', 'native'=>'Español (Republica Bolivariana de Venezuela)', 'lcid'=>'8202' ),
			'et'=>Array('default'=>'et-ee', 'english'=>'Estonian', 'native'=>'Eesti', 'lcid'=>'1061' ),
			'et-ee'=>Array('default'=>'et-ee', 'english'=>'Estonian (Estonia)', 'native'=>'eesti (Eesti)', 'lcid'=>'' ),
			'eu'=>Array('default'=>'eu-es', 'english'=>'Basque', 'native'=>'euskara', 'lcid'=>'1069' ),
			'eu-es'=>Array('default'=>'eu-es', 'english'=>'Basque (Basque)', 'native'=>'euskara (euskara)', 'lcid'=>'' ),
			'fa'=>Array('default'=>'fa-ir', 'english'=>'Persian', 'native'=>'فارسى', 'lcid'=>'1065' ),
			'fa-ir'=>Array('default'=>'fa-ir', 'english'=>'Persian (Iran)', 'native'=>'فارسى (ايران)', 'lcid'=>'' ),
			'fil'=>Array('default'=>'fil', 'english'=>'Filipino', 'native'=>'Filipino', 'lcid'=>'1024' ),
			'fi'=>Array('default'=>'fi-fi', 'english'=>'Finnish', 'native'=>'Suomi', 'lcid'=>'1035' ),
			'fi-fi'=>Array('default'=>'fi-fi', 'english'=>'Finnish (Finland)', 'native'=>'suomi (Suomi)', 'lcid'=>'' ),
			'fo'=>Array('default'=>'fo-fo', 'english'=>'Faroese', 'native'=>'føroyskt', 'lcid'=>'1080' ),
			'fo-fo'=>Array('default'=>'fo-fo', 'english'=>'Faroese (Faroe Islands)', 'native'=>'føroyskt (Føroyar)', 'lcid'=>'' ),
			'fr'=>Array('default'=>'fr-fr', 'english'=>'French', 'native'=>'Français', 'lcid'=>'1036' ),
			'fr-be'=>Array('default'=>'fr-be', 'english'=>'French (Belgium)', 'native'=>'français (Belgique)', 'lcid'=>'2060' ),
			'fr-ca'=>Array('default'=>'fr-ca', 'english'=>'French (Canada)', 'native'=>'français (Canada)', 'lcid'=>'3084' ),
			'fr-ch'=>Array('default'=>'fr-ch', 'english'=>'French (Switzerland)', 'native'=>'français (Suisse)', 'lcid'=>'4108' ),
			'fr-fr'=>Array('default'=>'fr-fr', 'english'=>'French (France)', 'native'=>'français (France)', 'lcid'=>'' ),
			'fr-lu'=>Array('default'=>'fr-lu', 'english'=>'French (Luxembourg)', 'native'=>'français (Luxembourg)', 'lcid'=>'5132' ),
			'fr-mc'=>Array('default'=>'fr-mc', 'english'=>'French (Principality of Monaco)', 'native'=>'français (Principauté de Monaco)', 'lcid'=>'' ),
			'gd'=>Array('default'=>'', 'english'=>'Gaelic (Scots)', 'native'=>'', 'lcid'=>'1084' ),
			'gd-ie'=>Array('default'=>'', 'english'=>'Gaelic (Irish)', 'native'=>'', 'lcid'=>'2108' ),
			'gl'=>Array('default'=>'gl-es', 'english'=>'Galician', 'native'=>'Galego', 'lcid'=>'' ),
			'gl-es'=>Array('default'=>'gl-es', 'english'=>'Galician (Galician)', 'native'=>'galego (galego)', 'lcid'=>'' ),
			'gu'=>Array('default'=>'gu-in', 'english'=>'Gujarati', 'native'=>'ગુજરાતી', 'lcid'=>'' ),
			'gu-in'=>Array('default'=>'gu-in', 'english'=>'Gujarati (India)', 'native'=>'ગુજરાતી (ભારત)', 'lcid'=>'' ),
			'he'=>Array('default'=>'he-il', 'english'=>'Hebrew', 'native'=>'עברית', 'lcid'=>'1037' ),
			'he-il'=>Array('default'=>'he-il', 'english'=>'Hebrew (Israel)', 'native'=>'עברית (ישראל)', 'lcid'=>'' ),
			'hi'=>Array('default'=>'hi-in', 'english'=>'Hindi', 'native'=>'हिंदी', 'lcid'=>'1081' ),
			'hi-in'=>Array('default'=>'hi-in', 'english'=>'Hindi (India)', 'native'=>'हिंदी (भारत)', 'lcid'=>'' ),
			'hr'=>Array('default'=>'hr-hr', 'english'=>'Croatian', 'native'=>'Hrvatski', 'lcid'=>'1050' ),
			'hr-ba'=>Array('default'=>'hr-ba', 'english'=>'Croatian (Bosnia and Herzegovina)', 'native'=>'hrvatski (Bosna i Hercegovina)', 'lcid'=>'' ),
			'hr-hr'=>Array('default'=>'hr-hr', 'english'=>'Croatian (Croatia)', 'native'=>'hrvatski (Hrvatska)', 'lcid'=>'' ),
			'hu'=>Array('default'=>'hu-hu', 'english'=>'Hungarian', 'native'=>'Magyar', 'lcid'=>'1038' ),
			'hu-hu'=>Array('default'=>'hu-hu', 'english'=>'Hungarian (Hungary)', 'native'=>'magyar (Magyarország)', 'lcid'=>'' ),
			'hy'=>Array('default'=>'hy-am', 'english'=>'Armenian', 'native'=>'Հայերեն', 'lcid'=>'' ),
			'hy-am'=>Array('default'=>'hy-am', 'english'=>'Armenian (Armenia)', 'native'=>'Հայերեն (Հայաստան)', 'lcid'=>'' ),
			'id'=>Array('default'=>'id-id', 'english'=>'Indonesian', 'native'=>'Bahasa Indonesia', 'lcid'=>'' ),
			'id-id'=>Array('default'=>'id-id', 'english'=>'Indonesian', 'native'=>'Bahasa Indonesia', 'lcid'=>'' ),
			'in'=>Array('default'=>'', 'english'=>'Indonesian', 'native'=>'', 'lcid'=>'1057' ),
			'is'=>Array('default'=>'is-is', 'english'=>'Icelandic', 'native'=>'íslenska', 'lcid'=>'1039' ),
			'is-is'=>Array('default'=>'is-is', 'english'=>'Icelandic (Iceland)', 'native'=>'íslenska (Ísland)', 'lcid'=>'' ),
			'it'=>Array('default'=>'it-it', 'english'=>'Italian', 'native'=>'Italiano', 'lcid'=>'1040' ),
			'it-ch'=>Array('default'=>'it-ch', 'english'=>'Italian (Switzerland)', 'native'=>'italiano (Svizzera)', 'lcid'=>'2064' ),
			'it-it'=>Array('default'=>'it-it', 'english'=>'Italian (Italy)', 'native'=>'italiano (Italia)', 'lcid'=>'' ),
			'ja'=>Array('default'=>'ja-jp', 'english'=>'Japanese', 'native'=>'日本語', 'lcid'=>'1041' ),
			'ja-jp'=>Array('default'=>'ja-jp', 'english'=>'Japanese (Japan)', 'native'=>'日本語 (日本)', 'lcid'=>'' ),
			'ji'=>Array('default'=>'', 'english'=>'Yiddish', 'native'=>'', 'lcid'=>'1085' ),
			'ka'=>Array('default'=>'ka-ge', 'english'=>'Georgian', 'native'=>'ქართული', 'lcid'=>'' ),
			'ka-ge'=>Array('default'=>'ka-ge', 'english'=>'Georgian (Georgia)', 'native'=>'ქართული (საქართველო)', 'lcid'=>'' ),
			'kk'=>Array('default'=>'kk-kz', 'english'=>'Kazakh', 'native'=>'Қазащb', 'lcid'=>'' ),
			'kk-kz'=>Array('default'=>'kk-kz', 'english'=>'Kazakh (Kazakhstan)', 'native'=>'Қазақ (Қазақстан)', 'lcid'=>'' ),
			'kn'=>Array('default'=>'kn-in', 'english'=>'Kannada', 'native'=>'ಕನ್ನಡ', 'lcid'=>'' ),
			'kn-in'=>Array('default'=>'kn-in', 'english'=>'Kannada (India)', 'native'=>'ಕನ್ನಡ (ಭಾರತ)', 'lcid'=>'' ),
			'ko'=>Array('default'=>'ko-kr', 'english'=>'Korean', 'native'=>'한국어', 'lcid'=>'2066' ),
			'ko-kr'=>Array('default'=>'ko-kr', 'english'=>'Korean (Korea)', 'native'=>'한국어 (대한민국)', 'lcid'=>'' ),
			'kok'=>Array('default'=>'kok-in', 'english'=>'Konkani', 'native'=>'कोंकणी', 'lcid'=>'' ),
			'kok-in'=>Array('default'=>'kok-in', 'english'=>'Konkani (India)', 'native'=>'कोंकणी (भारत)', 'lcid'=>'' ),
			'ky'=>Array('default'=>'ky-kg', 'english'=>'Kyrgyz', 'native'=>'Кыргыз', 'lcid'=>'' ),
			'ky-kg'=>Array('default'=>'ky-kg', 'english'=>'Kyrgyz (Kyrgyzstan)', 'native'=>'Кыргыз (Кыргызстан)', 'lcid'=>'' ),
			'lt'=>Array('default'=>'lt-lt', 'english'=>'Lithuanian', 'native'=>'Lietuvių', 'lcid'=>'1063' ),
			'lt-lt'=>Array('default'=>'lt-lt', 'english'=>'Lithuanian (Lithuania)', 'native'=>'lietuvių (Lietuva)', 'lcid'=>'' ),
			'lv'=>Array('default'=>'lv-lv', 'english'=>'Latvian', 'native'=>'Latviešu', 'lcid'=>'1062' ),
			'lv-lv'=>Array('default'=>'lv-lv', 'english'=>'Latvian (Latvia)', 'native'=>'latviešu (Latvija)', 'lcid'=>'' ),
			'mi-nz'=>Array('default'=>'mi-nz', 'english'=>'Maori (New Zealand)', 'native'=>'Reo Māori (Aotearoa)', 'lcid'=>'' ),
			'mk'=>Array('default'=>'mk-mk', 'english'=>'Macedonian', 'native'=>'македонски јазик', 'lcid'=>'1071' ),
			'mk-mk'=>Array('default'=>'mk-mk', 'english'=>'Macedonian (Former Yugoslav Republic of Macedonia)', 'native'=>'македонски јазик (Македонија)', 'lcid'=>'' ),
			'mn'=>Array('default'=>'mn-mn', 'english'=>'Mongolian', 'native'=>'Монгол хэл', 'lcid'=>'' ),
			'mn-mn'=>Array('default'=>'mn-mn', 'english'=>'Mongolian (Cyrillic, Mongolia)', 'native'=>'Монгол хэл (Монгол улс)', 'lcid'=>'' ),
			'mr'=>Array('default'=>'mr-in', 'english'=>'Marathi', 'native'=>'मराठी', 'lcid'=>'' ),
			'mr-in'=>Array('default'=>'mr-in', 'english'=>'Marathi (India)', 'native'=>'मराठी (भारत)', 'lcid'=>'' ),
			'ms'=>Array('default'=>'ms-my', 'english'=>'Malay', 'native'=>'Bahasa Malaysia', 'lcid'=>'1086' ),
			'ms-bn'=>Array('default'=>'ms-bn', 'english'=>'Malay (Brunei Darussalam)', 'native'=>'Bahasa Malaysia (Brunei Darussalam)', 'lcid'=>'' ),
			'ms-my'=>Array('default'=>'ms-my', 'english'=>'Malay (Malaysia)', 'native'=>'Bahasa Malaysia (Malaysia)', 'lcid'=>'' ),
			'mt'=>Array('default'=>'', 'english'=>'Maltese', 'native'=>'Malti', 'lcid'=>'1082' ),
			'mt-mt'=>Array('default'=>'mt-mt', 'english'=>'Maltese (Malta)', 'native'=>'Malti (Malta)', 'lcid'=>'' ),
			'n'=>Array('default'=>'', 'english'=>'Dutch (Standard)', 'native'=>'', 'lcid'=>'1043' ),
			'nb-no'=>Array('default'=>'nb-no', 'english'=>'Norwegian, Bokmal (Norway)', 'native'=>'Norsk, bokmål (Norge)', 'lcid'=>'' ),
			'nl'=>Array('default'=>'nl-nl', 'english'=>'Dutch', 'native'=>'Nederlands', 'lcid'=>'' ),
			'nl-be'=>Array('default'=>'nl-be', 'english'=>'Dutch (Belgium)', 'native'=>'Nederlands (België)', 'lcid'=>'2067' ),
			'nl-nl'=>Array('default'=>'nl-nl', 'english'=>'Dutch (Netherlands)', 'native'=>'Nederlands (Nederland)', 'lcid'=>'' ),
			'nn-no'=>Array('default'=>'nn-no', 'english'=>'Norwegian, Nynorsk (Norway)', 'native'=>'norsk, nynorsk (Noreg)', 'lcid'=>'' ),
			'no'=>Array('default'=>'nb-no', 'english'=>'Norwegian', 'native'=>'Norsk', 'lcid'=>'2068' ),
			'ns-za'=>Array('default'=>'ns-za', 'english'=>'Northern Sotho (South Africa)', 'native'=>'Sesotho sa Leboa (Afrika Borwa)', 'lcid'=>'' ),
			'pa'=>Array('default'=>'pa-in', 'english'=>'Punjabi', 'native'=>'ਪੰਜਾਬੀ', 'lcid'=>'' ),
			'pa-in'=>Array('default'=>'pa-in', 'english'=>'Punjabi (India)', 'native'=>'ਪੰਜਾਬੀ (ਭਾਰਤ)', 'lcid'=>'' ),
			'pl'=>Array('default'=>'pl-pl', 'english'=>'Polish', 'native'=>'Polski', 'lcid'=>'1045' ),
			'pl-pl'=>Array('default'=>'pl-pl', 'english'=>'Polish (Poland)', 'native'=>'polski (Polska)', 'lcid'=>'1045' ),
			'pt'=>Array('default'=>'pt-br', 'english'=>'Portuguese', 'native'=>'Português', 'lcid'=>'2070' ),
			'pt-br'=>Array('default'=>'pt-br', 'english'=>'Portuguese (Brazil)', 'native'=>'Português (Brasil)', 'lcid'=>'1046' ),
			'pt-pt'=>Array('default'=>'pt-pt', 'english'=>'Portuguese (Portugal)', 'native'=>'português (Portugal)', 'lcid'=>'' ),
			'quz-bo'=>Array('default'=>'quz-bo', 'english'=>'Quechua (Bolivia)', 'native'=>'runasimi (Bolivia Suyu)', 'lcid'=>'' ),
			'quz-ec'=>Array('default'=>'quz-ec', 'english'=>'Quechua (Ecuador)', 'native'=>'runasimi (Ecuador Suyu)', 'lcid'=>'' ),
			'quz-pe'=>Array('default'=>'quz-pe', 'english'=>'Quechua (Peru)', 'native'=>'runasimi (Peru Suyu)', 'lcid'=>'' ),
			'rm'=>Array('default'=>'', 'english'=>'Rhaeto-Romanic', 'native'=>'', 'lcid'=>'1047' ),
			'ro'=>Array('default'=>'ro-ro', 'english'=>'Romanian', 'native'=>'Română', 'lcid'=>'1048' ),
			'ro-mo'=>Array('default'=>'ro-mo', 'english'=>'Romanian (Moldavia)', 'native'=>'', 'lcid'=>'2072' ),
			'ro-ro'=>Array('default'=>'ro-ro', 'english'=>'Romanian (Romania)', 'native'=>'română (România)', 'lcid'=>'' ),
			'ru'=>Array('default'=>'ru-ru', 'english'=>'Russian', 'native'=>'Русский', 'lcid'=>'1049' ),
			'ru-mo'=>Array('default'=>'ru-mo', 'english'=>'Russian (Moldavia)', 'native'=>'', 'lcid'=>'2073' ),
			'ru-ru'=>Array('default'=>'ru-ru', 'english'=>'Russian (Russia)', 'native'=>'русский (Россия)', 'lcid'=>'' ),
			's'=>Array('default'=>'', 'english'=>'Slovenian', 'native'=>'', 'lcid'=>'1060' ),
			'sa'=>Array('default'=>'sa-in', 'english'=>'Sanskrit', 'native'=>'संस्कृत', 'lcid'=>'' ),
			'sa-in'=>Array('default'=>'sa-in', 'english'=>'Sanskrit (India)', 'native'=>'संस्कृत (भारतम्)', 'lcid'=>'' ),
			'sb'=>Array('default'=>'', 'english'=>'Sorbian', 'native'=>'', 'lcid'=>'1070' ),
			'se-fi'=>Array('default'=>'se-fi', 'english'=>'Sami (Northern) (Finland)', 'native'=>'davvisámegiella (Suopma)', 'lcid'=>'' ),
			'se-no'=>Array('default'=>'se-no', 'english'=>'Sami (Northern) (Norway)', 'native'=>'davvisámegiella (Norga)', 'lcid'=>'' ),
			'se-se'=>Array('default'=>'se-se', 'english'=>'Sami (Northern) (Sweden)', 'native'=>'davvisámegiella (Ruoŧŧa)', 'lcid'=>'' ),
			'sk'=>Array('default'=>'sk-sk', 'english'=>'Slovak', 'native'=>'Slovenčina', 'lcid'=>'1051' ),
			'sk-sk'=>Array('default'=>'sk-sk', 'english'=>'Slovak (Slovakia)', 'native'=>'slovenčina (Slovenská republika)', 'lcid'=>'' ),
			'sl'=>Array('default'=>'sl-si', 'english'=>'Slovenian', 'native'=>'Slovenski', 'lcid'=>'' ),
			'sl-si'=>Array('default'=>'sl-si', 'english'=>'Slovenian (Slovenia)', 'native'=>'slovenski (Slovenija)', 'lcid'=>'' ),
			'sma-no'=>Array('default'=>'sma-no', 'english'=>'Sami (Southern) (Norway)', 'native'=>'åarjelsaemiengiele (Nöörje)', 'lcid'=>'' ),
			'sma-se'=>Array('default'=>'sma-se', 'english'=>'Sami (Southern) (Sweden)', 'native'=>'åarjelsaemiengiele (Sveerje)', 'lcid'=>'' ),
			'smj-no'=>Array('default'=>'smj-no', 'english'=>'Sami (Lule) (Norway)', 'native'=>'julevusámegiella (Vuodna)', 'lcid'=>'' ),
			'smj-se'=>Array('default'=>'smj-se', 'english'=>'Sami (Lule) (Sweden)', 'native'=>'julevusámegiella (Svierik)', 'lcid'=>'' ),
			'smn-fi'=>Array('default'=>'smn-fi', 'english'=>'Sami (Inari) (Finland)', 'native'=>'sämikielâ (Suomâ)', 'lcid'=>'' ),
			'sms-fi'=>Array('default'=>'sms-fi', 'english'=>'Sami (Skolt) (Finland)', 'native'=>'sääm´ǩiõll (Lää´ddjânnam)', 'lcid'=>'' ),
			'sq'=>Array('default'=>'sq-al', 'english'=>'Albanian', 'native'=>'Shqipe', 'lcid'=>'1052' ),
			'sq-al'=>Array('default'=>'sq-al', 'english'=>'Albanian (Albania)', 'native'=>'shqipe (Shqipëria)', 'lcid'=>'' ),
			'sr'=>Array('default'=>'sr-latn-cs', 'english'=>'Serbian', 'native'=>'Srpski', 'lcid'=>'2074' ),
			'sr-cyrl-ba'=>Array('default'=>'sr-cyrl-ba', 'english'=>'Serbian (Cyrillic) (Bosnia and Herzegovina)', 'native'=>'српски (Босна и Херцеговина)', 'lcid'=>'' ),
			'sr-cyrl-cs'=>Array('default'=>'sr-cyrl-cs', 'english'=>'Serbian (Cyrillic, Serbia)', 'native'=>'српски (Србија)', 'lcid'=>'' ),
			'sr-latn-ba'=>Array('default'=>'sr-latn-ba', 'english'=>'Serbian (Latin) (Bosnia and Herzegovina)', 'native'=>'srpski (Bosna i Hercegovina)', 'lcid'=>'' ),
			'sr-latn-cs'=>Array('default'=>'sr-latn-cs', 'english'=>'Serbian (Latin, Serbia)', 'native'=>'srpski (Srbija)', 'lcid'=>'' ),
			'sv'=>Array('default'=>'sv-se', 'english'=>'Swedish', 'native'=>'Svenska', 'lcid'=>'1053' ),
			'sv-fi'=>Array('default'=>'sv-fi', 'english'=>'Swedish (Finland)', 'native'=>'svenska (Finland)', 'lcid'=>'2077' ),
			'sv-se'=>Array('default'=>'sv-se', 'english'=>'Swedish (Sweden)', 'native'=>'svenska (Sverige)', 'lcid'=>'' ),
			'sw'=>Array('default'=>'sw-ke', 'english'=>'Kiswahili', 'native'=>'Kiswahili', 'lcid'=>'' ),
			'sw-ke'=>Array('default'=>'sw-ke', 'english'=>'Kiswahili (Kenya)', 'native'=>'Kiswahili (Kenya)', 'lcid'=>'' ),
			'sx'=>Array('default'=>'', 'english'=>'Sutu', 'native'=>'', 'lcid'=>'1072' ),
			'syr'=>Array('default'=>'syr-sy', 'english'=>'Syriac', 'native'=>'ܣܘܪܝܝܐ', 'lcid'=>'' ),
			'syr-sy'=>Array('default'=>'syr-sy', 'english'=>'Syriac (Syria)', 'native'=>'ܣܘܪܝܝܐ (سوريا)', 'lcid'=>'' ),
			'sz'=>Array('default'=>'', 'english'=>'Sami (Lappish)', 'native'=>'', 'lcid'=>'1083' ),
			'ta'=>Array('default'=>'ta-in', 'english'=>'Tamil', 'native'=>'தமிழ்', 'lcid'=>'' ),
			'ta-in'=>Array('default'=>'ta-in', 'english'=>'Tamil (India)', 'native'=>'தமிழ் (இந்தியா)', 'lcid'=>'' ),
			'te'=>Array('default'=>'te-in', 'english'=>'Telugu', 'native'=>'తెలుగు', 'lcid'=>'' ),
			'te-in'=>Array('default'=>'te-in', 'english'=>'Telugu (India)', 'native'=>'తెలుగు (భారత దేశం)', 'lcid'=>'' ),
			'th'=>Array('default'=>'th-th', 'english'=>'Thai', 'native'=>'ไทย', 'lcid'=>'1054' ),
			'th-th'=>Array('default'=>'th-th', 'english'=>'Thai (Thailand)', 'native'=>'ไทย (ไทย)', 'lcid'=>'' ),
			'tn'=>Array('default'=>'', 'english'=>'Tswana', 'native'=>'', 'lcid'=>'1074' ),
			'tn-za'=>Array('default'=>'tn-za', 'english'=>'Tswana (South Africa)', 'native'=>'Setswana (Aforika Borwa)', 'lcid'=>'' ),
			'tr'=>Array('default'=>'tr-tr', 'english'=>'Turkish', 'native'=>'Türkçe', 'lcid'=>'1055' ),
			'tr-tr'=>Array('default'=>'tr-tr', 'english'=>'Turkish (Turkey)', 'native'=>'Türkçe (Türkiye)', 'lcid'=>'' ),
			'ts'=>Array('default'=>'', 'english'=>'Tsonga', 'native'=>'', 'lcid'=>'1073' ),
			'tt'=>Array('default'=>'tt-ru', 'english'=>'Tatar', 'native'=>'Татар', 'lcid'=>'' ),
			'tt-ru'=>Array('default'=>'tt-ru', 'english'=>'Tatar (Russia)', 'native'=>'Татар (Россия)', 'lcid'=>'' ),
			'uk'=>Array('default'=>'uk-ua', 'english'=>'Ukrainian', 'native'=>'Yкраїньска', 'lcid'=>'1058' ),
			'uk-ua'=>Array('default'=>'uk-ua', 'english'=>'Ukrainian (Ukraine)', 'native'=>'україньска (Україна)', 'lcid'=>'' ),
			'ur'=>Array('default'=>'ur-pk', 'english'=>'Urdu', 'native'=>'اُردو', 'lcid'=>'1056' ),
			'ur-pk'=>Array('default'=>'ur-pk', 'english'=>'Urdu (Islamic Republic of Pakistan)', 'native'=>'اُردو (پاکستان)', 'lcid'=>'' ),
			'uz'=>Array('default'=>'uz-latn-uz', 'english'=>'Uzbek', 'native'=>'U\'zbek', 'lcid'=>'' ),
			'uz-cyrl-uz'=>Array('default'=>'uz-cyrl-uz', 'english'=>'Uzbek (Cyrillic, Uzbekistan)', 'native'=>'Ўзбек (Ўзбекистон)', 'lcid'=>'' ),
			'uz-latn-uz'=>Array('default'=>'uz-latn-uz', 'english'=>'Uzbek (Latin, Uzbekistan)', 'native'=>'U\'zbek (U\'zbekiston Respublikasi)', 'lcid'=>'' ),
			've'=>Array('default'=>'', 'english'=>'Venda', 'native'=>'', 'lcid'=>'1075' ),
			'vi'=>Array('default'=>'vi-vn', 'english'=>'Vietnamese', 'native'=>'Tiếng Việt', 'lcid'=>'1066' ),
			'vi-vn'=>Array('default'=>'vi-vn', 'english'=>'Vietnamese (Vietnam)', 'native'=>'Tiếng Việt (Việt Nam)', 'lcid'=>'' ),
			'xh'=>Array('default'=>'', 'english'=>'Xhosa', 'native'=>'', 'lcid'=>'1076' ),
			'xh-za'=>Array('default'=>'xh-za', 'english'=>'Xhosa (South Africa)', 'native'=>'isiXhosa (uMzantsi Afrika)', 'lcid'=>'' ),
			'zh-chs'=>Array('default'=>'(none)', 'english'=>'Chinese (Simplified)', 'native'=>'中文(简体)', 'lcid'=>'' ),
			'zh-cht'=>Array('default'=>'(none)', 'english'=>'Chinese (Traditional)', 'native'=>'中文(繁體)', 'lcid'=>'' ),
			'zh-cn'=>Array('default'=>'zh-cn', 'english'=>'Chinese (People\'s Republic of China)', 'native'=>'中文(中华人民共和国)', 'lcid'=>'2052' ),
			'zh-hk'=>Array('default'=>'zh-hk', 'english'=>'Chinese (Hong Kong S.A.R.)', 'native'=>'中文(香港特别行政區)', 'lcid'=>'3076' ),
			'zh-mo'=>Array('default'=>'zh-mo', 'english'=>'Chinese (Macao S.A.R.)', 'native'=>'中文(澳門特别行政區)', 'lcid'=>'' ),
			'zh-sg'=>Array('default'=>'zh-sg', 'english'=>'Chinese (Singapore)', 'native'=>'中文(新加坡)', 'lcid'=>'4100' ),
			'zh-tw'=>Array('default'=>'zh-tw', 'english'=>'Chinese (Taiwan)', 'native'=>'中文(台灣)', 'lcid'=>'1028' ),
			'zu'=>Array('default'=>'', 'english'=>'Zulu', 'native'=>'', 'lcid'=>'1077' ),
			'zu-za'=>Array('default'=>'zu-za', 'english'=>'Zulu (South Africa)', 'native'=>'isiZulu (iNingizimu Afrika)', 'lcid'=>'' )
		);
		foreach($language as $key=>$value)
			$language[$key]["id"] = $key;
		// languages google can translate into
		$translate = Array('sq', 'ar', 'bg', 'ca', 'zh-cn', 'zh-tw', 'hr', 'cs', 'da', 'nl', 'en', 'et', 'fil', 'fi', 'fr', 'gl', 'de', 'el', 'he', 'hi', 'hu', 'id', 'it', 'ja', 'ko', 'lv', 'lt', 'mt', 'no', 'pl', 'pt', 'ro', 'ru', 'sr', 'sk', 'sl', 'es', 'sv', 'th', 'tr', 'uk', 'vi');
		// try to find language settings
		if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach($langs as $value) { // start going through each one
				$choice = substr($value, 0, 2);
				if(in_array($choice, $translate))
					return $language[$choice];
			}
		}
		// default to english
		return Array("id"=>"en");
    }
}

class ServerManager {
	public function __construct() {
	}
    public function getInfo() {
    }
    public function sessionGrab() {
    }
    public function sessionStab() {
    }
}

?>
