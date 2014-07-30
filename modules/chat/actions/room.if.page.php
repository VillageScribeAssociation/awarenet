<? /*
<page>
	<template>iframe.template.php</template>
	<title>:: %%websiteName%% :: Chat :: room ::</title>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:chat::menu:]]</menu2>
	<content>
		<script src='%%serverPath%%modules/chat/js/room.js'></script>
		<script language='Javascript'>
			var rm = new Chat_RoomClient('%%UID%%');
			rm.userUID = "%%currentUserUID%%";
			rm.userName = "[[:users::name::userUID=%%currentUserUID%%:]]";			

			function rmTimeout() {
				rm.tick();
				setTimeout('rmTimeout();', 500);
				kutils.resizeIFrame();
			}

			rmTimeout();

		</script>
		<br/>
		[[:chat::leavebutton::roomUID=%%UID%%:]]
	</content>
	<head></head>
	<script></script>
	<jsinit></jsinit>
	<nav1></nav1>
	<nav2></nav2>
	<nav2></nav2>
	<banner></banner>
	<breadcrumb></breadcrumb>
</page>

*/ ?>
