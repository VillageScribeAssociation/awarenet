<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>window.template.php</template>
	<title>awareNet - [`|pc][`|pc]shell[`|pc][`|pc]</title>
	<content>
	<script>
		jsShellSession = '%%jsShellSession%%';
	</script>

	[[:p2p::selectpeer:]]
	<div 
		id='divHistory' 
		style='width: 200px; height: 60px; position: absolute; overflow: auto; padding-top: 5px;'
	>
	<div class='chatmessagegreen'>
	Logged in as [[:users::namelink::userUID=%%jsUserUID%%::target=_parent:]]<br/>
	Remote shell session %%jsShellSession%%
	</div>

	<div class='chatmessageblack'>
	Type <tt>live.help</tt> for a list of available commands.
	</div>
	</div>

	<textarea 
		name='content' 
		id='content'
		style='width: 200px; height: 60px; left: 0px; top: 100px; position:absolute;'
	></textarea>
	<br/>
</content>
	<nav1></nav1>
	<nav2></nav2>
	<script>
		//------------------------------------------------------------------------------------------
		//	create the shellwindow object
		//------------------------------------------------------------------------------------------

		kwnd.onLoad = function() { kshellwindow = new Live_RemoteShellWindow('%%jsShellSession%%'); }

    </script>
	<jsinit>
    </jsinit>
	<banner></banner>
	<head><script src='%%serverPath%%modules/live/js/remoteshellwindow.js'></script></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:abuse::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
