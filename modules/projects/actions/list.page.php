<? /*
<page>
<template>twocol-rightnav.template.php</template>
<content>[[:theme::navtitlebox::width=570::label=Projects:]]
[[:projects::listall:]]</content>
<title>awareNet - all projects</title>
<script></script>
<nav1>
[[:theme::navtitlebox::label=Create New Project::toggle=divNewProjectForm::hidden=yes:]]
[`|lt]div id='divNewProjectForm' style='visibility: hidden; display: none;'[`|gt]
[[:projects::newprojectform:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Project Categories::toggle=divProjectTags:]]
[`|lt]div id='divProjectTags'[`|gt]
[[:tags::modelcloud::refModule=projects::refModel=Projects_Project:]]
[`|lt]/div[`|gt]
[`|lt]br/[`|gt]
[`|lt]br/[`|gt]

[[:theme::navtitlebox::label=Most Active::toggle=divActiveProjects:]]
[`|lt]div id='divActiveProjects'[`|gt]
[[:projects::mostactivenav::num=10:]]
[`|lt]/div[`|gt]
</nav1>
<nav2></nav2>
<banner></banner>
<head></head>
<menu1>[[:home::menu:]]</menu1>
<menu2>[[:projects::menu:]]</menu2>
<section></section>
<subsection></subsection>
<breadcrumb>[[:theme::breadcrumb::label=Projects - ::link=/projects/:]]
[[:theme::breadcrumb::label=All::link=/projects/:]]</breadcrumb>
</page>\n*/ ?>
