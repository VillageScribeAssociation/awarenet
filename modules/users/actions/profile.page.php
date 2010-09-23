<? /*
<page>
<template>twocol-rightnav.template.php</template>
<content>[[:theme::navtitlebox::label=User Profile::width=570:]]
[[:users::profile:]]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Add Wall Post::width=570::toggle=divWallAddComment::hidden=yes:]]
[`|lt]div id='divWallAddComment' style='visibility: hidden; display: none;'[`|gt]
[[:comments::addcommentform::refModule=users::refModel=Users_User::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=/users/profile/[`|pc][`|pc]userRa[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Wall::width=570::toggle=divWall:]]
[`|lt]div id='divWall'[`|gt]
[[:comments::list::refModule=users::refModel=Users_User::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Friends::width=570::toggle=divUserFriends:]]
[`|lt]div id='divUserFriends'[`|gt]
[[:users::listfriends::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]
</content>
<title>awareNet - [`|pc][`|pc]userName[`|pc][`|pc] (profile)</title>
<script></script>
<nav1>[`|pc][`|pc]profilePicture[`|pc][`|pc]
[`|pc][`|pc]chatButton[`|pc][`|pc]

[[:theme::navtitlebox::label=Groups::toggle=divUserGroups:]]
[`|lt]div id='divUserGroups'[`|gt]
[[:groups::listusergroupsnav::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Projects::toggle=divUserProjects:]]
[`|lt]div id='divUserProjects'[`|gt]
[[:projects::listuserprojectsnav::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:users::friendrequestprofilenav::userUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]</nav1>
<nav2></nav2>
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
