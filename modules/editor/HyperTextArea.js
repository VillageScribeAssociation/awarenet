// HyperTextArea http://hypertextarea.sourceforge.net 
// Type javascript:HyperTextArea.about() into your browser location on any page 
// hosting a HyperTextArea for license details.

function HyperTextArea(name, html, width, height,resourcePath,styleSheetUrl,delayRender){
	this.isRichText = false;
	this.rng = null;
	
	this.name = name;
	this.html = html||"";
	this.width = width;
	this.height = height;
	this.resourcePath = resourcePath||"";
	this.styleSheetUrl = styleSheetUrl||null;
	if(this.resourcePath.length && this.resourcePath.substring(this.resourcePath.length-1) != "/"){
		this.resourcePath = this.resourcePath + "/";
	}
	this.delayRender = delayRender||false;
	this.controlNames = new Array();
	this.controlsByName = new Array();
	this.toolbarNames = new Array();
	this.designModeRetryCount = 0;
	this.isSrcView = false;

	this.init = function(){
		HyperTextArea.areas[this.name] = this;
		//check to see if designMode mode is available
		if (document.getElementById) {
			if (document.all) {
				//check for Internet Explorer 5+
				this.isRichText = true;
			} else {
				//check for browsers that support designmode
				//make sure that this is not safari (and perhaps other khtml based browsers) 
				// which returns "inherit" for document.designMode 
				if (document.designMode && document.designMode.toLowerCase() != "inherit"){
					this.isRichText = true;
				}
			}
		}

	
		this.addControl(new Toolbar("toolbar1"));

		//add default controls
		this.addControl(
				new TextFormatButton("bold","Bold","images/post_button_bold.gif","bold")
			);
		this.addControl(
				new TextFormatButton("italic","Italic","images/post_button_italic.gif","italic")
			);
		//this.addControl(new Spacer());
		this.addControl(
				new TextFormatButton("left","Align Left",
									"images/post_button_left_just.gif","justifyleft")
			);
		this.addControl(
				new TextFormatButton("center","Center",
									"images/post_button_centre.gif","justifycenter")
			);
		this.addControl(
				new TextFormatButton("right","Align Right",
									"images/post_button_right_just.gif","justifyright")
			);
		this.addControl(new Spacer());
		this.addControl(
				new TextFormatButton("orderedlist","Ordered List",
									"images/post_button_numbered_list.gif","insertorderedlist")
			);
		this.addControl(
				new TextFormatButton("unorderedlist","Unordered List",
									"images/post_button_list.gif","insertunorderedlist")
			);
		this.addControl(new Spacer());
		this.addControl(
				new TextFormatButton("outdent","Outdent",
									"images/post_button_outdent.gif","outdent")
			);
		this.addControl(
				new TextFormatButton("indent","Indent","images/post_button_indent.gif","indent")
			);
		this.addControl(
				new TextFormatButton("forecolor","Text Color",
									"images/post_button_textcolor.gif","forecolor")
			);
		//this.addControl(
		//		new TextFormatButton("hilitecolor","Background Color",
		//							"images/post_button_bgcolor.gif","hilitecolor")
		//	);
		this.addControl(new Spacer());
		this.addControl(
				new Button("insertImage","images/post_button_image.gif","Insert Image","addImage")
			);
		this.addControl(
				new TextFormatButton("link","Create Link",
									"images/post_button_hyperlink.gif","createlink")
			);
		this.addControl(
				new Button("insertTable","images/post_button_table.gif",
							"Insert Table","insertTableDialog")
			);

		this.addControl(new Toolbar("toolbar0"));
		//menus
		fontMenu = new Menu("fontname","fontname");
		fontMenu.addItems("","Font","Arial, Helvetica, sans-serif","Arial",
						"Courier New, Courier, mono","Courier New",
						"Times New Roman, Times, serif","Times New Roman",
						"Verdana, Arial, Helvetica, sans-serif","Verdana");

		this.addControl(fontMenu);
		
		sizeMenu = new Menu("fontsize","fontsize");
		sizeMenu.addItems("","Size",1,1,2,2,3,3,4,4,5,5,6,6,7,7);
		this.addControl(sizeMenu);
		
		styleMenu = new Menu("formatblock","formatblock");
		styleMenu.addItems("","style","<p>","paragraph","<h1>","title","<h2>","subtitle",
						"<h3>","sub-h3","<h4>","sub-h4","<address>","address","<pre>","pre",
						"<blockquote>","blockquote");

		this.addControl(styleMenu);
		
	}
	
	this.addControl = function(control){
		control.resourcePath = this.resourcePath;
		control.area = this;
		i = this.controlNames.length
		this.controlNames[eval(i)] = control.name;
		this.controlsByName[control.name] = control;
	}
	
	this.getControl = function(name){
		return this.controlsByName[name];
	}
	
	this.replaceControl = function(oldName,newControl){
		if(this.getControl(oldName)){
			for(i=0;i<this.controlNames.length;i++){
				if(this.controlNames[i] == oldName){
					break;
				}
			}
		}else{
			i = this.controlNames.length;
		}
		newControl.area = this;
		newControl.resourcePath = this.resourcePath;
		this.controlNames[i] = newControl.name;
		this.controlsByName[newControl.name] = newControl;
	}
	
	this.insertControl = function(i,control){
		control.resourcePath = this.resourcePath;
		control.area = this;
		this.controlNames.splice(i,0, control.name);
		this.controlsByName[control.name] = control;
	}
	
	this.removeControl = function(name){
		for(i=0;i<this.controlNames.length;i++){
			if(this.controlNames[i] == name){
				this.controlNames.splice(i,1);
				return true;
			}
		}
		return false;
	}

	this.render = function(html){
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

	this._renderDefault = function(html){
		document.writeln('<textarea name="' + this.name + '" id="' + this.name + '" ' 
						+ 'style="width: ' + this.width + 'px; height: ' + this.height + 'px;">' 
						+ html + '</textarea>');
	}

	this.getControlNames = function(lable){
		text= lable + "\n\n";
		for(i=0;i<this.controlNames.length;i++){
			text = text + "\t" + this.controlNames[i] + "\n";
		}
		return text;
		
	}
	
	this._renderControls = function(){
		text = "";
		for(x=0;x<this.controlNames.length;x++){
			control = this.controlsByName[this.controlNames[x]];
			if(control){
				text = text + control.getRenderedText();
			}
		}	
		text = text + '</tr>';
		text = text + '</table>';
		document.writeln(text);
	}
	
	this._renderRTE = function(){
		document.writeln('<style type="text/css">');
		document.writeln('.btnImage {cursor: pointer; cursor: hand;}');
		//document.writeln('body {font-family: Geneva, Arial, Helvetica, Sans-serif; ' 
		//				+ 'font-size: 10px; }');
		document.writeln('</style>');

		this._renderControls();
		document.writeln('<iframe id="' + this.name + '" width="' + this.width + 'px" height="' + this.height + 'px" frameborder="1" style="border:1px dashed gray;"></iframe>');
		document.writeln('<br /><input type="checkbox" id="chkSrc' + this.name + '" onclick="HyperTextArea.getArea(\''+this.name+'\').toggleHTMLSrc();" />&nbsp;View Source');
		//reimplement this so that it is not in an iframe, rather in a div.
		document.writeln('<iframe width="145" height="130" id="cp' + this.name + '" name="cp' + this.name + '" marginwidth="0" marginheight="0" scrolling="no" frameborder="1" style="visibility:hidden; position: absolute;"></iframe>');
		document.writeln('<input type="hidden" id="hdn' + this.name + '" name="' + this.name + '" value="">');
		document.getElementById('hdn' + this.name).value = this.html;
		this.initializeContent(this.html);
		setTimeout('onHyperTextAreaLoad(\'' + this.name + '\')', 500);
	}

	this.initializeContent = function(html){
		HyperTextArea.activeArea = this;
		var frameHtml = "<html>\n";
		frameHtml += "<head>\n";
		if (this.styleSheetUrl){
			frameHtml += "<link media=\"all\" type=\"text/css\" href=\"" + this.styleSheetUrl + "\" rel=\"stylesheet\">\n";
		}
		frameHtml += "</head>\n";
		frameHtml += "<body>\n";
		frameHtml += html;
		frameHtml += "</body>\n";
		frameHtml += "</html>";
		
		if (document.all) {
			var oRTE = frames[this.name].document;
			oRTE.open();
			oRTE.write(frameHtml);
			oRTE.close();
		} else {
			var oRTE = document.getElementById(this.name).contentWindow.document;
			oRTE.open();
			oRTE.write(frameHtml);
			oRTE.close();
			//attach a keyboard handler for Mozilla to make keyboard shortcuts for formatting text
			oRTE.addEventListener("keypress", kb_handler, true);
		}
	}

	this.setContent = function(html){
		HyperTextArea.activeArea = this;
		var oRTE;
		if (document.all) {
			oRTE = frames[this.name].document;
		} else {
			oRTE = document.getElementById(this.name).contentWindow.document;
		}
		body = oRTE.getElementsByTagName("body");
		body.innerHTML = html;
	}

	this.update = function(){
		this.setViewMode(false);

		//set message value
		var oHdnMessage = document.getElementById('hdn' + this.name);
		var oRTE = document.getElementById(this.name);
		
		if (this.isRichText) {
			if (oHdnMessage.value == null) oHdnMessage.value = "";
			if (document.all) {
				oHdnMessage.value = frames[this.name].document.body.innerHTML;
			} else {
				oHdnMessage.value = oRTE.contentWindow.document.body.innerHTML;
			}
			//if there is no content (other than formatting) set value to nothing
			if (stripHTML(oHdnMessage.value.replace("&nbsp;", " ")) == "") oHdnMessage.value = "";
			this.html = oHdnMessage.value;
		}
	}

	this.setToolbarsVisible = function(isVisible){
			visibleStyle = isVisible?"visible":"hidden";
			for (x=0;x<this.toolbarNames.length;x++){
				document.getElementById(this.toolbarNames[x] + "_" + this.name).style.visibility = visibleStyle;
			}
	}

	this.setViewMode = function(isSrcView){
		HyperTextArea.activeArea = this;
		//contributed by Bob Hutzel (thanks Bob!)
		var oRTE;
		if (document.all) {
			oRTE = frames[this.name].document;
		} else {
			oRTE = document.getElementById(this.name).contentWindow.document;
		}
		
		//only change the view if it is different than the current state
		if (isSrcView && !this.isSrcView){
			this.isSrcView = true;
			this.setToolbarsVisible(false);
			if (document.all) {
				oRTE.body.innerText = oRTE.body.innerHTML;
			} else {
				var htmlSrc = oRTE.createTextNode(oRTE.body.innerHTML);
				oRTE.body.innerHTML = "";
				oRTE.body.appendChild(htmlSrc);
			}
		}else if(!isSrcView && this.isSrcView){
			this.isSrcView = false;
			this.setToolbarsVisible(true);
			if (document.all) {
				oRTE.body.innerHTML = oRTE.body.innerText;
			} else {
				var htmlSrc = oRTE.body.ownerDocument.createRange();
				htmlSrc.selectNodeContents(oRTE.body);
				oRTE.body.innerHTML = htmlSrc.toString();
			}
		}
		
	}
	
	this.toggleHTMLSrc = function(){
		if (document.getElementById("chkSrc" + this.name).checked) {
			this.setViewMode(true);
		} else {
			this.setViewMode(false);
		}
	}

	//TODO would really like to be able to plug functionality in here.  This would include the 
	//ability to launch a wizard, and then insert arbitrary text at the insertion point
	this.formatText = function (command,option){
		HyperTextArea.activeArea = this;
		var oRTE;
		if (document.all) {
			oRTE = frames[this.name];
		} else {
			oRTE = document.getElementById(this.name).contentWindow;
		}
		
		if ((command == "forecolor") || (command == "hilitecolor")) {
			this.command = command;
			controlElement = document.getElementById(this.name +"_" + command);
			cp = document.getElementById('cp' + this.name);
			this.cpWindow.area = this;
			cp.style.left = getOffsetLeft(controlElement) + "px";
			cp.style.top = (getOffsetTop(controlElement) + controlElement.offsetHeight) + "px";
			if (cp.style.visibility == "hidden") {
				cp.style.visibility="visible";
			} else {
				cp.style.visibility="hidden";
			}
			
			//get current selected range
			var sel = oRTE.document.selection; 
			if (sel != null) {
				this.rng = sel.createRange();
			}
		} else if (command == "createlink") {
			// TODO need a way to make tihs more flixible.  Would especially like to be able to
			// insert a link with both the containing text and the URL
			var szURL = prompt("Enter a URL:", "");
			oRTE.document.execCommand("Unlink",false,null)
			oRTE.document.execCommand("CreateLink",false,szURL)
		} else {
			oRTE.focus();
		  	oRTE.document.execCommand(command, false, option);
			oRTE.focus();
		}

	}

	this.setColor = function(color){
		HyperTextArea.activeArea = this;
		
		var oRTE;
		if (document.all) {
			oRTE = frames[this.name];
		} else {
			oRTE = document.getElementById(this.name).contentWindow;
		}
		
		//var parentCommand = parent.command;
		if (document.all) {
			//retrieve selected range
			var sel = oRTE.document.selection; 
			if (this.command == "hilitecolor") this.command = "backcolor";
			if (sel != null) {
				var newRng = sel.createRange();
				newRng = this.rng;
				newRng.select();
			}
		} else {
			oRTE.focus();
		}
		oRTE.document.execCommand(this.command, false, color);
		oRTE.focus();
		document.getElementById('cp' + this.name).style.visibility = "hidden";
	}
	
	this.addImage = function(imagePath){
		HyperTextArea.activeArea = this;
		if(!imagePath){
			imagePath = prompt('Enter Image URL:', 'http://');				
		}
		var oRTE;
		if (document.all) {
			oRTE = frames[this.name];
		} else {
			oRTE = document.getElementById(this.name).contentWindow;
		}

		if ((imagePath != null) && (imagePath != "")) {
			oRTE.focus()
			oRTE.document.execCommand('InsertImage', false, imagePath);
		}
		oRTE.focus()
	}
	
	//TODO should look into ways to make this cross browser and platform
	this.checkspell = function(){
		try {
			var tmpis = new ActiveXObject("ieSpell.ieSpellExtension");
			tmpis.CheckAllLinkedDocuments(document);
		}
		catch(exception) {
			if(exception.number==-2146827859) {
				if (confirm("ieSpell not detected.  Click Ok to go to download page."))
					window.open("http://www.iespell.com/download.php","DownLoad");
			} else {
				alert("Error Loading ieSpell: Exception " + exception.number);
			}
		}
		
	}
	
	this.select = function(menu,cmd){
		HyperTextArea.activeArea = this;
		var oRTE;
		if (document.all) {
			oRTE = frames[this.name];
		} else {
			oRTE = document.getElementById(this.name).contentWindow;
		}
		
		var idx = menu.selectedIndex;
		// First one is always a label
		if (idx != 0) {
			var selected = menu.options[idx].value;
			oRTE.document.execCommand(cmd, false, selected);
			menu.selectedIndex = 0;
		}
		oRTE.focus();
		
	}
	
	this.insertTableDialog = function(){
		w = window.open("","tableDialog","width=300,height=150");
		w.area = this;
		d = w.document;
		d.open();
		d.write(getTableDialogAsString());
		d.close();
		
	}
	this.insertTable = function(rows,cols,spacing,padding,border){
		rows = rows||3;
		cols = cols||3;
		spacing = spacing||2;
		padding = padding||2;
		if(border == true){
			border = 1;
		}
		border = border||0;
		
		if (document.all) {
			oRTE = frames[this.name];
		} else {
			oRTE = document.getElementById(this.name).contentWindow;
		}
		doc = oRTE.document;

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
		this.insertElement(table);
	}
	
	this.insertElement = function(el){
		if (document.all) {
			oRTE = frames[this.name];
		} else {
			oRTE = document.getElementById(this.name).contentWindow;
		}
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
        }else{
        	selection = oRTE.getSelection();
			var range = selection.getRangeAt(0);
			selection.removeAllRanges();
			range.deleteContents();
			var container = range.startContainer||selection.focusNode;
			var pos = range.startOffset;
			afterNode = container.childNodes[pos];
			try{
				if (container.nodeName.toLowerCase() == "body" && pos < container.childNodes.length && container.childNodes[pos + 1]){
					afterNode = container.childNodes[pos+1]
					container.insertBefore(el, afterNode);
				}else{
					container.insertBefore(el, container.afterNode);
				}
			}catch (e){
				//if this is a text node, then break it up into a text node, new element, text node
				if(container.nodeName.toLowerCase() == "#text"){
					text0 = container.data.substring(0,range.startOffset);
					text1 = container.data.substring(range.startOffset,container.data.length-1);
					container.data = text0;
					parent = container.parentNode;
					parent.insertBefore(el,container.nextSibling);
					newTextNode = document.createTextNode(text1);
					parent.insertBefore(newTextNode,el.nextSibling);
				}else {
					alert(el.nodeName.toLowerCase() + " cannot be placed here for the following reason:\n\n" + e);
				}
			}
        }          
		
	}
	
	this.init();
	if(! this.delayRender){
		this.render(this.html);
	}

}

