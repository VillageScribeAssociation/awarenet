//==================================================================================================
//*	Code for drag and drop in picturelogin
//==================================================================================================
//--------------------------------------------------------------------------------------------------
//	Globals
//--------------------------------------------------------------------------------------------------
	var _startX = 0;            // mouse starting positions
	var _startY = 0;
	var _offsetX = 0;           // current element offset
	var _offsetY = 0;
	var _dragElement = null;    // needs to be passed from OnMouseDown to OnMouseMove
	var _oldZIndex = 0;         // we temporarily increase the z-index during drag			
	
//--------------------------------------------------------------------------------------------------
//	method that checks if mouse hit is over source or dropx element
//--------------------------------------------------------------------------------------------------
	function whatHitElement() {
		var retval = null;
		var match = isWithinElement(document.getElementById("source"));
		if(match) { //drop in source				
			retval = "source";
		} else {
			var str = "";
			var index = 1;
			while(!match && index < 20) {
				str = "drop" + index;
				match = isWithinElement(document.getElementById(str));
				if(match) { //drop in a drop field
					retval = str;
				}
				index = index + 1;
			}
		}
		return retval;
	}		
//--------------------------------------------------------------------------------------------------
//	method that tries to figure out what the absolute position a certain element is
//--------------------------------------------------------------------------------------------------
	function getAbsolutePosition(element) {
		var r = null;
		if (element) {
			r = { x: element.offsetLeft, y: element.offsetTop };
			if (element.offsetParent) {
			  var tmp = getAbsolutePosition(element.offsetParent);
			  r.x += tmp.x;
			  r.y += tmp.y;
			}
		}
		return r;
	  };
			  			  
//--------------------------------------------------------------------------------------------------
//	clear value of formatting
//--------------------------------------------------------------------------------------------------
	function ExtractNumber(value)
	{
		var n = parseInt(value);

		return n == null || isNaN(n) ? 0 : n;
	}
//--------------------------------------------------------------------------------------------------
//	allowDrop method not used at the moment
//--------------------------------------------------------------------------------------------------
	function allowDrop(ev)
	{
		var id = ev.target.id;
		if (-1 < id.search("drop") && 0 == ev.target.children.length) {
			ev.preventDefault();
		}
	}
//--------------------------------------------------------------------------------------------------
//	check if element is within source or dropx elements
//--------------------------------------------------------------------------------------------------
	function isWithinElement(element) {
		var bRetVal = false;
		if (_dragElement && element) {
			var r = getAbsolutePosition(_dragElement);
			if (r) {
				var elemX = r.x;
				var elemY = r.y;
				var source = element;

				r = getAbsolutePosition(source);
				if (r) {
					var sourceX = r.x;
					var sourceY = r.y;
					var sourceH = source.offsetHeight;
					var sourceW = source.offsetWidth;
					if ( (elemX > sourceX && elemX < (sourceX + sourceW)) && (elemY > sourceY && elemY < (sourceY + sourceH)) ) {
						bRetVal = true;
					}
				}
			}
		}
		return bRetVal;
	}
//--------------------------------------------------------------------------------------------------
//	dropInSource method appends _dragElement to source element (add picture back to picture source)
//  deletes picture icon in picture source first if already exists. 
//  modifies id of element from [0-9] to 's'[0-9]
//--------------------------------------------------------------------------------------------------
	function dropInSource()
	{
		if (_dragElement) {
			var source = document.getElementById("source");
			var elem = _dragElement;
			if (elem.id.match(/s[0-9]/i)) {
				id = elem.id;
			} else {
				id = "s" + elem.id;
			}
			var previous = document.getElementById(id);
			if (previous) {
				source.removeChild(previous);
			}
			elem.id = id;
			elem.style.top = 0;
			elem.style.left = 0;
			source.appendChild(elem);
		}
	}
//--------------------------------------------------------------------------------------------------
//	dropInDrop method drops _dragElement in dropElem that is provided (appends it). Modifies id of element
//  from 's'[0-9] to just [0-9]
//--------------------------------------------------------------------------------------------------
	function dropInDrop(dropElem)
	{
		if (_dragElement && dropElem) {
			var sink = document.getElementById(dropElem);
			var elem = _dragElement;
			var id = elem.id;
			if (id.match(/s[0-9]/i)) {
				elem.id = id.slice( 1 );
			}
			elem.style.top = 0;
			elem.style.left = 0;
			if (!sink.hasChildNodes()) {
				sink.appendChild(elem);
			}
		}
	}
