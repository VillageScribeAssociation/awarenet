
//--------------------------------------------------------------------------------------------------
//*	AJAX to connect like button with server-side module
//--------------------------------------------------------------------------------------------------
//+	note that like buttons should be inside a span called spanLike%%refUID%%

//--------------------------------------------------------------------------------------------------
//|	note that the current user 'likes' something
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object being liked [string]
//arg: refUID - UID of object being liked [string]

function like_assert(refModule, refModel, refUID) {
	//alert("assert like " + refModule + "::" + refModel + "::" + refUID);

	this.refModule = refModule;
	this.refModel = refModel;
	this.refUID = refUID;

	that = this;	

	var url = jsServerPath + 'like/likejs/';

	var cbFn = function(responseText, status) {
		if (-1 != responseText.indexOf('<ok/>')) {
			like_setunlike(that.refModule, that.refModel, that.refUID);
		} else {
			alert(responseText);
			like_setlike(that.refModule, that.refModel, that.refUID);
		}
	}

	params = "refModule=" + refModule + "&refModel=" + refModel + "&refUID=" + refUID;
	like_setbusy(refModule, refModel, refUID);
	kutils.httpPost(url, params, cbFn);
}

//--------------------------------------------------------------------------------------------------
//|	note that the current user 'unlikes' something
//--------------------------------------------------------------------------------------------------
//arg: refModule - name of a kapenta module [string]
//arg: refModel - type of object being liked [string]
//arg: refUID - UID of object being liked [string]

function like_unassert(refModule, refModel, refUID) {
	//alert("assert unlike " + refModule + "::" + refModel + "::" + refUID);

	this.refModule = refModule;
	this.refModel = refModel;
	this.refUID = refUID;

	that = this;	

	var url = jsServerPath + 'like/unlikejs/';

	var cbFn = function(responseText, status) {
		if (-1 != responseText.indexOf('<ok/>')) {
			like_setlike(that.refModule, that.refModel, that.refUID);
		} else {
			alert(responseText);
			like_setunlike(that.refModule, that.refModel, that.refUID);
		}
	}

	params = "refModule=" + refModule + "&refModel=" + refModel + "&refUID=" + refUID;
	like_setbusy(refModule, refModel, refUID);
	kutils.httpPost(url, params, cbFn);
}

//--------------------------------------------------------------------------------------------------
//|	disable like/unlike link while AJAX request processes
//--------------------------------------------------------------------------------------------------
//returns: true on success, false on failure [bool]

function like_setbusy(refModule, refModel, refUID) {
	//alert('change link to unlike');
	var theSpan = document.getElementById('spanLike' + refUID);	
	if (!theSpan) { return false; }
	var throbber = "themes/clockface/images/throbber-inline.gif";
	theSpan.innerHTML = "<img src='" + jsServerPath + throbber + "'/>";
	return true;
}

//--------------------------------------------------------------------------------------------------
//|	update a 'like' link on the page to 'unlike'
//--------------------------------------------------------------------------------------------------
//returns: true on success, false on failure [bool]

function like_setunlike(refModule, refModel, refUID) {
	//alert('change link to unlike');
	var theSpan = document.getElementById('spanLike' + refUID);	
	if (!theSpan) { return false; }
	
	var onClick = "like_unassert('" + refModule + "', '" + refModel + "', '" + refUID + "')";
	theSpan.innerHTML = "<a href='javascript:void(0);' onClick=\"" + onClick + "\">[unlike]</a>";
	return true;
}

//--------------------------------------------------------------------------------------------------
//|	update a 'unlike' link on the page to 'like'
//--------------------------------------------------------------------------------------------------
//returns: true on success, false on failure [bool]

function like_setlike(refModule, refModel, refUID) {
	//alert('change link to like');
	var theSpan = document.getElementById('spanLike' + refUID);	
	if (!theSpan) { return false; }
	
	var onClick = "like_assert('" + refModule + "', '" + refModel + "', '" + refUID + "')";
	theSpan.innerHTML = "<a href='javascript:void(0);' onClick=\"" + onClick + "\">[like]</a>";
	return true;
}
