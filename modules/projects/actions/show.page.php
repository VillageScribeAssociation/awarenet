<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - [`|pc][`|pc]projectTitle[`|pc][`|pc] (project)</title>
	<content>
		<script language='Javascript'> var project = new Projects_Editor('%%UID%%'); </script>

		[[:theme::navtitlebox::width=570::label=Project:]]
		<div id='divProject%%UID%%'>
		[[:projects::show::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		</div>

		[[:projects::addsectionform::projectUID=%%raUID%%:]]
	</content>
	<nav1>

		[[:projects::listmembersnav::raUID=[`|pc][`|pc]raUID[`|pc][`|pc]:]]

		[[:projects::requestsjoinnav::projectUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		[[:projects::askjoinnav::projectUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]

		[[:live::manageattachments::refModule=projects::refModel=projects_project::refUID=%%UID%%:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Add A Comment::toggle=divAddCommentNav::hidden=yes:]]
		[`|lt]div id=[`|sq]divAddCommentNav[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:comments::addcommentformnav::refModule=projects::refModel=projects[`|us]project::refUID=[`|pc][`|pc]UID[`|pc][`|pc]::return=/projects/[`|pc][`|pc]raUID[`|pc][`|pc]:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Recent Comments::toggle=divCommentsNav:]]
		[`|lt]div id=[`|sq]divCommentsNav[`|sq][`|gt]

		[[:live::river::rivermodule=comments::riverview=listnav::riverpagevar=pageNo::allow=refModule|refModel|refUID::refModule=projects::refModel=projects[`|us]project::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]

		[`|lt]/div[`|gt]
		</div>
		[`|lt]br/[`|gt]

		[[:projects::historynav::UID=[`|pc][`|pc]UID[`|pc][`|pc]::num=1::label=Last Edit:]]

		<div class='block'>
		[[:theme::navtitlebox::label=Tags (this project)::toggle=divTagsNav:]]
		[`|lt]div id=[`|sq]divTagsNav[`|sq][`|gt]
		[[:tags::cloud::url=projects/tag/::refModule=projects::refModel=projects[`|us]project::refUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Their Other Projects::toggle=divOtherProjects::hidden=yes:]]
		<div id='divOtherProjects' style='visibility: hidden; display: none;'>
		[[:projects::listsamembersanav::UID=[`|pc][`|pc]UID[`|pc][`|pc]:]]
		</div>
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

		<div class='block'>
		[[:theme::navtitlebox::label=Tags (all projects)::toggle=divTagsAllNav:]]
		[`|lt]div id=[`|sq]divTagsNav[`|sq][`|gt]
		[[:tags::modelcloud::refModule=projects::refModel=projects[`|us]project:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		</div>
		[`|lt]br/[`|gt]

	</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head><script src='%%serverPath%%modules/projects/js/editor.js'></script></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:projects::menu::projectUID=[`|pc][`|pc]UID[`|pc][`|pc]:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>
		[[:theme::breadcrumb::label=Projects - ::link=/projects/:]]
		[[:theme::breadcrumb::label=[`|pc][`|pc]projectTitle[`|pc][`|pc]::link=/projects/[`|pc][`|pc]projectRa[`|pc][`|pc]:]]
	</breadcrumb>
</page>

*/ ?>
