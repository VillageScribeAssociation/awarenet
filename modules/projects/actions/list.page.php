<? header('HTTP/1.1 403 Forbidden'); exit('403 - forbidden'); /*
<?xml version="1.0" ?>

<page>
	<template>twocol-rightnav.template.php</template>
	<title>[`|pc][`|pc]websiteName[`|pc][`|pc] - all projects</title>
	<content>
		[[:theme::navtitlebox::width=570::label=Projects:]]
		[[:live::river::rivermodule=projects::riverview=summarylist::riverpagevar=page::allow=num|pagination|status::num=7::status=notclosed::pagination=no:]]
	</content>
	<nav1>
		[[:theme::navtitlebox::label=Create New Project::toggle=divNewProjectForm::hidden=yes:]]
		[`|lt]div id=[`|sq]divNewProjectForm[`|sq] style=[`|sq]visibility: hidden[`|sc] display: none[`|sc][`|sq][`|gt]
		[[:projects::newprojectform:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=Project Categories::toggle=divProjectTags:]]
		[`|lt]div id=[`|sq]divProjectTags[`|sq][`|gt]
		[[:tags::modelcloud::refModule=projects::refModel=projects[`|us]project:]]
		[`|lt]/div[`|gt]
		<div class='foot'></div>
		[`|lt]br/[`|gt]

		[[:theme::navtitlebox::label=Most Active::toggle=divActiveProjects:]]
		[`|lt]div id=[`|sq]divActiveProjects[`|sq][`|gt]
		[[:projects::mostactivenav::num=10:]]
		[`|lt]/div[`|gt]
</nav1>
	<nav2></nav2>
	<script></script>
	<jsinit></jsinit>
	<banner></banner>
	<head></head>
	<menu1>[[:home::menu:]]</menu1>
	<menu2>[[:projects::menu:]]</menu2>
	<section></section>
	<subsection></subsection>
	<breadcrumb>[[:theme::breadcrumb::label=Projects - ::link=/projects/:]]
[[:theme::breadcrumb::label=All::link=/projects/:]]</breadcrumb>
</page>

*/ ?>
