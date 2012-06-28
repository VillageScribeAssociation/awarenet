<?

	require_once($kapenta->installPath . 'modules/images/models/images.set.php');

//--------------------------------------------------------------------------------------------------
//|	initialize a jQuery slideshow on the page
//--------------------------------------------------------------------------------------------------
//:	see http://www.pikachoose.com/ for more information
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object which may own images [string]
//arg: refUID - UID of object which may own images [string]

function images_pikachoose($args) {
	global $user;
	global $page;
	global $session;

	$size = 'slide';												//%	image preset
	$cssFile = "%%serverPath%%modules/images/css/pikachoose.css";	//%	style
	$html = '';														//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('refModule', $args)) { return '(refModule not given)'; }
	if (false == array_key_exists('refModel', $args)) { return '(refModel not given)'; }
	if (false == array_key_exists('refUID', $args)) { return '(refUID not given)'; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	//TODO: checking and sanitization here
	$set = new Images_Images($refModule, $refModel, $refUID);

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	select image size
	//----------------------------------------------------------------------------------------------

	switch($args['area']) {
		case 'indent':	
			$size = 'slideindent';
			$cssFile = "%%serverPath%%modules/images/css/pikachoose.indent.css";
			break;

		case 'mobile':
			$size = 'mobileslide';
			$cssFile = "%%serverPath%%modules/images/css/pikachoose.mobile.css";
			break;
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$html .= ''
	 . "<div class='pikachoose'>\n"
	 . "<ul id='pikame" . $refUID . "' class='jcarousel-skin-pika'>\n";

	foreach($set->members as $item) {
		$title = htmlentities($item['title']);
		$caption = htmlentities($item['caption']);
		$caption = str_replace("\n", '<br/>', $caption);
		$caption = str_replace("\r", '', $caption);

		$html .= ''
		 . "\t<li>\n"
			 . "\t\t<a href='%%serverPath%%images/show/" . $item['alias'] . "'>\n"
			 . "\t\t<img src='%%serverPath%%images/s_" . $size . "/" . $item['alias'] . "'/></a>\n"
			// . "<span><b>$title</b><br/>$caption</span>"
		 . "\t</li>\n";


	}

	$html .= "</ul>\n</div>";

	//----------------------------------------------------------------------------------------------
	//	add to jQuery initialization
	//----------------------------------------------------------------------------------------------

	//$page->jsinit .= "\n\t\t\t$('#pikame').PikaChoose({carousel:true});\n";

	$jsInit = "
		var link = $('<link>');
		link.attr({
			type: 'text/css',
			rel: 'stylesheet',
			href: '" . $cssFile . "'
		});
		$('head').append( link );

		var preventStageHoverEffect = function(self) {
				self.wrap.unbind('mouseenter').unbind('mouseleave');
				self.imgNav.append('<a class=\"tray\"></a>');
				self.imgNav.show();
				self.hiddenTray = true;
				self.imgNav.find('.tray').bind('click',function(){
				if(self.hiddenTray){
					self.list.parents('.jcarousel-container').animate({height:'80px'});
				}else{
					self.list.parents('.jcarousel-container').animate({height:'1px'});
				}
			self.hiddenTray = !self.hiddenTray;
			});
		}

		if (navigator.appName == 'Microsoft Internet Explorer') {
			$('#pikame" . $refUID . "').PikaChoose({bindsFinished: preventStageHoverEffect, carousel:false});
		} else {
			$('#pikame" . $refUID . "').PikaChoose({bindsFinished: preventStageHoverEffect, carousel:true});
		}
	";

	//$page->jsinit .= $jsInit;

	$html .= ''
	 . "<script src='%%serverPath%%modules/images/js/jquery.pikachoose.full.js'></script>"
	 . "<script>\n$jsInit\n</script>\n";

	return $html;
}

?>
