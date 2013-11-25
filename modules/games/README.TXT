
//==================================================================================================
//*	HOW TO ADD A GAME TO THIS MODULE
//==================================================================================================

Simple falsh and canvas games can be easily incorporated into this module.  Suppose your game is 
called 'mygame': create a new kapenta package for your game and copy its files under:

	~/modules/games/assets/mygame/

You will also need to create an install script:

	~/modules/games/inc/mygame.install.php

Which will contain two functions:

	function /* (string) */ games_install_mygame() { ... }
	function /* (string) */ games_install_mygame_status_report() { ... }

The first will be run to install your game (set up registry, perform downloads, etc), the second
will be run when displaying the installation status of the games module on the admin console, and
is an opportunity to check versions, etc.

Support for per-game cron scripts may be added in future.

	~/modules/games/inc/mygame.cron.php
	function /* (string) */ games_cron_daily_mygame() { ... }

In order for your game to show up in the games list you will need a summary view:

	~/modules/games/views/summary_mygame.fn.php
	function /* (string) */ games_summary_mygame(/* array */ $args) { ... }

In addition, you game will need to be listed in the registry, in the games.list field:

	games.list := "game1,game2,mygame,anothergame,game3"

A Games_Register utility object is available to register, reorder and unregister the games 
available on a particular instance.
