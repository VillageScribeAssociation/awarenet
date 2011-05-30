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
//+	MAJOR TODOS
//+		
//+	(1) add a member recording whether current browser is ie, remove a lot of checks for this
//+		in the code.
//+
//+	(2)	single parser for HTML and kapenta blocks, converting between then on load and unload
//+	

//--------------------------------------------------------------------------------------------------
//	WYSWYG editor
//--------------------------------------------------------------------------------------------------

function HyperTextArea (name, html, width, height, resourcePath, styleSheetUrl, delayRender) {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	this.isRichText = false;								//_	design mode is available? [bool]
	this.rng = null;										//_	active range [range]
	this.name = name;										//_ frame id and TA name [string]
	this.html = html||"";									//_	contents? [string]
	this.width = width;										//_ pixels [int]
	this.height = height;									//_	pixels [int]
	this.resourcePath = resourcePath||"";					//_	location for resources [string]
	this.resourcePath = jsServerPath + 'modules/editor/';	//_	TODO: set this from view.fn.php
	this.styleSheetUrl = styleSheetUrl||null;				//_	location of stylesheet [string]
	this.delayRender = delayRender||false;					//_	true cancels initial render [bool]
	this.controlNames = new Array();						
	this.controlsByName = new Array();	
	this.toolbarNames = new Array();
	this.designModeRetryCount = 0;
	this.isSrcView = false;									//_ toggles plain vs rich text [bool]

	if ((this.resourcePath.length > 0)
		&& (this.resourcePath.substring(this.resourcePath.length - 1) != "/")) { 

		this.resourcePath = this.resourcePath + "/"; 	
	}	// TODO: tidy this

	//----------------------------------------------------------------------------------------------
	//.	add toolbars and controls
	//----------------------------------------------------------------------------------------------

	this.init = function(){
		HyperTextArea.areas[this.name] = this;		// set link to self?

		//------------------------------------------------------------------------------------------
		//	check to see if designMode mode is available
		//------------------------------------------------------------------------------------------
		if (document.getElementById) {				// does this method exist?
			if (document.all) {  					// check for Internet Explorer 5+	
				this.isRichText = true;				// apparently		

			} else {
				// check for browsers that support design mode
				// make sure that this is not safari (and perhaps other khtml based browsers) 
				// which returns "inherit" for document.designMode 
				if ((document.designMode) && (document.designMode.toLowerCase() != "inherit")) 
					{ this.isRichText = true; }
			}
		}

		//------------------------------------------------------------------------------------------
		//	add controls
		//------------------------------------------------------------------------------------------
	
		this.addControl(new Toolbar("toolbar1"));

		//add default controls
		this.addControl(new TextFormatButton("bold","Bold","images/post_button_bold.gif","bold"));
		this.addControl(new TextFormatButton("italic","Italic","images/post_button_italic.gif","italic"));
		//this.addControl(new Spacer());
		this.addControl(new TextFormatButton("left","Align Left","images/post_button_left_just.gif","justifyleft"));
		this.addControl(new TextFormatButton("center","Center",	"images/post_button_centre.gif","justifycenter"));
		this.addControl(new TextFormatButton("right","Align Right", "images/post_button_right_just.gif","justifyright"));
		this.addControl(new Spacer());
		this.addControl(new TextFormatButton("orderedlist","Ordered List","images/post_button_numbered_list.gif","insertorderedlist"));
		this.addControl(new TextFormatButton("unorderedlist","Unordered List","images/post_button_list.gif","insertunorderedlist"));
		this.addControl(new Spacer());
		this.addControl(new TextFormatButton("outdent","Outdent","images/post_button_outdent.gif","outdent"));
		this.addControl(new TextFormatButton("indent","Indent","images/post_button_indent.gif","indent"));
		this.addControl(new TextFormatButton("forecolor","Text Color","images/post_button_textcolor.gif","forecolor"));
		//this.addControl(
		//		new TextFormatButton("hilitecolor","Background Color",
		//							"images/post_button_bgcolor.gif","hilitecolor")
		//	);
		this.addControl(new Spacer());
		//this.addControl(new Button("insertImage","images/post_button_image.gif","Insert Image","addImage"));
		this.addControl(new Button("insertHorizontalRule","images/post_button_hr.gif","Insert Horizontal Rule","addHr"));
		this.addControl(new TextFormatButton("link","Create Link","images/post_button_hyperlink.gif","createlink"));
		//this.addControl(new Button("insertTable","images/post_button_table.gif","Insert Table","insertTableDialog"));

		//this.addControl(new Toolbar("toolbar0"));
		//menus
		//fontMenu = new Menu("fontname","fontname");
		//fontMenu.addItems("","Font","Arial, Helvetica, sans-serif","Arial",
		//				"Courier New, Courier, mono","Courier New",
		//				"Times New Roman, Times, serif","Times New Roman",
		//				"Verdana, Arial, Helvetica, sans-serif","Verdana");

		//this.addControl(fontMenu);
		
		//sizeMenu = new Menu("fontsize","fontsize");
		//sizeMenu.addItems("","Size",1,1,2,2,3,3,4,4,5,5,6,6,7,7);
		//this.addControl(sizeMenu);
		
		styleMenu = new Menu("formatblock","formatblock");
		styleMenu.addItems("","style","<p>","paragraph","<h1>","title","<h2>","subtitle",
						"<h3>","sub-h3","<h4>","sub-h4","<address>","address","<pre>","pre",
						"<blockquote>","blockquote");

		this.addControl(styleMenu);
		
	} // end this.init
	
	//----------------------------------------------------------------------------------------------
	//	add a control
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
	//	get a control given its name
	//----------------------------------------------------------------------------------------------
	
	this.getControl = function(name) { return this.controlsByName[name]; }

	//----------------------------------------------------------------------------------------------
	//	replace a control given its name and a new control (apparently not used)
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
	//	replace a control given its name and a new control (apparently not used)
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
	//	remove a control given its name
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

	//----------------------------------------------------------------------------------------------
	//.	display/refresh contents of editable iframe (WYSWYG content area)
	//----------------------------------------------------------------------------------------------
	//arg: html - html content of iframe document body [string]
	
	this.render = function(html) {
		HyperTextArea.activeArea = this;
		text="this.render\n\n";
		for(i=0;i<this.controlNames.length;i++){
			text = text + "\t" + this.controlNames[i] + "\n";
		}
		if (this.isRichText) {
			this._renderRTE(html);
		} else {
			this._renderDefault(html);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	display contents as text in a textarea (HTML tags shown)
	//----------------------------------------------------------------------------------------------
	//arg: html - raw html to be placed in textarea [string]

	this._renderDefault = function(html){
		document.writeln('<textarea name="' + this.name + '" id="' + this.name + '" ' 
						+ 'style="width: ' + this.width + 'px; height: ' + this.height + 'px;">' 
						+ html + '</textarea>');
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
		document.writeln(text);
	}

	//----------------------------------------------------------------------------------------------
	//.	render rich text editor (the entire WYSWYG editor UI)
	//----------------------------------------------------------------------------------------------
	//TODO: work out how this knows where to add them, consider improving that

	this._renderRTE = function(){
		//------------------------------------------------------------------------------------------
		//	add mouse style for buttons  (TODO: consider adding this style def to theme css.)
		//------------------------------------------------------------------------------------------
		document.writeln('<style type="text/css">');
		document.writeln('.btnImage {cursor: pointer; cursor: hand;}');
		document.writeln('</style>');

		//------------------------------------------------------------------------------------------
		//	add controls
		//------------------------------------------------------------------------------------------
		this._renderControls();

		//------------------------------------------------------------------------------------------
		//	add content (editable) iframe
		//------------------------------------------------------------------------------------------
		var ifHtml = ""
			 + "<iframe id='" + this.name + "' "
			 + "width='" + this.width + "px' "
			 + "height='" + this.height + "px' "
			 + "frameborder='1' "
			 + "style='border:1px dashed gray;'>"
			 + "</iframe>";

		document.writeln(ifHtml);

		//------------------------------------------------------------------------------------------
		//	add 'view source' checkbox
		//------------------------------------------------------------------------------------------
		var cbHtml = "<br/>"
			+ "<input type='checkbox' "
			 + "id='chkSrc" + this.name + "' "
			 + "onclick=\"HyperTextArea.getArea(\'" + this.name + "\').toggleHTMLSrc();\" />"
			 + "&nbsp;View Source";

		document.writeln(cbHtml);

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

		document.writeln(hiHtml);

		//------------------------------------------------------------------------------------------
		//	add hidden form field and load it with WYSWYG contents
		//------------------------------------------------------------------------------------------
		var hfHtml = "<input type='hidden' "
			 + "id='hdn" + this.name + "' "
			 + "name='" + this.name + "' value='' />";		

		document.writeln(hfHtml);
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
	// add content stylesheet, use default iframe stylesheet for now

	this.initializeContent = function(html) {
		HyperTextArea.activeArea = this;									// this confuses me
		var useSSUrl = jsServerPath + "themes/clockface/css/iframe.css";	//%	default css [string]
		var frameHtml = '';		

		//------------------------------------------------------------------------------------------
		//	assemble iframe HTML
		//------------------------------------------------------------------------------------------
		frameHtml += "<html>\n<head>\n";		// start document (add doctype here?)

		if (this.styleSheetUrl) { useSSUrl = $this.styleSheetUrl; }
		
		//------------------------------------------------------------------------------------------
		// add content stylesheet, use default iframe stylesheet for now
		//------------------------------------------------------------------------------------------
		frameHtml += "<link media=\"all\" type=\"text/css\" "
			 + "href=\"" + useSSUrl + "\" rel=\"stylesheet\">\n";

		html = editorProcessBlocks(html);	// kapenta only (TODO: use new html parser)

		frameHtml += "</head>\n";
		frameHtml += "<body>\n";
		frameHtml += html;
		frameHtml += "</body>\n";
		frameHtml += "</html>";

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
	//.	change content of editable iframe
	//----------------------------------------------------------------------------------------------
	//arg: html - content to be used for iframe body [string]

	this.setContent = function(html) {
		HyperTextArea.activeArea = this;
		var oRTE;											//%	iframe DOM root [object:document]
		if (document.all) {	oRTE = frames[this.name].document; }
		else { oRTE = document.getElementById(this.name).contentWindow.document; }
		body = oRTE.getElementsByTagName("body");		// when is this needed, why not oRTE.body?
		body.innerHTML = html;							// set body only (not head)
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
		//TODO: PARSE CONTENT FOR STORAGE/TRANSMISSION HERE *********************************************************************************
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

		HyperTextArea.activeArea = this;
		
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
		HyperTextArea.activeArea = this;

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
	//TODO: disable in kapenta, or integrate with images module

	this.addImage = function(imagePath){
		var oRTE;											//%	iframe's document window [object]
		HyperTextArea.activeArea = this;

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
		HyperTextArea.activeArea = this;

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
				// TODO: tidy this if statement
				if ( ('body' == container.nodeName.toLowerCase())
					&& (pos < container.childNodes.length)
					&& (container.childNodes[pos + 1])) {
					afterNode = container.childNodes[pos+1]
					container.insertBefore(el, afterNode);

				} else { container.insertBefore(el, container.afterNode); }

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
	this.icon = icon;
	this.command = command;
	this.option = option||"";

	// these two values are set by the HyperTextArea object
	this.area = null;				//_	pointer to HyperTextArea [object]
	this.resourcePath = null;		//_	base location for graphics, etc [string]

	//----------------------------------------------------------------------------------------------
	//.	member variables
	//----------------------------------------------------------------------------------------------
	this.getRenderedText = function(){
		text = '<td><div id="' + this.area.name + '_' + this.name + '">'
		text = text + '<img class="btnImage" src="'+this.resourcePath+this.icon+'" width="25" height="24" alt="'+this.label+'" title="'+this.label+'" onClick="HyperTextArea.getArea(\''+ this.area.name +'\').getControl(\''+this.name+'\').execute()">';
		text = text + '</div></td>';		
		return text;
	}
	
	this.execute = function(){
		this.area.formatText(this.command,this.option);
	}
}

//--------------------------------------------------------------------------------------------------
//	OBJECT representing toolbar button
//--------------------------------------------------------------------------------------------------

function Button(name, icon, title, methodName) {
	this.name = name;
	this.getRenderedText = function(){
		text = '<td><div id="' + name + '">'
		text = text + '<img class="btnImage" src="' + this.resourcePath+icon + '" width="25" height="24" alt="' + title + '" title="' + title + '" onClick="HyperTextArea.getArea(\'' + this.area.name + '\').'+methodName+'()" />';
		text = text + '</div></td>';
		return text;
	}
}

//--------------------------------------------------------------------------------------------------
//	OBJECT representing toolbar spacer
//--------------------------------------------------------------------------------------------------

function Spacer(name){
	this.name = name
	this.getRenderedText = function(){
		return '<td>&nbsp;</td>'
	}
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
//	object representing menus
//--------------------------------------------------------------------------------------------------

function Menu(name,cmd){
	this.name = name;
	this.cmd = cmd;
	this.area = null;
	this.items = new Array();
	this.addItem = function(value,lable){
		this.items[this.items.length] = new MenuItem(value,lable);
	}
	this.addItems = function(){
		for (i=0;i<arguments.length;i=i+2){
			this.addItem(arguments[i],arguments[i+1]);
		}
	}
	this.getRenderedText = function(){
		text = "<td><select name='"+this.name+"' id='"+this.name+"_"+this.area.name+"' onchange='HyperTextArea.getArea(\""+ this.area.name +"\").select(this,\""+this.cmd+"\");'>\n";
		for (i=0;i<this.items.length;i++){
			thisItem = this.items[i]
			text = text + "<option value='"+thisItem.value+"'>"+thisItem.lable+"</option>\n";
		}
		text = text + "</select></td>";
		return text;
	}
}

//--------------------------------------------------------------------------------------------------
//	OBJECT representing menu items
//--------------------------------------------------------------------------------------------------

function MenuItem(value,lable){
	this.value = value;
	this.lable = lable;
}

//--------------------------------------------------------------------------------------------------
//	Hypertext areas init
//--------------------------------------------------------------------------------------------------

HyperTextArea.areas = new Array();

//  why not in the object?
HyperTextArea.getArea = function(name) { return HyperTextArea.areas[name]; }

HyperTextArea.activeArea = null;

//--------------------------------------------------------------------------------------------------
//	Attach 'about' method to HyperTextArea
//--------------------------------------------------------------------------------------------------
//  why not in the object?
HyperTextArea.about = function(){
	var area;
	for(i in HyperTextArea.areas){
		area = HyperTextArea.getArea(i);
		break;
	}
	window.open(area.resourcePath + "about.html");
}

//--------------------------------------------------------------------------------------------------
//	Attach 'updateAllAreas' method to HyperTextArea
//--------------------------------------------------------------------------------------------------

HyperTextArea.updateAllAreas = function(){
	//iterate over all areas and call update
	for(i in HyperTextArea.areas){
		area = HyperTextArea.areas[i];
		area.update();
	}
}

HyperTextArea.forms = new Array();

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

//-------------------------------------------------------------------------------------------------
//	on node insert check to see that images have been added correctly
//	kapenta only! - remove if you're not using this to manage block insertions
//-------------------------------------------------------------------------------------------------

function nodeinsert_handler(evt) {
	var oRTE = getEditorDocument(evt.target);

	if ('IMG' == evt.target.tagName) {		
		replaceImageButtons(oRTE);

	} else {
		// remove any images which were not added by dragging a button
		removeSimpleImages(oRTE);
	}
}

function stripHTML(oldString) {
	//alert('stripHTML');
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

function getPaletteAsString(){
	hexArray = new Array("00","55","AA","FF");
	out = "";
	out2 = "";
	line = ""
	row = 1;
	count = 1;
	for(i=hexArray.length-1;i>=0;i--){
		val0 = hexArray[i];
		for(j=hexArray.length-1;j>=0;j--){
			val1 = hexArray[j];
			for(k=hexArray.length-1;k>=0;k--){
				val2 = hexArray[k];
				hexVal = val0+val1+val2;
				line = line + "\n <td id='#"+hexVal+"' bgcolor='#"+hexVal+"' width='15' height='15' onmouseover='this.style.border=\"1px dotted white\"' onmouseout='this.style.border=\"1px solid gray\"' onclick='area.setColor(this.id)'><img width='1' height='1'></td>";
				if(count==1 || (row % 2) == 0){
					if(((count - 1) % 8) == 0){
						out = out + "\n<tr>";
						row++
					}
					out = out + line;
					if((count % 8) == 0){
						out = out + "\n</tr>";
					}
				}else{
					if(((count - 1) % 8) == 0){
						out2 = out2 + "\n<tr>";
						row++
					}
					out2 = out2 + line;
					if((count % 8) == 0){
						out2 = out2 + "\n</tr>";
					}
				}
				line = "";
				count++;
			}
		}
	}
	out = '<table cellpadding="0" cellspacing="1" border="0" align="center">' + out + out2 + "</table>";
	return out;
}

//--------------------------------------------------------------------------------------------------
//	returns the content of a popup window for inserting tables
//--------------------------------------------------------------------------------------------------
//ugly and seriously unpleasant to use  TODO: improve this

function getTableDialogAsString(){
	out = '<form name="tableDialog">';
	out = out + '<table width="100%" cellpadding="2" cellspacing="2" border="0">';
	out = out + '<tr>';
	out = out + '<td>Rows: <input type="text" name="rows" size="2" value="3"/></td>';
	out = out + '<td>Columns: <input type="text" name="cols" size="2" value="3"/></td>';
	out = out + '</tr>';
	out = out + '<tr>';
	out = out + '<td>Spacing: <input type="text" name="spacing" size="2" value="2"/></td>';
	out = out + '<td>Padding: <input type="text" name="padding" size="2" value="2"/></td>';
	out = out + '</tr>';
	out = out + '<tr>';
	out = out + '<td colspan="2">Border: <input type="checkbox" name="border" value="1"/></td>';
	out = out + '</tr>';
	out = out + '<tr>';
	out = out + '<td colspan="2"><div align="center"><input type="button" name="cancel" value="Cancel" onclick="self.close()"/><input type="button" name="button" value="Insert Table" onclick="window.opener.HyperTextArea.activeArea.insertTable(this.form.rows.value,this.form.cols.value,this.form.spacing.value,this.form.padding.value,this.form.border.checked);self.close()"/></div></td>';
	out = out + '</tr>';
	out = out + '</table>';
	out = out + '</form>';
	return out;
}

//--------------------------------------------------------------------------------------------------
//	try turn on browser design mode in the HyperTextArea's iframe
//--------------------------------------------------------------------------------------------------

function enableDesignMode(areaName){
	try{
		if (document.all) { frames[areaName].document.designMode = "On"; }
		else{
			contentDocument = document.getElementById(areaName).contentDocument;
			contentDocument.designMode = "on";
		}	
		self.status = "";
		area.setContent(area.html)

	}catch(e){
		//------------------------------------------------------------------------------------------
		//	attempt to recover from any exceptions
		//------------------------------------------------------------------------------------------
		exp = "enableDesignMode('" + areaName + "')";
		area = HyperTextArea.getArea(areaName);
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

//--------------------------------------------------------------------------------------------------
//	
//--------------------------------------------------------------------------------------------------
//:	this is set by HyperTextArea._renderRTE via a timeout

function onHyperTextAreaLoad(areaName) {
	self.status = "attempting to set designMode property";
	area = HyperTextArea.getArea(areaName);							// get HyperTextArea given name
	area.cpWindow = frames['cp' + areaName].window;					// iframe window
	
	//allows the area to determine what form it belongs to
	area.form = document.getElementById("hdn" + areaName).form;		// hdn = hidden?
	
	//attaches to the forms submit handler only once
	formAlreadyStored = false;
	for(i=0;i<HyperTextArea.forms.length;i++){
		if(area.form == HyperTextArea.forms[i]){
			formAlreadyStored = true;
			break;
		}
	}
	if(!formAlreadyStored){
		onSubmitFunc = area.form.onsubmit;
		area.form.onsubmit = function(){
			HyperTextArea.updateAllAreas();
			if(onSubmitFunc){
				onSubmitFunc();
			}
		}
		HyperTextArea.forms[HyperTextArea.forms.length] = area.form;
	}
	area.setContent(area.html)
	if (document.all) {						// checking for IE?
		cp = frames["cp" + areaName];
		cp.document.write(getPaletteAsString());
	} else {
		cp = document.getElementById("cp"+areaName);
		d = cp.contentDocument;
		d.open();
		d.write(getPaletteAsString());
		d.close()
	}
	enableDesignMode(areaName);
	area.setContent(area.html)
}


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
		img = imgs[i];
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

function editorProcessBlocks(html) {
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
//	make an html image tage from an image block tag
//-------------------------------------------------------------------------------------------------

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

//-------------------------------------------------------------------------------------------------
// end of kapenta specific functions
//-------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------
	//	log a message (debugging)
	//---------------------------------------------------------------------------------------------
	function htmlLog(msg) {
		var logDiv = document.getElementById('divLog');
		logDiv.innerHTML = logDiv.innerHTML + msg + "<br>\n";
	}

	//----------------------------------------------------------------------------------------------
	//	object to clean html of unwanted tags, attributes, javascript, etc
	//----------------------------------------------------------------------------------------------

	function KHtmlCleaner() {

		//------------------------------------------------------------------------------------------
		//	member variables
		//------------------------------------------------------------------------------------------

		this.output = '';				//_ clean html [string]
		this.tagType = '';				//_	type of tag currently being processed [string]
		this.hangingEq = false;			//_	if the last token was an equals sign [bool]
		this.selfClose = false;			//_	if this is a self-closing tag [bool]
		this.tagAtName = new Array();	//_	attributes of current tag [array]
		this.tagAtVal = new Array();	//_	attribute values of current tag [array]

		//------------------------------------------------------------------------------------------
		//	configuration
		//------------------------------------------------------------------------------------------

		this.allowTags = new Array(
			'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'p', 'br', 'b', 'i', 'u', 'ul', 'ol', 'li', 'table',
			'tbody', 'th', 'td', 'tr', 'span', 'small'
		);

		//------------------------------------------------------------------------------------------
		//.	parse raw string for html tags
		//------------------------------------------------------------------------------------------

		this.parseTags = function() {

			//--------------------------------------------------------------------------------------
			//	working variables
			//--------------------------------------------------------------------------------------
			var thisChar = '';				//%	current char being examined [string]
			var nextChar = '';				//%	next char to be examined, if any [string]
			var thisHtmlCharNo = 0;			//%	char position we're scanning from [int]
			var buffer = '';				//%	piece of the document being worked on [string]
			var mode = 'outside';			//%	state of state machine [string]
	
			//--------------------------------------------------------------------------------------
			//	consider each character in source HTML
			//--------------------------------------------------------------------------------------

			for (thisHtmlCharNo = 0; thisHtmlCharNo < html.length; thisHtmlCharNo++) {
				thisChar = html.substr(thisHtmlCharNo, 1);				// current char
				nextChar = '';
				if ((thisHtmlCharNo + 1) < html.length) { 				
					nextChar = html.substr(thisHtmlCharNo + 1, 1);		// next char, if any
				}

				//----------------------------------------------------------------------------------
				// change states (only pay attention to opening of tags if not already in one)
				//----------------------------------------------------------------------------------
				switch (mode) {
					case 'outside':
						//--------------------------------------------------------------------------
						//	not inside an html tag change state when we encounter '<'
						//--------------------------------------------------------------------------
						if ('<' == thisChar) {						// start of a tag
							this.throwToken(buffer, 'outside');		// throw anything in buffer
							buffer = '';							// clear the buffer
							mode = 'tag';							// change mode
							thisHtmlCharNo--;						// reprocess this in tag mode

						} else {
							switch (thisChar) {						// disallow quotes
								case "'":	thisChar = "&apos;";	break;
								case "\"":	thisChar = "&quot;";	break;
							}
							buffer = buffer + thisChar;				// add current char to buffer
						}
						break;

					case 'tag':
						//--------------------------------------------------------------------------
						//	inside an html tag, look for whitespace, tokens, strings and '>'
						//--------------------------------------------------------------------------
						switch(thisChar) {
							case ' ':	this.throwToken(' ', 'ws');		break;	// throw whitespace
							case "\t":	this.throwToken("\t", 'ws');	break;	// ...
							case "\n":	this.throwToken("\n", 'ws');	break;
							case "\r":	this.throwToken("\r", 'ws');	break;
							case '<':	this.throwtoken("<", 'start');	break;	// throw control
							case '=':	this.throwtoken("=", 'equals');	break;	// chars

							case '/':
								if ('>' == nextChar) {				// end of self closing tag
									this.throwToken(thisChar + nextChar, 'endsc');
									thisHtmlCharNo++;				// skip the next char									

								} else {							// start of token
									mode = 'token';					// change to token mode
									thisHtmlCharNo--;				// reprocess this in token mode
								}
								break;

							case '>':								// end of this tag
								this.throwtoken(">", 'end');		// throw it
								mode = 'outside';					// change mode
								break;							

							case "'":
								mode = 'sq';						// change mode
								thisHtmlCharNo--;					// reprocess this in sq mode
								break;

							case "\"":
								mode = 'dq';						// change mode
								thisHtmlCharNo--;					// reprocess this in dq mode
								break;

							default:
								mode = 'token';						// change to token mode
								thisHtmlCharNo--;					// reprocess this in token mode
								break;

						}
						break;	// .................................................................

					case 'sq':
						//--------------------------------------------------------------------------
						//	inside a single quoted string
						//--------------------------------------------------------------------------
						buffer = buffer + thisChar;					// add current char to buffer
						if (("'" == thisChar) && ("'" != buffer)) {	// end of sq string
							this.throwToken(buffer, 'sq');			// throw it
							buffer = '';							// clear the buffer
							mode = 'tag';							// and change mode back to tag
						}
						break;	// .................................................................

					case 'sq':
						//--------------------------------------------------------------------------
						//	inside a single quoted string
						//--------------------------------------------------------------------------
						buffer = buffer + thisChar;					// add current char to buffer
						if (("'" == thisChar) && ("'" != buffer)) {	// end of sq string
							this.throwToken(buffer, 'sq');			// throw it
							buffer = '';							// clear the buffer
							mode = 'tag';							// and change mode back to tag
						}
						break;	// .................................................................

					case 'dq':
						//--------------------------------------------------------------------------
						//	inside a double quoted string
						//--------------------------------------------------------------------------
						buffer = buffer + thisChar;					// add current char to buffer
						if (("\"" == thisChar) && ("\"" != buffer)) {	// end of dq string
							this.throwToken(buffer, 'dq');			// throw it
							buffer = '';							// clear the buffer
							mode = 'tag';							// and change mode back to tag
						}
						break;	// .................................................................

					case 'token':
						//--------------------------------------------------------------------------
						//	inside a tag name, attrib name or unquoted value
						//--------------------------------------------------------------------------
						endOfToken = false;
						switch (thisChar) {
							case '=':	endOfToken = true; break;
							case ' ':	endOfToken = true; break;
							case "\t":	endOfToken = true; break;
							case "\n":	endOfToken = true; break;
							case "\r":	endOfToken = true; break;
							case ">":	endOfToken = true; break;
							case '/':	if ('>' == nextChar) { endOfToken = true; }	break;
						}

						if (true == endOfToken) {
							this.throwToken(buffer, 'token');		// throw it
							buffer = '';							// clear the buffer
							mode = 'tag';							// go back to tag mode
							thisHtmlCharNo--;						// reprocess this in tag mode

						} else { buffer = buffer + thisChar; }		// still within token

						break;	// .................................................................

				}

			} // end for each char

		} // end this.parseTags

		//------------------------------------------------------------------------------------------
		//.	catch thrown tokens and evaluate
		//------------------------------------------------------------------------------------------
		this.throwToken = function (tkVal, tkType) {
			logDebug("token: " + tkVal + " type: " + tkType + "<br>\n");
			switch(tkType) {
				case 'outside':											// not an html tag part
					this.output = this.output + tkVal;		
					break;	// .....................................................................

				case 'start':
					this.tagType = '';									// clear all working vars
					this.hangingEq = false;		
					this.selfClose = false;
					this.tagAtName = new Array();	
					this.tagAtVal = new Array();
					break;

				case 'equals':											// separates k,v pairs
					this.hangingEq = true;
					break;	// .....................................................................

				case 'token':
					if ('' == this.tagType) { this.tagType = tkVal; }	// this is the tag name
					else {												// this is attrib or value
						if (true == this.hangingEq) {				
							tagAtIdx = (this.tagAtVal.length - 1);		// last to be added
							this.tagAtVal[tagAtIdx]	= tkVal;			// this is an attrib value 
							this.hangingEq = false;						// no longer hanging

						} else {
							tagAtIdx = this.tagAtVal.length;
							tkVal = tkVal.toLowerCase();				// lowercase is tidier
							this.tagAtName[tagAtIdx] = tkVal;			// this is an attrib name
							this.tagAtVal[tagAtIdx] = '';				// set blank value
						}
					}
					break;	// .....................................................................

				case 'sq':												// single quoted string
					if (true == this.hangingEq) {
						tagAtIdx = (this.tagAtVal.length - 1);			// last to be added
						this.tagAtVal[tagAtIdx]	= tkVal;				// this is an attrib value 
						this.hangingEq = false;							// no longer hanging
					}	
					break;	// .....................................................................

				case 'dq':												// single quoted string
					if (true == this.hangingEq) {
						tagAtIdx = (this.tagAtVal.length - 1);			// last to be added
						this.tagAtVal[tagAtIdx]	= tkVal;				// this is an attrib value 
						this.hangingEq = false;							// no longer hanging
					}	
					break;	// .....................................................................

				case 'endsc':
					this.selfClose = true;
					this.addTag();
					break;

				case 'end':
					this.addTag();
					break;	// .....................................................................

			}
		} // end this.throwToken

		//------------------------------------------------------------------------------------------
		//.	finished with current tag, redact and add it to output
		//------------------------------------------------------------------------------------------

		this.addTag = function() {
			var allowed = false;						//%	if this tag is allowed [bool]
			var tnLower = this.tagType.toLowerCase();	//%	for comparison below [string]
			var tagStr = '<' + this.tagType + ' ';		//%	redacted/rebuilt HTML tag [string]

			for (var i = 0; i < this.allowTags.length; i++) 
				{ if (this.allowTags[i] == tnLower) { allowed = true; }	}

			if (false == allowed) { return false; }

			for (var i = 0; i < this.tagAtName.length; i++) { 
				var atName =  this.tagAtName[i];					//%	attributre name [string]
				var atVal =  this.tagAtVal[i];						//%	attribute value [string]

				if (true == this.allowAttrib(tnLower, atName)) {	// If this attrib is allowed
					tagStr = tagStr + atName;						// add attribute name.
					if ('' != atVal) {								// If there is a value
						if ('style' == atName) {					// and this is a 'style' attrib
							var cStyle = this.cleanStyle(atVal);	// clean the value
							tagStr = tagStr + '=' + cleanStyle;		// before adding it.

						} else { tagStr = tagStr + '=' + atVal;	}	// not 'style', just add it

					} else { tagStr = tagStr + ' '; }				// no value, leave a space

				}	
			}
			
			this.output = this.output + tagStr;						// we're done, add to output
		} // end this.addTag

		//------------------------------------------------------------------------------------------
		//.	discover if an attribute is allowed
		//------------------------------------------------------------------------------------------
		//arg: tagType - eg, 'img', 'table', 'html' [string]
		//arg: tagType - eg, 'img', 'table', 'html' [string]
		//returns: true is it's allowed, false if not [bool]

		this.allowAttrib = function(tagType, attribute) {
			tagType = tagType.toLowerCase();
			attribute = attribute.toLowerCase();
			if ('class' == attribute) { return true; }	// any tag may have any class

			//--------------------------------------------------------------------------------------
			//	some tags may have specific attributes, eg: a -> href, img -> src
			//--------------------------------------------------------------------------------------
			switch (tagType) {
				case 'span':
					switch (attribute) {
						case 'style': return true;
					}
					break; // ......................................................................

				case 'a':
					switch (attribute) {
						case 'href': return true;
					}
					break; // ......................................................................

				case 'img':
					switch (attribute) {
						case 'src': return true;
						case 'border': return true;
						case 'alt': return true;
						case 'style': return true;
					}
					break; // ......................................................................

			}

			return false;
		} // end this.allowAttrib

	} // end class KHtmlCleaner

