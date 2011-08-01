<?

//--------------------------------------------------------------------------------------------------
//.	temporary action to test user settings
//--------------------------------------------------------------------------------------------------
	
	if ('admin' != $user->role) { $page->do403(); }

	$user->storeSetting('anothertest.setting', '1234value');

	$value = $user->getSetting('test.setting');

	echo "stored and returned: $value <br/>";

	//db select count(UID) as unc, username from users_user group by username order by unc;

/*

2

	

MUSA

2

	

2011mawas.tq

2

	

nom2011.ssp

2

	

josh

2

	

kila

2

	

sinovuyo

2

	

nobebe

2

	

g10g7064

2

	

piza

2

	

2011ben10.lk

2

	

aware109

2

	

aware079

2

	

ziphozihle

2

	

nom2011.ss

2

	

nobebeb

2

	

nkwera

2

	

magwalao

2

	

gushaa

2

	

jelal

2

	

siphesihle

2

	

asive

2

	

esther

2

	

aware066

2

	

nom2011.bk

2

	

ngaloa

2

	

vuyiseka

3

	

aware094

3

	

ngogela

3

	

boois

3

	

nom2011.ama

3

	

aware039

3

	

siya

3

	

ntombomzi

3

	

aware100

3

	

lisa

4

	

borncute

4

	

sinethemba

4

	

M.Rose

4

	

sgailer

11

	

nolubabalo


db update users_user set username='nolubabalo-banned-1' where UID=''
db update users_user set username='nolubabalo-banned-1' where UID='114481067852956877
db update users_user set username='nolubabalo-banned-1' where UID='265912973170584305
db update users_user set username='nolubabalo-banned-1' where UID='156576951025018742
db update users_user set username='nolubabalo-banned-1' where UID='184650992119048258
db update users_user set username='nolubabalo-banned-1' where UID='568521691598923799
db update users_user set username='nolubabalo-banned-1' where UID='113719030983669920
db update users_user set username='nolubabalo-banned-1' where UID='183224041415278856
db update users_user set username='nolubabalo-banned-1' where UID='148669284310491067
db update users_user set username='nolubabalo-banned-1' where UID='782423262472004209
db update users_user set username='nolubabalo-banned-1' where UID='526936274985547490
db update users_user set username='nolubabalo-banned-1' where UID='161271787170558309

*/

?>
