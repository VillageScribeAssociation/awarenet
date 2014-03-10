<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//	discover and delete images which do not exist on either server
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authentication
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$listeu = "
184709872312493152 - data/images/1/8/4/184709872312493152.jpg does not exist on local server.
112166452863516971 - data/images/1/1/2/112166452863516971.jpg does not exist on local server.
198973078218398657 - data/images/1/9/8/198973078218398657.jpg does not exist on local server.
178556432413361141 - data/images/1/7/8/178556432413361141.jpg does not exist on local server.
201012151015618072 - data/images/2/0/1/201012151015618072.jpg does not exist on local server.
395453046104664883 - data/images/3/9/5/395453046104664883.jpg does not exist on local server.
112744419614423329 - data/images/1/1/2/112744419614423329.jpg does not exist on local server.
998307799131490029 - data/images/9/9/8/998307799131490029.jpg does not exist on local server.
187941414735855352 - data/images/1/8/7/187941414735855352.jpg does not exist on local server.
104018138559834807 - data/images/1/0/4/104018138559834807.jpg does not exist on local server.
102499340356768363 - data/images/1/0/2/102499340356768363.jpg does not exist on local server.
108999442180855738 - data/images/1/0/8/108999442180855738.jpg does not exist on local server.
159653801611468950 - data/images/1/5/9/159653801611468950.jpg does not exist on local server.
259967471119493529 - data/images/2/5/9/259967471119493529.jpg does not exist on local server.
674398732226026685 - data/images/6/7/4/674398732226026685.jpg does not exist on local server.
363831936205838211 - data/images/3/6/3/363831936205838211.jpg does not exist on local server.
381692683191889595 - data/images/3/8/1/381692683191889595.jpg does not exist on local server.
114730159517695878 - data/images/1/1/4/114730159517695878.jpg does not exist on local server.
822343848368721970 - data/images/8/2/2/822343848368721970.jpg does not exist on local server.
170416521812476750 - data/images/1/7/0/170416521812476750.jpg does not exist on local server.
630556499126622189 - data/images/6/3/0/630556499126622189.jpg does not exist on local server.
489082881752318308 - data/images/4/8/9/489082881752318308.jpg does not exist on local server.
147060735910111367 - data/images/1/4/7/147060735910111367.jpg does not exist on local server.
149571784221216896 - data/images/1/4/9/149571784221216896.jpg does not exist on local server.
136859865416834298 - data/images/1/3/6/136859865416834298.jpg does not exist on local server.
416416027104130289 - data/images/4/1/6/416416027104130289.jpg does not exist on local server.
116654792717878284 - data/images/1/1/6/116654792717878284.jpg does not exist on local server.
128470618985435325 - data/images/1/2/8/128470618985435325.jpg does not exist on local server.
207517281122478525 - data/images/2/0/7/207517281122478525.jpg does not exist on local server.
145547579299996483 - data/images/1/4/5/145547579299996483.jpg does not exist on local server.
389019112833020992 - data/images/3/8/9/389019112833020992.jpg does not exist on local server.
904371555153117891 - data/images/9/0/4/904371555153117891.jpg does not exist on local server.
142858230599200855 - data/images/1/4/2/142858230599200855.jpg does not exist on local server.
249448570262576140 - data/images/2/4/9/249448570262576140.jpg does not exist on local server.
187270872235014461 - data/images/1/8/7/187270872235014461.jpg does not exist on local server.
162770845411716234 - data/images/1/6/2/162770845411716234.jpg does not exist on local server.
203491260116116873 - data/images/2/0/3/203491260116116873.jpg does not exist on local server.
211869712841070450 - data/images/2/1/1/211869712841070450.jpg does not exist on local server.
890287478749627273 - data/images/8/9/0/890287478749627273.jpg does not exist on local server.
179383277816518405 - data/images/1/7/9/179383277816518405.jpg does not exist on local server.
609702880475556600 - data/images/6/0/9/609702880475556600.jpg does not exist on local server.
110965668635566190 - data/images/1/1/0/110965668635566190.jpg does not exist on local server.
113797285720829570 - data/images/1/1/3/113797285720829570.jpg does not exist on local server.
629852621125817275 - data/images/6/2/9/629852621125817275.jpg does not exist on local server.
530310251748347698 - data/images/5/3/0/530310251748347698.jpg does not exist on local server.
643032647193717503 - data/images/6/4/3/643032647193717503.jpg does not exist on local server.
694773348101221895 - data/images/6/9/4/694773348101221895.jpg does not exist on local server.
115762424317828426 - data/images/1/1/5/115762424317828426.jpg does not exist on local server.
156988098750763962 - data/images/1/5/6/156988098750763962.jpg does not exist on local server.
248897071715934974 - data/images/2/4/8/248897071715934974.jpg does not exist on local server.
418149260211452967 - data/images/4/1/8/418149260211452967.jpg does not exist on local server.
815131667836382068 - data/images/8/1/5/815131667836382068.jpg does not exist on local server.
209670489847316898 - data/images/2/0/9/209670489847316898.jpg does not exist on local server.
601356892156698059 - data/images/6/0/1/601356892156698059.jpg does not exist on local server.
524513125743908835 - data/images/5/2/4/524513125743908835.jpg does not exist on local server.
145268103310118104 - data/images/1/4/5/145268103310118104.jpg does not exist on local server.
196286107914051046 - data/images/1/9/6/196286107914051046.jpg does not exist on local server.
927677991188157426 - data/images/9/2/7/927677991188157426.jpg does not exist on local server.
115848830071231279 - data/images/1/1/5/115848830071231279.jpg does not exist on local server.
625438624185600497 - data/images/6/2/5/625438624185600497.jpg does not exist on local server.
128317614384365879 - data/images/1/2/8/128317614384365879.jpg does not exist on local server.
453240606184171233 - data/images/4/5/3/453240606184171233.jpg does not exist on local server.
432612723106563205 - data/images/4/3/2/432612723106563205.jpg does not exist on local server.
875414506432100534 - data/images/8/7/5/875414506432100534.jpg does not exist on local server.
207194086013265411 - data/images/2/0/7/207194086013265411.jpg does not exist on local server.
807644135174388446 - data/images/8/0/7/807644135174388446.jpg does not exist on local server.
502763092114058895 - data/images/5/0/2/502763092114058895.jpg does not exist on local server.
162524663471091827 - data/images/1/6/2/162524663471091827.jpg does not exist on local server.
171210735911031162 - data/images/1/7/1/171210735911031162.jpg does not exist on local server.
121999918018436037 - data/images/1/2/1/121999918018436037.jpg does not exist on local server.
295141751921756981 - data/images/2/9/5/295141751921756981.jpg does not exist on local server.
615087759200871622 - data/images/6/1/5/615087759200871622.jpg does not exist on local server.
800122230143801276 - data/images/8/0/0/800122230143801276.jpg does not exist on local server.
194275658315148515 - data/images/1/9/4/194275658315148515.jpg does not exist on local server.
791230551201514787 - data/images/7/9/1/791230551201514787.jpg does not exist on local server.
152877764631094936 - data/images/1/5/2/152877764631094936.jpg does not exist on local server.
580871027121154058 - data/images/5/8/0/580871027121154058.jpg does not exist on local server.
210498707671568939 - data/images/2/1/0/210498707671568939.jpg does not exist on local server.
193322436924809693 - data/images/1/9/3/193322436924809693.jpg does not exist on local server.
761739503156282973 - data/images/7/6/1/761739503156282973.jpg does not exist on local server.
248993588116973557 - data/images/2/4/8/248993588116973557.jpg does not exist on local server.
191088431499910849 - data/images/1/9/1/191088431499910849.jpg does not exist on local server.
185217504020161022 - data/images/1/8/5/185217504020161022.jpg does not exist on local server.
174691385051912404 - data/images/1/7/4/174691385051912404.jpg does not exist on local server.
443199102604310849 - data/images/4/4/3/443199102604310849.jpg does not exist on local server.
465511175624701485 - data/images/4/6/5/465511175624701485.jpg does not exist on local server.
809022895972899481 - data/images/8/0/9/809022895972899481.jpg does not exist on local server.
120511219660327436 - data/images/1/2/0/120511219660327436.jpg does not exist on local server.
189100096012863772 - data/images/1/8/9/189100096012863772.jpg does not exist on local server.
808379414954958231 - data/images/8/0/8/808379414954958231.jpg does not exist on local server.
389478702855243009 - data/images/3/8/9/389478702855243009.jpg does not exist on local server.
751779278199233466 - data/images/7/5/1/751779278199233466.jpg does not exist on local server.
992166938147130052 - data/images/9/9/2/992166938147130052.jpg does not exist on local server.
199695507818663895 - data/images/1/9/9/199695507818663895.jpg does not exist on local server.
";

	$listorgza = "136859865416834298 - data/images/1/3/6/136859865416834298.jpg does not exist on local server.
