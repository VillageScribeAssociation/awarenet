<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]userName[`|pc][`|pc] (profile)</title>
	<content>[[:theme::navtitlebox::label=User Profile::width=570:]]
[[:users::profile:]]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Add Wall Post::width=570::toggle=divWallAddComment::hidden=yes:]]
[`|lt]div id=[`|sq]divWallAddComment[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
[[:comments::addcommentform::refModule=users::refModel=users[`|us]user::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Wall::width=570::toggle=divWall:]]
[`|lt]div id=[`|sq]divWall[`|sq][`|gt]
[[:comments::list::refModule=users::refModel=users[`|us]user::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Friends::width=570::toggle=divUserFriends:]]
[`|lt]div id=[`|sq]divUserFriends[`|sq][`|gt]
[[:users::listfriends::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]
</content>
	<nav1>[`|pc][`|pc]profilePicture[`|pc][`|pc]
[`|pc][`|pc]chatButton[`|pc][`|pc]

[[:badges::awarded::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]

[[:theme::navtitlebox::label=Groups::toggle=divUserGroups:]]
[`|lt]div id=[`|sq]divUserGroups[`|sq][`|gt]
[[:groups::listusergroupsnav::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Projects::toggle=divUserProjects:]]
[`|lt]div id=[`|sq]divUserProjects[`|sq][`|gt]
[[:projects::listuserprojectsnav::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:users::friendrequestprofilenav::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[[:badges::award::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
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