//==================================================================================================
// HyperTextArea http://hypertextarea.sourceforge.net 
// Type javascript:HyperTextArea.about() into your browser location on any page 
// hosting a HyperTextArea for license details.
//==================================================================================================
//
//--------------------------------------------------------------------------------------------------
//*	HyperTextArea as modified for use with the Kapenta framework
//--------------------------------------------------------------------------------------------------
//+
//+	Contents of this file:
//+	
//+		HyperTextArea - the editor itself
//+		TextFormatButton - a button which format text (bold, italic, etc)
//+		Button - generic toobar button
//+
//--------------------------------------------------------------------------------------------------
//+	How to use this WYSWYG editor
//--------------------------------------------------------------------------------------------------
//+
//+	To create a new editor in your html document, add a (usually hidden) div to the document with
//+	class HyperTextArea or HyperTextArea64.  The title of the div should be the name of the HTML
//+	for field this HTA will use.  The height and width of the div will be copied to the HTA.  Eg:
//+
//+		<div class='HyperTextArea' title='myFormField' style='visibility: hidden; display: none;'>
//+		This text will be preloaded into the HyperTextArea.
//+		</div>
//+
//+	One then calls khta.convertDivs();

//--------------------------------------------------------------------------------------------------
//	WYSWYG editor
//--------------------------------------------------------------------------------------------------

