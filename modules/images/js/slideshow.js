
//==================================================================================================
//*	kapenta javascript slideshow
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	slideshow object
//--------------------------------------------------------------------------------------------------
//+	images to be added by js calls emedded in the page (UID, title, caption, licence?)
//+	TODO: read strip width from container element

//arg: divId - ID of HTML element to render into [string]
//arg: objName - name of this object (for calling timeouts) [string]

function KSlideshow(divId, objName) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	this.divId = divId;				//_	div in which this will be rendered [string]
	this.objName = objName;			//_ name of this Javascript object (for timeouts) [string]
	this.images = new Array();		//_	KSlideshowImage objects [array]
	this.current = 0;				//_	index of currently displayed image [string]

	this.width = 560;				//_	width of slideshow, pixels [int]
	this.height = 300;				//_	height of slide, pixels [int]

	this.stripWidth = 9;			//_	max number of images to display in the strip [int]
	this.stripPos = 0;				//_	index of first image shown in the strip [int]
	
	this.fadeFrom = 0;				//_	index of image in array [int]
	this.fadeTo = 0;				//_	index of image in array [int]
	this.transition = 0.0;			//_	current state of wipe (0-1) [float]

	this.paused = false;			//_	automatically cycle through slides until paused [bool]

	//----------------------------------------------------------------------------------------------
	//.	render container divs
	//----------------------------------------------------------------------------------------------	
	//;	note that this should be called by the instantiating script after images are added
	//returns: true on success, false on failure [bool]

	this.render = function() {
		var theDiv = document.getElementById(this.divId);
		if (!theDiv) { return false; }

		var imgset = '';
		for (var i = 0; i < this.images.length; i++) {
			imgset = imgset
			 + "<img"
			 + " id='imgSlide" + this.images[i].UID + "'"
			 + " src='" + kutils.serverPath + "images/slide/" + this.images[i].UID + "'"
			 + " style='display: none;' />\n"
		}

		//order important, see: http://www.quirksmode.org/css/opacity.html
		var opacity = "opacity: .7; "
		 + "-ms-filter:\"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)\"; "
		 + "filter: alpha(opacity=50);";

		var html = ''
		 + "<div id='" + this.divId + "img' >\n"
		 + "<table noborder cellpadding='0' cellspacing='0' width='" + this.width + "'>\n"
		 + "<tr>\n"
		 + "<td id='tdCurrent' width='" + this.width + "px' height='" + this.height + "px' bgcolor='#aaaaaa' valign='top'>"
		 + "<div id='divText' style='color: #fff; margin: 10px;'>"
		 + "<span id='divTitle'"
		 + " style='font-size: 150%; background-color: #444; " + opacity + "'"
		 + "></span><br/>"
		 + "<span id='divCaption'"
		 + " style='font-size: 120%; background-color: #444; " + opacity + "'"
		 + "></span>"
		 + "</div>"
		 + "</td>\n"
		 + "<td id='tdTransition' width='0px' height='" + this.height + "px' bgcolor='#aaaaaa'></td>\n"
		 + "</tr>\n"
		 + "</table>\n"
		 + "</div>\n"
		 + imgset
		 + "<div id='" + this.divId + "strip'></div>\n";
		
		var clean = String(html).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

		theDiv.innerHTML = html; // + "<hr><br/><pre>" + clean + "</pre>";

		if (this.images.length > 0) {
			this.images[0].show('tdCurrent');
			this.setCaption(0)
		}
		this.renderStrip();

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	show the strip
	//----------------------------------------------------------------------------------------------	

	this.renderStrip = function() {
		var divStrip = document.getElementById(this.divId + 'strip');
		var imageset = '';

		for (var i = this.stripPos; (i < this.images.length) && (i < this.stripPos + this.stripWidth); i++) {
			imageset = imageset
			 + "<span"
			 + " id='spanImg" + this.images[i].UID + "'"
			 + " style='height: 60px; background-color: #aaa;'"
			 + ">\n"
			 + "<img"
			 + " src='" + kutils.serverPath + "images/s_thumbsm/" + this.images[i].UID + "'"
			 + " onClick=\"" + this.objName + ".wipe('" + i + "')\""
			 + ">\n"
			 + "</span>\n"
		}

		var urlArrowLeft = "themes/clockface/images/icons/arrow_left.jpg";
		var urlArrowRight = "themes/clockface/images/icons/arrow_right.jpg";

		if (this.stripPos > 0) { urlArrowLeft = "themes/clockface/icons/arrow_left_green.png"; }
		if (this.stripPos < (this.images.length - this.stripWidth)) {
			urlArrowRight = "themes/clockface/images/icons/arrow_right_green.png";
		}

		divStrip.innerHTML = ''
		 + "<img"
		 + " src='" + kutils.serverPath + urlArrowLeft + "'"
		 + " onClick='" + this.objName + ".stripLeft();'"
		 + " />"
		 + imageset
		 + "<img"
		 + " src='" + kutils.serverPath + urlArrowRight + "'"
		 + " onClick='" + this.objName + ".stripRight();'"
		 + " />";

	}

	//----------------------------------------------------------------------------------------------
	//	add an image to the slideshow
	//----------------------------------------------------------------------------------------------	
	this.add = function(UID, title, caption) {
		var newId = this.images.length;
		this.images[newId] = new KSlideshowImage(UID, title, caption);
		this.images[newId].id = newId;
	}

	//----------------------------------------------------------------------------------------------
	//	start wipe between two images
	//----------------------------------------------------------------------------------------------	
	//arg: imgId - array index of image to wipe to [int]

	this.wipe = function(imgId) {
		this.fadeTo = imgId;
		this.images[imgId].show('tdTransition');
		this.transition = 0.0;
		this.setCaption(-1);
		this.doTransition();
	}

	//----------------------------------------------------------------------------------------------
	//	progressively change table cell widths to exchange images
	//----------------------------------------------------------------------------------------------	

	this.doTransition = function() {
		tdCurrent = document.getElementById('tdCurrent');
		tdTransition = document.getElementById('tdTransition');
		tdCurrent.style.width = Math.floor(this.width - (this.width * this.transition)) + 'px';
		tdTransition.style.width = Math.ceil(this.width * this.transition) + 'px';

		if (this.transition >= 1) {
			this.images[this.fadeTo].show('tdCurrent');
			tdCurrent.style.width = this.width + 'px';
			tdTransition.style.width = '0px';
			this.fadeFrom = this.fadeTo;
			this.setCaption(this.fadeTo);
		} else {
			this.transition = this.transition + 0.1;
			setTimeout(this.objName + ".doTransition()",50);
		}
	}

	//----------------------------------------------------------------------------------------------
	//	move the image strip one place to the right
	//----------------------------------------------------------------------------------------------	

	this.stripRight = function() {
		if (this.stripPos < (this.images.length - this.stripWidth)) {
			this.stripPos = this.stripPos + 1;
		}
		this.renderStrip();
	}

	//----------------------------------------------------------------------------------------------
	//	move the image strip one place to the left
	//----------------------------------------------------------------------------------------------	

	this.stripLeft = function() {
		if (this.stripPos > 0) { this.stripPos = this.stripPos - 1; }
		this.renderStrip();
	}

	//----------------------------------------------------------------------------------------------
	//	set the slide caption
	//----------------------------------------------------------------------------------------------	
	//arg: imgId - index of an image to set title and caption by, or -1 to clear [in]

	this.setCaption = function(imgId) {
		var divTitle = document.getElementById('divTitle');
		var divCaption = document.getElementById('divCaption');
		if (imgId > 0) {
			divTitle.innerHTML = this.images[imgId].title;
			divCaption.innerHTML = this.images[imgId].caption;
		} else {
			divTitle.innerHTML = '';
			divCaption.innerHTML = '';
		}
	}

	//----------------------------------------------------------------------------------------------
	//	set the slide caption
	//----------------------------------------------------------------------------------------------	


}

