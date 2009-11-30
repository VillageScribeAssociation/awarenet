<?

for ($i = 0; $i < 20; $i++) {
	echo "USE antest" . $i . ";<br/>\n";
	echo "CREATE TABLE sync (
  UID varchar(30),<br/>\n
  source varchar(30),<br/>\n
  type varchar(50),<br/>\n
  data text,<br/>\n
  peer varchar(30),<br/>\n
  status varchar(30),<br/>\n
  received varchar(30),<br/>\n
  timestamp varchar(20),<br/>\n
  KEY idxsyncUID (UID(10))<br/>\n
);<br/>\n";
}

?>