function HyperTextArea(name, html, width, height, delayRender, divId) {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	this.isRichText = false;						//_	design mode is available? [bool]
	this.rng = null;								//_	active range [range]
	this.name = name;								//_ frame id and TA name [string]
	this.html = html || '';							//_	contents? [string]
	this.width = width;								//_ pixels [int]
	this.height = height;							//_	pixels [int]
	this.resourcePath = '';							//_	location for resources [string]
	this.styleSheetUrl = '';						//_	location of stylesheet [string]
	this.delayRender = delayRender || false;		//_	true cancels initial render [bool]
	this.controlNames = [];							//_	set of control names [array]	
	this.controlsByName = [];						//_	set of control obejcts [array]
	this.toolbarNames = [];							//_	set of toolbar names [array]
	this.designModeRetryCount = 0;					//_	TODO: investigate [int]
	this.isSrcView = false;							//_ toggles plain vs rich text [bool]
	this.divId = divId || '';						//_	div in which area is rendered [string]

	this.html = this.html.replace(/%%serverPath%%/g, jsServerPath);		// fixes images

	//----------------------------------------------------------------------------------------------
	//.	default control set
	//----------------------------------------------------------------------------------------------

	this.controls = [
	 ['t', 'toolbar1'],
	 [ 'c',	'bold',		'Bold',			'post_button_bold.gif',		'bold'			],
	 [ 'c',	'italic',	'Italic',		'post_button_italic.gif',	'italic'		],
	 [ 'c',	'left',		'Align Left',	'post_button_jleft.gif',	'justifyleft'	],
	 [ 'c',	'center',	'Center',		'post_button_centre.gif',	'justifycenter'	],
	 [ 'c',	'right',	'Align Right',	'post_button_jright.gif',	'justifyright'	],
	 [ 's' ],
	 [ 'c',	'orderedlist',   'Ordered List',   'post_button_ol.gif', 'insertorderedlist'   ],
	 [ 'c', 'unorderedlist', 'Unordered List', 'post_button_ul.gif', 'insertunorderedlist' ],
	 [ 's' ],
	 [ 'c', 'outdent', 'Outdent', 'post_button_outdent.gif', 'outdent' ],
	 [ 'c', 'indent',  'Indent',  'post_button_indent.gif',  'indent'  ],
	 [ 's' ],
	 [ 'b', 'insertHorizontalRule', 'post_button_hr.gif', 'Insert Horizontal Rule','addHr' ],
	 [ 'c', 'link',  'Create Link', 'post_button_hyperlink.gif',  'createlink'  ],
	 [ 's' ],
	 [ 'p' ]
	];

	this.fonts = [
	 ["","Font"],
	 ["Arial, Helvetica, sans-serif","Arial"],
	 ["Courier New, Courier, mono","Courier New"],
	 ["Times New Roman, Times, serif","Times New Roman"],
	 ["Verdana, Arial, Helvetica, sans-serif","Verdana"]
	];

	this.preformats = [
	 ['<p>','paragraph'],
	 ['<h1>', 'title'],
	 ['<h2>','subtitle'],
	 ['<h3>', 'sub-h3'],
	 ['<h4>','sub-h4'],
	 ['<address>','address'],
	 ['<pre>','pre'],
	 ['<blockquote>','blockquote']
	];

	//----------------------------------------------------------------------------------------------
	//.	initialize HTA, set up controls, menus, reousrce path, div, etc
	//----------------------------------------------------------------------------------------------

	this.init = function() {
		//khta.areas[this.name] = this;					// set link to self?

		//------------------------------------------------------------------------------------------
		//	check to see if designMode mode is available
		//------------------------------------------------------------------------------------------
		if (document.getElementById) {					// does this method exist?
			if (document.all) {  						// check for Internet Explorer 5+	
				this.isRichText = true;					// apparently		

			} else {
				// check for browsers that support design mode
				// make sure that this is not safari (and perhaps other khtml based browsers) 
				// which returns "inherit" for document.designMode 
				if ((document.designMode) && (document.designMode.toLowerCase() != "inherit")) 
					{ this.isRichText = true; }
			}
		}

		if ('' == this.divId) { this.divId = 'divHTA' + this.name; }

		//------------------------------------------------------------------------------------------
		//	set up relative URLs
		//------------------------------------------------------------------------------------------
		if (jsServerPath) {	
			this.resourcePath = jsServerPath + 'modules/editor/'; 
			this.styleSheetUrl = jsServerPath + 'themes/clockface/css/default.css'; 
		}

		//------------------------------------------------------------------------------------------
		//	add controls
		//------------------------------------------------------------------------------------------
		this.addEditorControls();	
		
	} // end this.init

	//==============================================================================================
	//	CONTENT IO
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	change content of editable iframe
	//----------------------------------------------------------------------------------------------
	//arg: html - content to be used for iframe body [string]

	this.setContent = function(html) {
		HyperTextArea.activeArea = this;
		var oRTE;										//%	iframe DOM root [object:document]		
		if (document.all) {	oRTE = frames[this.name].document; }
		else { oRTE = document.getElementById(this.name).contentWindow.document; }
		body = oRTE.getElementsByTagName("body");		// when is this needed, why not oRTE.body?
		this.html = html;
		body.innerHTML = this.html;						// set body only (not head)
	}

	//----------------------------------------------------------------------------------------------
	//.	get content of editable iframe
	//----------------------------------------------------------------------------------------------
	//returns: raw html [string]

	this.getContent = function() {
		alert('getContent');
		khta.activeArea = this;
		var oRTE;										//%	iframe DOM root [object:document]
		if (document.all) {	oRTE = frames[this.name].document; }
		else { oRTE = document.getElementById(this.name).contentWindow.document; }
		body = oRTE.getElementsByTagName("body");		// when is this needed, why not oRTE.body?
		return this.htmlEncode(body[0].innerHTML);		// get body only (not head), sanitize

	}

	//----------------------------------------------------------------------------------------------
	//.	set content of editable iframe
	//----------------------------------------------------------------------------------------------

	this.setContent = function(html) {
		khta.activeArea = this;
		var oRTE;										//%	iframe DOM root [object:document]
		if (document.all) {	oRTE = frames[this.name].document; }
		else { oRTE = document.getElementById(this.name).contentWindow.document; }
		body = oRTE.getElementsByTagName("body");		// when is this needed, why not oRTE.body?
		body.innerHTML = html;							// get body only (not head)
	}

	//----------------------------------------------------------------------------------------------
	//.	 sets the value of hidden form field, this.html
	//----------------------------------------------------------------------------------------------

	this.update = function() {
		this.setViewMode(false);
		var oHdnMessage = document.getElementById('hdn' + this.name);
		var oRTE = document.getElementById(this.name);
	
		//------------------------------------------------------------------------------------------
		//	get a clean version of iframe content (not copy)
		//------------------------------------------------------------------------------------------
		//TODO: PARSE CONTENT FOR STORAGE/TRANSMISSION HERE 	*********************
		//replaceImagesWithBlocks(oRTE.contentWindow.document);  // kapenta only
		//replaceImagesWithBlocks(oRTE.contentWindow.document);  // kapenta only - TODO why twice?
	
		// strip any newlines and carriage returns
		var raw = oRTE.contentWindow.document.body.innerHTML;		// TODO: is this necessary at this point?
		raw = raw.replace(new RegExp("\\n", "g"), '');				// have the parser do it
		raw = raw.replace(new RegExp("\\r", "g"), '');
		raw = oRTE.contentWindow.document.body.innerHTML;

		//------------------------------------------------------------------------------------------
		//	make the update to hidden form field
		//------------------------------------------------------------------------------------------
		if (this.isRichText) {
			if (null == oHdnMessage.value) oHdnMessage.value = "";
			if (document.all) {
				oHdnMessage.value = frames[this.name].document.body.innerHTML;
			} else {
				oHdnMessage.value = oRTE.contentWindow.document.body.innerHTML;
			}
			// if there is no content (other than formatting) set value to nothing
			if ('' == stripHTML(oHdnMessage.value.replace("&nbsp;", " "))) 
				{ oHdnMessage.value = ""; }

			// set this editor's HTML (NB: also set in/by constructor)
			this.html = oHdnMessage.value;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	set editor content from div
	//----------------------------------------------------------------------------------------------
	//arg: divId - id of a div [string]
	//returns: true on success, flase on failure [bool]

	this.setContentFromDiv = function(divId) {
		var theDiv = document.getElementById(divId);
		if (theDiv) { 
			this.setContent(theDiv.innerHTML);
			return true;
		}
		return false;
	}
	
	//----------------------------------------------------------------------------------------------
	//.	convert extened characters to HTML entities
	//----------------------------------------------------------------------------------------------

	this.htmlEncode = function(str) {
		var i = str.length;
		var aRet = [];

		while (i--) {
			var iC = str[i].charCodeAt();
			if (iC < 65 || iC > 127 || (iC > 90 && iC < 97)) {
				aRet[i] = '&#' + iC + ';';
			} else {
				aRet[i] = str[i];
			}
		}
		return aRet.join('');
	}

	//==============================================================================================
	//	CONTROLS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	add editor controls
	//----------------------------------------------------------------------------------------------

	this.addEditorControls = function() {
		for (var i in this.controls) {
			var ary = this.controls[i];
			switch(ary[0]) {
				case 't': this.addControl(new Toolbar(ary[1]));							break;	//..
				case 's': this.addControl(new Spacer());								break;	//..
				case 'b': this.addControl(new Button(ary[1], ary[2], ary[3], ary[4]));	break;	//..

				case 'c': 
					this.addControl(new TextFormatButton(ary[1], ary[2], ary[3], ary[4]));
					break;	//......................................................................

				case 'f': 
					fontMenu = new Menu("fontname","fontname");
					fontMenu.addItems(this.fonts);
					this.addControl(fontMenu);	
					break;	//......................................................................	

				case 'p':
					styleMenu = new Menu("formatblock","formatblock");
					styleMenu.addItems(this.preformats);
					this.addControl(styleMenu);	
					break;	//......................................................................

				case 'z':
					sizeMenu = new Menu("fontsize","fontsize");
					sizeMenu.addItems("","Size",1,1,2,2,3,3,4,4,5,5,6,6,7,7);
					this.addControl(sizeMenu);
					break;

			}
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	add a control
	//----------------------------------------------------------------------------------------------
	//arg: control - a Button or TextFormatButton [object]

	this.addControl = function(control) {
		control.resourcePath = this.resourcePath;		// set reource path
		control.area = this;							// add link to self
		i = this.controlNames.length					
		this.controlNames[eval(i)] = control.name;		// TODO: why eval?
		this.controlsByName[control.name] = control;
	}

	//----------------------------------------------------------------------------------------------
	//.	get name of a contro given it's label (i think?)
	//----------------------------------------------------------------------------------------------
	//arg: label - list of control names begins with this [string]

	this.getControlNames = function(label) {
		text = label + "\n\n";
		for (var i = 0; i < this.controlNames.length; i++) 
			{ text = text + "\t" + this.controlNames[i] + "\n"; }

		return text;		
	}

	//----------------------------------------------------------------------------------------------
	//	get a control given its name
	//----------------------------------------------------------------------------------------------
	
	this.getControl = function(cname) { return this.controlsByName[cname]; }

	//----------------------------------------------------------------------------------------------
	//.	replace a control given its name and a new control (apparently not used)
	//----------------------------------------------------------------------------------------------
	//arg: oldName - name of an existing control [string]
	//arg: newControl - a new control [object]

	this.replaceControl = function(oldName, newControl) {
		if (this.getControl(oldName)) {
			for(i = 0; i < this.controlNames.length; i++) 
				{ if (this.controlNames[i] == oldName) { break; } }
		} else { i = this.controlNames.length; }

		newControl.area = this;								// set area new control
		newControl.resourcePath = this.resourcePath;		// set resource path on new control
		this.controlNames[i] = newControl.name;				
		this.controlsByName[newControl.name] = newControl;
	}

	//----------------------------------------------------------------------------------------------
	//.	
	//----------------------------------------------------------------------------------------------
	//arg: i - index in this.controlNames [int]	
	//arg: control - a new button, spacer, etc [object]

	this.insertControl = function(i, control) {
		control.resourcePath = this.resourcePath;
		control.area = this;
		this.controlNames.splice(i,0, control.name);
		this.controlsByName[control.name] = control;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a control given its name
	//----------------------------------------------------------------------------------------------
	//arg: name - name of a control [string]	
	//returns: true on success, false on failure [bool]

	this.removeControl = function(name) {
		for(i = 0; i < this.controlNames.length; i++) {
			if(this.controlNames[i] == name){
				this.controlNames.splice(i,1);
				return true;
			}
		}
		return false;
	}

	//==============================================================================================
	//	CONTROLS
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	display/refresh contents of editable iframe (WYSWYG content area)
	//----------------------------------------------------------------------------------------------
	//arg: html - html content of iframe document body [string]
	
	this.render = function(html) {
		HyperTextArea.activeArea = this;
		var text= "this.render\n\n";
		for(var i = 0; i < this.controlNames.length; i++) {
			text = text + "\t" + this.controlNames[i] + "\n";
		}
		if (this.isRichText) { this._renderRTE(html); }
		else { this._renderDefault(html); }
	}

	//----------------------------------------------------------------------------------------------
	//.	write a line of text to the div in which this is rendered
	//----------------------------------------------------------------------------------------------
	//arg: txt - line to write to output div [string]

	this.writeln = function(txt) {
		if ('' == this.divId) { 
			document.writeln(txt); 	
		} else {
			var rDiv = document.getElementById(this.divId);
			if (!rDiv) {
				document.writeln("<div id='" + this.divId + "'></div>");
				rDiv = document.getElementById(this.divId);
			}
			rDiv.innerHTML = rDiv.innerHTML + txt;
		}
		
	}

	//----------------------------------------------------------------------------------------------
	//.	display contents as text in a textarea (HTML tags shown)
	//----------------------------------------------------------------------------------------------
	//arg: html - raw html to be placed in textarea [string]

	this._renderDefault = function(html) {
		var taHtml = ''
		 + "<textarea"
		 + " name='" + this.name + "'"
		 + " id='" + this.name + "'"
		 + "style='width: " + this.width + "px; height: " + this.height + "px;'>"
		 + html
		 + '</textarea>';

		this.writeln(taHtml);
	}

	//----------------------------------------------------------------------------------------------
	//.	add editor controls (format buttons and such) to document containing editor (not iframe)
	//----------------------------------------------------------------------------------------------
	//TODO: work out how this knows where to add them, consider improving that
	
	this._renderControls = function() {
		text = "";
		for(var x = 0; x < this.controlNames.length; x++){
			control = this.controlsByName[this.controlNames[x]];
			//TODO: better check for null, undefined, etc
			if (control) { text = text + control.getRenderedText(); }
		}	
		text = text + '</tr>';
		text = text + '</table>';
		this.writeln(text);
	}

	//----------------------------------------------------------------------------------------------
	//.	render rich text editor (the entire WYSWYG editor UI)
	//----------------------------------------------------------------------------------------------
	//TODO: work out how this knows where to add them, consider improving that

	this._renderRTE = function() {
		//------------------------------------------------------------------------------------------
		//	add mouse style for buttons  (TODO: consider adding this style def to theme css.)
		//------------------------------------------------------------------------------------------
		this.writeln("<style type='text/css'>.btnImage {cursor: pointer; cursor: hand; }</style>");

		//------------------------------------------------------------------------------------------
		//	add controls
		//------------------------------------------------------------------------------------------
		this._renderControls();

		//------------------------------------------------------------------------------------------
		//	add content (editable) iframe
		//------------------------------------------------------------------------------------------
		var ifHtml = ''
			 + "<iframe id='" + this.name + "' "
			 + "width='" + this.width + "px' "
			 + "height='" + this.height + "px' "
			 + "frameborder='1' "
			 + "style='border:1px dashed gray;'>"
			 + "</iframe>";

		this.writeln(ifHtml);

		//------------------------------------------------------------------------------------------
		//	add 'view source' checkbox
		//------------------------------------------------------------------------------------------
		var cbHtml = "<br/>"
			+ "<input type='checkbox' "
			 + "id='chkSrc" + this.name + "' "
			 + "onclick=\"khta.getArea(\'" + this.name + "\').toggleHTMLSrc();\" />"
			 + "&nbsp;View Source";

		this.writeln(cbHtml);

		//------------------------------------------------------------------------------------------
		//	add hidden iframe, not yet sure what this is for (TODO)
		//------------------------------------------------------------------------------------------
		//original comment: reimplement this so that it is not in an iframe, rather in a div.

		var hiHtml = ""
			+ "<iframe width='14' height='130' "
			 + "id='cp" + this.name + "' "
			 + "name='cp" + this.name + "' "
			 + "marginwidth='0' marginheight='0' scrolling='no' frameborder='1' "
			 + "style='visibility: hidden; position: absolute;'>"
			 + "</iframe>";		

		this.writeln(hiHtml);

		//------------------------------------------------------------------------------------------
		//	add hidden form field and load it with WYSWYG contents
		//------------------------------------------------------------------------------------------
		var hfHtml = "<input type='hidden' "
			 + "id='hdn" + this.name + "' "
			 + "name='" + this.name + "' value='' />";		

		this.writeln(hfHtml);
		document.getElementById('hdn' + this.name).value = this.html;

		// initialize content (below)
		this.initializeContent(this.html);

		// call onHyperTextAreaLoad(this.name) in a half second
		setTimeout('onHyperTextAreaLoad(\'' + this.name + '\')', 500);

	}  // end this._renderRTE

	//----------------------------------------------------------------------------------------------
	//.	initialize content of content-editable iframe
	//----------------------------------------------------------------------------------------------
	//arg: html - content to be used for iframe body [string]
	//; adds content stylesheet, uses default iframe stylesheet for now

	this.initializeContent = function(html) {
		HyperTextArea.activeArea = this;									// this confuses me
		var useSSUrl = jsServerPath + "themes/clockface/css/iframe.css";	//%	default css [string]
		var frameHtml = '';													//% content [string]
		var styleSheet = '';												//%	[string]

		//------------------------------------------------------------------------------------------
		// add content stylesheet, use default iframe stylesheet for now
		//------------------------------------------------------------------------------------------
		if (this.styleSheetUrl) { 
			useSSUrl = this.styleSheetUrl; 
			styleSheet += "<link media='all' type='text/css' href='" + useSSUrl + "' rel='stylesheet'>\n";
		}

		//------------------------------------------------------------------------------------------
		//	assemble iframe HTML
		//------------------------------------------------------------------------------------------
		frameHtml = ''
		 + "<!DOCTYPE html PUBLIC "
		  + "'-//W3C//DTD XHTML 1.0 Transitional//EN' "
		  + "'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n"
		 + "<html>\n"
		 + "<head>\n"
		 + "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />"
		 + styleSheet
		 + "</head>\n"
		 + "<body>" + html + "</body>\n"
		 + "</html>";

		//------------------------------------------------------------------------------------------
		//	insert frameHtml into the content-editable iframe
		//------------------------------------------------------------------------------------------

		if (document.all) {							// internet explorer check?
			var oRTE = frames[this.name].document;	
			oRTE.open();							// oRTE is now a document object
			oRTE.write(frameHtml);					// write new document
			oRTE.close();							// perhaps this triggers onLoad?
			if (oRTE.addEventListener) {
				// this doesn't seem to work, TODO: test, then remove
				oRTE.addEventListener("DOMNodeInserted", nodeinsert_handler, false); // kapenta
			}

		} else {
			var oRTE = document.getElementById(this.name).contentWindow.document;
			oRTE.open();							// oRTE is now a document object
			oRTE.write(frameHtml);
			oRTE.close();
			//attach a keyboard handler for Mozilla to make keyboard shortcuts for formatting text
			if (oRTE.addEventListener) {
				// this does not work for all browsers, improve
				oRTE.addEventListener("keypress", kb_handler, true);
				oRTE.addEventListener("DOMNodeInserted", nodeinsert_handler, false); // kapenta
			}
		}
	}


	//----------------------------------------------------------------------------------------------
	//.	 set all toolbars to be visible or hidden
	//----------------------------------------------------------------------------------------------
	//arg: isVisible - truthy? [bool]

	this.setToolbarsVisible = function(isVisible) {
		var visibleStyle = isVisible ? "visible" : "hidden";		// TODO: remove ?:
		for (var i = 0; i < this.toolbarNames.length; i++) {
			var toolBar = document.getElementById(this.toolbarNames[i] + "_" + this.name);
			toolBar.style.visibility = visibleStyle;
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	 set view mode to editing HTML, or editing plain text (source)
	//----------------------------------------------------------------------------------------------
	//arg: isSrcView - set to truthy to view source [bool]
	//TODO: consider replacing blocks here to make them more easily editable

	this.setViewMode = function(isSrcView) {
		var oRTE;
		HyperTextArea.activeArea = this;		// this confuses me

		//------------------------------------------------------------------------------------------
		//	set oRTE to be the iframe's document object
		//------------------------------------------------------------------------------------------
		//contributed by Bob Hutzel (thanks Bob!)
		if (document.all) { oRTE = frames[this.name].document; }	
		else { oRTE = document.getElementById(this.name).contentWindow.document; }
		
		//------------------------------------------------------------------------------------------
		//only change the view if it is different than the current state
		//------------------------------------------------------------------------------------------
		if (isSrcView && !this.isSrcView) {
			//--------------------------------------------------------------------------------------
			//	toggle source view ON (content-editable HTML to content-editable text)
			//--------------------------------------------------------------------------------------
			this.isSrcView = true;				// record new state
			this.setToolbarsVisible(false);		// hide the toolbars

			if (document.all) { oRTE.body.innerText = oRTE.body.innerHTML; }
			else {
				var htmlSrc = oRTE.createTextNode(oRTE.body.innerHTML);
				oRTE.body.innerHTML = "";
				oRTE.body.appendChild(htmlSrc);
			}

		} else if (!isSrcView && this.isSrcView) {
			//--------------------------------------------------------------------------------------
			//	toggle source view OFF (content-editable text to content-editable HTML)
			//--------------------------------------------------------------------------------------
			this.isSrcView = false;				// record new state
			this.setToolbarsVisible(true);		// show all toolbars
			if (document.all) { oRTE.body.innerHTML = oRTE.body.innerText; }
			else {
				var htmlSrc = oRTE.body.ownerDocument.createRange();	// ownerDocument?
				htmlSrc.selectNodeContents(oRTE.body);
				oRTE.body.innerHTML = htmlSrc.toString();
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	 toggle view mode between 'html' and source
	//----------------------------------------------------------------------------------------------
	
	this.toggleHTMLSrc = function() {
		if (document.getElementById("chkSrc" + this.name).checked) { this.setViewMode(true); }
		else { this.setViewMode(false); }
	}

	//==============================================================================================
	//	CONTENT MANIPULATION AND FORMATTING
	//==============================================================================================

	//----------------------------------------------------------------------------------------------
	//.	 this runs 'commands' against the DOM, as used by the text format buttons
	//----------------------------------------------------------------------------------------------
	//see compatability matrix here: http://www.quirksmode.org/dom/execCommand.html
	//TODO: would really like to be able to plug functionality in here.  This would include the 
	//ability to launch a wizard, and then insert arbitrary text at the insertion point

	this.formatText = function(command,option) {
		var oRTE;

		HyperTextArea.activeArea = this;			// not entirely sure what this accomplishes

		//------------------------------------------------------------------------------------------
		//	set oRTE to be the iframe's document object
		//------------------------------------------------------------------------------------------
		if (document.all) { oRTE = frames[this.name]; }
		else { oRTE = document.getElementById(this.name).contentWindow; }

		//------------------------------------------------------------------------------------------
		//	maybe a switch here?
		//------------------------------------------------------------------------------------------		
		if ((command == "forecolor") || (command == "hilitecolor")) {
			//--------------------------------------------------------------------------------------
			//	TODO: investigate further, discover if this is even used
			//--------------------------------------------------------------------------------------
			this.command = command;
			controlElement = document.getElementById(this.name +"_" + command);	// div containing button
			cp = document.getElementById('cp' + this.name);		// 'copy' iFrame
			this.cpWindow.area = this;							// set copy iframe to this iframe?
			cp.style.left = getOffsetLeft(controlElement) + "px";	
			cp.style.top = (getOffsetTop(controlElement) + controlElement.offsetHeight) + "px";
			if (cp.style.visibility == "hidden") { cp.style.visibility="visible"; }
			else { cp.style.visibility="hidden"; }
			
			//get current selected range
			var sel = oRTE.document.selection; 
			if (sel != null) { this.rng = sel.createRange(); }	// and do what with it?

		} else if (command == "createlink") {
			//--------------------------------------------------------------------------------------
			//	make a hyperlink aroudn the selected range
			//--------------------------------------------------------------------------------------
			// TODO need a way to make tihs more flixible.  Would especially like to be able to
			// insert a link with both the containing text and the URL
			var szURL = prompt("Enter a URL:", "");
			oRTE.document.execCommand("Unlink",false,null)
			oRTE.document.execCommand("CreateLink", false, szURL)

		} else {
			//--------------------------------------------------------------------------------------
			//	all other commands (bold, italic, etc?)
			//--------------------------------------------------------------------------------------
			oRTE.focus();
		  	oRTE.document.execCommand(command, false, option);
			oRTE.focus();
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	set foreground or background color
	//----------------------------------------------------------------------------------------------
	//arg: imagePath - URL of image [string]

	this.setColor = function(color) {
		var oRTE;						//% iframe document [object]

		khta.activeArea = this;
		
		if (document.all) {	oRTE = frames[this.name]; }
		else { oRTE = document.getElementById(this.name).contentWindow; }
		
		//var parentCommand = parent.command;
		//------------------------------------------------------------------------------------------
		//	retrieve selected range
		//------------------------------------------------------------------------------------------
		if (document.all) {
			var sel = oRTE.document.selection; 
			if (this.command == "hilitecolor") { this.command = "backcolor"; }
			if (sel != null) {
				var newRng = sel.createRange();
				newRng = this.rng;
				newRng.select();
			}
		} else { oRTE.focus(); }

		//------------------------------------------------------------------------------------------
		//	have the browser make the change
		//------------------------------------------------------------------------------------------
		oRTE.document.execCommand(this.command, false, color);
		oRTE.focus();
		document.getElementById('cp' + this.name).style.visibility = "hidden";
	}

	//----------------------------------------------------------------------------------------------
	//.	insert an hr into the rich text editor (oRTE)
	//----------------------------------------------------------------------------------------------

	this.addHr = function(){
		var oRTE;											//%	iframe's document window [object]
		khta.activeArea = this;

		//------------------------------------------------------------------------------------------
		//	set oRTE to be the iframe's document object
		//------------------------------------------------------------------------------------------
		if (document.all) {	oRTE = frames[this.name]; }
		else { oRTE = document.getElementById(this.name).contentWindow;	}

		//------------------------------------------------------------------------------------------
		//	have the browser insert the image
		//------------------------------------------------------------------------------------------
		oRTE.focus();
		oRTE.document.execCommand('InsertHorizontalRule', false, 0);
		oRTE.focus();
	}

	//----------------------------------------------------------------------------------------------
	//.	insert an image into the rich text editor (oRTE)
	//----------------------------------------------------------------------------------------------
	//arg: imagePath - URL of image [string]
	//TODO: integrate with images module

	this.addImage = function(imagePath) {
		var oRTE;											//%	iframe's document window [object]
		khta.activeArea = this;

		if (!imagePath) { imagePath = prompt('Enter Image URL:', 'http://'); }

		//------------------------------------------------------------------------------------------
		//	set oRTE to be the iframe's document object
		//------------------------------------------------------------------------------------------
		if (document.all) {	oRTE = frames[this.name]; }
		else { oRTE = document.getElementById(this.name).contentWindow;	}

		//------------------------------------------------------------------------------------------
		//	have the browser insert the image
		//------------------------------------------------------------------------------------------
		if ((null != imagePath) && ("" != imagePath)) {
			oRTE.focus();
			oRTE.document.execCommand('InsertImage', false, imagePath);
		}
		oRTE.focus();
	}

	//----------------------------------------------------------------------------------------------
	//.	fire spellcheck extention
	//----------------------------------------------------------------------------------------------
	//TODO: replace this with FireFox equivalent, or remove

	this.checkspell = function() {
		try {
			var tmpis = new ActiveXObject("ieSpell.ieSpellExtension");
			tmpis.CheckAllLinkedDocuments(document);
		}
		catch(exception) {
			if(-2146827859 == exception.number) {
				if (confirm("ieSpell not detected.  Click Ok to go to download page.")) {
					// not wild about this, we should not be encouraging random 3rd party downloads
					window.open('http://www.iespell.com/download.php', 'Download');
				}
			} else {
				//alert("Error Loading ieSpell: Exception " + exception.number);
			}
		}		
	}

	//----------------------------------------------------------------------------------------------
	//.	this fires a browser command when a menu <select> item is selected
	//----------------------------------------------------------------------------------------------
	//TODO: investigate further
	//arg: menu - the chosen menu [?]
	//arg: cmd - the chosen command [?]

	this.select = function(menu, cmd) {
		var oRTE;											//%	iframe's document window [object]
		khta.activeArea = this;

		if (document.all) { oRTE = frames[this.name]; }
		else { oRTE = document.getElementById(this.name).contentWindow; }
		
		var idx = menu.selectedIndex;
		// First one is always a label
		if (idx != 0) {
			var selected = menu.options[idx].value;
			oRTE.document.execCommand(cmd, false, selected);
			menu.selectedIndex = 0;
		}
		oRTE.focus();
	}

	//----------------------------------------------------------------------------------------------
	//.	opens a popup window for inserting a table into the document
	//----------------------------------------------------------------------------------------------
	//TODO: this is hella clunky, integrate with kapenta or remove, preferably remove	

	this.insertTableDialog = function() {
		w = window.open("","tableDialog","width=300,height=150");
		w.area = this;
		d = w.document;
		d.open();
		d.write(getTableDialogAsString());
		d.close();		
	}

	//----------------------------------------------------------------------------------------------
	//.	inserts an empty table into the content iframe
	//----------------------------------------------------------------------------------------------
	//TODO: this is hella clunky, integrate with kapenta or remove, preferably remove	

	this.insertTable = function(rows, cols, spacing, padding, border) {
		var oRTE;											//%	iframe's document window [object]
		if (document.all) { oRTE = frames[this.name]; }						
		else { oRTE = document.getElementById(this.name).contentWindow; }
		doc = oRTE.document;								// why? TODO: fix

		//------------------------------------------------------------------------------------------
		//	check arguments
		//------------------------------------------------------------------------------------------
		rows = rows||3;
		cols = cols||3;
		spacing = spacing||2;
		padding = padding||2;
		if (true == border) { border = 1; }
		border = border||0;
		
		//------------------------------------------------------------------------------------------
		//	make table element and insert
		//------------------------------------------------------------------------------------------
		table = doc.createElement("table");
		table.setAttribute("border", border);
		table.setAttribute("cellpadding", padding);
		table.setAttribute("cellspacing", spacing);
		table.setAttribute("class", "hyperTable");
		
		for (var i=0; i < rows; i++) {
			var tr = doc.createElement("tr");
			for (var j=0; j < cols; j++) {
				var td = doc.createElement("td");
				var content = doc.createTextNode('\u00a0');
				td.appendChild(content);
				tr.appendChild(td);
			}
			table.appendChild(tr);
		}

		this.insertElement(table);			// NB: neat, use this to add images
	}

	//----------------------------------------------------------------------------------------------
	//.	inserts an empty table into the content iframe
	//----------------------------------------------------------------------------------------------
	//TODO: document this
	
	this.insertElement = function(el) {
		var oRTE;											//%	iframe's document window [object]

		if (document.all) {	oRTE = frames[this.name]; }
		else { oRTE = document.getElementById(this.name).contentWindow; }

		doc = oRTE.document;
		if (document.all) {
			selection = doc.selection;
			var html = el.outerHTML;
			var range = selection.createRange();
			try {
				range.pasteHTML(html);        
			} catch (e) {
				// catch error when range is evil for IE
			}

        } else {

        	selection = oRTE.getSelection();
			var range = selection.getRangeAt(0);
			selection.removeAllRanges();
			range.deleteContents();
			var container = range.startContainer||selection.focusNode;
			var pos = range.startOffset;
			afterNode = container.childNodes[pos];
			try {

				if (
					('body' == container.nodeName.toLowerCase()) &&
					(pos < container.childNodes.length) &&
					(container.childNodes[pos + 1])
				) {
					afterNode = container.childNodes[pos+1]
					container.insertBefore(el, afterNode);
				} else {
					container.insertBefore(el, container.afterNode);
				}

			} catch (e) {
				//----------------------------------------------------------------------------------
				// if this is a text node, then break it up into a text node, new element, text node
				//----------------------------------------------------------------------------------
				if(container.nodeName.toLowerCase() == "#text"){
					text0 = container.data.substring(0,range.startOffset);
					text1 = container.data.substring(range.startOffset,container.data.length-1);
					container.data = text0;
					parent = container.parentNode;
					parent.insertBefore(el,container.nextSibling);
					newTextNode = document.createTextNode(text1);
					parent.insertBefore(newTextNode,el.nextSibling);
				} else {
					//alert(el.nodeName.toLowerCase() + " cannot be placed here for the following reason:\n\n" + e);
				}
			}

        } // end if(document.all)

	}
	
	this.init();										// NB **************************************
	if(! this.delayRender) { this.render(this.html); }

}	// end of HyperTextArea

//--------------------------------------------------------------------------------------------------
//	OBJECT representing text format button
//--------------------------------------------------------------------------------------------------

function TextFormatButton(name, label, icon, command, option) {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	this.name = name;
	this.label = label;
	this.command = command;
	this.option = option||"";
	this.area = null;						//_	pointer to HyperTextArea, set by HTA [object]

	this.icon = icon;		//

	//----------------------------------------------------------------------------------------------
	//.	member variables
	//----------------------------------------------------------------------------------------------
	this.getRenderedText = function() {
		text = '<td>'
		 + "<div id='" + this.area.name + '_' + this.name + "'>"
		 + "<img"
		 + " class='btnImage'"
		 + " src='" + khta.resourcePath + 'images/' + this.icon + "'"
		 + " width='25' height='24'"
		 + " alt='" + this.label + "' title='" + this.label + "'"
		 + " onClick=\""
			 + "khta.getArea('" + this.area.name + "')"
			 + ".getControl('" + this.name + "')"
			 + ".execute()\""
		 + "/>"
		 + '</div></td>';		
		return text;
	}

	//----------------------------------------------------------------------------------------------
	//.	calls browser execCommand() or similar
	//----------------------------------------------------------------------------------------------	
	this.execute = function() { this.area.formatText(this.command, this.option); }
}

//--------------------------------------------------------------------------------------------------
//	OBJECT representing toolbar button
//--------------------------------------------------------------------------------------------------

function Button(name, icon, title, methodName) {
	this.name = name;
	this.icon = icon;
	this.title = title;
	this.methodName = methodName;

	//----------------------------------------------------------------------------------------------
	//.	Render as HTML
	//----------------------------------------------------------------------------------------------

	this.getRenderedText = function() {
		var text = '<td>'
		 + "<div id=\"' + name + '\">"
		 + "<img"
		 + " class='btnImage'"
		 + " src='" + khta.resourcePath + 'images/' + icon + "'"
		 + " width='25' height='24'"
		 + " alt='" + title + "' title='" + title + "'"
		 + " onClick=\""
			 + "khta.getArea('" + this.area.name + "')." + this.methodName + "();"
		 + "\" />"
		 + '</div></td>';
		return text;
	}
}

//--------------------------------------------------------------------------------------------------
//	OBJECT representing toolbar spacer
//--------------------------------------------------------------------------------------------------

function Spacer(name){
	this.name = name;
	this.getRenderedText = function() { return '<td>&nbsp;</td>'; }
}

//--------------------------------------------------------------------------------------------------
//	OBJECT represnting toolbars
//--------------------------------------------------------------------------------------------------

function Toolbar(name, isFirstToolbar){
	this.name = name
	this.isFirstToolbar = isFirstToolbar||false;
	this.getRenderedText = function(){
		this.area.toolbarNames[this.area.toolbarNames.length] = this.name;
		text = '<table id="' + this.name + '_' + this.area.name + '" cellpadding="1" cellspacing="0"><tr>'
		if(this.isFirstToolbar){
			text = '</tr></table>\n' + text;
		} 
		return text; 
	}
}

//--------------------------------------------------------------------------------------------------
//	OBJECT representing menus, implemented as a select list
//--------------------------------------------------------------------------------------------------

function Menu(name, cmd){
	this.name = name;
	this.cmd = cmd;
	this.area = null;
	this.items = new Array();

	//----------------------------------------------------------------------------------------------
	//.	add a new item to the menu
	//----------------------------------------------------------------------------------------------
	//arg: value - select option value [string]
	//arg: label - select option label [string]
	
	this.addItem = function(value, label) {
		this.items[this.items.length] = new MenuItem(value, label);
	}

	//----------------------------------------------------------------------------------------------
	//.	add pairs of items to the menu
	//----------------------------------------------------------------------------------------------
	//arg: pairs - 2*n set of key => value pairs [array] 

	this.addItems = function(pairs) {
		for (var i in pairs) { this.addItem(pairs[i][0], pairs[i][1]); }
	}

	//----------------------------------------------------------------------------------------------
	//.	render the menu as HTML
	//----------------------------------------------------------------------------------------------
	//returns: html select element [string]

	this.getRenderedText = function() {
		var areaname = this.area.name;
		var opts = '';
		for (var i in this.items) { opts = opts + this.items[i].toHtml(); }

		var text = '<td>'
		 + "<select"
		 + " name='" + this.name + "'"
		 + " id='"+ this.name + "_" + this.area.name + "'"
		 + " onchange=\""
			 + "khta.getArea('" + areaname + "').select(this, '" + this.cmd + "');"
		 + "\">\n"
		 + opts
		 + "</select></td>";

		return text;
	}
}

//--------------------------------------------------------------------------------------------------
//	OBJECT representing menu items
//--------------------------------------------------------------------------------------------------

function MenuItem(value, label) {
	this.value = value;
	this.label = label;
	this.toHtml = function() { 
		return "<option value='" + this.value + "'>" + this.label + "</option>\n";
	}
}

//--------------------------------------------------------------------------------------------------
//	OBJECT collection of all Hypertext Areas
//--------------------------------------------------------------------------------------------------

function KHyperTextAreas() {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	this.areas = new Array();								//_	TODO: investigate [array]
	this.forms = new Array();								//_	TODO: investigate [array]
	this.resourcePath = jsServerPath + 'modules/editor/';	//_	reource images, etc [string]
	this.activeArea = null;									//_	TODO: investigate [object]
	this.log = '';											//_	debug log [string]
	this.logEnabled = true;									//_	for now [bool]

	//----------------------------------------------------------------------------------------------
	//.	create a new HyperTextArea
	//----------------------------------------------------------------------------------------------
	//arg: name - name of HTA and HTML form field [string]
	//arg: html - content to preload [string]
	//arg: width - width of editor [int]
	//arg: height - height of editor [int]
	//arg: divId - name of div into which editor will be rendered [string]
	//returns: new HyperTextArea object [object]

	this.create = function(name, html, width, height, divId) {
		var delayRender = false;
		newArea = new HyperTextArea(name, html, width, height, delayRender, divId);
		this.areas[this.areas.length] = newArea;
		return newArea;
	}

	//----------------------------------------------------------------------------------------------
	//.	create a new HyperTextArea and set its content to that of an existing div
	//----------------------------------------------------------------------------------------------
	//arg: name - name of HTA and HTML form field [string]
	//arg: contentDivId - id of a (hidden) div containg HTML to be edited [string]
	//arg: width - width of editor [int]
	//arg: height - heightof editor [int]
	//arg: editorDivId - id of div into which editor will be rendered [string]
	//returns: new HyperTextArea object [object]

	this.createFromDiv = function(name, contentDivId, width, height, editorDivId) {
		var delayRender = false;
		var html = '';
		var contentDiv = document.getElementById(divId);
		if (contentDiv) { html = contentDiv.innerHTML; }

		newArea = new HyperTextArea(name, html, width, height, delayRender, divId);
		this.areas[this.areas.length] = newArea;
		return newArea;
	}

	//----------------------------------------------------------------------------------------------
	//.	create a new HyperTextArea and set its content to that of an existing div, base64_encoded
	//----------------------------------------------------------------------------------------------
	//;	this is used to prevent broken HTML from screwing up the load process
	//arg: name - name of HTA and HTML form field [string]
	//arg: contentDivId - id of a (hidden) div [string]
	//arg: width - width of editor [int]
	//arg: height - height of editor [int]
	//arg: editorDivId - height of editor [int]
	//returns: new HyperTextArea object [object]

	this.createFromDiv = function(name, contentDivId, width, height, editorDivId) {
		var delayRender = false;
		var html = '';
		var contentDiv = document.getElementById(divId);
		if (!kutils) { 
			html = 'WARNING: Kapenta Utilities not loaded, cannot base64_decode.';
			alert(html);
		} else {
			if (contentDiv) { html = kutils.base64_decode(contentDiv.innerHTML); }
			else { html = 'WARNING: Content div not found: ' + divId; }
		}
		newArea = new HyperTextArea(name, html, width, height, delayRender, divId);
		this.areas[this.areas.length] = newArea;
		return newArea;
	}

	//----------------------------------------------------------------------------------------------
	//.	get a HTA given it's name
	//----------------------------------------------------------------------------------------------
	//arg: name - name of HyperTextArea [string]

	this.getArea = function(name) { 
		for (var i in this.areas) {	
			if (name == this.areas[i].name) { return this.areas[i]; } 
		}
	}

	//--------------------------------------------------------------------------------------------------
	//. iterate over all areas and call update (sets hidden form field?)
	//--------------------------------------------------------------------------------------------------
	this.updateAllAreas = function () {
		for (i in this.areas) { area = this.areas[i].update(); }
	}

	//----------------------------------------------------------------------------------------------
	//.	try turn on browser design mode in the HyperTextArea's iframe
	//----------------------------------------------------------------------------------------------

	this.enableDesignMode = function(areaName) {
		try {
			if (document.all) { frames[areaName].document.designMode = "On"; }
			else {
				contentDocument = document.getElementById(areaName).contentDocument;
				contentDocument.designMode = "on";
			}	
			self.status = "";
			area.setContent(area.html)

		} catch(e) {
			//--------------------------------------------------------------------------------------
			//	attempt to recover from any exceptions
			//--------------------------------------------------------------------------------------
			exp = "khta.enableDesignMode('" + areaName + "')";
			area = this.getArea(areaName);
			area.designModeRetryCount++
			self.status = "Error in setting designMode property on attempt number "
				+ area.designModeRetryCount + ".  Retrying.";

			if (area.designModeRetryCount <= 10) { t = setTimeout(exp,100); }
			else {
				self.status = areaName + " failed to initialize properly";
				throw e;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	show 'about.html' in first area
	//----------------------------------------------------------------------------------------------
	this.about = function(){
		window.open(this.resourcePath + "about.html");
	}

	//----------------------------------------------------------------------------------------------
	//.	convert divs of class 'HyperTextArea' into WYSWYG editors
	//----------------------------------------------------------------------------------------------
	//note: field name will be div ID
	
	this.convertDivs = function() {
		var allDivs = document.getElementsByTagName('DIV');		// get all divs
		for (i = 0; i < allDivs.length; i++) {
			var currDiv = allDivs[i];
			
			//--------------------------------------------------------------------------------------
			//	if editor class
			//--------------------------------------------------------------------------------------
			var className = currDiv.getAttribute('class');
			if (className) {
				className = className.toLowerCase();
				if (('hypertextarea' == className) || ('hypertextarea64' == className)) {
					//------------------------------------------------------------------------------	
					//	get all properties of new HTA
					//------------------------------------------------------------------------------
					var html = currDiv.innerHTML;
					var title = currDiv.getAttribute('title');
					var width = currDiv.getAttribute('width');
					var height = currDiv.getAttribute('height');
					var divId = currDiv.getAttribute('id');	

					if (!title) { title = 'content'; }
					if (!divId) { divId = 'divHta'; }
					if (!width) { width = 500; }
					if (!height) { width = 400; }

					if ('hypertextarea64' == className ) { 
						html = base64.decode(html, 1, false, true); 
					}

					msg = ''
					 + "<b>Creating HyperTextArea:</b><br/>\n"
					 + "divID: " + divId + "<br/>\n"
					 + "Title: " + title + "<br/>\n"
					 + "Width: " + width + "<br/>\n"
					 + "Height: " + height + "<br/>\n"
					 + "Content: " + html + "<br/>\n"
					;

					//alert(msg);
					currDiv.innerHTML = '';

					//------------------------------------------------------------------------------	
					//	create and render the editor
					//------------------------------------------------------------------------------
					var temp = this.create(title, html, width, height, divId);

					//------------------------------------------------------------------------------	
					//	prevent re-rendering
					//------------------------------------------------------------------------------
					currDiv.setAttribute('class', 'HyperTextAreaLoaded');
					currDiv.style.visibility = 'visible'; 
					currDiv.style.display = 'block'; 
				}
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	log a message to the debug console
	//----------------------------------------------------------------------------------------------

	this.log = function(msg) {
		var logDiv = document.getElementById('divLog');
		if (logDiv) { logDiv.innerHTML = logDiv.innerHTML + msg + "<br>\n";	}
	}

	//----------------------------------------------------------------------------------------------
	//.	get content of an HTA
	//----------------------------------------------------------------------------------------------
	//arg: areaName - name of a HyperTextArea [string]

	this.getContent = function(areaName) {
		var area = this.getArea(areaName);
		return area.getContent();
	}

	//----------------------------------------------------------------------------------------------
	//.	set content of an HTA
	//----------------------------------------------------------------------------------------------
	//arg: areaName - name of a HyperTextArea [string]
	//arg: html - content to set [string]

	this.setContent = function(areaName, html) {
		var area = this.getArea(areaName);
		return area.setContent(html);		
	}

}

//==================================================================================================
//	TODO: decipher this and add to HyperTextArea object
//==================================================================================================

function getOffsetTop(elm) {
	var mOffsetTop = elm.offsetTop;
	var mOffsetParent = elm.offsetParent;
	
	while(mOffsetParent){
		mOffsetTop += mOffsetParent.offsetTop;
		mOffsetParent = mOffsetParent.offsetParent;
	}
	
	return mOffsetTop;
}

function getOffsetLeft(elm) {
	var mOffsetLeft = elm.offsetLeft;
	var mOffsetParent = elm.offsetParent;
	
	while(mOffsetParent) {
		mOffsetLeft += mOffsetParent.offsetLeft;
		mOffsetParent = mOffsetParent.offsetParent;
	}
	
	return mOffsetLeft;
}

function kb_handler(evt) {
	//contributed by Anti Veeranna (thanks Anti!)

	if (evt.ctrlKey) {
		var key = String.fromCharCode(evt.charCode).toLowerCase();
		var cmd = '';
		switch (key) {
			case 'b': cmd = "bold"; break;
			case 'i': cmd = "italic"; break;
			case 'u': cmd = "underline"; break;
		};

		if (cmd) {
			evt.target.ownerDocument.execCommand(cmd,false,true);
			// stop the event bubble
			evt.preventDefault();
			evt.stopPropagation();
		}
 	}
}

//--------------------------------------------------------------------------------------------------
//	on node insert check to see that images have been added correctly
//	kapenta only! - remove if you're not using this to manage block insertions
//--------------------------------------------------------------------------------------------------

function nodeinsert_handler(evt) {
	var oRTE = getEditorDocument(evt.target);

	if ('IMG' == evt.target.tagName) {		
		replaceImageButtons(oRTE);

	} else {
		//	remove any images which were not added by dragging a button
		//	NOTE: this functionality is disabled while moving to server-side HTML parser
		//removeSimpleImages(oRTE);
	}
}

//--------------------------------------------------------------------------------------------------
//|	removes HTML tags from a string
//--------------------------------------------------------------------------------------------------
//arg: oldString - string to remove tags from [string]
//returns: string with HTML tags removed [string]
//TODO: move this to kapenta utils.js object

function stripHTML(oldString) {
	var newString = oldString.replace(/(<([^>]+)>)/ig,"");
	
	//replace carriage returns and line feeds
	newString = escape(newString)
	newString = newString.replace("%0D%0A"," ");
	newString = newString.replace("%0A"," ");
	newString = newString.replace("%0D"," ");
	newString = unescape(newString)
	
	//trim string
	newString = trim(newString);
	
	return newString;
}

//--------------------------------------------------------------------------------------------------
//|	removes leading, trailing and repeated spaces from a string
//--------------------------------------------------------------------------------------------------
//arg: inputString - string to be trimmed [string]
//returns: cleaned string [string]
//TODO: make type safe, return null string on error
//TODO: convert other forms of whitespace: tabs, cr, lf, etc

function trim(inputString) {
   // Removes leading and trailing spaces from the passed string. Also removes
   // consecutive spaces and replaces it with one space. If something besides
   // a string is passed in (null, custom object, etc.) then return the input.
   if (typeof inputString != "string") { return inputString; }
   var retValue = inputString;
   var ch = retValue.substring(0, 1);
   while (ch == " ") { // Check for spaces at the beginning of the string
      retValue = retValue.substring(1, retValue.length);
      ch = retValue.substring(0, 1);
   }
   ch = retValue.substring(retValue.length-1, retValue.length);
   while (ch == " ") { // Check for spaces at the end of the string
      retValue = retValue.substring(0, retValue.length-1);
      ch = retValue.substring(retValue.length-1, retValue.length);
   }
   while (retValue.indexOf("  ") != -1) { 
	  // Note that there are two spaces in the string - look for multiple spaces within the string
      retValue = retValue.substring(0, retValue.indexOf("  ")) + retValue.substring(retValue.indexOf("  ")+1, retValue.length); // Again, there are two spaces in each of the strings
   }
   return retValue; // Return the trimmed string back to the user
}

//--------------------------------------------------------------------------------------------------
//|	draw color palette as HTML table
//--------------------------------------------------------------------------------------------------
//returns: HTML table [string]
//TODO: tidy code

function getPaletteAsString(){
	var hexArray = new Array("00","55","AA","FF");
	var out = "";
	var out2 = "";
	var line = ""
	var row = 1;
	var count = 1;

	for(var i = (hexArray.length - 1); i >= 0; i--) {
		var val0 = hexArray[i];

		for (j = (hexArray.length - 1); j >= 0; j--) {
			var val1 = hexArray[j];

			for (k = (hexArray.length - 1); k >= 0; k--) {
				var val2 = hexArray[k];
				var hexVal = val0 + val1 + val2;

				var onMouseOverJs = "this.style.border='1px dotted white'"
				var onMouseOutJs = "this.style.border='1px solid gray'";
				var onClickJs = "area.setColor(this.id)";

				line = line + "\n "
				 + "<td"
				 + " id='#" + hexVal + "'"
				 + " bgcolor='#" + hexVal + "'"
				 + " width='15' height='15'"
				 + " onmouseover=\"" + onMouseOverJs + "\""
				 + " onmouseout=\"" + onMouseOutJs + "\""
				 + " onclick=\"" + onClickJs + "\""
				 + "><img width='1' height='1'></td>";

				if (1 == count || ((row % 2) == 0)) {
					if (0 == ((count - 1) % 8)) {
						out = out + "\n<tr>";
						row++
					}
					out = out + line;
					if (0 == (count % 8)) { out = out + "\n</tr>"; }

				} else {
					if(((count - 1) % 8) == 0){
						out2 = out2 + "\n<tr>";
						row++
					}
					out2 = out2 + line;
					if(0 == (count % 8)) { out2 = out2 + "\n</tr>"; }
				}

				line = "";
				count++;
			}
		}
	}

	out = ''
	 + "<table cellpadding='0' cellspacing='1' border='0' align='center' width='200'>"
	 + out
	 + out2
	 + "</table>";
	return out;
}

//--------------------------------------------------------------------------------------------------
//	returns the content of a popup window for inserting tables
//--------------------------------------------------------------------------------------------------
//ugly and seriously unpleasant to use  TODO: improve this

function getTableDialogAsString(){
	out = '<form name="tableDialog">'
	 + '<table width="100%" cellpadding="2" cellspacing="2" border="0">'
	 + '  <tr>'
	 + '    <td>Rows: <input type="text" name="rows" size="2" value="3"/></td>'
	 + '    <td>Columns: <input type="text" name="cols" size="2" value="3"/></td>'
	 + '  </tr>'
	 + '  <tr>'
	 + '    <td>Spacing: <input type="text" name="spacing" size="2" value="2"/></td>'
	 + '    <td>Padding: <input type="text" name="padding" size="2" value="2"/></td>'
	 + '  </tr>'
	 + '  <tr>'
	 + '    <td colspan="2">Border: <input type="checkbox" name="border" value="1"/></td>'
	 + '  </tr>'
	 + '  <tr>'
	 + '    <td colspan="2">'
	 + '      <div align="center">'
	 + '        <input type="button" name="cancel" value="Cancel" onclick="self.close()"/>'
	 + '        <input type="button" name="button" value="Insert Table"'
 	     + ' onclick="'
		 + 'window.opener.khta.activeArea'
		 + '.insertTable(' 
			 + 'this.form.rows.value, '
			 + 'this.form.cols.value, '
			 + 'this.form.spacing.value, '
			 + 'this.form.padding.value, '
			 + 'this.form.border.checked'
		 + ');'
		 + 'self.close()'
	 + '"/></div></td>'
	 + '</tr>'
	 + '</table>'
	 + '</form>';

	return out;
}


//--------------------------------------------------------------------------------------------------
//	
//--------------------------------------------------------------------------------------------------
//:	this is set by HyperTextArea._renderRTE via a timeout

function onHyperTextAreaLoad(areaName) {
	khta.log('attempting to set designMode property');
	area = khta.getArea(areaName);									// get HyperTextArea given name
	area.cpWindow = frames['cp' + areaName].window;					// iframe window
	
	//allows the area to determine what form it belongs to
	area.form = document.getElementById("hdn" + areaName).form;		// hdn = hidden?
	
	//attaches to the forms submit handler only once
	formAlreadyStored = false;
	for(i = 0; i < khta.forms.length; i++){
		if (area.form == khta.forms[i]){
			formAlreadyStored = true;
			break;
		}
	}
	if (!formAlreadyStored) {
		onSubmitFunc = area.form.onsubmit;
		area.form.onsubmit = function(){
			khta.updateAllAreas();
			if (onSubmitFunc) { onSubmitFunc(); }
		}
		khta.forms[khta.forms.length] = area.form;
	}
	area.setContent(area.html)

	if (document.all) {						// checking for IE, or if page is fully loaded?
		cp = frames['cp' + areaName];
		cp.document.write(getPaletteAsString());

	} else {
		cp = document.getElementById('cp' + areaName);
		d = cp.contentDocument;
		d.open();
		d.write(getPaletteAsString());
		d.close()
	}

	khta.enableDesignMode(areaName);
	area.setContent(area.html)
}


//==================================================================================================
//	BEGIN KAPENTA SPECIFIC FUNCTIONS
//==================================================================================================

//-------------------------------------------------------------------------------------------------
//	get editor source, given a node from the editor
//	kapenta only! - remove if you're not using this to manage block insertions
//-------------------------------------------------------------------------------------------------

function getEditorDocument(someNode) {
	if (9 == someNode.nodeType) {
		return someNode;
	} else {
		return getEditorDocument(someNode.parentNode);
	}
}

//-------------------------------------------------------------------------------------------------
//	replace buttons with the images they represent
//	kapenta only! - remove if you're not using this to manage block insertions
//-------------------------------------------------------------------------------------------------

function replaceImageButtons(oRTE) {
	var imgs = oRTE.getElementsByTagName('IMG');	// get all images in this rich text editor

	for (i = 0; i < imgs.length; i++) {				// look for instructions in button alt
		var img = imgs[i];
		if ((img.alt != undefined) && (img.alt != null) && (img.alt != '')) { 
			// get plugin details from alt
			var args = img.alt.split('|');
			var plugin = args[0];

			//-------------------------------------------------------------------------------------
			//	replace button with image
			//-------------------------------------------------------------------------------------
			if (plugin == 'images') {
				var size = '';
				var raUID = '';

				// get image details
				for (j = 1; j < args.length; j++) {
					if (args[j].indexOf('=') > 0) {
						parts = args[j].split('=');
						switch(parts[0]) {
							case 'raUID': 	raUID = parts[1];	break;
							case 'size': 	size = parts[1];	break;
						}
					}
				}

				// set image src and alt
				var newSrc = jsServerPath + 'images/' + size + '/' + raUID;
				img.src = newSrc;
				img.alt = 'imageexpanded|raUID=' + raUID + '|size=' + size + '|';

			}
		}
	}	
}

//-------------------------------------------------------------------------------------------------
//	remove images added incorrectly (they won't have an alt beginning with 'imageexpanded')
//	kapenta only! - remove if you're not using this to manage block insertions
//-------------------------------------------------------------------------------------------------

function removeSimpleImages(oRTE) {
	var imgs = oRTE.getElementsByTagName('IMG');
	var srcStr = '';
	for (var i in imgs) {
		var img = imgs[i];
		if (img != undefined) {
			if ((img.alt == undefined) || (img.alt == null) || (img.alt == '')) {
				// no alt
				if (undefined != img.parentNode) { img.parentNode.removeChild(img);	}
			} else {
				// alt must begin imageexpanded or some other plugin name
				var rmThis = true;
				if ((img.alt.length > 13) && (img.alt.substring(0, 14) == 'imageexpanded|')) { rmThis = false; }
				if (true == rmThis) {
					if (undefined != img.parentNode) { img.parentNode.removeChild(img);	}
				}
			}
		} // end if img undefined
	} // end for
}

//-------------------------------------------------------------------------------------------------
//	replace images with blocks before saving
//	kapenta only! - remove if you're not using this to manage block insertions
//-------------------------------------------------------------------------------------------------

function replaceImagesWithBlocks(oRTE) {
	var imgs = oRTE.getElementsByTagName('IMG');	// get all images in this rich text editor
	for (var i in imgs) {							// look for instructions in button alt
		img = imgs[i];
		if ((img != undefined) && (img.alt != undefined) && (img.alt != null) && (img.alt != '')) { 
			// get plugin details from alt
			var args = img.alt.split('|');
			var plugin = args[0];

			//-------------------------------------------------------------------------------------
			//	replace image with block tag
			//-------------------------------------------------------------------------------------
			if (plugin == 'imageexpanded') {
				var size = '';
				var raUID = '';

				// get image details
				for (j = 1; j < args.length; j++) {
					if (args[j].indexOf('=') > 0) {
						parts = args[j].split('=');
						switch(parts[0]) {
							case 'raUID': 	raUID = parts[1];	break;
							case 'size': 	size = parts[1];	break;
						}
					}
				}

				// replace DOM image node with text node
				if (size == 'widtheditor') { size = 'width570'; }
				var block = '[[:images::' + size + '::raUID=' + raUID + ':]]';
				var txtNode = oRTE.createTextNode(block);
				img.parentNode.replaceChild(txtNode, img);

			} 
		}
	}	
}

//-------------------------------------------------------------------------------------------------
//	find all blocks in a piece of text/html
//-------------------------------------------------------------------------------------------------
//TODO: remove this, parse on server side [string]

function editorProcessBlocks(html) {
	return html;
	var blocks = editorFindBlocks(html);
	for (var i in blocks) {
		var block = blocks[i];
		cblock = block.replace(/\[\[:/g, '');	// remove wrapper
		cblock = cblock.replace(/:\]\]/g, '');

		var args = cblock.split('::');

		switch(args[0]) {
			case 'images':
				if ( (args[1] == 'slideshow') || 
					 (args[1] == 'swapbutton') ||
					 (args[1] == 'thumb') ||  
					 (args[1] == 'default') ) {
					// leave it be (block as editable text)
				} else {
					// replace with actual image tag
					var imgtag = editorMkImageFromBlock(args);
					html = editorReplaceAll(html, block, imgtag);
					break;
				}
		}
	}

	return html;
}

//-------------------------------------------------------------------------------------------------
//	ugly, but better than screwing with RegEx
//-------------------------------------------------------------------------------------------------
//TODO: move to HyperTextArea

function editorReplaceAll(html, find, repl) {
	if (find.length < 3) { return html; }
	while (html.indexOf(find) >= 0) {
		var startPos = html.indexOf(find);
		var strBefore = html.substring(0, startPos);
		var strAfter = html.substring((startPos + find.length), html.length);
		html = strBefore + repl + strAfter;
	}
	return html;	
}

//-------------------------------------------------------------------------------------------------
//	make an html image tag from an image block tag
//-------------------------------------------------------------------------------------------------
//TODO: remove, this can be done server-side

function editorMkImageFromBlock(args) {
	var imgtag = '';
	var size = args[1];
	var raUID = '';

	//---------------------------------------------------------------------------------------------
	//	get any arguments (at present only UID) TODO: add arguments for caption, etc
	//---------------------------------------------------------------------------------------------
	for(var i in args) {
		var arg = args[i];
		if (arg.indexOf('=') > 0) {
			var parts = arg.split('=');
			switch(parts[0]) {
				case 'raUID':		raUID = parts[1]; 	break;
				case 'imageUID':	raUID = parts[1]; 	break;
			}
		}
	}

	//---------------------------------------------------------------------------------------------
	//	assemble into HTML, alt property allows image to be converted back to block tag
	//---------------------------------------------------------------------------------------------
	if ('width570' == size) { size = 'widtheditor'; }
	var src = jsServerPath + 'images/' + size + '/' + raUID;
	var alt = 'imageexpanded|size=' + size + '|raUID=' + raUID + '|';
	imgtag = "<img class='im" + size + "' src='" + src + "' border='0' alt='" + alt + "' blockTag='[[:blocktag:]]' />";
	return imgtag;
}

//-------------------------------------------------------------------------------------------------
//	find all blocks in a piece of text/html
//-------------------------------------------------------------------------------------------------
//TODO: remove

function editorFindBlocks(html) {
	var blocks = new Array();

	html = html.replace(/\r/g, '');				// strip newlines
	html = html.replace(/\n/g, '');
	html = html.replace(/\[\[:/g, "\n[[:");		// place blocks on their own line
	html = html.replace(/:\]\]/g, ":]]\n");

	var lines = html.split("\n");				// for each line which might be a block
	var idx = 0;

	for (i = 0; i < lines.length; i++) {
		line = lines[i];						
		if (line.length > 8) {
			//--------------------------------------------------------------------------------------
			//	if this line begins with [[:: and ends with ::]]
			//--------------------------------------------------------------------------------------
			if ((line.substring(0, 3) == '[[:') && (line.substring((line.length - 3), line.length) == ':]]')) {
				blocks[idx] = line;
				idx++;
			}

	  }
	}
	
	return blocks;
}

//==================================================================================================
// end of kapenta specific functions
//==================================================================================================




//==================================================================================================
//	EXPERIMENTING WITH DIFFERENT BASE64 LIBRARY
//==================================================================================================
//
///
// This file implements base64 encoding and decoding.
// Encoding is done by the function base64Encode(), decoding
// by base64Decode(). The naming mimics closely the corresponding
// library functions found in PHP. However, this implementation allows
// for a more flexible use.
//
// This implementation follows RFC 3548 (http://www.faqs.org/rfcs/rfc3548.html),
// so the copyright formulated therein applies.
//
// Dr.Heller Information Management, 2005 (http://www.hellerim.de).
//
///



var base64 = function(){};

// provide for class information
base64.classID = function() {
  return 'system.utility.base64';
};

//disallow subclassing
base64.isFinal = function() {
  return true;
};

// original base64 encoding
base64.encString = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
// URL and file name safe encoding
base64.encStringS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

/// BEGIN_DOC(base64).METHOD(encode)
///
// method String base64.encode(INPUTTYPE inp [, bool uc [, bool safe]])
//
// Encode input data into a base64 character string.
//
// Function arguments:
//     INPUTTYPE inp:        data to be encoded. INPUTTYPE may be String or Array.
//                           Any other INPUTTYPE results in an output value of null.
//                           If INPUTTYPE is String each character is converted into 
//                           two bytes each of which is encoded separately.
//     bool uc               Optional. If this parameter has a value of 'true' which is
//                           the default, code of each character is treated as a 16-bit
//                           entity (UniCode), i.e. as two bytes. Otherwise, the codes
//                           are truncated to one byte (8-bit character set) which
//                           may result in information loss. If INPUTTYPE is Array,
//                           the value of this parameter has no effect.
//     bool safe:            Optioanal. If this parameter is set to true, the standard base64 
//                           character set is replaced with a modified version where
//                           the characters '+' and '/' are replace with '-' and '_',
//                           repectively, in order to avoid problems with file system
//                           namings which otherwise could occur on some systems.
//                           By default, the value of this argument is assumed to be
//                           false.
// Return value:             The function returns a character string consisting of
//                           the base64 representaion of the input. Its length is a
//                           multiple of 4. If the encoding yields less than this
//                           the string is stuffed with the '=' character. In each case, 
//                           the string maybe empty but not null if no error occurred.
// Errors:                   Whenever an error occurs, null is returned. Parameter values
//                           not defined above are considered errors.
// Remarks:                  If the input array contains something different from
//                           a byte at some position the first 8 bits only of this entity are
//                           processed silently without returning an error, which probably
//                           results in garbage converted to base64.
//
/// END_DOC
base64.encode = function(inp, uc, safe) {
  // do some argument checking
  if (arguments.length < 1) return null;
  var readBuf = new Array();    // read buffer
  if (arguments.length >= 3 && safe != true && safe != false) return null;
  var enc = (arguments.length >= 3 && safe) ? this.encStringS : this.encString; // character set used
  var b = (typeof inp == 'string'); // how input is to be processed
  if (!b && (typeof inp != 'object') && !(inp instanceof Array)) return null; // bad input
  if (arguments.length < 2) {
    uc = true;                  // set default
  } // otherwise its value is passed from the caller
  if (uc != true && uc != false) return null;
  var n = (!b || !uc) ? 1 : 2;  // length of read buffer
  var out = '';                 // output string
  var c = 0;                    // holds character code (maybe 16 bit or 8 bit)
  var j = 1;                    // sextett counter
  var l = 0;                    // work buffer
  var s = 0;                    // holds sextett
  
  // convert  
  for (var i = 0; i < inp.length; i++) {  // read input
    c = (b) ? inp.charCodeAt(i) : inp[i]; // fill read buffer
    for (var k = n - 1; k >= 0; k--) {
      readBuf[k] = c & 0xff;
      c >>= 8;
    }
    for (var m = 0; m < n; m++) {         // run through read buffer
      // process bytes from read buffer
      l = ((l<<8)&0xff00) | readBuf[m];   // shift remaining bits one byte to the left and append next byte
      s = (0x3f<<(2*j)) & l;              // extract sextett from buffer
      l -=s;                              // remove those bits from buffer;
      out += enc.charAt(s>>(2*j));        // convert leftmost sextett and append it to output
      j++;
      if (j==4) {                         // another sextett is complete
        out += enc.charAt(l&0x3f);        // convert and append it
        j = 1;
      }
    }        
  }
  switch (j) {                            // handle left-over sextetts
    case 2:
      s = 0x3f & (16 * l);                // extract sextett from buffer
      out += enc.charAt(s);               // convert leftmost sextett and append it to output
      out += '==';                        // stuff
      break;
    case 3:
      s = 0x3f & (4 * l);                 // extract sextett from buffer
      out += enc.charAt(s);               // convert leftmost sextett and append it to output
      out += '=';                         // stuff
      break;
    default:
      break;
  }

  return out;
  
}

/// BEGIN_DOC(base64).METHOD(decode)
///
// method RETURNTYPE base64.decode(String inp [, enum outType [, bool safe [, bool lax]]])
//
// Encode input data into a base64 character string.
//
// Function arguments:
//     String inp:           base64 encoded data string to be decoded.
//     enum outType          Optional. This parameter specifies the type of the output and determines
//                           how the input data is to be interpreted.:
//                             0  - binary data; create a byte array (default)
//                             1  - 8-bit character string, assuming 1-byte characters encoded in inp
//                             2  - 16-bit (UniCode) character string, assuming 2-byte 
//                                  characters encoded in inp
//                           If 2 is passed to the function, but the number of base64 characters
//                           is odd, a value of null is returned.
//     bool safe             Optional. If this parameter is set to true, the standard base64 
//                           character set is replaced with a modified version where
//                           the characters '+' and '/' are replaced with '-' and '_',
//                           repectively, in order to avoid problems with file system
//                           namings which otherwise could occur on some systems.
//                           By default, the value of this argument is assumed to be
//                           false.
//     bool lax              Optional. If set to true, the function skips all input characters which
//                           cannot be processed. This includes the character '=', too, if
//                           it is followed by at least one different character before the string
//                           ends. However, if skipping infeasible characters amounts to a number
//                           of allowed base64 characters which is not amultiple of 4,
//                           this is considered an error and null is returned.
//                           If lax is set to false (the default), null is returned
//                           whenever an infeasible character is found.
//                           The purpose of this parameter is to give support in cases
//                           where data has been base64 encoded and later on was folded by
//                           some other software, e.g. CRLFs have been inserted in email.
//                           exchange.
// Return value:             The function's processing result value is stored in a string or in
//                           a byte array before it is returned, depending on the value 
//                           assigned to the type parameter. In each case, the value
//                           maybe empty but not null if no error occurred.
// Errors:                   Whenever an error occurs, null is returned. Parameter values
//                           not defined above are considered errors.
//
/// END_DOC

base64.decode = function(inp, outType, safe, lax) {

  // do some argument checking
  if (arguments.length < 1) return null;
  if (arguments.length < 2) outType = 0 ;// produce character array by default
  if (outType != 0 && outType != 1 && outType != 2) return null;
  if (arguments.length >= 3 && safe != true && safe != false) return null;
  var sEnc = (arguments.length >= 3 && safe) ? this.encStringS : this.encString;  // select encoding character set
  if (arguments.length >= 4 && lax != true && lax != false) return null;
  var aDec = new Object();                // create an associative array for decoding
  for (var p = 0; p < sEnc.length; p++) { // populate array
    aDec[sEnc.charAt(p)] = p;
  }
  var out = (outType == 0) ? new Array() : '';
  lax = (arguments.length == 4 && lax); // ignore non-base64 characters
  var l = 0;               // work area
  var i = 0;               // index into input
  var j = 0;               // sextett counter
  var c = 0;               // input buffer
  var k = 0;               // index into work area
  var end = inp.length;    // one position past the last character to be processed
  var C = '';
  // check input
  if (lax) {
    var inpS = '';         // shadow input
    var ignore = false;    // determines wether '=' must be counted
    var cnt = 0;
    for (var p = 1; p <= inp.length; p++) {    // check and cleanup string before trying to decode
      c = inp.charAt(end - p);
      if (c == '=') {
        if (!ignore) {
          if (++cnt > 1) ignore = true;
        } else {
          continue;
        }
      } else if (undefined != aDec[c]) { // the character is base64, hence feasible
        if (!ignore) ignore = true;      // no more '=' allowed
        inpS = c + inpS;                 // prepend c to shadow input
      }
    }
    for (var p = 0; p <= cnt; p++) {     // at most cnt '=''s were garbage, a number in 
      if (p == 2) return null;           // [inpS.length, inpS.length + cnt] must be a
      if ((inpS.length + cnt)%4 == 0) break;  // multiple of 4
    }
    if (inpS.length%4==1) return null;   // must be 0, 2, or 3 for inpS to contain correctly base64 encoded data
    inp = inpS;                          // inp now contains feasible characters only
    end = inp.length;
  } else {
    if (inp.length%4 > 0) return null;   // invalid length
    for (var p = 0; p < 2; p++) {        // search for trailing '=''s
      if (inp.charAt(end - 1) == '=') {
        end--;
      } else {
        break;
      }
    }
  }
  // convert
  for (i = 0; i < end; i++) {
    l <<= 6;                             // clear space for next sextett
    if (undefined == (c = aDec[inp.charAt(i)])) return null; // lax must be false at this place!
    l |= (c&0x3f);    // append it
    if (j == 0) {
      j++;    
      continue;                          // work area contains incomplete byte only
    }
    if (outType == 2) {
      if (k == 1) {                      // work area contains complete double byte
        out += String.fromCharCode(l>>(2*(3-j)));  // convert leftmost 16 bits and append them to string
        l &= ~(0xffff<<(2*(3-j)));       // clear the 16 processed bits
      }
      k = ++k%2;
    } else {                             // work area contains complete byte
      if (outType == 0) {
        out.push(l>>(2*(3-j)));          // append byte to array
      } else {
        out += String.fromCharCode(l>>(2*(3-j))); // convert leftmost 8 bits and append them to String
      }
      l &= ~(0xff<<(2*(3-j)));           // clear the 8 processed bits
    }
    j = ++j%4;                           // increment sextett counter cyclically
  }
  if (outType == 2 && k == 1) return null;  // incomplete double byte in work area

  return out;
}

//--------------------------------------------------------------------------------------------------
//	Hypertext areas init
//--------------------------------------------------------------------------------------------------

	khta = new KHyperTextAreas();

