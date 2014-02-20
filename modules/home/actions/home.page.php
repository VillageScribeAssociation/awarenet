<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>home.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - welcome</title>

    <content>

		[[:theme::registerbutton:]]

		<a href='%%serverPath%%schools/list/'>
		<img src='%%serverPath%%themes/%%defaultTheme%%/images/buttons/btn_schools.png' border='0' />
		</a>
		<br/><br/>

		<a href='%%serverPath%%projects/'>
		<img src='%%serverPath%%themes/%%defaultTheme%%/images/buttons/btn_preview.png' border='0' />
		</a>
		<br/><br/>

		<div class='blockPictures'>
		<form name='siteLogin' method='POST' action='%%serverPath%%picturelogin/picturelogin/'>
		<input type='hidden' name='action' value='Pictures' />

		<table border='1' width='100%'>
		  <tr>
		    <td><b><FONT COLOR="#FF0000">Password through Pictures</FONT></br></td>
		    <td align='center'><input type='submit' value='Pictures' /></td>
		  </tr>
		</table>
		</form>
		</div>

		<div class='block'>
		<form name='siteLogin' method='POST' action='%%serverPath%%users/login/'>
		<input type='hidden' name='action' value='login' />

		<table noborder width='100%'>
		  <tr>
		    <td width='80px'><b>Username</b></td>
		    <td><input type='text' name='user' value='' size='20' style='width: 100%;' /></td>
		  </tr>
		  <tr>
		    <td><b>Password</b></td>
		    <td><input type='password' name='pass' size='20' value='' style='width: 100%;' /></td>
		  </tr>
		  <tr>
		    <td></td>
		    <td align='right'><input type='submit' value='Log In' /></td>
		  </tr>
		</table>
		</form>
		</div>
.
		<br/><br/><br/>
				
		<script src='%%serverPath%%modules/videos/js/flowplayer-3.2.6.min.js'></script>

		<h2>Tutorials</h2> 
		<a href='#playTutorial1' id="play1">
			<img src='%%serverPath%%modules/tutorials/assets/awarenet_getting_started.png' border='0' 'height="200" width="300"' />
		</a>
		<script type="text/javascript">
		
			$('#play1').click(function() {
			myBlockTag = "[[" + 
			":videos::player::width=600::height=400::filename=modules/tutorials/assets/awarenet_getting_started.mp4" +
			"::cover=modules/tutorials/assets/awarenet_getting_started.png::autoPlay=yes:]]";
			myDivId = "divLeftContent";
			klive.removeBlock(myBlockTag, false);
			klive.bindDivToBlock(myDivId, myBlockTag, false);
			// disable the click
			return false;		  
			});		
			  	
		</script>	
		<br/><br/>

		<a href='#playTutorial2' id="play2">
			<img src='%%serverPath%%modules/tutorials/assets/awarenet_tutorial_2.png' border='0' 'height="200" width="300"' />
		</a>
		<script type="text/javascript">
		  	$('#play2').click(function() {
				myBlockTag = "[[" + 
				":videos::player::width=600::height=400::filename=modules/tutorials/assets/awarenet_tutorial_2.mp4" +
				"::cover=modules/tutorials/assets/awarenet_tutorial_2.png::autoPlay=yes:]]";
				myDivId = "divLeftContent";
				klive.removeBlock(myBlockTag, false);
				klive.bindDivToBlock(myDivId, myBlockTag, false);
				// disable the click
				return false;		  
			});
		</script>	

		<h2>Technical Background</h2>
		<a href='#playTutorial3' id="play3">
			<img src='%%serverPath%%modules/tutorials/assets/awarenet_intro.png' border='0' 'height="200" width="300"' />
		</a>
		<script type="text/javascript">
		  	$('#play3').click(function() {
				myBlockTag = "[[" + 
				":videos::player::width=600::height=400::raUID=awarenet-intro.flv::autoPlay=yes:]]";
				myDivId = "divLeftContent";
				klive.removeBlock(myBlockTag, false);
				klive.bindDivToBlock(myDivId, myBlockTag, false);
				// disable the click
				return false;		  
			});
		</script>	

	</content>
	<nav1></nav1>
	<nav2></nav2>
	<script>awareNetChat = false[`|sc]</script>
	<jsinit>galleryNav[`|us]init()[`|sc]
msgSubscribe([`|sq]comments-gallery-[`|pc][`|pc]imageUID[`|pc][`|pc][`|sq], msgh[`|us]comments)[`|sc]
msgh[`|us]commentsRefresh()[`|sc]</jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2></menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=awareNet - home:]]</breadcrumb>
</page>

*/ ?>