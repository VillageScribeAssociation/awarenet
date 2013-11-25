<? /*
<page>
<template>twocol-rightnav.template.php</template>
<content>
		<div class='block'>
		[[:theme::navtitlebox::width=570::label=Schools:]]
		[`|lt]h1[`|gt]Schools By region[`|lt]/h1[`|gt]
		</div>
		<div class='spacer'></div>
		[[:schools::geographic:]]
</content>
<title>%%websiteName%% - Schools by Region</title>
<script></script>
<nav1>
	<div class='block'>
	[[:theme::navtitlebox::label=All School Categories::toggle=divTags:]]
	[`|lt]div id='divTags'[`|gt]
	[[:tags::modelcloud::refModule=schools::refModel=schools_school:]]
	[`|lt]/div[`|gt]
	<div class='foot'></div>
	</div>
	[`|lt]br/[`|gt]

	<div class='block'>
	[[:theme::navtitlebox::label=All Schools By Name::toggle=divAllByName:]]
	[`|lt]div id='divAllByName'[`|gt]
	[[:schools::listalllinksnav:]]
	[`|lt]/div[`|gt]
	<div class='foot'></div>
	</div>
	[`|lt]br/[`|gt]
</nav1>
<nav2></nav2>
<banner></banner>
<head></head>
<menu1>[[:home::menu:]]</menu1>
<menu2>[[:schools::menu:]]</menu2>
<section></section>
<subsection></subsection>
<breadcrumb>[[:theme::breadcrumb::label=Schools - ::link=/schools/:]]
[[:theme::breadcrumb::label=By Region::link=/schools/geographic/:]]</breadcrumb>
</page>
*/ ?>