//--------------------------------------------------------------------------------------------------
//	mouseUp method that implements the mouseup DOM event
//--------------------------------------------------------------------------------------------------
	function mouseUp(e)
	{
		// IE is retarded and does not pass the event object 
		if (e == null) { e = window.event; }

		if (_dragElement != null)
		{
			_dragElement.style.zIndex = _oldZIndex;
			
			var dropElem = whatHitElement();
			if (!dropElem) {
				if (_dragElement.parentNode.id === "source") {
					dropInSource();
				} else {
					dropInDrop(_dragElement.parentNode.id);
				}
			} else if (dropElem === "source") {
				dropInSource();
			} else if (dropElem.match(/drop/i)) {
				dropInDrop(dropElem);
			}
			
			// this is how we know we are not dragging      
			_dragElement = null;

			// we are done with these events until the next OnMouseDown
			document.onmousemove = null;
			document.ontouchmove = null;
			document.onselectstart = null;
			_dragElement.ondragstart = null;
		}
	}
//--------------------------------------------------------------------------------------------------
//	method that implements the mousemove DOM event
//--------------------------------------------------------------------------------------------------
	function mouseMove(e)
	{
		// IE is retarded and does not pass the event object 
		if (e == null) { e = window.event; }
		
		// this is the actual "drag code"
		if (_dragElement) {
			_dragElement.style.left = (_offsetX + e.clientX - _startX) + "px";
			_dragElement.style.top = (_offsetY + e.clientY - _startY) + "px";	
		}			
	}
//--------------------------------------------------------------------------------------------------
//	method that implements the mousedown DOM event that initiates the drag and drop cycle
//--------------------------------------------------------------------------------------------------
	function mouseDown(e)
	{				
		// IE is retarded and does not pass the event object 
		if (e == null) { e = window.event; }
		
		//	touch events on phones and tablets work differently
		if (e.touches) {
			e.clientX = e.touches[0].clientX;
			e.clientY = e.touches[0].clientY;
			e.srcElement = e.touches[0].target;
		}

	    // IE uses srcElement, others use target
	    var target = e.target != null ? e.target : e.srcElement;
	    
	    if (target) {

			// we need to access the element in OnMouseMove
			_dragElement = target;
		
			var where = whatHitElement();
			if (where != null && (where === "source" || where.match(/drop/i))) {				
				// grab the mouse position
				_startX = e.clientX;
				_startY = e.clientY;
		
				// grab the clicked elements position
				_offsetX = ExtractNumber(target.style.left);
				_offsetY = ExtractNumber(target.style.top);

				// bring the clicked element to the front while it is being dragged
				target.style.position="relative";
				_oldZIndex = target.style.zIndex;
				target.style.zIndex = 10000;

				// tell our code to start moving the element with the mouse
				document.onmousemove = mouseMove;
				document.ontouchmove = mouseMove;
		
				// cancel out any text selections
//						document.body.focus();

				// prevent text selection in IE
				document.onselectstart = function () { return false; };
				// prevent IE from trying to drag an image
				target.ondragstart = function() { return false; };
			}
		}				
	}
//--------------------------------------------------------------------------------------------------
//	allowDropSource method not used at the moment
//--------------------------------------------------------------------------------------------------
	function allowDropSource(ev)
	{
		ev.preventDefault();
	}
//--------------------------------------------------------------------------------------------------
//	drag method not used at the moment
//--------------------------------------------------------------------------------------------------
	function drag(ev)
	{
		ev.dataTransfer.setData("Text",ev.target.id);
	}
//--------------------------------------------------------------------------------------------------
//	dropSource method not used at the moment
//--------------------------------------------------------------------------------------------------
	function dropSource(ev)
	{
		ev.preventDefault();
		var data = ev.dataTransfer.getData("Text");
		var source = document.getElementById("source");
		var elem = document.getElementById(data);
		var id = "s" + elem.id;
		var previous = document.getElementById(id);
		if (previous) {
			source.removeChild(previous);
		}
		elem.id = id;
		source.appendChild(elem);
		return false;
	}
//--------------------------------------------------------------------------------------------------
//	drop method not used at the moment
//--------------------------------------------------------------------------------------------------
	function drop(ev)
	{
		ev.preventDefault();
		var data = ev.dataTransfer.getData("Text");
		var sink = ev.target;
		var elem = document.getElementById(data);
		var id = elem.id;
		elem.id = id.slice( 1 );
		sink.appendChild(elem);
		return false;
	}
			
//--------------------------------------------------------------------------------------------------
//	method that initialises the drag and drop functionality. it sets the document events
//--------------------------------------------------------------------------------------------------
	function InitDragDrop()
	{
//				alert("ok");
		document.onmousedown = mouseDown;
		document.onmouseup = mouseUp;
		if(document.ontouchend) {
			document.ontouchend = mouseUp;
		}		
	}
	
	window.onload = InitDragDrop;


