<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]groupName[`|pc][`|pc] (group)</title>
	<content>
		<div class='block'>
		[[:theme::navtitlebox::width=570::label=About:]]
		[[:groups::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		</div>
		<br/>

		[[:theme::navtitlebox::width=570::label=Announcements::toggle=divAnnouncements:]]
		<div id='divAnnouncements'>
		<div class='spacer'></div>
		[[:announcements::river::refModule=groups::refModel=groups_group::refUID=%%UID%%::num=2:]]
		</div>
		<div class='foot'></div>
		<br/>

		<a name='discussion'></a>
		[[:theme::navtitlebox::label=Discussion::width=570::toggle=divGroupComments:]]
		<div id='divGroupComments'>
		<div class='spacer'></div>
		[[:comments::river::refModule=groups::refModel=groups_group::refUID=%%UID%%::num=10:]]
		</div>
		<div class='foot'></div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Add a comment::width=570::toggle=divAddGroupComment::hidden=yes:]]
		<div id='divAddGroupComment' style='visibility: hidden; display: none;'>
		[[:comments::addcommentform::refModule=groups::refModel=groups_group::refUID=%%UID%%::return=groups/%%raUID%%:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>

	</content>
	<nav1>
		<div class='block'>
		[[:theme::navtitlebox::label=Schools::toggle=divActiveSchools:]]
		<div id='divActiveSchools'>
		[[:groups::listschoolsnav::groupUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>

		<div class='block'>
		[[:theme::navtitlebox::label=Members::toggle=divMembersNav:]]
		<div id='divMembersNav'>
		[[:groups::listmembersnav::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		[`|lt]br/[`|gt]
		[[:groups::listadmins::groupUID=[`|pc][`|pc]raUID[`|pc][`|pc]::target=[`|us]parent:]]
		</div>
		<div class='foot'></div>
		</div>
		<br/>
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:groups::menu::action=show::UID=%%UID%%:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Groups - ::link=/groups/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]groupName[`|pc][`|pc]::link=/groups/[`|pc][`|pc]raUID[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
