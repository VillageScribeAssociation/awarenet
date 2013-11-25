<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]userName[`|pc][`|pc] (profile)</title>
	<content>

		<div class='block'>
		[[:theme::navtitlebox::label=User Profile::toggle=divProfile:]]
		<div id='divProfile'>
		[[:users::profile:]]
		</div>
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Add Wall Post::width=570::toggle=divWallAddComment::hidden=yes:]]
		[`|lt]div id=[`|sq]divWallAddComment[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:comments::addcommentform::refModule=users::refModel=users[`|us]user::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]


		[[:theme::navtitlebox::label=Wall::width=570::toggle=divWall:]]
		<div class='spacer'></div>
		[`|lt]div id=[`|sq]divWall[`|sq][`|gt]
		[[:live::river::mod=comments::view=list::pv=pageNo::allow=refModule|refModel|refUID::refModule=users::refModel=users[`|us]user::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Friends::width=570::toggle=divUserFriends:]]
		<div id='divUserFriends'>
		[[:users::listfriendsgrouped::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>
	</content>
	<nav1>
		<div class='block'>
		[`|pc][`|pc]profilePicture[`|pc][`|pc]
		</div>
		<br/>		

		[[:badges::awarded::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]

		[[:groups::listusergroupsnav::userUID=%%UID%%::ntb=yes:]]

		[[:projects::listuserprojectsnav::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]

		[[:users::friendrequestprofilenav::userUID=%%UID%%:]]

		[[:badges::award::userUID=%%UID%%:]]

</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:users::menu::userUID=[`|pc][`|pc]userUID[`|pc][`|pc]:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=People - ::link=/users/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]userName[`|pc][`|pc] - ::link=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
[[:theme::breadcrumb::label=Profile::link=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
