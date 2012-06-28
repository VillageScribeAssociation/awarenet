<? /*
[[:theme::navtitlebox::width=570::label=Image:]]
<!-- <a href='%%serverPath%%images/full/%%alias%%'>
<img src='%%serverPath%%images/width570/%%alias%%' border='0' />
</a> -->

<a href='#' id='link%%UID%%'>
<img src='%%serverPath%%images/width570/%%alias%%' border='0' />
</a>

<br/>
[[:theme::navtitlebox::width=570::label=About::toggle=divImageAbout:]]
<div id='divImageAbout'>
<h1>%%title%%</h1>
<p>%%caption%%</p>

<small>
	<a href='%%serverPath%%images/full/%%alias%%'>[show original size]</a>
	<a href='%%serverPath%%images/setprofilepicture/%%alias%%'>[set as my profile picture]</a>
	<a href='%%serverPath%%images/setbackground/%%alias%%'>[set as my page background]</a>
	[[:like::link::refModule=images::refModel=images_image::refUID=%%UID%%:]]
	[[:sketchpad::sketchlink::imageUID=%%UID%%:]]
</small>

%%userEditBlock%%
</div>
<br/>
*/ ?>
