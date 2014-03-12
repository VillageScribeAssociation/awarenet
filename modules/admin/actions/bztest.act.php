<?php

//--------------------------------------------------------------------------------------------------
//*	temporary action to test BZIP
//--------------------------------------------------------------------------------------------------

	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	$fileName = 'data/test.bz';

	$data = "House of Plantagenet Royal Arms of England (1198-1340).svg
Armorial of Plantagenet
Country 	Kingdom of England, Kingdom of France, Lordship of Ireland, Principality of Wales
Ancestral house 	Angevins
Titles 	

    King of England
    King of Germany
    Lord of Ireland
    Prince of Wales
    Duke of Aquitaine
    Duke of Normandy
    Duke of Brittany
    Count of Anjou
    Count of Maine
    Count of Nantes
    Count of Poitou
    Lord of Cyprus
    Plantagenet claim to France
    Plantagenet claim to Jerusalem
    Plantagenet claim to Sicily
    Plantagenet claim to Rome
    Plantagenet claim to Castile

Founder 	Geoffrey Plantagenet, Count of Anjou
Final sovereign 	Richard III of England
Founding 	1126
Cadet branches 	

    House of Lancaster
        House of Beaufort
    House of York

The House of Plantagenet (play /plænˈtædʒənət/ plan-TAJ-ə-nət) is the name given in England's historical narrative for the 14 Kings that ruled for the 331 years from 1154 until 1485. There is little evidence for the Plantagenet name before the mid-fifteenth century and it was evidently popularised by Shakespeare.

As a dynasty it is considered a branch of the Angevins. The dynasty's origins as a royal house result from the matchmaking of Fulk V le Jeune[1] which were planned to neutralise the threat of the rise of Normandy. First he married his daughter Alice to William Adelin heir to Henry I of England. Following William's drowning he married his daughter Sibylla of Anjou to William Clito heir to Robert Curthose although King Henry had this annulled because of the political threat of a rival claim to his throne. Finally he married his son Geoffrey to Henry's heir Matilda thereby bringing about the historical convergence of the Angevins, House of Normandy and the House of Wessex.

Plantagenet kings first ruled the Kingdom of England in the 12th century. Their paternal ancestors originated in the French province of Gâtinais and gained the County of Anjou through marriage during the 11th century. The dynasty accumulated several other holdings, building the Angevin Empire that at its peak stretched from the Pyrenees to Ireland and the border with Scotland.

In total fifteen Plantagenet monarchs, including those belonging to cadet branches, ruled England from 1154 until 1485. The senior branch ruled from Henry II until the deposition of Richard II in 1399. After that a junior branch, the House of Lancaster, ruled for some fifty years before clashing over control of England with another branch, the House of York, in a civil war known as the Wars of the Roses. After three ruling Lancastrian monarchs the crown passed to three Yorkist monarchs, the last of whom, Richard III, was killed in the Battle of Bosworth Field in 1485. The legitimate male line went extinct with the execution of Richard's nephew, Edward, Earl of Warwick in 1499. However an illegitimate scion, Arthur Plantagenet, Viscount Lisle, was active at the court of Henry VIII of England. Several illegitimate lines persist, including the Dukes of Beaufort.

A distinctive English culture and art emerged during the Plantagenet era, encouraged by some of the monarchs who were patrons of the 'father of English poetry', Geoffrey Chaucer. The Gothic architecture style was popular during the time, with buildings such as Westminster Abbey and York Minster remodelled in that style. There were also lasting developments in the social sector, such as John of England's sealing of the Magna Carta. This was influential in the development of constitutional law. Political institutions such as the Parliament of England and the Model Parliament originate from the Plantagenet period, as do educational institutions including the universities of Cambridge and Oxford.

The eventful political climate of the day saw the Hundred Years' War, where the Plantagenets battled with the House of Valois for control of the Kingdom of France, and the War of the Roses. Some of the Plantagenet kings were renowned as warriors: Henry V of England left his mark with a famous victory against larger numbers at the Battle of Agincourt, and Richard I of England had earlier distinguished himself in the Third Crusade; he was later romanticised as an iconic figure in English folklore.";

	$bFH = bzopen($fileName, 'w');

	if (false == $bFH) { $kapenta->page->do404("Could not open file for writing."); }

	echo "File handle: " . $bFH . "<br/>\n";

	bzwrite($bFH, $data);
	bzclose($bFH);

	echo "<pre>wrote " . strlen($data) . " bytes.</pre>";

?>
