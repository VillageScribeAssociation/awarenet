<?

//--------------------------------------------------------------------------------------------------
//*	Functionality helping to integrate picturelogin into other pages
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//|	return the style string
//--------------------------------------------------------------------------------------------------
	function getPictureLoginStyle() {
		$style = '@charset "UTF-8";
	 
			@font-face {
			  font-family: "untitled-font-1";
			  src:url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.eot");
			  src:url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.eot?#iefix") format("embedded-opentype"),
				url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.ttf") format("truetype"),
				url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.svg#untitled-font-1") format("svg"),
				url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.woff") format("woff");
			  font-weight: normal;
			  font-style: normal;
			}
	 
			[data-icon]:before {
			  font-family: "untitled-font-1" !important;
			  content: attr(data-icon);
			  font-style: normal !important;
			  font-weight: normal !important;
			  font-variant: normal !important;
			  font-size: 235%;
			  text-transform: none !important;
			  speak: none;
			  line-height: 1;
			  -webkit-font-smoothing: antialiased;
			  -moz-osx-font-smoothing: grayscale;
			}
	 
			[class^="icon-"]:before,
			[class*=" icon-"]:before {
			  font-family: "untitled-font-1" !important;
			  font-style: normal !important;
			  font-weight: normal !important;
			  font-variant: normal !important;
			  text-transform: none !important;
			  speak: none;
			  line-height: 1;
			  -webkit-font-smoothing: antialiased;
			  -moz-osx-font-smoothing: grayscale;
			} 

	
		#source
		{float:left; width:100%; height:126px; margin:1px;padding:1px;border:1px solid #aaaaaa;}

		#drop1
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop2
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop3
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop4
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop5
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop6
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop7
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop8
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop9
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop10
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop11
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop12
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop13
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop14
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop15
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop16
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop17
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop18
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop19
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
		#drop20
		{float:left; width:35px; height:35px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	';	
		return $style;
	}

	function getPictureLoginScript() {
		global $kapenta;
		$script = '<script src="' . $kapenta->serverPath . 'modules/picturelogin/js/dragdrop.js">
		</script>';
		
		return $script;
	}
	
?>
