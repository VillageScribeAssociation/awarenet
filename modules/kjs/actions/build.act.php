<?php

	require_once($kapenta->installPath . "modules/kjs/inc/builder.class.php");

//--------------------------------------------------------------------------------------------------
//*	parse the KJS build file and produce a single Javascript file
//--------------------------------------------------------------------------------------------------
//TODO: cache the output of this

	$buildFile = 'data/kjs/build.txt';

	$builder = new KJS_Builder($buildFile, true);

	$kapenta->fs->put('data/kjs/lastbuild.js', $builder->output);

	echo $builder->output;

?>
