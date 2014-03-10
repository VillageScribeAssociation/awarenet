<?

//-------------------------------------------------------------------------------------------------
//	test of javascript testarea loader
//-------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }

	$text = "ギリシア神話によれば、人間に養蜂を教えたのはアリスタイオスである。蜂蜜と人類の関わりは古く、スペインのアラニア洞窟で発見された約1万年前の壁画に蜂の巣から蜜を取る女性の姿が描かれている。メソポタミア文明の象形文字にも蜂蜜に関することがらが記載され、古代エジプトの壁画に養蜂の様子がえがかれている。また、蜂蜜はこのような歴史から世界最古の甘味料ともいわれている。

古代ギリシアの哲学者・アリストテレスは著書『動物誌』にて、養蜂について記述している。そこではミツバチが集める蜜は花の分泌物ではなく、花の中にたまった露であると述べている。

旧約聖書ではイスラエル人の約束の地・カナンが「乳と蜜の流れる場所」と描写されており、ハチミツは豊饒さのシンボルとして扱われている。

中世ヨーロッパでは照明用のロウソクの原料である蜜蝋をとるために、修道院などで養蜂が盛んに行われた。

19世紀にいたるまでは蜂蜜を得るには蜂の巣を壊してコロニーを壊滅させ、巣板を取り出すしかなかった。1853年、アメリカのラングストロス（L.L.Langstroth）は、可動式巣枠を備えた巣箱や蜜を絞るための遠心分離器を発明し、蜂蜜や蜜蝋の採取時にコロニーを崩壊させずに持続的にミツバチを飼育する技術である近代養蜂の開発に成功した。彼はこの成果を『巣とミツバチ』'The Hive and the Honey Bee'に著している。現在に至るまで養蜂の基本的な手法はラングストロスの方法と変化していない。

日本における養蜂のはじまりは『大日本農史』によれば642年とされている。平安時代には、宮中への献上品の中に蜂蜜の記録がある。江戸時代には、巣箱を用いた養蜂などがはじまったとされる。日本における古典的な養蜂はニホンミツバチを使ったものであり、現在の一般的なセイヨウミツバチによるそれとはやや異なる。現在も山間部ではニホンミツバチによる養蜂が行われている地域がある。明治時代に入り西洋種のミツバチが輸入され、近代的な養蜂器具が使われるようになり養蜂がさかんになる。現在、市場で幅を利かせる蜂蜜は中国などからの安価な輸入品と一部の国からの輸入や国産の高級蜂蜜に二極分化している。";

	echo "<h2>utf8/base64 Javascript Test</h2>";
	echo "<textarea id='taContent' rows='30' cols='80'></textarea>\n";
	echo "<script src='" . $kapenta->serverPath . "core/utils.js'></script>\n";
	echo "<script>\n";
	echo $utils->base64EncodeJs('content', $text);
	echo "base64_loadTextArea('taContent', content);\n";
	echo "</script>\n";

?>