112744419614423329 - data/images/1/1/2/112744419614423329.jpg does not exist on local server.
416416027104130289 - data/images/4/1/6/416416027104130289.jpg does not exist on local server.
643032647193717503 - data/images/6/4/3/643032647193717503.jpg does not exist on local server.
116654792717878284 - data/images/1/1/6/116654792717878284.jpg does not exist on local server.
807644135174388446 - data/images/8/0/7/807644135174388446.jpg does not exist on local server.
198973078218398657 - data/images/1/9/8/198973078218398657.jpg does not exist on local server.
102499340356768363 - data/images/1/0/2/102499340356768363.jpg does not exist on local server.
178556432413361141 - data/images/1/7/8/178556432413361141.jpg does not exist on local server.
128317614384365879 - data/images/1/2/8/128317614384365879.jpg does not exist on local server.
395453046104664883 - data/images/3/9/5/395453046104664883.jpg does not exist on local server.
108999442180855738 - data/images/1/0/8/108999442180855738.jpg does not exist on local server.
201012151015618072 - data/images/2/0/1/201012151015618072.jpg does not exist on local server.
890287478749627273 - data/images/8/9/0/890287478749627273.jpg does not exist on local server.
998307799131490029 - data/images/9/9/8/998307799131490029.jpg does not exist on local server.
822343848368721970 - data/images/8/2/2/822343848368721970.jpg does not exist on local server.
927677991188157426 - data/images/9/2/7/927677991188157426.jpg does not exist on local server.
502763092114058895 - data/images/5/0/2/502763092114058895.jpg does not exist on local server.
162770845411716234 - data/images/1/6/2/162770845411716234.jpg does not exist on local server.
196286107914051046 - data/images/1/9/6/196286107914051046.jpg does not exist on local server.
791230551201514787 - data/images/7/9/1/791230551201514787.jpg does not exist on local server.
381692683191889595 - data/images/3/8/1/381692683191889595.jpg does not exist on local server.
184709872312493152 - data/images/1/8/4/184709872312493152.jpg does not exist on local server.
162524663471091827 - data/images/1/6/2/162524663471091827.jpg does not exist on local server.
389019112833020992 - data/images/3/8/9/389019112833020992.jpg does not exist on local server.
609702880475556600 - data/images/6/0/9/609702880475556600.jpg does not exist on local server.
625438624185600497 - data/images/6/2/5/625438624185600497.jpg does not exist on local server.
453240606184171233 - data/images/4/5/3/453240606184171233.jpg does not exist on local server.
249448570262576140 - data/images/2/4/9/249448570262576140.jpg does not exist on local server.
110965668635566190 - data/images/1/1/0/110965668635566190.jpg does not exist on local server.
113797285720829570 - data/images/1/1/3/113797285720829570.jpg does not exist on local server.
152877764631094936 - data/images/1/5/2/152877764631094936.jpg does not exist on local server.
630556499126622189 - data/images/6/3/0/630556499126622189.jpg does not exist on local server.
209670489847316898 - data/images/2/0/9/209670489847316898.jpg does not exist on local server.
904371555153117891 - data/images/9/0/4/904371555153117891.jpg does not exist on local server.
580871027121154058 - data/images/5/8/0/580871027121154058.jpg does not exist on local server.
601356892156698059 - data/images/6/0/1/601356892156698059.jpg does not exist on local server.
187270872235014461 - data/images/1/8/7/187270872235014461.jpg does not exist on local server.
629852621125817275 - data/images/6/2/9/629852621125817275.jpg does not exist on local server.
171210735911031162 - data/images/1/7/1/171210735911031162.jpg does not exist on local server.
112166452863516971 - data/images/1/1/2/112166452863516971.jpg does not exist on local server.
432612723106563205 - data/images/4/3/2/432612723106563205.jpg does not exist on local server.
363831936205838211 - data/images/3/6/3/363831936205838211.jpg does not exist on local server.
248897071715934974 - data/images/2/4/8/248897071715934974.jpg does not exist on local server.
418149260211452967 - data/images/4/1/8/418149260211452967.jpg does not exist on local server.
815131667836382068 - data/images/8/1/5/815131667836382068.jpg does not exist on local server.
524513125743908835 - data/images/5/2/4/524513125743908835.jpg does not exist on local server.
211869712841070450 - data/images/2/1/1/211869712841070450.jpg does not exist on local server.
170416521812476750 - data/images/1/7/0/170416521812476750.jpg does not exist on local server.
145268103310118104 - data/images/1/4/5/145268103310118104.jpg does not exist on local server.
530310251748347698 - data/images/5/3/0/530310251748347698.jpg does not exist on local server.
615087759200871622 - data/images/6/1/5/615087759200871622.jpg does not exist on local server.
489082881752318308 - data/images/4/8/9/489082881752318308.jpg does not exist on local server.
147060735910111367 - data/images/1/4/7/147060735910111367.jpg does not exist on local server.
149571784221216896 - data/images/1/4/9/149571784221216896.jpg does not exist on local server.
156988098750763962 - data/images/1/5/6/156988098750763962.jpg does not exist on local server.
800122230143801276 - data/images/8/0/0/800122230143801276.jpg does not exist on local server.
194275658315148515 - data/images/1/9/4/194275658315148515.jpg does not exist on local server.
207194086013265411 - data/images/2/0/7/207194086013265411.jpg does not exist on local server.
128470618985435325 - data/images/1/2/8/128470618985435325.jpg does not exist on local server.
694773348101221895 - data/images/6/9/4/694773348101221895.jpg does not exist on local server.
115762424317828426 - data/images/1/1/5/115762424317828426.jpg does not exist on local server.
295141751921756981 - data/images/2/9/5/295141751921756981.jpg does not exist on local server.
203491260116116873 - data/images/2/0/3/203491260116116873.jpg does not exist on local server.
115848830071231279 - data/images/1/1/5/115848830071231279.jpg does not exist on local server.
259967471119493529 - data/images/2/5/9/259967471119493529.jpg does not exist on local server.
159653801611468950 - data/images/1/5/9/159653801611468950.jpg does not exist on local server.
114730159517695878 - data/images/1/1/4/114730159517695878.jpg does not exist on local server.
179383277816518405 - data/images/1/7/9/179383277816518405.jpg does not exist on local server.
207517281122478525 - data/images/2/0/7/207517281122478525.jpg does not exist on local server.
674398732226026685 - data/images/6/7/4/674398732226026685.jpg does not exist on local server.
145547579299996483 - data/images/1/4/5/145547579299996483.jpg does not exist on local server.
142858230599200855 - data/images/1/4/2/142858230599200855.jpg does not exist on local server.
104018138559834807 - data/images/1/0/4/104018138559834807.jpg does not exist on local server.
875414506432100534 - data/images/8/7/5/875414506432100534.jpg does not exist on local server.
187941414735855352 - data/images/1/8/7/187941414735855352.jpg does not exist on local server.
121999918018436037 - data/images/1/2/1/121999918018436037.jpg does not exist on local server.
";

	//---------------------------------------------------------------------------------------------
	//	split into lines
	//---------------------------------------------------------------------------------------------
	$leu = explode("\n", $listeu);
	foreach($leu as $lineeu) {
		if (strlen(trim($lineeu)) > 0) {
			$parts = explode("-", $lineeu);
			$found = 'no';
			if (strpos($listorgza, $parts[0]) != false) { $found = 'yes'; }

			if ('yes' == $found) {
				$model = new Images_Image($parts[0]);
				if (true == $model->loaded) {
					$model->finalDelete();
					echo "deleted " . $parts[0] . "<br/>\n"; flush();
				} else {
					echo "could not delete " . $parts[0] . "<br/>\n"; flush();
				}
			}

			//echo $parts[0] . " - $found<br/>\n";

		}
	}

?>