function TextFormatButton(name,label,icon,command,option){
	this.name = name;
	this.label = label;
	this.icon = icon;
	this.command = command;
	this.option = option||"";
	//the next two values are set by the HyperTextArea object
	this.area = null;
	this.resourcePath = null;
		
	this.getRenderedText = function(){
		text = '<td><div id="'+this.area.name+'_'+this.name+'">'
		text = text + '<img class="btnImage" src="'+this.resourcePath+this.icon+'" width="25" height="24" alt="'+this.label+'" title="'+this.label+'" onClick="HyperTextArea.getArea(\''+ this.area.name +'\').getControl(\''+this.name+'\').execute()">';
		text = text + '</div></td>';		
		return text;
	}
	
	this.execute = function(){
		this.area.formatText(this.command,this.option);
	}
}

function Button(name,icon,title,methodName){
	this.name=name;
	this.getRenderedText = function(){
		text = '<td><div id="'+name+'">'
		text = text + '<img class="btnImage" src="'+this.resourcePath+icon+'" width="25" height="24" alt="'+title+'" title="'+title+'" onClick="HyperTextArea.getArea(\''+ this.area.name +'\').'+methodName+'()">';
		text = text + '</div></td>';		
		return text;
	}
}

function Spacer(name){
	this.name = name
	this.getRenderedText = function(){
		return '<td>&nbsp;</td>'
	}
}
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