//--------------------------------------------------------------------------------------------------
//	object to represent a single image in the slideshow
//--------------------------------------------------------------------------------------------------

//arg: UID - UID of an Images_Image object [string]
//arg: title - image title [string]
//arg: caption - image caption [string]

function KSlideshowImage(UID, title, caption) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	this.UID = UID;				//_	UID of an Images_Image object [string]
	this.title = title;			//_	default is filename [string]
	this.caption = caption;		//_	html [string]
	this.id = 0;				//_	array index of this item, set by KSlideshow.add() [int]

	//----------------------------------------------------------------------------------------------
	//	set the opacity of this image
	//----------------------------------------------------------------------------------------------
	//arg: aplha - transparency of the image [float]

	function setOpacity(alpha) {
		var theImage = document.getElementById('imgSlide' + this.UID);
		if (!theImage) { return; }										//	element must exist
		if (alpha > .99) { return; }									//	never exceeds .99

		theImage.style.opacity = aplha;									//	TODO: credit here
		theImage.style.MozOpacity = aplha;	
		theImage.style.filter = "alpha(opacity=" + (aplha*100) + ")";
	}	

	//----------------------------------------------------------------------------------------------
	//	show this image
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]	

	this.show = function(cellId) {
		//alert('making visible: imgSlide' + this.UID);
		var theTd = document.getElementById(cellId);
		theTd.style.backgroundImage = "url(" + kutils.serverPath + "images/slide/" + this.UID + ")";
	}

}


