<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]groupName[`|pc][`|pc] (group)</title>
	<content>
		[[:theme::navtitlebox::width=570::label=About:]]
		[[:groups::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		[`|lt]br/[`|gt]
		[[:theme::navtitlebox::width=570::label=Announcements:]]
		[`|lt]br/[`|gt]
		[[:announcements::list::refModule=groups::refModel=groups[`|us]group::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::num=10:]]
		[[:announcements::newlink::refModule=groups::refModel=groups[`|us]group::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
[`|lt]br/[`|gt]
	</content>
	<nav1>
		[[:theme::navtitlebox::label=Schools::toggle=divActiveSchools:]]
		<div id='divActiveSchools'>
		[[:groups::listschoolsnav::groupUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		</div>
		<br/>

		[[:theme::navtitlebox::label=Members:]]
		[[:groups::listmembersnav::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		[`|lt]br/[`|gt]
		[[:groups::listadmins::groupUID=[`|pc][`|pc]raUID[`|pc][`|pc]::target=[`|us]parent:]]
	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:groups::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Groups - ::link=/groups/:]]
[[:theme::breadcrumb::label=[`|pc][`|pc]groupName[`|pc][`|pc]::link=/groups/[`|pc][`|pc]raUID[`|pc][`|pc]:]]</breadcrumb>
</page>

*/ ?>