function MenuItem(value,lable){
	this.value = value;
	this.lable = lable;
}

HyperTextArea.areas = new Array();
HyperTextArea.getArea = function(name){
	return HyperTextArea.areas[name];
}
HyperTextArea.activeArea = null;
HyperTextArea.about = function(){
	var area;
	for(i in HyperTextArea.areas){
		area = HyperTextArea.getArea(i);
		break;
	}
	window.open(area.resourcePath + "about.html");
}
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

function kb_handler(evt, rte) {
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
function enableDesignMode(areaName){
	try{
		if (document.all) {
			frames[areaName].document.designMode = "On";
		}else{
			contentDocument = document.getElementById(areaName).contentDocument;
			contentDocument.designMode = "on";
		}	
		self.status = "";
		area.setContent(area.html)
	}catch(e){
		//attempt to recover from any exceptions
		exp = "enableDesignMode('"+areaName+"')";
		area = HyperTextArea.getArea(areaName);
		area.designModeRetryCount++
		self.status = "Error in setting designMode property on attempt number "+ area.designModeRetryCount +".  Retrying.";
		if(area.designModeRetryCount <= 10){
			t = setTimeout(exp,100);
		}else{
			self.status = areaName + " failed to initialize properly";
			throw e;
		}
	}
}

function onHyperTextAreaLoad(areaName) {
	self.status = "attempting to set designMode property";
	area = HyperTextArea.getArea(areaName);
	area.cpWindow = frames['cp' + areaName].window;
	
	//enables the area to determine what form it belongs to
	area.form = document.getElementById("hdn"+areaName).form;
	
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
	if (document.all) {
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