/*



var imgs = new Array();		
var zInterval = null;		//%	not used?
var current = 0;			//%	index of current image in array
var pause = false;			//%	slideshow is paused? not used

function so_init() {	// called by document load
	if(!document.getElementById || !document.createElement) { return; }

	// DON'T FORGET TO GRAB THIS FILE AND PLACE IT ON YOUR SERVER IN THE SAME DIRECTORY AS THE JAVASCRIPT!
	// http://slayeroffice.com/code/imageCrossFade/xfade2.css

	/*

	#imageContainer {
		height:309px;
	}

	#imageContainer img {
		display:none;
		position:absolute;
		top:0; left:0;
	}



	css = document.createElement("link");
	css.setAttribute("href","iframe.css");
	css.setAttribute("rel","stylesheet");
	css.setAttribute("type","text/css");
	document.getElementsByTagName("head")[0].appendChild(css);

	//----------------------------------------------------------------------------------------------
	//	load images into array
	//----------------------------------------------------------------------------------------------
	imgs = document.getElementById("imageContainer").getElementsByTagName("img");
	for (i = 1; i < imgs.length; i++) { imgs[i].xOpacity = 0; }		// make all transparent
	imgs[0].style.display = "block";								// set the fist to .99
	imgs[0].xOpacity = .99;
	
	setTimeout(so_xfade,5000);						// crossfade every 5 seconds
}

function so_xfade() {
	cOpacity = imgs[current].xOpacity;				//%	current opacity? [float]
	nIndex = imgs[current+1] ? current + 1 : 0;		//% index of next image

	nOpacity = imgs[nIndex].xOpacity;				//% opacity of next image [float]
	
	cOpacity-=.05; 									//	reduce opacity by 5% on current image
	nOpacity+=.05;									//	increase opacity by 5% on current image
	
	imgs[nIndex].style.display = "block";			//	make sure next image is shown
	imgs[current].xOpacity = cOpacity;				//	set new opacity (our variable)
	imgs[nIndex].xOpacity = nOpacity;				//	"
	
	setOpacity(imgs[current]); 						//	copy our varibale into element style?
	setOpacity(imgs[nIndex]);						//	"
	
	if (cOpacity <= 0) {							//-	IF FADE COMPLETE
		imgs[current].style.display = "none";		//	hide image when competely transparent
		current = nIndex;							//	index for next transition
		setTimeout(so_xfade,5000);					//	begin next transition in 5 seconds

	} else {										//-	IF FADE INCOMPLETE
		setTimeout(so_xfade,50);					//	next step on 0.05 seconds
	}												//	(all 20 steps in ~ 1 second)
	
	//----------------------------------------------------------------------------------------------
	//	apply our opacity value to the element style
	//----------------------------------------------------------------------------------------------
	//arg: obj - an image element in the dom [object]

	function setOpacity(obj) {
		if (obj.xOpacity > .99) {					// opacity never exceeds .99
			obj.xOpacity = .99;
			return;
		}

		obj.style.opacity = obj.xOpacity;								//	THIS, 
		obj.style.MozOpacity = obj.xOpacity;							//	this right here, 
		obj.style.filter = "alpha(opacity=" + (obj.xOpacity*100) + ")";	//  is what we need
	}
	
}
*/
