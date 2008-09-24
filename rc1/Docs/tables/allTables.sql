-- phpMyAdmin SQL Dump
-- version 2.11.7
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Čtvrtek 28. srpna 2008, 13:48
-- Verze MySQL: 5.0.60
-- Verze PHP: 5.2.6-pl2-gentoo

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `dev`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_blog`
--

CREATE TABLE IF NOT EXISTS `vypecky_blog` (
  `id_blog` smallint(3) NOT NULL auto_increment,
  `key` varchar(50) NOT NULL,
  `label` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `time` int(10) NOT NULL,
  `id_user` smallint(3) NOT NULL,
  PRIMARY KEY  (`id_blog`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

--
-- Vypisuji data pro tabulku `vypecky_blog`
--

INSERT INTO `vypecky_blog` (`id_blog`, `key`, `label`, `text`, `time`, `id_user`) VALUES
(1, 'tak-uplne-prvni-blog-na-vypeckach', 'Tak úplně první blog na výpečkách', '<p dir="ltr">Tak tu máme končně funkci blogu! Můžeme psát o čem budeme chtít, ahodnotíme si je a popřípadě shrneme poznatky v diskusi. Pokus zaplotem Tak se nebojte a pište!</p>\r\n<p dir="ltr">"uvozovky"</p>\r\n<p style="text-align: center;">\r\n<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="425" height="355" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n<param name="wmode" value="transparent" />\r\n<param name="src" value="http://www.youtube.com/v/nm-fVU6L0P4&amp;hl=en" /><embed type="application/x-shockwave-flash" width="425" height="355" src="http://www.youtube.com/v/nm-fVU6L0P4&amp;hl=en" wmode="transparent"></embed>\r\n</object>\r\n</p>\r\n<p> </p>', 1205676021, 3),
(16, 'vypecky-20', 'Vypecky 2.0', '<p><span style="font-size: small;"><span style="font-size: x-small;"><font size="2">Pánové a dámy, kluci a holky, ctitelé vepřových výpečků a mezidruhové erotiky, dovolujeme si vám hrdě oznámit, že byl právě spuštěn web Výpečky 2.0. Jak si můžete povšimnout, byl rozšířen o několik zajímavých funkcí. Za prvé je zprovozněn blog, na kterém se snad budete moci dočíst jak se žije na výpečkách. Naší snahou bude poskytovat čerstvé informace o kalbách, kulturních akcích, o našich drahých kamarádech (kdo s kým, kde a jak) a tak všelijak podobně. Zároveň jsme rozjeli guesbook (kniha návštěv), kde můžete publikovat své inteligentní komentáře a diskutovat o aktuálních tématech. Pro ty šťastné z vás, kteří obdrží heslo, bude fungovat služba sdílení dat (primárně studijních materiálů ale i různých jiných cypovin). V nejbližší době snad také zprovozníme fotogalerku, kde budete mít možnost shlédnout obrazovou dokumentaci dění na výpečkách Balbínova 20. Sekce komiks prošla zásadní úpravou, takže je teď přehlednější a skýtá možnost hodnocení a komentářů k jednotlivým výpotkům našich chorých myslí. Jinak se také můžete podívat jak si žije náš sklepník Blahoš (pro neznalé vypečený server). Tak to je prozatím asi tak vše. Doufám, že oceníte bezesné noci, které Kubin strávil programováním a vylepšováním výpeček a budete sem, vy lumpi, chodit.</font> </span></span></p>\r\n<p><span style="font-size: x-small;"><span style="font-size: x-small;">Howg</span><br /></span></p>', 1206638031, 4),
(17, 'nevaz-se-odvaz-se', 'Nevaž se, odvaž se', '<p>Krátká zpráva o průběhu letošních svátků velikonočních:</p>\r\n<p> </p>\r\n<p>Jak možná někteří víte, přijel k nám do VM na velikonoce Jéňa, zvaný Zkrat. Tento rodilý brňák má o Valašsku značně romantizující a zkreslené představy, které se mu za jeho sporadických návštěv snažíme vyvrátit. Zatím bohužel neúspěšně. Pamatuju se, že v průběhu své první návštěvy v malebné krajině Beskydských hor, se neustále dožadoval čerstvé žinčice a kontaktu s horaly strážícími hranice s Rakousy. Jak je vidět, Brňákům dělá geografické a časové zařazení folklórních fenomenů značné potíže. Nu, některé základní bludy se nám již podařilo vyvrátit, takže pondělní vycházka na Dušnou proběhla celkem bez větších obtíží. Pominu-li Janův instrumentální úžas nad tím, že číšnice v malebné hospůdce na Malé Lhotě nebyla oděna v krpcích a valašském kroji, a že se pivo nepodávalo v hliňených korbelích, probíhalo vše celkem hladce. Klimatické podmínky ovšem nebyly nijak úžasné. Hustý déšť ve vyšších nadmořských výškách vystřídala ladovská chumelenice a na Dušnou jsme dorazili obaleni vzorky ornice z rozličných beskydských polností. Nicméně, nemohu opomenout doporučit vaší pozornosti hospodu na Dušné. Malebná nálevna, jako z konce minulého století disponující piliňákama, nerezovým výčepem, stylovým hospodským, výborným pivem a ještě lepším gulášem. Pokud někdy zabloudíte do těchto končin, rozhodně si ji nenechejte ujít. Celou vycházku s námi absolvoval i můj bratranec Hondzik, který si před časem v Rakousích zvládnul přervat všechny šlachy v koleně (made in snowpark). Jak dokázal ujít celou značně kluzkou trasu bez jediného vazu, mi zůstává záhadou. Pravděpodobně za to můžou úžasné stabilizační účinky slivovice. Celou vycházku, jsme zakončili na Vsetíně (malebné město bez náměstí, oplývající značným počtem extremistických skupin (hokejisti, opálení bratři, šampóni, metalisti, plešky atd.), ze kterého jsme se rychle pakovali zpět do náruče VM a následně na naši chatu. Tam pak proběhl lehce nevázaný večírek s velikonoční tématikou (probíralo se tuším především rozmnožování a jeho techniky, což je takové hodně jarní téma). Z celé akce plyne poučení: I bez vazů se můžete odvázat. Howg</p>', 1206639995, 4),
(18, 'za-malo-penez-hodne-muziky', 'Za málo peněz, hodně muziky', '<p>Ještě bych si dovolil podotknout, že ve Vídeńském klubu <a href="http://www.flex.at/">Flex</a> hraje tuto sobotu světoznámý a oblíbený bůh Francouzské taneční scény <a href="http://en.wikipedia.org/wiki/Laurent_Garnier">Laurent Garnier</a>. \r\nCelé to stojí nějakých směšných 16 evro, takže zejména pro Brňáky je to\r\nmust have. Jinak 25.4.2008 ve 20:00 zahraje v Pražském divadle <a href="http://www.archatheatre.cz/cz/menu/program/390.html">Archa</a> další elektronický šílenec Amon Tobin. Opět za směšnou částku 430\r\nkorun. Takže, jestli jsou tihle hoši váš šálek čaje, rozhodně si to\r\nnenechejte ujít.</p>\r\n<p style="text-align: center;"><img src="data/images/20080329-01.jpg" alt="" width="202" height="286" /></p>', 1206640821, 4),
(20, 'zmena-je-zivot', 'Změna je život', '<p>Včerejší neočekávaný pivní dýchánek s Michalem, přinesl své ovoce. Během Synkovy nesmírně záživné přednášky na téma Poststrukturalismus v sociologii a morální implikace Dungeons and Dragons, bylo vemli těžké udržet se při vědomí. A tak se stalo, že jsem, tupě zírajíc do šestého piva, vyšpekuloval další podobu našich vypečených stránek. V mírné kocovině jsme si s Kubinem ukuchtili postní řízečky a hned po obědě se vrhli na kompletní předělávku designu stánek. Snad se je podaří brzo nahodit. Jinak zítra ráno vyrážíme s kolegou Zkratem na avizovaný trip do matičky Vídně. O výsledcích, následcích a důsledcích naší cesty vás budu podrobně informovat.</p>\r\n<p>Mimochodem jsem se dozvěděl velmi smutnou novinu. Za nedlouho nám zavřou úžasnou vinárnu Ambra, ve které jsme si nejednou příjemně poklábosili a nejednou se podívali na dno nejedné sklenice. Howg</p>', 1206746595, 4),
(21, 'vienna', 'Vienna', '<p>Nekompromisní beaty, masivní zvukové plochy a  mírně disharmonické melodické linky. To všechno nám včera s neuvěřitelnou silou provrtávalo bubínky, plnilo hlavy a rozechvívalo vnitřní orgány. Laurent udělal čest svému jménu a odehrál naprosto unikátní set. Takhle holt nikdo jiný nezahraje. Flex byl nacpaný k prasknutí, tělo na tělo, pot, dým, smrad a horko - prostě úžasná atmosféra. Pivo bylo studené a rakouský lid vřelý. I VJing zaslouží pochvalnou zmínku. Ten je v Rakousích tradičně na dobré úrovni a ani včerejšek nebyl výjimkou (osobně mě velmi potěšily umně sestříhané ukázky z Trona, které na ůchylném francouzké techno sedly jak prdel na hrnec). Ještě bych rád poděkoval Simonovi, který nám poskytl přepychovou snídani, pohodlí svého bytu a několik užitečných rad o optice. Brňáků bohužel mnoho nedorazilo a nutno říct, že přišli o hodně (včetně cesty rakouským ECéčkem :-). Howg</p>', 1206881739, 4),
(22, 'pohadka-o-masinkach-a-zle-cernokneznici-zababe', 'Pohádka o mašinkách a zlé černokněžnici Zababě', '<p>K sobotní návštěvě Vídně se váže ještě jedna velmi poučná a místy i vtipná historka, kterou si nemůžu nechat pro sebe. Asi tak ve čtvrtek jsem se rozhodl, že se do Vídně dopravím vlakem. Důvodů bylo hned několik: na rozdíl od autobusu jezdí vlak častěji, je pohodlnější, rychlejší a s in-rail kartou není ani tak drahý. Problém ovšem byl, že jsem tehdy ještě zmíněnou kartu nevlastnil. Sejně jsem už dlouho přemýšlel nad tím, že si ji pořídím (vlakem jezdím poměrně často) a tak jsem si řekl: "Proč ne teď?" V dobré víře jsem do vyhledávače zadal adresu eshopu čd. Nákup probíhal celkem bezproblémově, až do chvíle kdy po mně systém žádal číslo platného průkazu (OP, Pas nebo tak). To jsem suveréně napsal z hlavy a celou transakci odeslal. Za minutku mi došel PDF formulář s provizorní kartu a informace o koupi. Při kontrole jsem s hrůzou zjistil, že číslo OP, které jsem zadal, není tak docela stejné, jako to na mé občance. Prvních 600 v tahu (teda už jsem podal žádost o reklamaci, tak se to snad v dobré obrátí).</p>\r\n<p>Nic zlého netuše jsem si tedy koupil druhou in kartu doufajíc, že tu první prostě stornuju. Zbylé operace proběhly v pořádku, dokonce i to 10 místné číslo se mi tentokrát povedlo zadat bez chyby. V sobotu ráno jsem s pohledem suveréna nakráčel k okýnku a očekával svou zpáteční jízdenku Brno-Wienna za 340 Kč včetně jednodenní lítačky na vídeňskou mhd. Když jsem předložil svou provizorní kartu, paní za pokladnou se na mě podívala jako na někoho, kdo nosí sandály, bílé ponožky a z plezíru si pod nosem pěstuje chapadla. Hrdě mi oznámila, že na tenhle cár papíru mi žádnou slevu nedá (a už vůbec ne zadarmo), že si musím měsíc počkat až mi vydají plastovou kartu. Nato jsem se poněkud rozčílil, a poměrně nelichotivými slovy jsem ji upozornil, že to teda jako ne, že jsem si zaplatil 600 a ať mi laskavě rychle dá tu slevu, jinak že ji poplivu přepážku. Na to se milá paní odešla poradit se svým kolegou, jestli mi může, či nemůže na ten papír poskytnou slevu. Radila se dobrých 5 minut a já už začal být pěkně nervózní, protože obvykle nechodívám na vlak nijak zběsile brzo. Konečně se vynořila za přepážkou a s vítězoslavným úsměvem mi sdělila, že na daný doklad se slevy poskytují, leč žel bohu jen na území ČR. Za hranicemi zkrátka musím mít tu zku... plastovou kartu.</p>\r\n<p>Chvíli jsem se ještě pokoušel chabě odporovat, že jsem si zaplatil za službu, kterou mi odmítají poskytnou, ale bylo to jako bych mluvil do kníraté zdi (paní očividně dost flákala depilaci). Nakonec jsem schlíple zaplatil 740 za jízdenku a za stálého klení vystartoval k perónu, protože vlak se už pomalu rozjížděl. Dneska jsem zjistil, že ta paní byla v právu a já, že jsem naprostý idiot (to jsem teda zjistil už mnohem dřív, samozřejmě). V obchodních podmínkách je opravdu úplně dole, šestkou písmem uvedeno: "provizorní doklad platí pouze na území ČR". Po pravdě nechápu proč, protože Rakušákům jsou a mají být naše slevy ukradené, ale je to tak. Tímto se tedy oné příjemné, profesionálně vystupující dámě reprezentativního vzhledu (jaké už na pokladnách čd bývají) hluboce omlouvám a hlasitě volám: "Long live ČD"</p>\r\n<p>Howg</p>', 1206991103, 4),
(24, 'ranni-ptace-dal-doskace', 'Ranní ptáče, dál doskáče...', '<p>Nesnáším Hellenu!!! Už za těch nemnohých nocí, kdy jsem zakempoval u ní v bytě, jsem objevil tuhletu strašlivou skvrnu na jejím jinak dobrém charakteru. Dovolte mi demonstrovat na typickém příkladě: 8:00 - jdeme na pivo někam do centra (nejspíš Koule, Čáp, Sklo, ČH, nebo Lékárna), všichni se dobře baví a spokojeně usrkávají z 1. půllitru, 11:00 - jdeme na pivo někam jinam (mýdlo, kašna, caverna) všichni se baví ještě lépe a hlasitěji a spokojeně upíjí z 5. půllitru. 01:00 -  Všichni se baví naprosto náramně, nikdo neví proč, o čem, jak a kde, ale tak to prostě je a nic na tom nezmění ani 9. pivo. 02:00 -  Tam, kde sedíme, už před hodinou chtěli zavřít, jsme poslední zákazníci, úroveň zábavy dosáhla slovy nevyjádřitelných výšin, všichni se čelem znaveně opírají o 10. pivo a číšnice pod námi demonstrativně vytírá a snaží se nás přiklopit židlí. 02:10 - Zase na cestách (tentokrát směr vat 69, modrý nebo jiný nonstop). 4:00 - zábava dosahuje zenového stádia, už není třeba nic říkat (a ani se o to pokoušet) ani nic dělat a všichni si přesto dokonale rozumí. Naše duše jsou vzájemně propojené a 11. pivo jimi volně proplouvá, jako zlatavá přílivová vlna. V duši se rozhostí svatý mír a všechny nás zalije pocit hřejivého lidumilství a spokojenosti. 05:00 - potácíme ser k baru, platíme, vycházíme ven (bůhvíjak se u východu vždycky objeví tlupa křivých schodů, které tam při příchodu prokazatelně nebyly). Šedivý rozbřesk nad Brnem nás vždycky nemile zaskočí, zjistíme, že jsme někde v úplném centru, na Starku je to zatraceně daleko a nohy se bez našeho svolení zapsaly do kurzu šamanských tanců. Jde se k Helleně (páč bydlí na České). 07:30 - toho kdo spí nejblíž H (nevímproč to jsem skoro vždycky já) budí bolestivý dloubanec do žeber a následná průtrž slov, pestrá koláž skládající se z autobiografických prvků, Sci-fi, absurdního dramatu, černého humoru, povídek krutě připravených o pointu, atp. Je celkem zřejmé, že se takto napadený člověk snaží co nejrychleji znovu usnout, ale jakmile se o to pokusí, je tu další bolestivý dloubanec do žeber. Důvod, proč u H vůbec jsme je, že po 10. pivu prostě zapomeneme, jaké to bude ráno. Dneska ovšem nasadila H svému rannímu slovnímu průjmu korunu. Jen to bolestivé dloubání prstem vyměnila za ještě bolestivější zvuky. Začala mně totiž budit na dálku - telefonem :-)</p>', 1207382368, 4),
(25, 'pohadka-o-repe', 'Pohádka o řepě', '<p>Tahleta historie je sice staršího data, ale vzhledem k její zábavnosti a katastrofickým důsledkům, které ještě stále ovlivňují život na Výpečkách, ji nemohu vynechat. Jak rezidenti z Balbínovy 20 dobře ví, je náš privát místem sice občasných, ale o to krutějších a sadističtějších vědeckých pokusů na červených řepách. Nevím, kde se to v naší Katce vzalo, ale jisté je, že s takovou mírou krutosti k nebohé kořenové zelenině, jsem se ještě nesetkal. Nerad bych se pouštěl do spekulací ohledně příčin této zavilé nenávisti, snad má své kořeny v tíživém puchu šerosvitných školních jídelen, snad v nucených brigádách na polnostech socialistické vlasti, kdo ví. Jisté je, že náš řepný Mengele si jednou za čas uvědomí, že má málo železa a že by bylo fajn mít zase chvíli červenou moč. V blízkém stánku se zeleninou pak pečlivě vybírá své nebohé oběti, nese je domů, vkládá do hrnce a několik hodin nemilosrdně vaří. Zmučenou řepu pak nechává odležet, aby ji následně nastrouhal na malé rudé kousky a s gustem konzumoval.</p>\r\n<p>Tedy, tohle je ten nejoptimističtější scénář. V ostatních případech zůstala nebohá řepa v hrnci podstatně déle. Katka takto testovala odolnost varem zmrzačené řepy proti plísni a hnilobě. (zjistilo se, že řepa proti oběma výše jmenovaným, nijak zvlášť odolná není, a to ani vařená). Na naše četné výtky, že takové pokus jsou krajně nehumánní, reagovala Kateřina démonickým úšklebkem a koupí nové řepy. Nepomohly ani výhružky, že ji udáme na LOZ (Liga na ochranu zeleniny).</p>\r\n<p>V nedávné době dostupily její kruté pokusy nových výšin. Asi tak před měsícem jsem přišel domů a už ve dvěřích mě známý pach hlíny a stuchliny ujistil, že se na sporáku odehrává další fáze řepné genocidy. Odběhl jsem tedy rychle do svého pokoje, protože pohled na vařenou řepu mě upřímně děsí a zapnul si počítač. Po 20 minutách soustředěné práce jsem byl vyrušen faktem, že sotva dohlédnu na monitor půl metru přede mnou. Do toho se přidal sílící zápach řepy, do něhož se teď mísily hutné tóny spáleniny. Hustou řepnou mlhou jsem se prodíral ke sporáku (po cestě jsem dvakrát vrazil do Rákosníčka) abych vypnul plyn. Hlasitým řevem jsem uvědomil Katku, že její test ohnivzornosti vařené řepy právě skončil a rozrazil jsem kuchyňské okno.  Pominu-li Katčin úžas nad tím, že jsem řepu nevypnul před tím než vzplála jasným plamenem, (zřejmě si myslí, že jsem s řepou telepaticky spojen) obešel se celý incident bez větších rozmišek. Jenom má doměnka, že tímto pokus skončil, se ukázala jako mylná. Hrnec trůnil na plotně ještě několik dní.</p>\r\n<p>Když už jsem pohled na zmučenou řepu nemohl dál snášet, odnesl jsem ho na zahradu. Jednou jsem si Katku dovolil na řepu upozornit, ale z její krajně podrážděné reakce a důrazné výzvy ať si kuwa hledím svého, jsem pochopil, že pokus ještě neskončil. Ba že se jedná o Katčinu dosud největší práci, grandiózní výplod choré a pokroucené mysli. Šlo zřejmě o syntézu předešlých pokusů - tedy o výzkum odolnosti spálené řepy proti hnilobě a plísni. A nutno přiznat, že minimálně na plíseň to fungovalo. Když jsem řepu před 14 dny vyhazoval do popelnice, zaváněla sice nelibě, ale po plísni ani stopy.</p>\r\n<p>Další stinnou stránkou řepného zvěrstva je, že si Katka ke svému pokusu vybrala náš největší hrnec (ve kterém jsme donedávna vařili guláš) a ten do dnešního dne nebyla schopna uvést do původního stavu. Z tohoto důvodu jsme byli nuceni snížit objem vařených pokrmů a na Výpečky se tak pomalu vkrádá hlad a podvýživa.</p>', 1207387660, 4),
(26, 'requiem-za-krygl', 'Requiem za krýgl', '<p>Tak jaro je ofici&aacute;lně tady. Jeho př&iacute;chod jsme oslavili prvn&iacute;, obvzl&aacute;&scaron;tě vypečenou jarn&iacute; grilovačkou. J&iacute;dlo bylo skvěl&eacute; (hlavně z&aacute;sluhou Sylvy a bř&iacute; Matasů), v&iacute;no pitn&eacute;, konverzace nev&aacute;zla a m&iacute;sty byla i lehce inteligentn&iacute;. Snad se podař&iacute; nahodit i nějak&aacute; fota. Jedin&yacute;m kazem na jinak bezchybn&eacute; akci zůst&aacute;v&aacute; ztr&aacute;ta m&eacute;ho drah&eacute;ho půllitru Plzeň, kter&yacute; se mnou pro&scaron;el 5 let vysok&eacute; &scaron;koly, pod&iacute;val se se mnou do Řecka, přežil nespočet beček, kaleb, narozeninov&yacute;ch p&aacute;rty a jin&yacute;ch sodomi&iacute;. To on probděl mnoh&eacute; noci u m&eacute;ho lůžka, aby r&aacute;no poskytl m&yacute;m vyschl&yacute;m &uacute;stům dou&scaron;ek chladiv&eacute; vody. To on byl m&yacute;m věrn&yacute;m společn&iacute;kem v nemoci, kdy jsem z něj usrk&aacute;val heřm&aacute;nkov&yacute; čaj s medem, i ve zdrav&iacute;, kdy jsem z něj usrk&aacute;val něco docela jin&eacute;ho. Ob&aacute;v&aacute;m se, že celou trag&eacute;dii je nutno připsat na vrub Kubinovi, kter&yacute;, znaven přem&iacute;rou alkoholu, nezvl&aacute;dl přesun ze zahrady do kuchyně a v marn&eacute; snaze znovu nab&yacute;t stability, se zř&iacute;til př&iacute;mo na prostřen&yacute; stůl. Ztr&aacute;ty byly značn&eacute;. Někteř&iacute; celou ud&aacute;lost označuj&iacute; za porcel&aacute;nov&eacute; 9/11. Dnes r&aacute;no se tedy konal hromadn&yacute; pohřeb n&aacute;dob&iacute;, n&aacute;sledovan&yacute; karem z pozůstatků večern&iacute; hodovačky. V dopoledn&iacute;ch hodin&aacute;ch se začali sch&aacute;zet př&aacute;tel&eacute;, aby popř&aacute;li upř&iacute;mnou soustrast. Je&scaron;tě teď přes z&aacute;plavu slz sotva vid&iacute;m na kl&aacute;vesnici. Nu snad i tato ztr&aacute;ta přebol&iacute;, čas zacel&iacute; r&aacute;ny a nezbyde než smutn&aacute; vzpom&iacute;nka na &scaron;ťastně str&aacute;ven&aacute; společn&aacute; l&eacute;ta. Čest jeho pam&aacute;tce!</p>', 1207478388, 4),
(27, 'zvon', 'Zvon', '<p class="MsoNormal">Není víkend\r\njako víkend, občas se najde zvláštní a jedinečný zážitek, který i po letech dojímá a bere za srdce a člověk by\r\ndal nevím co, za to, aby mohl vrátit čas a tyto chvíle nevídané pohody a štěstí\r\npřipomněl. Bohužel jsou i víkendy na které člověk dlouho vzpomíná a běhá mu mráz po zádech. Na minulý víkend nezapomenu\r\nještě dlouho a zajisté ani celé osazenstvo domu č.p 277 v Bělotíně.</p>\r\n<p class="MsoNormal">Jako každý\r\npátek chodím „sportovat“. Málo kdy však opravdu nějaký sport provozuji. S místními\r\n„sportovci“ spíše hodnotíme kvalitu domácích destilátů a když náhodou zbude\r\nčas, tak si zahrajeme setík volejbalu, návštěva místní hospůdky...................... A tento víkend mému\r\nžaludku po deseti pivech a osmi panácích kalvádosu ta malá kofola nesedla. Pak\r\nuž jen cesta domů příkopem, doblitý\r\nhajzl a průser je na světě. Stav toalety se dost blížil záchodkům ČD Hranice na\r\nMoravě, a tak jsem se dal do očisty. Vše probíhalo dle plánu, stačilo už jen\r\nzlikvidovat poblitou přílohu MF Dnes a je to. Nacpat do hajzlu, spláchnout a dívat\r\nse, jak mi voda z přetékající mísy teče do bot.</p>\r\n<p class="MsoNormal">A ráno to\r\nbylo ještě horší. Musel jsem s pravdou ven a pustit se do opravy ucpané\r\ntoalety. Po opakovaném spláchnutí byla voda až v pokoji pro hosty, a tak musel\r\njít ostych stranou a „ručka šmátralka“ se pustila do díla. Než jsem se nadál, byly\r\nnoviny ještě hlouběji v potrubí. Po zjištění, že fyziologicky nebude možno\r\nloketní kost obtočit kolem dvou porcelánových kolen, jsem začal shánět pružinu\r\nna proražení potrubních usazenin. Nemusím doufám připomínat, že jsem stále doufal\r\nve všemocné spláchnutí a v této chvíli jsem již klečel v bazénku o hloubce\r\nvýšky prahu u dveří. Bohužel pružina byla nedostupná, tak jsem zvolil\r\nmechanicky podobné zařízení: zelená zahradní hadice. Přetlačil jsem tu mršku\r\nzelenou přes sifon a doufal v konečné proražení novinového špuntu. Poté, co\r\njsem nacpal do záchodu asi pět metrů hadice, došlo k zablokování i druhé\r\ntoalety v přízemí budovy napojené na stejné potrubí. Přibližně v tu dobu to\r\nzačalo vypadat, jako by domem projel hovnocuc s otevřenou výpustí. V tu chvíli\r\nse rodina rozhodla, že je na čase na mě začít vážně naléhat a dožadovat se\r\nnápravy věci, a čas plynul. Hodiny už odbíjely 14.00 a konec utrpení mého\r\ni celé rodiny v nedohlednu.</p>\r\n<p class="MsoNormal">Je s\r\npodivem, že žena vždy začne protestovat, když se muž snaží k opravám v\r\ndomácnosti použít zábavní pyrotechniku. Můj pokus o cílený odstřel novin\r\npetardou byl striktně odmítnut, jakož i použití silných žíravin a rukožerných\r\nlouhů. V tu chvíli zasáhla matka rodu: debilovi-opraváři byla udělena červená\r\nkarta, přihlížející diváci ho inzultovali a byl poslán do sprch. Matka vyřešila\r\ncelý problém za použití obyčejného gumového zvonu na odpady. Ano, přesně toho\r\nnástroje, který vysokoškolák zamítl hned na začátku celé akce. Tak jednoduchá\r\nvěc přece nemůže řešit problém, přesahující schopnosti studenta-technika s\r\npokročilou znalostí matematiky, fyziky a dalších přírodních věd.</p>\r\n<p class="MsoNormal">Z toho plyne\r\npoučení: ta nejjednodušší cesta bývá zpravidla nejlepší. A proč? Zde jsou\r\nvýsledky mé práce bez použití zvonu: celková doba: 6,2 hod, vytopené patro: 3x,\r\nnásledný úklid: 3 hod.a dva litry Sava, očista pracovníka: 1 hod.. To vše je\r\ndaň za hrdost a vědecký přístup k faktu, že jsem prase. Za diskuzi by asi\r\nstálo, zda místo použití zvonu, není na místě léčba mého alkoholismu, ale to\r\nsnad proberu příště.</p>\r\n<p class="MsoNormal">Carpe\r\nDiem</p>', 1207481398, 9),
(28, 'jenom-na-uvod', 'Jenom na úvod', '<p>Dobrý den,</p>\r\n<p>asi to je zrovna dnešek. Jak sem se díval na tu čáru živaota, co ji mám v dlani, tak na dnešek tam bylo něco právě s výpečkama. Asi ta registrace. Nešlo to poznat přesně Jenom mě při té registraci zklamalo, že to chtělo mejl jenom jednou. Jak si člověk jednou zvykne dávat to dvakrát, tak už se z toho těžko dostává. Zachránilo mě to pole "poznámka". Tak sem to tam napsal ještě jednou. Neva?</p>', 1208032525, 11),
(29, 'vikend-doma', 'Víkend doma', '<p>Do Valmezu se dostanete poměrně snadno, horší už je dostat se z něj. Tuhletu jednoduchou pravdu, jsem si ověřil mnohokrát. Přesto jsem, nevím proč, zase doufal, že to tentokrát dopadne jinak. Důvody mé návštěvy tohoto alkoholiky, schizofreniky, psychotiky a katolíky prolezlého podhorského městečka (ve kterém jsem se mimochodem narodil a 20 let žil), byly čistě pracovní. Nic zlého netuše, předpokládal jsem, že si prostě nafotím, zabalím a tradá zpátky do Brna. Už v pátek jsem ale zjistil, že to nebude jen tak. Focení se protáhlo do pozdních večerních hodin a konec stále v nedohlednu (zkuste nasvítit broušené sklo tak, aby nevypadalo, jako když se posere kometa).</p>\r\n<p>Sobotní návrat do Brna jsem tedy vzdal a odjel do malebné hospůdky Ranč, abych spolu se svými kamarády zapil hořkou pachuť nezdaru a vyčistil si hlavu. Čistilo se vespolně, poctivě a z perspektivy dnešního rána snad i trochu moc. Matně si vzpomínám, že se někdo pokoušel hrát šipky - jednou se mu i povedlo trefit se do té velké černé skříně, na které je terč. Zbytek šipek pak se zlověstným hvízdáním prolétával místností, zabodával se do nábytku, do záclon a padal nám do půllitrů. Zpětně se dost divím, že jsem odcházel se stejným počtem očí, s jakým jsem přišel. Celému večeru nasadil korunu Ondřej, který byl místní servírkou vyzván na páku. Slečna to byla dobře stavěná a Ondřej s ní po dlouhém zápolení gentlemansky prohrál. Jak jsme se později dozvěděli, šlo o bývalou mistryni republiky v dámské  páce. (Viz obr. níže)</p>\r\n<hr />\r\n<p style="text-align: center;"><img src="data/images/dsc-6270.JPG" alt="" width="480" height="324" /></p>\r\n<hr />\r\n<p>V sobotu se mi všechno podezřele dařilo, povedlo se dofotit, dodělat si věci do školy, dobře poobědvat a odmítnout všechny, kdo mě tahali na pivo. Dokonce to vypadalo, že se mi podaří odjet sedmým vlakem do Brna. Ale co čert nechtěl, asi v šest jsem se rozhodl pro malou svačinku a vytáhl si ze špajzu rohlík. Nerad bych pekařům v Albertu zazlíval jejich kreativní přístup, ale všechno má své meze. Klidně si o mně myslete, že jsem konzervativní fosil, ale minerály do pečiva prostě nepatří (navíc myslím, že ani rozsáhlé klinické studie neprokázaly jakýkoli nutriční přínos konzumace ruly).</p>\r\n<p>A tak se stalo, že jsem si rozlomil zub. Zcela pominu, že to bolí ještě hůř, než si představujete. Hlavní totiž je, že jsem v téhle bohemzapomenuté díře nucen zůstat až do pondělí, kdy mně snad z mého utrpení vysvobodí zubař.</p>', 1208033346, 4),
(30, 'wwwanonymnialkoholicicz', 'www.anonymnialkoholici.cz', '<p></p>\r\n<p class="MsoNormal"><span> </span>Před\r\nnedávnem jsem zpytoval své svědomí a na místo sám sobě, jsem otázku „jsem\r\nalkoholik? “ položil Googlu. Asi nikoho nepřekvapí, že hned první odkaz byl <a href="http://www.anonymnialkoholici.cz/">www.anonymnialkoholici.cz</a> a nabízel\r\nmi test, který zaručeně odhalí, zda jsem s Démonem Alkoholu zadobře, nebo se ze\r\nmě stal jeho poslušný otrok. V tu chvíli mi takový test připadal jako výborný způsob,\r\njak strávit čas před odchodem na pivo.</p>\r\n<p class="MsoNormal"><span> </span>Hned první\r\ndvě otázky byly na tělo: „pijete někdy sám?„ a „kolikrát do týdne?“ . Tak na tu\r\nprvní by se snad i dalo odpovědět kladně, ale ta druhá se nalézala zcela\r\nmimo<span> </span>stupnici. Také otázka „jak často\r\nmyslíte na konzumaci alkoholu?“ mnou zůstala zcela nepochopena. Snad proto, že\r\nse pro mě chlastání stalo instinktem, se myšlením na něj nemusím vůbec zabývat.\r\nA co se narušení mého společenského života alkoholem týče, nebýt alkoholu,\r\npřátele žádné nemám, o mé pozici v hierarchii Výpečků snad netřeba ani\r\npolemizovat a kreténovi, který se mi nebál položit otázku, zda můj společenský\r\nživot upadá kvůli alkoholu, bych přál zažít jeden večer se mnou v\r\nknajpě...............kdo mě zná, pochopí.</p>\r\n<p class="MsoNormal"><span> </span><span> </span>Jak může mé okolí vnímat můj postoj k\r\nalkoholu, když většinou chlastám sám v koutě? S touto variantou zřejmě tvůrce\r\n„Kladiva na ožraly“ nepočítal - další bod pro mě. Už vedu šest nula. Sice\r\nzásluhou nedostatečně velké stupnice, ale to nijak neumenšuje mé nadšení nad zábavností\r\nnalezeného testu a s pořekadlem „hlad zapiješ, žízeň nezajíš“ na rtech, se s\r\nchutí pouštím do večeře tak trochu jiného, vypečeného pečiva :-)</p>\r\n<p class="MsoNormal"><span> </span>Patří láhev\r\nslivovice v nádržce toalety do kategorie „schováváte si alkohol po domě?“ To\r\nje otázka hodná starověkých filozofů, řekl jsem si a přenechal ji dalším\r\ngeneracím myslitelů. Technicky je slivovice alkohol, a proto jde o schovávání\r\nalkoholu - tudíž jsem vinen. Jenže já ji používám jako lék na mé ranní\r\nnevolnosti - a má snad někdo právo mě soudit za ukrývání léků? Další bod pro\r\nmně, a stále jsem dle testu abstinent.</p>\r\n<p class="MsoNormal"><span> </span>„Pivo je tekutý\r\nchléb a odříkaného chleba největší krajíc“ - to je zásada vyrovnané a pestré\r\nstravy studenta VŠ, a proto jsem další testovou otázku, zda „upřednostňujete\r\nkonzumaci alkoholu před běžnou stravou?“, ani nedočetl. Hned mi bylo hned\r\njasné, že tímto způsobem se může ptát pouze člověk, který nikdy nestudoval. V\r\ntu chvíli se pro mě tvůrce testu stal nekompetentním pisálkem a já se přestal\r\ndivit, že nad testem stále vítězím a snaha udělat ze mně pomocí několika\r\nnedopečených otázek anonymního alkoholika, jasně selhává.</p>\r\n<p class="MsoNormal"><span> </span>Naštěstí přišel\r\nčas začít se normálně bavit - tzn. zahrát si spolu s dalšími abstinenty na\r\ncarské důstojníky na silvestrovském večírku. Díky vydatné domácí stravě jsem se\r\n"připravil" už na privátě a do knajpky se výborně a náležitě těšil.\r\nBohužel se mi celou cestu do hlavy vkrádal nepříjemný dovětek: „Pokud došlo ke\r\nkonzumaci alkoholu již během řešení testu, vyhledejte urychleně lékařskou pomoc.“</p>\r\n<p class="MsoNormal" style="text-align: right;" align="right">Carpe Diem</p>\r\n<p class="MsoNormal" style="text-align: right;" align="right"></p>', 1208036257, 9),
(31, 'o-zubni-vile-a-jinych-kratochvilich', 'O Zubní víle a jiných kratochvílích', '<p>Tak zub je venku. Včera jsem strávil idylickou půlhodinku v zubařském křesle a voila - místo čtyřky mám v dásních úhlednou mezeru zvící menšího Suezu. S takovovou dírou v zubech uděláte díru do světa. Můžete si do ní například švihácky vetknout cigaretu, plivat po refýžích a kolemjdoucí dámy ohromovat širokým úsměvem protřelého rváče. Už teď mohu s jistotou říct, že fronty fanynek před Balbínovou 20 se opět výrazně protáhnou.</p>\r\n<p>Ale řeknu vám, je to zvláštní pocit, nenávratně přijít o část svého těla - teda pokud se mi v 50 neprořežou třetí zuby. Paní doktorka se mě snažila utěšit a tvrdila, že mám zubů plnou hubu, ale stejně... Po dlouhé době jsem se opět pobavil nad účinky lokální anesteze. Hned po návratu domů na mně udeřila matka, kde jsem se probůh takhle brzo ožral. S ochrnutou hubou trvalo překvapivě dlouho vysvětlit, že za mou špatnou artikulaci opravdu nemůžou čtyři velké rumy, ale paní zubařka. Mluvidla jsem plně rozhýbal až tak kolem druhé hodiny.</p>\r\n<p>Taky mě nasrala zubní víla. Asi se jí nelíbilo, že byl můj zub skládací (paní doktorce se ho podařilo při vytahování rozebrat na několik kusů) a tak mi pod polštářem nenechala než kulové s přehazovačkou. Nu což, snad bude příště shovívavější. Pro tentokrát to bolestné ještě oželím a večerní pivo si zaplatím za svoje!</p>\r\n<p>PS: Musím říct že poznámka mé mámy o ranním opilství mě dost inspirovala a tak jsem dnes ráno zapil smutek i bolest velkou slivovicí.</p>', 1208244417, 4),
(32, 'creativity-night-08', 'Creativity Night 08', '<p><a title="./data/images/flyer-creativity-night-369.jpg" rel="thumbnail" href="data/images/flyer-creativity-night-369.jpg"> </a><a title="./data/images/flyer-creativity-night-369.jpg" rel="thumbnail" href="data/images/flyer-creativity-night-369.jpg"> </a></p>\r\n<p style="text-align: center;">Všechny ctitele suchého humoru a mokrého piva bych rád pozval na úterní anglistickou párty na Flédě. Vstup je mírný, kulturní program bohatý, děvčata i hoši pěkně urostlí, čistě oblečení, slušní a inteligentní. Startuje se 15. dubna ve 20:00.</p>\r\n<p style="text-align: center;"><img title="flyer-creativity-night-369.jpg" src="data/images/flyer-creativity-night-369.jpg" alt="flyer-creativity-night-369.jpg" width="278" height="399" /></p>', 1208245045, 4),
(33, 'konecne-opravdovy-web-seznam-zmena-vylepseni', 'Konečně opravdový WEB! - seznam změna vylepšení', '<p>Tak jsme po dlouhé a usilovné práci opět vylepšili internetové stránky Vepřových Výpeček. Doufám že se vám budou líbit více než předchozí.</p>\r\n<p>Ještě bych podotknul, že toto je stále testovací verze, která se bude měnit a upravovat. Rád bych využil diskuzi k tomuto blogu, kde prosím pište odhalené chyby nebo vylepšení.</p>\r\n<p>Malý seznam funkcí, které jsou právě ve vývoji a budou co nejdříve přidány:</p>\r\n<ul>\r\n<li>RSS kanál <img title="not.jpg" src="data/images/not.jpg" alt="not.jpg" width="14" height="14" /></li>\r\n<li>Avatary pro přihlášené uživatele <img title="kaspar-fajfka.png" src="data/images/kaspar-fajfka.png" alt="kaspar-fajfka.png" width="14" height="14" /></li>\r\n<li>Kategorie se zajímavými odkazy <img title="OK" src="data/images/kaspar-fajfka.png" alt="OK" width="14" height="14" /> na 99% již hotovo<br /></li>\r\n<li>Změna vzhledu sdílených dat   <img title="not.jpg" src="data/images/not.jpg" alt="not.jpg" width="14" height="14" /></li>\r\n<li>přidání funkce uploadu souborů  <img title="kaspar-fajfka.png" src="data/images/kaspar-fajfka.png" alt="kaspar-fajfka.png" width="14" height="14" /></li>\r\n<li>Vytvoření vlastního fóra <img title="not.jpg" src="data/images/not.jpg" alt="not.jpg" width="14" height="14" /></li>\r\n<li>Kategorie s novinkami (jednoduché, krátké novinky, kódové jméno "Jitrničky" :-))<img title="kaspar-fajfka.png" src="data/images/kaspar-fajfka.png" alt="kaspar-fajfka.png" width="14" height="14" /></li>\r\n<li>Práce na optimalizaci SEO <img title="not.jpg" src="data/images/not.jpg" alt="not.jpg" width="14" height="14" /> na 50% již hotovo<br /></li>\r\n</ul>\r\n<p>Pokud Vás něco napadne tak pište do komentářů!!</p>\r\n<p><strong>Hodně zdaru a vypečené nálady pro práci s novým webem!!</strong></p>\r\n<p>Seznam změn provdených od nahození nového webu:</p>\r\n<ul>\r\n<li>19.04.2008 - Přidána podpora systému <a href="http://texy.info/">TEXY</a> do komentářů, v nejbližší době i do guestbooku.</li>\r\n<li>19.04.2008 - Přidána podpora Texy i do guestbooku a přepracování designu guestbooku, tak aby byl totožný s designem stránek.</li>\r\n<li>19.04.2008 - Do postranního panelu blogu přidány nejdiskutovanější blogy</li>\r\n<li>20.04.2008 - Přidána podpora pro avatary, což je obrázek uživatele. Zatím se zobrazují pouze v guestbooku a komentářích, ale možná by se dali využít i u blogů. Avatar se přidává a odstraňuje přes účet.</li>\r\n<li>22.04.2008 - Do blogu přidána funkce pro přidávání souborů jakéhokoliv typu. Php soubory jsou standartně přejmenovány na phps soubory (scripty, které se neprovádí)</li>\r\n<li>24.04.2008 - Přidán boční panel na přihlášení a odhlášení</li>\r\n<li>25.04.2008 - Opravena chyba s mazáním avataru</li>\r\n<li>06.05.2008 - Ve fotogalerii přidán popisek fotek a upraveno stahování souborů</li>\r\n<li>12.05.2008 - Předělána knihovna pro práci s obrázky v textech (rozšíření třídy files), doplněn ověřovací kód do guestbooku (guestbook byl napaden spambotem) a odstranění povinné emailové adresy</li>\r\n<li>12.05.2008 - Přidána kategorie s odkazy, které lze řadit do kategorií</li>\r\n<li>13.05.2008 - Opravena spousta chyb, rozšíření knihoven pro práci s databází a hlavně přidány <strong><a title="Novinky" href="http://vypecky.info/index.php?category=13">Novinky</a>!</strong> kde můžete psát lehké zprávy o dění ve světě<img title="Smile" src="jscripts/tiny_mce/plugins/emotions/img/smiley-smile.gif" border="0" alt="Smile" />.</li>\r\n</ul>', 1208273133, 3),
(34, 'vypecky-30', 'Výpečky 3.0', '<p>Tak přátelé, Kubin zase zamakal a podařilo se mu nahodit Výpečky 3.0. Až ho potkáte, kupte mu aspoň pivo :-). Pokud budete mít nějaké připomínky ke grafice, směřujte je na mně - tzn. pod tento příspěvek, na mail, nebo do knihy návštěv.</p>', 1208354633, 4),
(35, 'creativity-night-aftermath', 'Creativity Night - Aftermath', '<p>Tak včerejší párty na Flédě se, musím říct, celkem vyvedla. Program měl určité mezery, ale ty se podařilo úspěšně vyplnit alkoholem a hovorem s přáteli. Co se hudby týče, pochválil bych zejména Pavlíka a pražské <a href="http://www.agave9.com">Agave 9</a> - velice příjemná muzika. Bohužel hráli až jako poslední, takže si je vyslechlo už jen zdravé jádro ostřílených harcovníků. Výborné byly i staré vypalovačky v podání Irish Cream. Na fotky se můžete podívat do <a href="index.php?category=11&amp;action=showgalery&amp;galery=38">galerky</a>.</p>\r\n<p> </p>', 1208355239, 4),
(36, 'pavel-je-tata', 'Pavel je Tata!!!', '<p>A vážení, abych nezapomněl - pro všechny, kdo to náhodou ještě neví: "Pavel Dančes je čerstvý tata!!!" Včera se jeho Jance narodila krásná holčička Andulka - 3,65 kila, 50cm. Tak mu nezapomeňte pogratulavat! Po případě si s ním i vypít - tuším, že o víkendu se plánuje nějaká taškařice. Však on vám Pavel dá vědět :-)</p>', 1208355635, 4),
(37, 'prace-do-skoly', 'Práce do školy', '<p>Ono dělat něco do školy není jenom tak. S tím máme všichni dojista rozsáhlé zkušenosti. Tak například já teď musím napsat bakalářku. Termíny se mi s každým dnem zkracují a já hledám všemožné skuliny ve svých časových plánech, jak si čas vyhrazený na psaní, zacpat něčím jiným, než je právě psaní. A klesl jsem tak hluboko, že jsem do googlu zadal frázi "kvalitní humor". No, zas tak kvalitní to není, ale pokud musíte dělat něco do školy (nebo do práce, ono je to vlastně celkem jedno), tak to dozajista přijde <a href="http://patrikvogl.cz/321-kvalitni-humor-bez-zbytecnych-reci.html" target="_blank">vhod</a>.</p>', 1208520596, 11),
(38, 'fight-back', 'FIGHT BACK!!!', '<p style="text-align: justify;">Když už jsme u těch humorných kratochvílí... Tohle jsem si nemohl odpustit :-) Se vzrůstajícím náporem militantních nekuřáků, kteří nás vytlačují ze zastávek MHD, nemocnic, domovů důchodců, škol, jeslí a sirotčinců, zkrátka ze všech míst, kde všichni tak rádi kouříme, vzrůstá také potřeba hájit opakovaně znásilňovaná práva kuřáků. Je nutné vyzbrojit se stejnými prostředky, jaké třímají v rukou naši odpůrci a s hlavou vztyčenou říct jasné: "S náma nikdo vydrbávat nebude!!!."</p>\r\n<p style="text-align: center;">FIGHT BACK !!!</p>\r\n<p style="text-align: center;"><a title="./data/images/cigara2.jpg" rel="thumbnail" href="data/images/cigara2.jpg"><img style="opacity: 1;" src="data/images/cigara2.jpg" alt="" /></a></p>\r\n<p style="text-align: center;">Chcete-li si vyrobit podobnou protinekuřáckou krabičku, nálepka je ke <a href="data/nalepka/nalepka.pdf">stažení zde.</a></p>', 1208601915, 4),
(39, 'vernisaz', 'Vernisáž', '<p> </p>\r\n<p class="MsoNormal">Milí přátelé,</p>\r\n<p class="MsoNormal"><span> </span><span> </span>jsem pravidelnou čtenářkou Vašich příspěvků. Krátím si tak chvíle nejen před spaním, ale občas také mezi sledováním Southparku a House. Při louskání Kubových a Kubových elaborátů si však mnohdy postesknu a polituji sama sebe zjištěním, jak je můj život nudný. Já nemám ucpaný záchod, nejsem carský důstojník, proti Heleně nic nemám a vlakem nejezdím. A ještě k tomu mi došel House!!!</p>\r\n<p class="MsoNormal">Nicméně v pondělí se na mě přece jen usmálo štěstí. Po desetihodinové šichtě v závodu, jež mladým i postarším lidem svou efektivní marketingovou strategií vnucuje myšlenku, že se učí anglicky, mi zavolal kamarád Saďour, jestli bych nezašla na kafe. Byl krásný jarní den, což nám vnuklo myšlenku, že by snad zahrádka v Artu mohla být otevřená. <span> </span>Bohužel tomu tak nebylo, tak jsme holt byli nuceni zvolit jinou kavárnu. Takovou, o které toho moc nevíme… V Chat Noir na Lidické jsem byla jednou. Dělají tam dobrou kávu a jsou tam <strong>mladé</strong> příjemné servírky. Nu což.</p>\r\n<p class="MsoNormal">Již po cestě jsme se hluboce ponořili do rozhovoru, a tak nám, jak jsme usedali ke stolečku v kavárně, ušlo několik detailů, které se později ukázaly být velmi podstatnými. Vybrali jsme si místo u piána, dostali jsme kávu od <strong>mladé</strong> příjemné servírky. Hlavním tématem rozhovoru bylo jakési rozhořčení nad lidmi, kteří mluví moc, používají vágní pojmy a neumějí přesně vyjádřit myšlenku, a že ať už náhodou či ne, tito lidé mnohdy pocházejí zejména z uměleckých kruhů. Toto téma rozhovoru paradoxně nebylo podníceno prostředím, v němž jsme se ocitli, nýbrž čerstvou Saďourovou zkušeností.</p>\r\n<p class="MsoNormal">V tu chvíli jsme ještě vůbec netušili, že se nacházíme v pravém centru těchto lidí!!! Vodítek přitom bylo dost. Na stěnách visely abstraktní obrazy, na dveřích byl jakýsi plakát o čemsi (nevšimli jsme si) a v kavárně se podezřele začali scházet senioři. Ani po tom, co nás osmdesátiletý kytarista vyzval, ať si sedneme někam jinam, protože musí nazvučit, jsme si neuvědomili, že jsme v pasti.</p>\r\n<p class="MsoNormal">Seniorů přibývalo. Po čtvrthodinové zkoušce kytarového komba už kavárna praskala ve švech a některé mladší seniorky byly dokonce nuceny postávat u dveří. My dva mladí perspektivní dvacetipětiletí lidé jsme seděli uprostřed toho všeho, pili kávu, kouřili a teď už trochu více překvapeni jsme pokračovali v hovoru. Pak nám to tak trochu došlo, ale pořád ne úplně. „Hele, není to nějaká vernisáž nebo tak něco?“ „Hmm, ty jo, asi vypadnem.“</p>\r\n<p class="MsoNormal">Naneštěstí jsme nějak zaváhali a vypadnout se nám jen tak lehce nepodařilo. Kapela nezačala hrát. Místo toho z šedého davu povystoupil jakýsi pan architekt a jal se nám představovat dílo svého kolegy, ony abstraktní obrazy, kterých jsme si my bohužel povšimli až teď. <span> </span>Dostala jsem takový tísnivý pocit, že mě někdo dá pohlavek a že má na to právo. Takhle malá jsem nikdy nebyla. Moje předtucha nebyla mimo mísu. Hned na to byl Saďour jedním ze seniorů vyzván, aby co nejrychleji uhasil cigaretu.</p>\r\n<p class="MsoNormal">Přituhuje. Není úniku. Mají navrch. Mají to pod kontrolou. Všichni se na nás dívají a vyškrábou nám oči…</p>\r\n<p class="MsoNormal">Patnáctiminutový projev pana architekta se v žádném případě nedá vůbec k ničemu, co jsme v životě slyšeli, přirovnat. Je to těžké, nevíme, jestli se máme smát nebo brečet nebo si nějak vyklestit cestu ven skrz krvežíznivý dav. V každém případě to, o čem jsme se dříve poklidně bavili, teď nastalo v praxi. Pán popisuje umělecký obsah obrazů jazykem, který je více než jen košatý. Noří se do struktur, linií, barevných harmonií s návazností na francouzské strukturalisty, však najednou začíná mluvit o tom, že má někde rozhlednu a že to všem musí říci, co ve svém životě dokázal, načež se opět vrací k obrazcům a přirovnává je k hudbě. Po deseti minutách namočení se do jeho nepředstavitelných představ se naštěstí probírám, lapám po dechu a pár minut jeho perel nahrávám na telefon. Takže už no comment. Enjoy!</p>\r\n<p class="MsoNormal">Nahrávku si můžete stáhnout <a href="data/nalepka/Zaznam.mp3">tady.</a></p>\r\n<p class="MsoNormal">Přeji příjemný poslech a hlavně, přátelé, vždy pečlivě zvažte, kam jdete v Brně na kávu…</p>\r\n<p class="MsoNormal">Pozn. Aby nedošlo k nedorozumění, umění mám moc ráda.</p>', 1208703812, 13);
INSERT INTO `vypecky_blog` (`id_blog`, `key`, `label`, `text`, `time`, `id_user`) VALUES
(40, 'jedovata-jedovnice', 'Jedovatá Jedovnice', '<p> </p>\r\n<p class="MsoNormal">Tento víkend byl opravdu náročný. Jednak nás přijel navštívit můj drahý bratranec Hondzik, jednak na tuto sobotu připadla návštěva Ondřeje a další zeměměřičské zvěře v Jedovnici. Kdo má tu čest znát nějakého geodeta osobně ví, že fakulta geodézie každoročně pořádá pod záminkou výuky v terénu čtrnáctidenní chlastačku podporovanou z fondů Evropské unie. Tyto akce se většinou odehrávají v Jedovnici či jiné obci v blízkosti Brna disponující dostatečnou ubytovací kapacitou a větším počtem restaurantů.</p>\r\n<p class="MsoNormal">Reakce domorodého obyvatelstva na tuto událost se dost zásadně rozcházejí. Zatímco lidé pracující ve sféře služeb (hostinští atd.) berou příjezd geodetů jako příležitost oživit ekonomiku obce a vítají je tequilou a solí, místní matky zavírají okenice a na pětkrát zamčené dveře zpevňují dubovými trámky. Pro veškeré ženské pokolení ve věku od 15 do 60 let, platí po celou dobu výcviku přísný zákaz vycházení. Letos jsem měl možnost zjistit osobně, proč tomu tak je.</p>\r\n<p class="MsoNormal">Hned po příjezdu jsme hochy zastihli v pilné přípravě. Na laně zavěšeném mezi dvěma stromy  trénovali provazolezectví. Udržet rovnováhu je zde totiž často otázkou života a smrti. Cesta z hospody vede v těsné blízkosti rybníka Olšovce a jediné chybné našlápnutí může znamenat smrt utonutím (od kamarádů plně soustředěných na to, aby sami udrželi rovnováhu, se pomoci dočkáte jen těžko).</p>\r\n<p class="MsoNormal">Na ubytovně nás se širokým úsměvem a pánví plnou spišské borovičky přivítal Pavel. Zanedlouho začaly události nabírat vražedné tempo. Po krátké přípravě následoval rychlý výjezd do jakési restaurace, která se ukázala být po střechu nacpaná jinými geodety. Hustým lijákem jsme se tedy vydali směrem k pohostinství Chaloupky. V tu chvíli mi vzpomínka na mou nepromokavou bundu, pohodlně rozloženou v kufru Hondzikova Fordu, přišla značně bolestná (ještě že mám pláštěnku alespoň na foťák :-). Když se bratranec s druhou várkou opilců na zadním sedadle konečně objevil, pokusil jsem se k bundě dostat. Má zběsilá gestikulace a poskakování v lijavci byly předmětem všeobecného veselí, ale kufr mi byl otevřen až po 10 minutách řevu a kopání do karoserie.</p>\r\n<p class="MsoNormal">Když jsem se bratrance později ptal, co na větě: „Otevři kuwa ten kufr!!!“, nepochopil, vylezlo z něj, že se domníval, že se do kufru chci schovat před nepřízní počasí a on, věda, že tam není místo, mě chtěl ušetřit trpkého zklamání. S hořkým úsměvem jsem si svou, teď už zbytečnou, nepromokavou bundu hodil přes promočená záda a vydal se na cestu.</p>\r\n<p class="MsoNormal">Chaloupky byly sice obdobně narvané, ale naštěstí se zjistilo, že hospoda má podobnou kapacitu jako šalina – tzn. že se do ní vždycky vejde o jednoho víc. Naše patnáctičlenná skupinka se pružně vmáčkla ke stolu pro šest, kde už seděly dvě místní slečny (očividně zcela ignorovaly zákaz vycházení, bláhově se spoléhaje na svůj ne zcela konvenční vzhled). Netrvalo dlouho a nějaký dobroděj jim omluvně objednal drink. Slečny si to nenechaly líbit a rundu otočily (vzhledem k poměru 2:15 jsem se tomu i dost divil). To byla pro přítomné pány hozená rukavice a rozpoutala se zelená válka, která nemohla mít vítěze. Nikdy jsem netoužil vědět, jak chutná magické oko, ale bohužel jsem této zkušenosti neušel.</p>\r\n<p class="MsoNormal">Po lítém boji se zdecimované vojsko odebralo na místní diskotéku. Jak už to na vesnicích bývá, zacestovali jsme si zpátky časem (pro DJ očividně 20. století skončilo někdy v roce 1988) a upravili hladinu zelené několika dalšími likéry. Na místních záchodcích pak kdosi v hlubokém zamyšlení nad výsledkem své nevolnosti prohlásil, že takhle to asi vypadá, když se poblije duha. Znechucen uvolněnými mravy zeměměrné mládeže, kvantitou a kvalitou požitých lihovin a hudební produkcí, odebral jsem se asi ve 4 ráno na lože. Míru uvolněnosti mravů vám nejlépe osvětlí krátká příhoda z diskotéky. Jeden kamarád se opodál stojící drobné a křehké slečny otázal, kde že jsou tady toalety. Slečna mu to ochotně ukázala a nabídla se, že ho tam osobně doprovodí. Když kamarád, který situaci očividně nepochopil v plné šíři, zdvořile odvětil, že to snad zvládne sám, slečna nakvašeně odsekla, ať si teda trhne.</p>\r\n<p class="MsoNormal">Sobotní odpoledne jsme strávili rekonstrukcí pátečního večera. Na druhý den musela většina z nás spolu s Cimrmanovým Hlavsou přiznat, že z celého večera vidíme jen výjevy. Vyspávání kocoviny bylo však ověnčeno četnými kratochvílemi. Nejvíc to odnesl Ondřej, který zmožen včerejší rolí Donjuana, usnul tak jak byl, obut a v kraťasech. Nejprve jsme na něm vyzkoušeli depilaci pomocí kobercové pásky, kterou mu nějaký dobroděj několikrát obtočil lýtka. Z jeho výrazu se dalo usuzovat, že svou budoucí ženu do alotrií s horkým voskem rozhodně nutit nebude. Empatii se holt někdy musí pomoct. Když vyčerpán zkrášlovací metodou usnul podruhé, byl i s postelí vynesen na balkón. Balancování nad propastí v dosti nestabilní posteli ho probralo téměř nadobro. Tomu, že nebyl vynesen z ubytovny zabránila pouze nevhodná rozteč futer.</p>\r\n<p style="text-align: center;"> </p>\r\n<p class="MsoNormal">Jak je vidět, dosahovala úroveň zábavy značné výše. Vzhledem k nepřízni počasí se neuskutečnila plánovaná vycházka ke krasovým propadáním a celý den byl nečekaně zakončen odchodem do blízkého restaurantu. Ondřej se nám vrátí ve čtvrtek, a bude-li schopen myslet, mluvit, či alespoň psát, snad nám poví, jak to bylo dál, protože já se v obavě o své zdraví večerního sezení v hospodě už nezúčastnil.</p>\r\n<p class="MsoNormal">Fotodokumentace je ke zhlédnutí <a href="index.php?category=11&amp;action=showgalery&amp;galery=39">zde</a>.</p>', 1208811423, 4),
(41, 'brnenska-anabaze-jeni013', 'Brněnská anabáze jeni013', '<p><span style="color: #000000; font-size: small;">Aááách, tak jsem tedy doma...</span></p>\r\n<p><span style="color: #000000; font-size: small;">Před minutkou jsem vylezl z vany, kde jsem ze sebe hodiny smýval houbou, jarem, pískem a jelenem špínu města (Brno) i hnůj vesnice (JEDovnice). Začalo to už ve čtvrtek. Přijel jsem do Brna ucházet se o zaměstnání. Tato sama o sobě dosti stresující situace zakrývá se dnes v paměti nadýchaným obláčkem s mandlovou příchutí, neb čert mi kázal navštívit kamarády v jejich příbytku na Balbínově 20. Obyčejně bývám k okolí velice vnímavý, řekl bych až přehnaně, toho večera se však prozřetelnosti nepodařilo naznačit mi špatný směr, jimiž se můj vůz ubíral. Auto si vesele broukalo večerní zácpou, ale co jsem odbočil na Starku, ouha! - z převodovky dým, ze spojky smrad, z motoru kašel a na světlech z prdele klika, když jsem se tak tak vyhnul fabce, co tam parkovala na střeše.</span></p>\r\n<p><span style="color: #000000; font-size: small;">Zazvonil jsem u dveří - otevřel mi Jack. Nezmohl se dojetím na slovo, tak jsem se pozval dál a pustil se s chutí do poslední porce večeře. Gulášek byl výborný a mezitím se po domě rozkřiklo, že se hodlám zdržet. Kubin odjel ještě večer, Katy hned druhý den. Na pivo jsme tedy vytáhli nic netušícího Michálka. Má odvěká averze k alkoholu dala Vepřovým Výpečkům (VV) do ruky nebezpečnou zbraň. Auto (což na VV není tak neobvyklé...) se střízlivým řidičem (...vyloučené). S prvními doušky se do mysli začaly vkrádat i neobvykle kreativní nápady. Nejen že výběr hospody se rozšířil z okruhu 100m od jakékoli refýže na 50m od jakéhokoli parkoviště, ale nápady jako nakoupit lahváče a vylít se někde u Jihlavy nebyly prosazeny jen díky absenci paliva a spolihlivosti vozu.</span></p>\r\n<p><span style="color: #000000; font-size: small;">Ráno jsme se probudili až odpoledne. Po náročném večírku následoval všeobecný kopr. Vzpoměl jsem si, že jsem si chtěl nakoupit nějaké knihy, a netrvalo ani 4 hodiny a už jsem byl na cestě do města. Cestou jsem potkal panáčka, který cosi vleže a s rukama v želízkách vysvětloval kolemjdoucím policistům. Neustále při tom strkal hlavu pod zadní kolo zaparkovaného automobilu. Asi nějaká performance, řekl jsem si, když tu si mého zájmu chlapík všiml a začal promlouvat ke mě. Na mě však zrovinka dolehla lhostejnost velkoměsta, a tak jsem měl strašně napilno. Slíbili jsme s Kubou, že navštívíme Ondřeje na terénním výcviku geodetů kdesi v okolí Jedovnic. </span></p>\r\n<p><span style="color: #000000; font-size: small;">Cesta do Jedovnic je lemována mnoha úskalími. Především bych jmenoval páteční špičku. Je až s podivem, kolik lidí se snažilo cestovat odněkud kamkoliv silnicí, jež jak jsme později zjistili do Jedovnic nevede ani přibližně. Rychlostí babky s roštím jsme přibyli do Blanska. Zde leží nástraha č.2. V Blansku žijí lidé, kteří vám velice rádi pomohou cennou radou, když nevíte kudy kam. Pokud mohu radit - při orientaci po Blansku se řiďte výhradně instinktem, letokruhy na pařezech, polárkou, či hodinkami (digitání nedoporučuji - budou Vám platné jak ochotný domorodec). V Jedovnicích je pak již orientace snadná - přijedete k Hostinci V Chaloupkách Za Restaurací Olšovec se dáte doprava a kolem Hospody Baruchov k Baru Riviéra, ale to už jste skoro na místě.</span></p>\r\n<p><span style="color: #000000; font-size: small;">Ondra byl moc rád že nás vidí. Bylo mi to podezřelé, protože to často neslýchám, a až později jsem pochopil, že se nejednalo o radost že vidí nás, ale že vidí vůbec. Načapali jsme jeho družinu při roztomilé zábavě se slick lajnou. Prostě přivážete provázek mezi dva stromy a pak se po něm procházíte tam a zpět a zase tam a zpět a tak pořád dokola až do omrzení. Tato hra má, jako každá jiná, své favority, i své outsidery. Například Jakub se na lajně pohyboval skutečně jistě v obou směrech za což si zasloužil obdiv. Ostatní se spokojili i s malým krůčkem, já jsem se ve své skromnosti spokojil se zdravým přistáním, aniž bych slickline zatížil. Po takové dřině musí přijít zákonitě zábava. Chvilku to trvalo, ale po menším přemlouvání bylo asi 15 lidí ochotno jít do hospody. Po krapet zmatečném přesunu v průtrži mračen nás mile přivítal</span><span style="color: #000000; font-size: small;"> hostinec</span><span style="color: #000000; font-size: small;"> V Chaloupkách, jen si nebylo kam sednout. Nakonec se 2 sympatické slečny rozhodly u svého stolu uskrovnit, čímž daly nevědomky celému večeru zelený nádech.</span></p>\r\n<p><span style="color: #000000; font-size: small;">Řeči se vedou, pivo se pije, a snad aby se pánové zachovali galantně a nedýchali na dámy pivní smrady z úst, objednali si kolečko mátového likéru. Netrvalo dlouho a za štopečku se tahalo notně. Vrcholem byla pak ukázka krvavých zad – video snad prozradí více. Zábava eskalovala, V Chaloupkách se pomalu zavíralo – všichni byli připraveni na zlatý hřeb večera. V baru Riviera se tu noc konal hudební večer na téma současná populární hudba (čti DiskoTrysko). Geodeti nejsou žádně Béčka a hned bylo jasné, kdo tady drží pevně dramaturgii večera. Mezitím co si DJ dopisoval domácí úkol z Vlastivědy, na parketu a hlavně na baru se rozpoutávalo hotové inferno… Byly viděny nové choreografické kreace – Baryšnikov se vytasil se zbrusu novou figurou – let od baru na parket, skok, skluz a nedotočené salto vzad, to vše s partnerkou zavěšenou na zádech – lze se domnívat, že tohle je ten zatím pečlivě tajený skok Zlomená peruť. Byl jsem v transu. V místnosti panoval všeobjímající duch laciného alkoholu, nezávazného sexu a rytmické hudby. A v tu chvíli jsem dostal hlad. Vše se rozplynulo, kouzlo pominulo a já jsem měl hlad. A ve všem tom zmatku jsem našel spřízněnou duši. Jack měl taky hlad, Nešlo to jinak, sedli jsme do auta a fičeli rovnou cestou na Svoboďák. U Araba jsme nafasovali toho nejchutnějšího burgera.</span></p>\r\n<p><span style="color: #000000; font-size: small;">Sobota byla coby after party také zajímavá, ale o tom zas jindy (třeba to tu napíše Ondřej sám, jak to dopadlo…).</span></p>', 1208821417, 10),
(46, 'vysvetleni', 'Vysvětlení', '<p>Po vzoru hesla: "Nejlepší obrana je útok," musím reagovat na rozpustilé narážky na téma Jedovnice, geodeti, výuka v terénu a alkohol, uveřejněné v předchozích blozích .  Není pochyb o mravních hodnotách obou pisálků, kteří s heslem na rtech „podle sebe soudím tebe“ z geodetů udělali ožralá prasata s nekonečnou žížní, či zvěř, kterou VUT vypustilo do obce Jedovnice. Proto považuji za nutné, předložit vysvětlení událostí 14.4-22.4 2008.</p>\r\n<p>Tak trochu mi připadá, že nikdo z negeodetů nepochopil pojem "výuka v terénu". Jedná se o součást povinné vyuky a slouží k ucelenému rozvoji a aplikaci vědomostí nabytých za předchozího studia. Hlavní náplní je práce na zadných úlohách a vyhotovení náležitých protokolů. Pokud zbude volný čas, následuje psychická  a fyzická regenerace, doplněná o teoretickou přípravu na další den. Během doby výuky není jak pro studenty, tak pro učitele přípustná konzumace alkoholu, jakož i jiných drog. Navíc je každý student povinnen ctít přísahu složenou při  posvátném aktu Imatrikulace. Doslova: "Svým chováním nezavdám nikomu příčinu k pochybnostem o dobrých mravech studenta i celého Vysokého Učení Technického.....".</p>\r\n<p>Rád bych uvedl na pravou míru několik událostí zmíněných v  nevkusných a nevalných příspěvcích blogu. Nejožehavějším tématem je fakt, že někteří studenti byli snad  viděni při konzumaci alkoholu. Nejednalo se o svévolné porušení řádu, ale o cílené opatření vedení výuky. Zvláště vybraní jedinci byli vysláni vypít všechen alkohol v místních knajpách, putykách, restauracích, barech, bistrech, hotelech, samoobsluhách a kioscích, aby se zbytek osazenstva Tyršovy osady nemohl opít. Bohužel snaha zablokovat zmíněná zařízení byla přímo úměrná jejich množsví a její plnění na hranici lidských sil.  Našlo se však jedno <em>komando smrti</em>, které splnilo úkol nad rámec očekávání: Členové: Schreier, Matas, Rybecký, Kozák, Kosvica, Vančura, Trantinová a Röschl dokázali vyřadit z provozu skoro tři „doupata neřesti“. A jak? Jednoduše. Vrchní se prostě uběhal k smrti. Samozřejmě se našel jeden případ   svévolné  konzumace alkoholu osobou nepověřenou, ale i tento problém se vyřešil. Viník se obětoval a vypil sám pět litrů vína, čímž odčinil provinění a po vypumpování žaludku mu byla svěřena funkce kontrolora.</p>\r\n<p>Další hojně zmiňovanou událostí je návštěva Diskotéky Riviera. Připadá mi zbytečné a nevhodné zmiňovat se o tom, že Ondřej je v tanečních kreacích jen o trochu více progresivní než ostatní tanečníci a ruku na srdce, kolikrát jste, jako malé děti spadli, než jste udělali první krok? Zpytujte svědomí než po někom hodíte kamenem a jeho pokusy vstát budete častovat názvey jako „Zlomený šíp“.  Jinak tato diskotéka sloužila jako náborové středisko do nových<em> komand smrti</em>.<br />Některí studenti byli  dokonce  nařčeni z užívání marihuany. To je naprostá lež. Rozbitím rychlovarné konvice padla jediná možnost, jak do těla dostat bylinné výtažky proti všemožným chorobám. Jediné řešení bylo přistoupit ke zplynování a následné inhalaci. A pokud měl kdokoliv pochyby o legálnosti celé farmaceutické akce, měl to oznámit <em>komandům smrti</em>, nebo rovnou na Policii ČR. To se týče i ostatního chování studentů. Nevkusné a sprosté komentáře a druhořadé vtípky publikované prostřednictvím "seriozniho" serveru  www.vypecky.info, jsou prach obyčejnou snahou o senzaci, nemající s realitou nic společného. O takovou škváru by neměl zájem ani Blesk.<br /> Není pravda, že se ženy z obce Jedovnice před mírumilovnými a slušnými geodety schovávaly v kryptě pod kostelem.  Úkryt pod ochranou Boží rodičky byl zřízen z důvodu příjezdu dvou erotomanů-alkoholiků s valašským přízvukem ve Fordu C-MAX.</p>\r\n<p>Označení „geodetická zvěř“ bude předáno právním zástupcům Fakulty stavební a doufám, že si  pisálci konečně uvědomili, jak při komentování výuky v terénu šlápli vedle a ukřivdili nevinným, slušným a inteligentním studentům. Již teď je ale jasné, že z  obou grafomanů hovořila  obyčejná závist.<br /><br /><br /><br /></p>', 1209150764, 9),
(44, 'jedovnice-aftermath', 'Jedovnice Aftermath', '<p>Zdravím. Tak kluci se nám konečně vrátili z výuky v terénu. Ántré to bylo vskutku velkolepé. Včera asi tak kolem 4 odpoledne se spříšerným jekotem rozdrnčel domovní zvonek a do dveří se vpotácela dvě zarostlá, pobledlá individua. Kruhy pod očima jim zakrývaly větší část obličeje, ten zbytek byl posetý jaterními skvrnami. Bylo jasné, že výuka v terénu si opět vybrala svou krutou daň. Není nic smutnějšího, než vidět dva jinochy, jinak kypící zdravím a mladistvou energií, naprosto zničené vysokoškolským studiem. Z rozjuchaných opilců po delší snaze zkrotit mluvidla vylezlo, že svůj triumfální návrat do Brna náležitě oslavili v restauraci Svitava. Vyčerpáni svým jednovětným proslovem okamžitě upadli do komatozního spánku, ze kterého se probrali až dnes v časných dopoledních hodinách. Doposud se z nich nepodařilo vypáčit, co se během posledního týdne jejich pobytu v Jedovnici událo - těžko říct jestli byly události posledních dnů natolik traumatické, že nám je prostě nechtěli sdělit, nebo jejich paměť potřebuje ještě několik dní rekonvalscence. Nezbývá, než doufat, že se nakonec rozpomenou a konečně nám objasní, co se to v té Jedovnici kua dělo.</p>\r\n<p>Tady jsou dvě krátká videa z naší návštěvy, která vám snad pomohou pochopit závažnost situace.</p>\r\n<p>Enjoy!!!</p>\r\n<p> </p>\r\n<p> </p>\r\n<p style="text-align: center;">\r\n<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="425" height="355" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n<param name="wmode" value="transparent" />\r\n<param name="src" value="http://www.youtube.com/v/kgqbSjJHDyU&amp;hl=en" /><embed type="application/x-shockwave-flash" width="425" height="355" src="http://www.youtube.com/v/kgqbSjJHDyU&amp;hl=en" wmode="transparent"></embed>\r\n</object>\r\n</p>\r\n<p style="text-align: center;">Kameraman nám bohužel odstřihnul památnou závěrečnou hlášku kolegy Šrajera: "Blahopřeju tatínku, je to chlup!"</p>\r\n<p style="text-align: center;">\r\n<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="425" height="355" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n<param name="wmode" value="transparent" />\r\n<param name="src" value="http://www.youtube.com/v/nm-fVU6L0P4&amp;hl=en" /><embed type="application/x-shockwave-flash" width="425" height="355" src="http://www.youtube.com/v/nm-fVU6L0P4&amp;hl=en" wmode="transparent"></embed>\r\n</object>\r\n</p>', 1209146261, 4),
(45, 'hon-na-mouchu', 'Hon na mouchu', '<p>Většina z vás už to asi viděla, ale přesto si dovolím nahodit jsem jeden krátký filmek ze života Výpečků (je třeba otestovat Kubinův systém uploadu mediálních souborů). Ano, jak správně tušíte, jedná se o záznam Ondřejova zápasu s obtížným hmyzem.  Choreografie dává tušit jeho hlubokou zálibu v moderním tanci, která jindy vyplývá na povrch pouze pod vlivem alkoholu. Do mysli se vkrádá přirovnání k Benderově tajné fascinaci Country hudbou, kterou odhalí pouze přítomnost magnetu.</p>\r\n<p>Enjoy !!!</p>\r\n<p> </p>\r\n<p style="text-align: center;">\r\n<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="320" height="240" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n<param name="src" value="data/userfiles/moucha21.swf" /><embed type="application/x-shockwave-flash" width="320" height="240" src="data/userfiles/moucha21.swf"></embed>\r\n</object>\r\n</p>\r\n<p style="text-align: center;"> </p>\r\n<p style="text-align: center;">Soubor si můžete <a href=" http://www.vypecky.info/download.php?url=./data/userfiles/&amp;file=moucha21.swf  ">stáhnout zde.</a></p>', 1209146645, 4),
(47, 'agave-9-na-skle', 'Agave 9 na Skle', '<p>Všem ctitelům britské kytarové hudby bych rád vřele doporučil květnový trojkoncert na Skleněné louce. V pátek 02.05.2008 zahrají Pavel Drábek a jeho pražská skupina <a href="http://www.agave9.com/">Agave 9</a>, němečtí <a href="http://www.myspace.com/playfellowband" target="_blank">playfellow</a> a taktéž němečtí <a href="http://solche.de/" target="_blank">Solche.</a> Šťourové by mohli namítat, že ani jedna kapela není z GB, tak jakápak britská kytarovka. My znalí věci však dobře víme, že britrock je žánrem nadnárodním a nikdo v něm nevyniká více než němci a češi. Takže si laskavě přijďte pro porci lehce melanocholické, bezstarostné, melodické,  kakofonické, energizující, přemýšlivé a nekompromisně rytmické muziky. Pavel, ačkoliv původem z matičky měst, je vlastně taky Výpeček a byla by ostuda jeho i ostatní borce nepodpořit :-).</p>\r\n<p>Sejdeme se na Skleňence. Howg</p>\r\n<p><strong></strong><a href="http://solche.de/" target="_blank"><br /></a></p>', 1209316963, 4),
(48, 'nedelni-nedelani', 'Nedělní nedělání', '<p> </p>\r\n<p class="MsoNormal">Některým z nás pomalu začíná zkouškové a jak tady před časem trefně poznamenal Náca: "Úměrně se vzrůstající potřebou studia, roste také potřeba tzv. substitučních aktivit." Tzn. starý známý kopr, který řešíme tak, že řvoucí a černočerné svědomí zaměstnáme nějakou jinou, nejlépe zbytečnou či absurdní činností. Ta samozřejmě se studiem nemůže souviset ani okrajově. Pro zjednodušení jsem tento jev pojmenoval "zkratová substituce oktrojovaných kognitivních procesů" – zkráceně ZSOKP (1). V důsledku ZSOKP došlo tuto neděli na Výpečkách ke skandálním událostem. Přirozený biotop našeho privátu byl brutálně narušen substitučními aktivitami. Habitat domu, dříve založený na teorii chaosu, byl nemilosrdně podroben pevnému řádu kartuziánské filozofie. Dramaticky se zvýšila míra uspořádanosti hmotných prvků interiéru, čehož se docílilo jejich přesunutím do nových lokací (2). V důsledku masivní aplikace detergentu se značně snížila jak denzita tak diverzita živočišných a rostlinných druhů a došlo tak k narušení komplexní symbiózy a potravního řetězce, jimiž se náš privát honosil dříve (3). Celým domem teď prostupuje intenzivní pocit depersonalizace a extirpace umocněný indigescí způsobenou exterminací aborigenních mikroorganismů (4).</p>\r\n<p class="MsoNormal">Nezbývá tedy než doufat, že se zcela zruinovaný ekosystém Výpečků časem sám vrátí do normálu.</p>\r\n<p class="MsoNormal">Příště prostě musíme substituční aktivity volit uvážlivěji.</p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal">Vysvětlivky:</p>\r\n<p class="MsoNormal">1 - modelovým příkladem ZSOKP je například fakt, že píšu tenhle článek.</p>\r\n<p class="MsoNormal">2 - je tady pořádek, ale nikdo nemůže nic najít</p>\r\n<p class="MsoNormal">3 - je tady sterilně čisto, smrdí to tu jako v nemocnici a není tu žádný hmyz a plísně.</p>\r\n<p class="MsoNormal">4 – necítíme se tu jako doma, máme depku a zažívací potíže.</p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal">PS: Jediná včerejší zkratová aktivita, která se povedla, byla návštěva dne otevřených dveří ve vozovně Komín spojená s prohlídkou starých trolejbusů a hasičských vozů. <a href="index.php?category=11&amp;action=showgalery&amp;galery=40">Tady</a> jsem přihodil pár fotek archivních trajflů.</p>', 1209383001, 4),
(49, 'je-to-takove-moje-hodnoceni-ale-cuju', 'je to takové moje hodnocení, ale čuju', '<p>Nazdar seci, toz su velice jakoze rad,ze sa ne podarilo sa zalogovat, kuva slozite to je pro ma, ja su normalni plebejec a nee ITak.Na majl ne prislo heslo ale fonty su nejake pokuwene, bo tam misto ňo bylo jakesik Ajkjsgkh nebo tak neco.Toz hlavne seckym zelam uspesne zvladnuti zkusek , bakalarek statnic a jinych zivotne dulezitych krawin, ktere prodluza meno na zvonku. Kua Zdar, zdravi cerstvy tata!</p>', 1209673844, 14),
(50, 'kulinarsky-svatek-prace', 'Kulinářský svátek práce', '<p>To, že mužské osazenstvo výpečků vaří rádo, často a snad i dobře, je stará známá věc. Včerejšek byl však z kulinářského hlediska dnem D. V dopoledních hodinách jsme se s Kubinem rozhodli, že k dlouhému seznamu osvědčených jídel, přidáme naší oblíbenou svíčkovou. Svíčková je taková kuchařská maturita. Na vysokou laťku, kterou nasadily naše matky a babičky, je dost těžké dosáhnout, natožpak ji překonat. Už v 11 započaly horečnaté přípravy. Byl státní svátek, takže jsme byli nuceni pořídit ingredience ve zlořečeném židenickém Kauflandu. Naše nákupní martyrium bylo částečně vykoupeno objevem DVD se skvělým hororem <a href="http://www.csfd.cz/film/34456-mrtva-hlidka-deathwatch/" target="_blank">Dead Watch</a> (Mrtvá hlídka) za pouhopouhých 30 Kč.</p>\r\n<p> </p>\r\n<p>Po návratu domů, došlo na krájení ingrediencí. Jednohlasně jsme se usnesli, že dodržet správný poměr kořenové zeleniny, je pro úspěšné dosažení kýžené chuti zcela nezbytné. Kubin vytáhl chemické váhy a bloček a ponořil se do vývoje sofistikovaného algoritmu na výpočet poměru kořenové zeleniny s přesností na miligramy. Když začal přemýšlet na tím, že bude třeba při výpočtu zohlednit vzdušnou vlhkost, rosný bod, teplotu a atmosferický tlak, kteréžto veličiny budou samozřejmě v průběhu vaření fluktuovat, došla mi trpělivost. Nevtíravě jsem ho upozornil, že by udělal lépe, kdyby šel nahoru a naprogramoval si jednoduchou aplikaci, která to spočítá za něj. Kubin s poněkud vyšinutým úsměvem ve tváři spěšně odběhl do svého pokoje a já jsem tak v poklidu, nezatížen algoritmy, mohl dokrájet zeleninu a dát se do vaření.</p>\r\n<p> </p>\r\n<p>Po 2 hodinách snažení jsme nad svíčkovou konečně zvítězili. A bylo to slavné vítězství. Myslím, že nebudu přehánět, když řeknu, že to co nám stálo na plotně, byl učiněný chuťový ohňostroj, matka všech svíčkových, propasírovaný smetanovozeleninový zázrak jehož plné sladkokyselé tóny vynáší lidskou duši do nebes. Za zdar projektu nesporně vděčíme osvědčenému pravidlu: "Na vaření je nejdůležitější podlévání." (podlévá se přímo do žaludku slivovicí, či jinou kvalitní lihovinou)</p>\r\n<p> </p>\r\n<p>Ale dost už o jídle. Jak je známo, minimálně polovinu každého gastronomického zážitku tvoří kulturní konzumace, stolničení a všechen ten rozruch kolem. Ti, kdo měli tu čest na Výpečkách pojíst, ví, že každé jídlo je dokonalým příkladem Wagnerova Gesamkunstwerku. Ani tentokrát tomu nebylo jinak. Jídlo strávené v kratochvilné společnosti hostujícího kolegy Zkrata, jsme proložili sledováním Mrtvé hlídky a následně se odebrali na večerní procházku zakončenou v open-air restaurantu Pod Kaštany. Tam nás po chvíli doplnili Michálek se Světlankou. Zbytek státního svátku jsme pak strávili v <img src="file:///C:/DOCUME~1/DELAMA~1/LOCALS~1/Temp/moz-screenshot.jpg" alt="" /> družném hovoru a ještě družnější zábavě (přišlo se na to, že když se spojí Kubinův a Světlančin zvonivý smích, na stole praskají sklenice). Doufám, že jste 1. máj strávil přinejmenším stejně příjemným způsobem a přeju poklidný víkend. (Pavel tuším zapíjí v Jasenici toho svého potomka, tak se nezapomeňte stavit)</p>\r\n<p> </p>\r\n<p>Howg</p>\r\n<p> </p>\r\n<p>PS: Komentovaný fotorecept na svíčkovou si můžete projít <a href="http://www.vypecky.info/fotogalerka/svickova">zde.</a> (Ten je tady hlavně pro tebe Pavle, abys měl v neděli čím překvapit Janu :-)</p>', 1209738228, 4),
(51, 'weekend-in-a-nutshell', 'Weekend in a nutshell', '<p>Tento víkend byl velmi bohatý na zážitky. V pátek se uskutečnil avizovaný koncert pražských <a href="http://www.agave9.com/">Agave 9</a> a jejich německých přátel. Když jsem vás na koncert (až na Michálka se Světlanou bohužel zbytečně) zval, netušil jsem, ve kterém ze tří pater Skleněné louky se bude konat. V pátek jsem s hrůzou zjistil, že to má být sklep. Tato část Skleněnky disponuje velice hustým ovzduším, svéráznou klientelou a ještě svéráznější obsluhou (nikoliv nepříjemnou). V praxi to vypadá tak, že za mlhou hustou, že by se dala krájet, sedí na baru několik rákosníčků a za pazvuků alternativní hudby popíjí zvětralé pivo. Celá, už tak dost smutná scenérie je osvětlena pěticí pomrkávajících 25W žárovek, což ji na veselosti nijak nepřidává. My znalí poměrů, jsme sklepní část Skleněnky žertovně přezdili na předpeklí.</p>\r\n<p> </p>\r\n<p>Navzdory mým počátečním obavám se koncert velice vydařil. Zvuk byl, vzhledem ke klenutému prostoru, více než ucházející, atmosféra hustá a přátelská. Vystoupení <a href="http://www.playfellow.de/">Playfellow</a> bylo hudebně velice zajímavé a technicky dobře zvládnuté. Charakter muziky se blížil ranným Radiohead a zejména zásluhou výborného zpěváka, jsem si koncert opravdu užil. Jen bych je rád viděl na větší stagi. Agave 9 opět poskytli zdravou porci šlapavého kytarového rocku s lehce melancholickým nádechem. Zkrátka podařený undergroundový (doslova) koncert.</p>\r\n<p> </p>\r\n<p>V sobotu jsem pak vyrazil do Hybrálce u Jihlavy, na Beatricí dlouho připravovanou anglistickou grilovačku.  V časných odpoledních hodinách jsme přibyli na zemědělskou usedlost Bětčiných rodičů. Byli jsme vřele přivítáni dvěma bulteriéry, chlebem a gulášem. Po obědě jsme opatřeni károu vyjeli do rodinných hvozdů na dřevo. Je s podivem, že jsme se zadaného úkolu zhostili bez vážněších zranění a v poměrně krátkém čase. Vzhledem k tomu, že naši partu hic tvořili výhradně vysokoškolsky vzdělaní "intelektuálové", přičetl bych zdar celé akce výhradně řádné dávce štěstí. S pocitem, že mně k tomu opravňuje má relativní fyzická zdatnost a chatrné zkušenosti ze skautských táborů, jsem se chopil pantoku. Tragický výsledek mé nabubřelosti na sebe nedal dlouho čekat. Rudolf, který mi nic zlého netuše přidržoval polena, to schytal pantokem do čela. Naštěstí byla rána dost slabá a tak se dal celý incident vyřešit kouskem náplasti. Přesto jsem však z toho pro sebe vyvodil důsledky a pantokem se nadále oháněl v bezpečné vzdálenosti od všeho živého.</p>\r\n<p> </p>\r\n<p>Zbytek večírku proběhl poměrně hladce. Jen mé rozhodnutí strávit noc pod širákem nebylo zrovna šťastné. Po celou dobu grilování byla z pochopitelných důvodů na zahradě přítomna Bětčina fena Kuba. Zmožena nadměrnou konzumací zbytků si v průběhu večera bezskrupulózně ulevila na trávník. Bětčina zahrada je velká - odhadem bych řekl tak 200 metrů čtverečních. Karimatka při tom zaujímá prostor zhruba 2m čtvereční. Tzn. šance, že ji umístíte přímo na psí exkrement, je 1 ku 100. No tak jsem měl prostě štěstí, no.</p>\r\n<p> </p>\r\n<p>Na druhý den jsem víkend zakončil krátkou vycházkou okolím Jihlavy, které se mí spolužáci ke své škodě odmítli zůčastnit. Do galerie přikládám pár kýčovitých fotek, demonstrujících sílu, s jakou jaro napadlo vysočinu. Můžete si je prohlédnout <a href="index.php?category=11&amp;action=showgalery&amp;galery=42">zde.</a></p>\r\n<p> </p>\r\n<p>Howg</p>\r\n<p> </p>\r\n<p>PS: Napiště někdo jak bylo na Pavlově bečici, ať se taky pobavíme.</p>', 1209978487, 4),
(52, 'rip-ambra', 'R.I.P. Ambra', '<p>Tak nám zavřeli Ambru. V téhle příjemné vinárně pod Kauničkama jsme prožili nejeden hezký večer, proto se skupinka nejvěrnějších rozhodla s Ambrou důstojně rozloučit. Bára a spol. zrežírovali rozlučkový večírek ve stylu třicátých let. Už když jsme se úterním večerem blížili k oblýskaným zdem stářím sešlé Ambry, měli jsme intenzivní pocit, že se chronometr přetočil o 70 let nazpátek. Na zídce před Kauničkami seděli dva jinoši v sudeťáckých šortkách s laclem a bílých punčochách. Kouřili balenou cigaretu, klátili nohama a častovali se německými vtipy. Dojem válečného Brna byl umocněn faktem, že Kaunicovy koleje byly tehdy sídlem gestapa.</p>\r\n<p>Uvnitř v Ambře už panovala vlastenečtější nálada. Kromě bohaté židovky, bylo možno spatřit partyzána, dámy a pány z lepší společnosti, kankánové tanečnice, funebráky atp. Já jsem se bohužel kostýmem nevybavil. Už v první chvíli mi bylo jasné, jaké chyby jsem se dopustil, jak svatokrádežně jsem pošlapal pietu důstojného loučení s Ambrou. Proto jsem běžel s prosíkem za Bárou a mámil z ní kostýmek. Bohužel jediný zapůjčený šat, který bylo možno navléct na mou dvoumetrovou figuru, byl kostým sestry z lazaretu. Nu co , povzdechl jsem si a začal na sebe soukat síťované punčochy a halit se do khaki blůzy. Utěšoval jsem se nadějí, že osvícená společnost vysokoškoláků přejde dvoumetrovou vousatou zdravotnici v síťovaných punčochách bez povšimnutí. A musím říci, že se ke mně všichni chovali opravdu galantně, zvláště když zjistili, že součástí kostýmu je kožená brašnička s červeným křížem, do níž Bára důmyslně ukryla láhev bylinného likéru.</p>\r\n<p>Večírek se opravdu vydařil. Ambrou zněl swingový orchestr Karla Vlacha, tančilo se, pilo, hodovalo. Zkrátka kar jak má být. Jen ta nahořklá chuť vzadu na patře, to smutné vědomí, že touhle vinárnou už nikdy nezazní veselý hlahol studentských hlasů, radostné cinkání sklenic a příborů, družný hovor, táhlá moravská píseň, zvonivý smích stokilové Bláži, útržky melodií z klapek rozladěného pijana... Hudba ztichne, okna potemní, židle a stoly osiří, přijedou bagry a dělnícin; slavná epocha ambrozie nenávratně skončí v oblacích prachu a hromadách stavebního odpadu.</p>\r\n<p>Čest její památce!!!</p>', 1210244752, 4),
(57, 'technicke-muzeum', 'Technické Muzeum', '<p>Minulý čtvrtek k nám přijel na návštěvu Jan. Po krátkém brífingu na Staré osadě mi společně s Káťou nabídli, abych se zúčastnil jejich plánované návštěvy technického muzea. Musím přiznat, že jsem se v tu chvíli hluboce zastyděl, protože ačkoliv v Brně žiji už pátým rokem, do tohoto muzea jsem ještě nezavítal. Taková šance doplnit si mezery v kulturním modrém životě se prostě neodmítá, a tak jsem pozvání nadšeně přijal.</p>\r\n<p> </p>\r\n<p>Hned u vstupu nás hodná paní pokladní upozornila, že se v přízemí koná odborný výklad na téma vodní kola, parní stroje a turbíny, který si rozhodně nemůžeme nechat ujít. Pospíšili jsme tedy do přízemí. Tam nás se širokým úsměvem přivítal milý strejda v šedém plášti a kožených sandálcích, ze kterých laškovně probleskovaly bílé ponožky. Když jsem viděl, s jakou jiskrou v oku hledí na ty zázraky mechaniky bylo mi jasné, že výklad bude stát za to. Například pohled, které věnoval parní lokomobile, lze snad spatřit pouze ve tváři mladého jinocha, lačně hledícího na ladné křivky těla milenčina. Komentoval, vysvětloval a demonstroval opravdu s vervou a ze široka. Do výkladů o turbinách a parních strojích vplétal trefné glosy s politicko-ekologickou tématikou a podivné vtípky, které nápadně připomínaly obsah dědečkových dikobrazů z let 1975 – 80. Exkurze byla vskutku zajímavá a poučná, výklad vyčerpávající, a tak, když jsme se konečně dopracovali přes vodní kola, páru, Francisovu, Peltonovu a Kaplanovu turbínu k té Parsonsově (ta se používá v dnešních elektrátrnách), byli jsme všichni značně vyčerpáni a z uší nám se sykotem vycházely obláčky páry.</p>\r\n<p> </p>\r\n<p>Přesto se po skončení výkladu strejda optal, nejsou-li náhodou nějaké dotázečky. Když jsem viděl, s jak zoufalou nadějí přejíždí po tvářích návštěvníků a hledá v jejich strhaných, tupých rysech sebemenší jiskřičku zájmu, zželelo se mi milého strejdy, pokusil jsem se vyloudit na své ovislé tváři alespoň lehký úsměv a nonšalantně jsem se ho zeptal: „Jakoupak má ta Parsonsova turbína asi účinnost?“ Už ve chvíli, kdy mi tato otázka sklouzla ze rtů, bylo jasné, že jsem dohrál. Strejdovi zazářily očka, zhluboka se nadechl a spustil. Dravá informační tsunami nás nemilosrdně smetla a stáhla pod hladinu.</p>\r\n<p> </p>\r\n<p>Už, už to s námi vypadalo zle, pak ale kdosi přišel se spásnou myšlenkou, strejdovi uprchnout a skrýt se v poblíž ležícím pavilonu motocyklů. To jsme ještě nevěděli, že strejda není vázán pouze na výstavní prostor turbín, ale že má na starosti celé přízemí – tedy i pavilon motorek. Asi po pěti minutách strávených o samotě úlevným prohlížením Jaw a ČZ, se nesměle přišoural do naší blízkosti, přitočil se k Janovi a spustil něco o plochodrážkách a dvoutaktech. Z šíře jeho zájmu a vědomostí bylo jasné, že jsme natrefili na pravého polyhistora (brouk pytlík). To ovšem strejda netušil, s kým má tu čest. Jan je, jako ostatně všichni Daňci, specialista na všechno co má motor a kola, zvláště pak na to, co má na sobě logo ČZ nebo Jawa. Je tedy pochopitelné, že převzal iniciativu a role se zcela prohodily. Jan začal hučet do strejdy.</p>\r\n<p> </p>\r\n<p>My s Káťou jsme je zanechali v družném a zaníceném hovoru a v klidu si doprohlíželi zbytek strojů. Obměkčen a rozjařen faktem, že nalezl spřízněnou duši, nám strejda ochotně dovolil překročit zábrany a na motorky si šáhnout. Káťu dokonce na jeden veterán posadil. Po další hodince už to ale přestalo bavit i Jana, a tak jsme společně utekli do druhého patra, kam už za námi strejda nemohl.</p>\r\n<p> </p>\r\n<p>Zbytek expozice byl velmi zajímavý, ale zaměstnanci muzea, kteří měli na starosti jiná patra, už bohužel nebyli tak sdílní. Na dalšího vědátora jsme narazili až v podkroví, v technické herně. Nevím, na jakém podkladě byl tento zakomplexovaný asociál určen jako dozor technicky zvídavé mládeže, ale jisté je, že s jeho vražedným pohledem v zádech, se člověk jen těžko opovážil na cokoliv sáhnout, nedejbože to pokazit. Být pětiletým hošíkem, strašila by mě jeho mastná přehazovačka, popelníkové brýle a uštěpačné poznámky ještě dlouho ve snách.</p>\r\n<p> </p>\r\n<p>Ale i tak byla v herně sranda a vymetli jsme všechny přítomné atrakce. Z muzea jsme se vypotáceli zcela vyčerpaní, ale šťastní jako blechy asi po 4.5 hodinách. Takže vy, kdo jste ještě nebyli: „Hajdy do technického muzea!“</p>', 1211015398, 4),
(54, 'techno-forever', 'Techno Forever', '<p> </p>\r\n<p class="MsoNormal">Legal disclaimer: Tento článek obsahuje silně naturalistické prvky, vulgarismy a politicky nekorektní stanoviska. Text není vhodný pro povahově citlivější jedince, děti, nezletilé, milovníky openairů, rovných beatů a těhotné ženy. Názory kolegy Drobka se nemusí shodovat s názory ostatních Výpečků a není je proto možno považovat za oficiální stanovisko této komunity. Enjoy! :-)</p>\r\n<p class="MsoNormal" style="text-indent: 35.45pt;">Minulý víkend byl pro mě dokonalým svědectvím globalizace. Člověk by si pomyslil, že se ho přece nijak zvlášť nemůže dotknout nic, o čem referují média. Již ve čtvrtek zahltily všechny datové kanály vedoucí k mému vědomí informace o děsivém faktu, že víkend v česku bude ve stylu techna. Je určitě snem každého vlastníka pozemku, když si na jeho louce děděné po generace, poskytující vydatnou píci pro brav i skot, někdo uspořádá technoparty. A noční můra v podobě nekončících basů, stovek aut, hektolitrů moči<span> </span>a tun odpadků je tady.<span> </span>Pokud se tohle děje v Čechách, pak je to v pořádku, avšak když byl ve výčtů budoucích „měsíčních krajin“<span> </span>zmíněn i Vítkov,<span> </span>v duchu jsem menšinové bílé obyvatelstvo města černého jak boty začal litovat.</p>\r\n<p class="MsoNormal"><span> </span>Jsem člověk přející, tolerantní, empatický a je mi jedno co člověk poslouchá za hudbu. Bohužel můj mozek asi nedokáže ocenit krásu techna a životního postoje jeho posluchačů. Být majitel farmaceutické firmy, nebo lihovaru, snad bych dokonce dokázal pochopit smysl technoparty, ale zatím mi krása několikadenního pobytu mezi odpadky a špínou zůstává utajena. Ale což, pokud mě osobně neobtěžují, ať si v tom svinstvu klidně shnijí. Dokonce jsem překousl i krutý fakt, že se ve večerních hodinách hudba rozlévala až do Bělotína, což je, při vzdálenosti 17 km vzdušnou čarou od<span> </span>Vítkova, obdivuhodný výkon mistrů zvuku. Ale jak už tomu bývá, všechno krásné jednou končí. Hodní, střízliví a čistí technaři se vydali do svých domovů. Vlakové spojení Vítkov-Suchdol.n.O a Suchdol.n.O-Brno, co víc si tance a hudby chtivá mládež může přát..........</p>\r\n<p class="MsoNormal"><span> </span>Původně jsem chtěl psát o cestě do Brna s baníkovci, bohužel mi tuto vzpomínku vytlačila událost ještě horší. Jako pravidelný cestující vlakem jsem si prazvláštním způsobem zvykl na zápach staletých nánosů moči linoucích se z toalet a při otvírání dveří vagonu jsem zmíněný odér očekával. Pach moči mě přivítal, bohužel ne stoleté, ale čerstvé. Při plném otevření dveří se na mě vyvalilo skutečné zlo. Směs zápachu kouře, spáleného paralenu, moči, stolice, alkoholu, potu a jiných výměšků málem poslal můj obsah žaludku na cestu kolem světa. Hned jak jsem očistil zamlžené brýle, uviděl jsem příčinu mé nevolnosti. Plná chodbička podivných tvorů: hadry rozervané, vzorky ornice v přírodně zdredovaných vlasech, tváře ožehlé a pravidelný tik v ruce svírající nádobu s alkoholem. Pohlaví jsem dokázal identifikovat asi u 13 % osazenstva, což bylo víceméně jedno. Kdybych tušil, v jaké uličce hrůzy budu trávit dvě hodiny cesty do Brna, vezmu si obojek proti blechám pro psy. Kupodivu jsem po chvíli přestal vnímat jakýkoliv zápach. Sice mi trochu slzely oči a pálila sliznice v ústech,<span> </span>ale dál jsem trpělivě nesl svůj kříž a po chvíli jsem jim dokonce začal závidět<span> </span>krabici vína v rukou. Myslel jsem že utrpení je u konce, avšak osud se mi vysmál do obličeje. Soucit i hněv se ve mně mísil při pohledu na slečnu spící ve stoje asi metr ode mne, když při ztrátě rovnováhy způsobené výhybkami v Přerově sjela tlamou umakart až na zem na které následně vytuhla.<span> </span></p>\r\n<p class="MsoNormal"><span> </span>I nejlepším z nás se občas stane nějaká nehoda a s úsměvem jsem sledoval, jak její, kdysi bíle kalhoty, zdobí mokrý flek mající původ ve špatné funkci svěračů. V jejím případě šlo o fatal error, během pěti minut ji přestaly fungovat i svěrače anální. A to už bylo i na mě celkem moc a celkem rychle jsem si ujasnil názor na techno i vše kolem něj. Překročil jsem „tu věc“ na podlaze a našel si celkem pohodlné místo k cestování na toaletě.</p>\r\n<p class="MsoNormal">Snad mě technokultura ještě někdy přesvědčí o opaku, ale zatím má u mě technař(ka) nálepku „ožralý, poblitý, pochcaný, posraný, ale free“.</p>', 1210787042, 9),
(58, 'hooverphonic-v-konviktu', 'Hooverphonic v Konviktu', '<p>Všem které by to snad mohlo zajímat: V pondělí 19.5.08 se v Olomouckém konviktu uskuteční koncert výtečné belgické kapelky <a href="http://www.hooverphonic.com/">Hooverphonic</a>. Koncert je součástí festivalu <a href="http://www.divadelniflora.cz/">Divadelní Flora</a> a měl by být zdarma. Hooverphonic na něm představí svou novou desku The President of the LSD Club:</p>\r\n<p> </p>\r\n<p style="text-align: center;"><a href="http://isohunt.com/torrent_details/27915556/hooverphonic?tab=summary"><img src="data/images/1197105631-cover.jpg" alt="" width="299" height="288" /></a></p>\r\n<p style="text-align: center;"> </p>\r\n<p style="text-align: left;">Takže pokud je tahle kapela alespoň trochu váš šálek čaje, nezapomeňte se dostavit. Basák a textař Max Callier přislíbil, že se na nové desce kapela vrátí zpět do vod živých hudebních nástrojů. Svůj slib dodržel a povedlo se mu vytvořit bohatou a barevnou hudbu, okořeněnou mnoha zvláštními instrumenty. Zároveň ale pořád poznáte, že jsou to Hooverphonic. Desku si můžete poslechnout <a href="http://isohunt.com/torrent_details/27915556/hooverphonic?tab=summary">zde</a>.</p>', 1211016508, 4);
INSERT INTO `vypecky_blog` (`id_blog`, `key`, `label`, `text`, `time`, `id_user`) VALUES
(61, 'dalsi-silenec-v-seznamu-uzivatelu', 'Další šílenec v seznamu uživatelů', '<p class="MsoNormal" style="text-indent: 35.4pt;">Tak po včerejším ustavičném urgování jednoho zdejšího webmajstra u sklenky vína (vlastně džbánku a ne jednoho) jsem si tady konečně taky založil profil. Na úvod jsem si dovolil rozšířit fotogalerii o několik dalších fotek vlastní tvorby dokumentujících onu Sodomu a Gomoru, jež se odehrávala nedávno kdesi v Moravském krasu. Původní záměr byl dokázat, že se opravdu jednalo o Výuku v terénu. Bohužel takovýchto fotek se můj fotoaparát letos nedočkal. Nejspíš na to asi nebyl čas...</p>\r\n<p class="MsoNormal" style="text-indent: 35.4pt;"> </p>\r\n<p class="MsoNormal" style="text-indent: 35.4pt;">Jako snad poslední člověk na světě (určitě jako poslední v 3. ročníku geodézie na FASTu) fotím stále na film. Pro mladší vysvětlím, že kinofilm je záznamové médium na uchování obrazu používané ve 20. století a výsledkem byl obraz na speciálním papíře ukládán například do alba (myšleno jako skutečná kniha na uchovávání fotografií, ne to, co vám vygeneruje Picasa). Fotografování na film má dodnes své kouzlo. Uvažujete nad každou fotografií a snažíte se s ní pohrát, aby výsledek stál za to. Neocenitelný je i ten pocit, když si jdete pro vyvolané fotky a těšíte se na to, co na nich bude. Je to příjemné zpestření i pro kolektiv. Po návratu z výukové akce si hned začnou všichni vyměňovat DVDčka narvané fotkami, ale stejně se skupinka lidí těší na to, co přinesu já.</p>\r\n<p class="MsoNormal" style="text-indent: 35.4pt;">A to jsme právě u jádra problému. Všechny dříve zmíněně věci jsou fajn, dokud nejedete na 10 dní kdesi do krasu pod záminkou vzdělávání se a pod slibem vystupování studenta vysoké školy se snažíte co nejrychleji splnit zadaný úkol, aby zbyl čas na ty kulturnější činnosti. Právě na průběh a následky těchto kulturních činností je digitální foťák neocenitelný. Fotografie na filmu je v tuto chvíli přepychem, jelikož kvalita fotografie je přímo úměrná fyzickému a psychickému stavu fotografovaných objektů a většinou také stavu samotného fotografa. A výsledek? Poslední den zjistíte, že máte plné 4 filmy a uvažujete, kde jste to stihli. V tu chvíli se ještě zesiluje ono očekávání, co na těch fotkách po vyvolání najdete.</p>\r\n<p class="MsoNormal" style="text-indent: 35.4pt;">Onen vytoužený den pro mě nastal v pátek 2. května. Po zaplacení neskutečných devíti stovek za vyvolání se mi dostalo do ruky něco neuvěřitelného. Dostal se mi do ruky materiál týkající se událostí oněch dubnových dní. Byl to materiál čítající 131 fotografií diskreditující pravděpodobně všechny členy onoho sacího Komanda smrti a jim přilehlé okolí. Měl jsem tehdy morální dilema z toho, jak s tímto materiálem naložit. Zveřejnit nebo rovnou spálit? Je to samozřejmě bráno s nadsázkou, nikdy jsem nevyhodil jedinou fotku, protože i ta sebevíc nekvalitní fotka má nějakou vypovídací hodnotu a člověk nikdy neví, kdy se bude hodit.</p>\r\n<p class="MsoNormal" style="text-indent: 35.4pt;">Pár těch nejslušnějších a nejzajímavějších fotek vám teď tady nabízím k nahlédnutí. Vybrat je byl problém, jelikož zdejší webmajstři stále ještě nezavedli sekci, kde by byl přístup jen pro starší 18-ti let. Jen podotýkám, že z celkového počtu 131 bylo za světla vyfoceno celých 25 fotografií…</p>\r\n<p class="MsoNormal" style="text-indent: 35.4pt;">Nemusím ani připomínat, že tyto řádky jsou napsány v době, kdy jsem měl ideální možnost se věnovat přípravě na zkoušku, která mě v brzké době čeká. Ale přesně v duchu příspěvku, který tady nedávno přidal kolega Delamancha, se opět našel důvod, proč se neučit.</p>\r\n<p class="MsoNormal" style="text-indent: 35.4pt;"> </p>\r\n<p class="MsoNormal" style="text-indent: 35.4pt;">No, dost bylo keců, stejně to nikoho nezajímá, těch pár fotek najdete <a href="http://vypecky.info/fotogalerka/jedovnice-08-vol-2/">tady</a> .</p>\r\n<p class="MsoNormal" style="text-indent: 35.4pt;"> </p>\r\n<p> </p>', 1211383390, 15),
(62, 'patecni-kejkle', 'Páteční kejkle', '<p> </p>\r\n<p class="MsoNormal"><span>Tento víkend v Brně byl, jak mnozí jistě víte, nabitý kulturou až k prasknutí. V pátek noc kejklířů, v sobotu ta muzejní. Čekalo na nás nespočet divadel, výstav, přehlídek, pouličních výstupů, ohňostrojů, velbloudů, artistů, žonglérů, klaunů a opilců. V pátek jsme si všichni plivli do dlaní, vyhrnuli rukávy a v dychtivém očekávání kulturní žranice rázným krokem vyrazili na Zelňák. Ještě doma jsme si vybrali dvě slušně vypadající představení v Huse, doufajíce, že alespoň na jedno zaručeně seženeme lístky. </span></p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal"><span>Když jsme po příchodu na místo určení zjistili, že se z Husy vine fronta jak na melouny, trochu jsme znejistěli. Stoupli jsme si však hrdinně na konec hada a trpělivě čekali, až na nás přijde řada. Je celkem typické, že cedulku vyprodáno, vyvěsila paní pokladní, až když jsme byli půl metru před kasou. Ondra ještě narychlo vybíral náhradní variantu a podařilo se mu najít jedno slušně vypadající představení. Když však po slečně poptával lístky, uvědomila ho s milým úsměvem, že vybraná hra sice vyprodána není, ale že začala už před půl hodinou. Se staženými ocasy jsme odkráčeli zpět do basecampu u kašny abychom ostatním sdělili neslavný výsledek naší expedice. Náš smutek však netrval dlouho. Jednak nás rozebrala povedená rakvičkárna pod muzeem, jednak se k nám zanedlouho přitočil kolega Havlík z Polárky a sdělil nám, že ve 23:30 budou mít na venkovním pódiu přehlídku toho nejlepšího, doplněnou hudbou, zpěvem a samozřejmě beatboxem. Jelikož všichni víme, jaký je Havlík kaBrňák a zač je toho v Polárce loket, svatosvatě jsme mu slíbili, že přijdeme. </span></p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal"><span>Bylo nebylo, žila byla jedna irská princezna, jmenovala se Niki a bydlela v malém království nad prodejnou párků na Kobližné. Jelikož to byla šlechtična osvícená a obětavá, učila ve svém volném čase chudé poddané hovořit jazykem královských ostrovů. Náhodou se mi dostalo cti, působit ve stejné vzdělávací instituci, a tak se stalo, že jsem se s tou princeznou seznámil. Mnoho příjemných večerů jsme spolu strávili, mnohá rána v nonstopu společně uvítali. Stalo se však, že přijel princ na bílém koni a milou Niki požádal o ruku. Ona jeho nabídku přijala a společně spolu odjeli do dalekého království kdesi u Readingu. Bohužel se před svým odjezdem princezna rozhodla se všemi patřičně rozloučit a vrhla párty* A tak byl nakonec mým jediným kulturním zážitkem ohňostroj a pár cvoků s hořícíma tenisákama. Asi v 10 mě totiž odchytila Niki a její pohůnkové, aby mě surově odvlekli do nenáviděného podniku Livingstone.</span></p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal"><span>Z Polárky nebylo nic. Místo kultury jsem si opět zacestoval časem a ochutnal něco osmdesátkového diska (kuwa). Alespoň, že mně princezna pozvala na toho Jamesona.</span></p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal">PS: ještě k té kašně na Zelňáku, kde jsme si zřídili shromaždiště. Volal nám Zkrat, kde že jsme a že by za námi došel. Já na to že u kašny, ať dojde.  Za hodinu další telefonát - Zkrat sedí U Kašny a čertí se, kde že jako jsme a že tam čeká už 15 minut. Chvilku trvalo, než jsem mu vysvětlil, že nesedíme U Kašny ale u kašny na Zelňáku, ale nakonec jsme se přece jen šťastně našli.</p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal">*anglicky "throw a party"</p>', 1211975520, 4),
(63, 'muzejni-sabes', 'Muzejní Šábes', '<p><span style="font-size: small;"><br /></span></p>\r\n<p class="MsoNormal"><span style="font-size: small;"><span>V Sobotu jsme vyrazili do města s nadějí, že tentokrát ta kultura určitě vyjde. Začali jsme v Moravské galerii, kde probíhaly ozvěny Anifestu. Nadšeně jsme se zabořili do křesílek a vychutnávali si ty nejlepší animáky. Naše spokojenost však netrvala dlouho. Asi po čtvrtém snímku se z hlavního sálu ozval příšerný rachot. Mistra zvuku zřejmě naprosto nezajímalo, že se v budově muzea někdo dívá na filmy, a že by rád slyšel i něco jiného než pravidelné tucání rádobytaneční hudby. Po pěti minutách marné snahy odezírat animovaným postavičkám ze rtů, jsme nakvašeně opustili křesílka a vydali se hledat příčinu toho pekelného kraválu. </span></span></p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal"><span style="font-size: small;"><span>Ve vstupní hale probíhala jakási módní přehlídka a nahluchlý kripl za mixpultem obstarával hudební doprovod. Což o to, přehlídka to byla zajímavá, modýlky průsvitné a dráždivé. Estetický prožitek byl však značně umenšen skutečností, že mi po shlédnutí asi 6 obnažených a ne zcela špatně tvarovaných ňader, začala téct krev z uší. </span></span></p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal"><span style="font-size: small;"><span>Rychle jsme prchli ze spárů neúprosných beatů a vydali se na bašty, odkud jsme se chystali pozorovat zahajovací soutěžní ohňostroj. Podobný nápad však mělo asi 50 milionů dalších lidí, takže jediná volná místa byla za stromy (které poněkud clonily výhled). Díky své, více než dvoumetrové, postavičce jsem ale nezoufal a odvážně vplul do davu. V prostoru za mnou se okamžitě začalo ozývat rozmrzelé mručení a sarkasmy typu „No tys nám tu chyběl“, </span></span><span style="font-size: small;"><span>„Nezacláněj Eifele</span></span><span style="font-size: small;"><span>“ a</span></span><span style="font-size: small;"><span> „Těba mamička dobre krmili“. Na takové jedovaté poznámky odpovídám zásadně větou „Vlezte mi na záda“, kteréžto výzvě jsem schopen i dostát (tedy v případě, že daný jedinec neváží více než 87 kilo, což je váha opilého Peťase). No, z ohňostroje jsem měl celkem kulové. Navíc se těsně za námi usídlila skupinka hospodských vtipálků, kteří celý průběh neustále vtipně glosovali. Věty jako „Kuwa to jiskří jak šalina v zimě“ a „Jo kdyby blafla Spolana, to by ste viděli ohňostroj“ dojem z efemérní krásy jiskrami posetého nebe příliš neumocní. </span></span></p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal"><span style="font-size: small;"><span>Hořké zklamání muzejní noci jsme zapili dvoudecí trpkého vína u Filipa na Petrově a vydali se na cestu domů. </span></span></p>\r\n<p class="MsoNormal"><span style="font-size: small;"><span>Takže jen tak pro pořádek, má kulturní bilance z tohoto víkendu činí jednu rakvičkárnu, 2 ohňostroje, 5 filmů z anifestu a 6 odhalených ňader.</span></span></p>\r\n<p class="MsoNormal"><span style="font-size: small;"><span>Long Live Culture!!!</span></span></p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal"> </p>\r\n<p class="MsoNormal"> </p>', 1211977691, 4),
(65, 'recky-zpravodaj-pepa-srsan', 'Řecký zpravodaj Pepa Sršáň', '<p style="text-align: left;">Několik zpravodajských vstupů z Atén v podání Jeníka Kratochvíla, toho času vystupujícího pod uměleckým pseudonymem Pepa Sršáň. Práce ópéráka očividně skýtá více zábavy, než by se na první pohled mohlo zdát. ENJOY!!!</p>\r\n<p style="text-align: center;">\r\n<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="450" height="354" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n<param name="allowfullscreen" value="true" />\r\n<param name="allowscriptaccess" value="always" />\r\n<param name="wmode" value="transparent" />\r\n<param name="src" value="http://www.stream.cz/object/87458-recti-holubi" /><embed type="application/x-shockwave-flash" width="450" height="354" src="http://www.stream.cz/object/87458-recti-holubi" wmode="transparent" allowscriptaccess="always" allowfullscreen="true"></embed>\r\n</object>\r\n</p>\r\n<p> </p>\r\n<p style="text-align: center;">\r\n<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="450" height="354" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n<param name="allowfullscreen" value="true" />\r\n<param name="allowscriptaccess" value="always" />\r\n<param name="wmode" value="transparent" />\r\n<param name="src" value="http://www.stream.cz/object/76252-atheny" /><embed type="application/x-shockwave-flash" width="450" height="354" src="http://www.stream.cz/object/76252-atheny" wmode="transparent" allowscriptaccess="always" allowfullscreen="true"></embed>\r\n</object>\r\n</p>\r\n<p> </p>\r\n<p style="text-align: center;">\r\n<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="450" height="354" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n<param name="allowfullscreen" value="true" />\r\n<param name="allowscriptaccess" value="always" />\r\n<param name="wmode" value="transparent" />\r\n<param name="src" value="http://www.stream.cz/object/87611-kalimaki-beach" /><embed type="application/x-shockwave-flash" width="450" height="354" src="http://www.stream.cz/object/87611-kalimaki-beach" wmode="transparent" allowscriptaccess="always" allowfullscreen="true"></embed>\r\n</object>\r\n</p>', 1212664302, 4),
(66, 'spiderman-forever', 'Spiderman Forever', '<p>Další z krátkých pořadů kolegy Kratochvíla. Tentokrát se jedná o sociologický rozbor patologických zálib a fixací ve vztahu ke konzumní ikonografii a symbolice postmoderní společnosti. ENJOY!!!</p>\r\n<p> </p>\r\n<p style="text-align: center;">\r\n<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="450" height="354" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n<param name="allowfullscreen" value="true" />\r\n<param name="allowscriptaccess" value="always" />\r\n<param name="wmode" value="transparent" />\r\n<param name="src" value="http://www.stream.cz/object/91095-spiderman-for-ever" /><embed type="application/x-shockwave-flash" width="450" height="354" src="http://www.stream.cz/object/91095-spiderman-for-ever" wmode="transparent" allowscriptaccess="always" allowfullscreen="true"></embed>\r\n</object>\r\n</p>', 1212963308, 4);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_categories`
--

CREATE TABLE IF NOT EXISTS `vypecky_categories` (
  `id_category` smallint(3) NOT NULL auto_increment,
  `id_section` smallint(3) NOT NULL,
  `urlkey` varchar(50) NOT NULL,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `params` varchar(200) default NULL,
  `protected` tinyint(1) NOT NULL default '0',
  `priority` smallint(2) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1' COMMENT 'je-li kategorie aktivní',
  `left_panel` tinyint(1) NOT NULL default '1' COMMENT 'Je li zobrazen levý panel',
  `right_panel` tinyint(1) NOT NULL default '1' COMMENT 'Ja li zobrazen pravý panel',
  PRIMARY KEY  (`id_category`),
  KEY `key` (`urlkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Vypisuji data pro tabulku `vypecky_categories`
--

INSERT INTO `vypecky_categories` (`id_category`, `id_section`, `urlkey`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `params`, `protected`, `priority`, `active`, `left_panel`, `right_panel`) VALUES
(1, 2, 'vypecky', 'Výpečky', 'Vepřové výpečky', NULL, NULL, NULL, NULL, '', 0, 10, 1, 1, 1),
(2, 2, 'portfolio', 'Portfolio', 'Naše portfolio', NULL, NULL, NULL, NULL, '', 0, 9, 1, 0, 1),
(3, 12, 'komiks', 'Komiks', 'Komiks Kuba a Kuba', 'Comics', 'Comics Kuba and Kuba', NULL, NULL, NULL, 0, 5, 1, 0, 0),
(4, 11, 'ucet', 'Účet', 'Účet na výpečkách', 'Account', 'Account on vypecky', NULL, NULL, NULL, 0, 5, 1, 1, 1),
(5, 1, 'forum', 'Fórum Výpeček', 'iframe', NULL, NULL, NULL, NULL, 'id=1', 0, 4, 0, 0, 0),
(6, 7, 'sdilena-data', 'Sdílená data', 'Sdílená data pro potřeby obyvatelů výpeček', NULL, NULL, NULL, NULL, '', 0, 1, 1, 1, 1),
(7, 6, 'email', 'Email', 'Přístup k výpečkovskému Mailu', NULL, NULL, NULL, NULL, '', 0, 1, 1, 0, 0),
(8, 6, 'jak-zije-blahos', 'Jak žije Blahoš', 'Statistiky o Blahošovi', NULL, NULL, NULL, NULL, '', 0, 1, 1, 0, 0),
(9, 8, 'kniha-navstev', 'Kniha návštěv', 'Napište nám', 'Guestbook', 'Our guestbook', NULL, NULL, NULL, 0, 1, 1, 1, 1),
(10, 1, 'tlacenka', 'Tlačenka', 'Tlačenka aneb Blog na výpečkách', NULL, NULL, NULL, NULL, '', 0, 11, 1, 1, 1),
(11, 3, 'fotogalerka', 'Fotogalerka', 'Fotogalerie z akcí a tak', NULL, NULL, NULL, NULL, '', 0, 5, 1, 0, 0),
(12, 5, 'odkazy', 'Odkazy', 'Odkazy na zajímavé stránky', 'Links', 'Links to interesting web pages', NULL, NULL, 'sectiontable=links_section', 0, 5, 1, 1, 1),
(13, 4, 'jitrnicky', 'Jitrničky', 'Jitrničky aneb novinky', NULL, NULL, NULL, NULL, NULL, 0, 5, 1, 1, 1),
(14, 9, 'chyby', 'Chyby', 'Hlášení chyb výpečkovského enginu', 'Errors', 'Reporting errors', NULL, NULL, NULL, 0, 0, 1, 1, 1),
(15, 2, 'sponsors', 'Sponzoři', 'Naši sponzoři', 'Sponsors', 'Our sponsors', NULL, NULL, NULL, 0, 0, 1, 1, 1),
(16, 13, 'portalusers', 'Uživatelé', 'Uživatelé portálu', 'Users', 'Portal Users', NULL, NULL, NULL, 0, 0, 1, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_changelog`
--

CREATE TABLE IF NOT EXISTS `vypecky_changelog` (
  `id_change` smallint(6) NOT NULL auto_increment,
  `id_user` smallint(6) NOT NULL,
  `time` int(11) NOT NULL,
  `file` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `label` varchar(500) NOT NULL,
  PRIMARY KEY  (`id_change`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Vypisuji data pro tabulku `vypecky_changelog`
--

INSERT INTO `vypecky_changelog` (`id_change`, `id_user`, `time`, `file`, `module`, `label`) VALUES
(1, 1, 1211127834, 'Links.class.php', 'links', 'Úprava funkce getLinkChangeParams - přidání zakódování url adresy'),
(2, 3, 1211131070, 'module.php', 'news', '\n<p>Upraven procesing texy při ukládání novinky</p>\n\n<!-- by Texy2! -->'),
(3, 3, 1211131128, 'show_photo.htpl', 'fotogalery', '\n<p>Opravení mazání fotografie</p>\n\n<!-- by Texy2! -->'),
(4, 3, 1211652865, 'links.class.php', 'třída Links', '\n<p>Kompletní přepracování třídy pro použití s CoolURL.</p>\n\n<!-- by Texy2! -->'),
(5, 3, 1211653020, 'všechny', 'všechny', '\n<p>Optimalizace pro SEO, zavedení CoolURL, integrace přepracované knihovny\nLinks.class.php</p>\n\n<!-- by Texy2! -->'),
(6, 3, 1211713180, 'module.php', 'Login', '\n<p>Úprava zobrazení registrace</p>\n\n<!-- by Texy2! -->'),
(7, 3, 1211715957, 'list.tpl', 'blog', '\n<p>Oprava šablony, v listu jsou zakončeny zkrácené texty celými slovy</p>\n\n<!-- by Texy2! -->');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_changes`
--

CREATE TABLE IF NOT EXISTS `vypecky_changes` (
  `id_change` smallint(5) unsigned NOT NULL auto_increment,
  `id_user` smallint(5) unsigned NOT NULL,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(5) unsigned NOT NULL,
  `label` varchar(500) default NULL,
  `time` int(11) default NULL,
  PRIMARY KEY  (`id_change`),
  KEY `id_user` (`id_user`,`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=305 ;

--
-- Vypisuji data pro tabulku `vypecky_changes`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_comics`
--

CREATE TABLE IF NOT EXISTS `vypecky_comics` (
  `id_comics` smallint(3) NOT NULL auto_increment,
  `label` varchar(200) NOT NULL,
  `file` varchar(200) NOT NULL,
  `number` smallint(3) NOT NULL,
  PRIMARY KEY  (`id_comics`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=82 ;

--
-- Vypisuji data pro tabulku `vypecky_comics`
--

INSERT INTO `vypecky_comics` (`id_comics`, `label`, `file`, `number`) VALUES
(44, 'Pornohvězda', '02-kubaakuba-pornohvezda.png', 2),
(64, 'Autoerotika', '22-kubaakuba-autoerotika.png', 22),
(45, 'Zpověď Harmonikáře', '03-kubaakuba-zpoved-harmoni.png', 3),
(63, 'Nevolnost', '21-kubaakuba-nevolnost.png', 21),
(62, 'Altruista', '20-kubaakuba-altruista.png', 20),
(61, 'Dítě', '19-kubaakuba-dite.png', 19),
(60, 'Chemik', '18-kubaakuba-chemik.png', 18),
(59, 'Cimbálovka', '17-kubaakuba-cimbalovka.png', 17),
(58, 'Předsevzetí', '16-kubaakuba-predsevzeti.png', 16),
(57, 'No comment', '15-kubaakuba-no-comment.png', 15),
(56, 'Staré Časy', '13-kubaakuba-stare-casy.png', 13),
(55, 'Profesionální deformace', '14-kubaakuba-deformace.png', 14),
(54, 'Guru', '12-kubaakuba-guru.png', 12),
(53, 'Co s načatým večerem', '11-kubaakuba-co-s-vecerem.png', 11),
(52, 'Krize identity', '10-kubaakuba-krize-identity.png', 10),
(51, 'Globální problém', '09-kubaakuba-globalni-probl.png', 9),
(50, 'Don Juan', '08-kubaakuba-donjuan.png', 8),
(49, 'Úleva', '07-kubaakuba-uleva.png', 7),
(48, 'Alkoholik', '06-kubaakuba-alkoholik.png', 6),
(47, 'Něco', '05-kubaakuba-neco.png', 5),
(46, 'Porucha', '04-kubaakuba-porucha.png', 4),
(43, 'Pavel', '01-kubaakuba-pavel.png', 1),
(65, 'Zpověď grafika', '23-kubaakuba-zpoved-grafika.png', 23),
(66, 'Zelenina', '24-kubaakuba-zelenina.png', 24),
(67, 'Zelenina II', '25-kubaakuba-zelenina2.png', 25),
(68, 'Vesmír', '26-kubaakuba-vesmir.png', 26),
(69, 'Opium', '27-kubaakuba-opium.png', 27),
(70, 'Stěhování', '28-kubaakuba-stehovani.png', 28),
(71, 'Lovkyně perel', '29-kubaakuba-lovkyne-perel.png', 29),
(72, 'Specialista', '30-kubaakuba-specialista.png', 30),
(73, 'Sportovci', '31-kubaakuba-sportovci.png', 31),
(74, 'Zloděj', '32-kubaakuba-zlodej.png', 32),
(75, 'Dvojník', '33-kubaakuba-dvojnik.png', 33),
(76, 'Mumie', '34-kubaakuba-mumie.png', 34),
(77, 'Bukvice', '35-kubaakuba-bukvice.png', 35),
(78, 'Sexuolog', '36-kubaakuba-sexuolog.png', 36),
(79, 'Evoluce', '37-kubaakuba-evoluce.png', 37),
(80, 'Co Čech to muzikant', '38-kubaakuba-cocechtomuzika.png', 38),
(81, 'Linka Důvěry', '39-kubaakuba-linkaduvery.png', 39);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_comments`
--

CREATE TABLE IF NOT EXISTS `vypecky_comments` (
  `id_comment` smallint(4) NOT NULL auto_increment,
  `id_category` smallint(3) NOT NULL,
  `id_article` smallint(3) NOT NULL,
  `id_user` smallint(4) default NULL,
  `nick` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `text` text NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY  (`id_comment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=137 ;

--
-- Vypisuji data pro tabulku `vypecky_comments`
--

INSERT INTO `vypecky_comments` (`id_comment`, `id_category`, `id_article`, `id_user`, `nick`, `mail`, `label`, `text`, `time`) VALUES
(17, 10, 1, 3, 'cuba', 'jakubmatas@gmail.com', 'Nový název', 'No abych řekl pravdu je to dosti vypečený nápad a při nejbližším hodováním nad upečeným bůčkem a orosené lahvinky slivovice to s osazenstvem proberu a popřípadě upravím!', 1206721854),
(13, 10, 1, NULL, 'BSBVB', '', 'Cuba a klávesnice', 'Cubo, cubo,\r\nnějak ti tam hapruje levý malíček na klávesnici..', 1206640420),
(14, 10, 1, NULL, 'BSBVB', '', 'Přejmenujte to', 'Kluci, je to od vás moc milé, že se snažíte udržovat živé zpravodajství se světa Balbínovy 20, ale není už jenom ten název BLOG trochu divný? Nevypečený? Co tak to přejmenovat na deníčky? Zkuste nad tím popřemýšlet..', 1206640734),
(15, 10, 16, NULL, 'Annre', 'matasek.o@atlas.cz', 'Blahos', 'No klucíí je to úžasné..Nemůžu se na tu krásu vynadívat. A nejvíce mě potěšila položka: jak žije Blahoš. No já si pořád myslel jak mu tam není smutno, tak sám, a ono nee..Když se podívám co ten lump tam všecičko tropí, tak z toho aj srdéčko zaplesá. No není to krása: Round Trip Time, Swap Memory, atd...Musim zas někdy zaskočit na pokec. \r\nHatatitla', 1206653528),
(16, 10, 16, 3, 'cuba', 'jakubmatas@gmail.com', 'Bezesné noci', 'No abych řekl pravdu, tak to nebyly bezesné noci strávené na vývoji nového webu, neboť většinu času jsem byl v mírném až velkém opojení alkoholem a práce tak z vesela ubíhala :-). A to jak žije blahoš jsem chtěl v podstatě ukázat jak to nemá s námi Vypečenými obyvateli Vepřových výpeček lehké!', 1206721710),
(22, 10, 27, 3, 'cuba', 'jakubmatas@gmail.com', '', 'Tak tohle bych chtěl vidět, na vlastní oči. Mít kameru tak výborný nápad na teenagerskou komedii. :-D ', 1207661042),
(23, 10, 28, NULL, 'delamancha', 'j.vemola@gmail.com', 'neva', 'Nevadí, ale spletl sis blog s guestbookem :-)', 1208033411),
(24, 10, 28, 3, 'cuba', 'jakubmatas@gmail.com', 'v poklidu', 'V poklidu, jestli chceš, upravím ti to, ale v podstatě to ničemu nevadí. :-D ', 1208084702),
(25, 10, 29, 3, 'cuba', 'jakubmatas@gmail.com', 'No jo Valmez', 'Jojo tak tohle moc dobře známe. Ještě snad nikdy se mi také nepodařilo odjet z Valmezu, tak jek jsem chtěl.', 1208084998),
(26, 10, 30, 3, 'cuba', 'jakubmatas@gmail.com', 'Odkaz', 'No sám jsem si chtěl test udělat, abych se konečně dozvěděl pravdu, ale bohužel zmíněný odkaz není funkční. Prosím o opravu! Již se nemůžu dočkat až z výsledku vypadne "Jste abstinent" :-D ', 1208085302),
(27, 10, 30, 3, 'cuba', 'jakubmatas@gmail.com', 'Test', 'Zkoušel jsem test najít, ale nepovedlo se. Každopádně jsem na jeden zajímacý narail a tady je odkaz: <a href="http://www.drogy-info.cz/index.php/dotaznik/view?did=2" target="_blank">http://www.drogy-info.cz/index.php/dotaznik/view?did=2</a>', 1208085673),
(29, 10, 36, 11, 'BSBVB', 'drimalt@seznam.cz', '', 'Pokud se nepletu, tak by to mělo být v pátek večír na Jasenici. (Ne porod, ale taškařice)', 1208445356),
(30, 10, 28, 11, 'BSBVB', 'drimalt@seznam.cz', '', 'Bando nezodpovědná, vy mě budete učit co kam mám dávat.. Guestbook je přeci pro hosty, ne? Já sem chtěl jenom naznačit, že jsem si právě založil tlačenku a ať se mnou teda počítáte!', 1208446294),
(31, 10, 28, 3, 'cuba', 'jakubmatas@gmail.com', '', 'Né mě šlo o to abys to napsal pod článek o uvedení webu, tak abych si to mohl lehce přečíst a podívat se na to. Nikdo po tobě nechce, abys to psal do guestbooku ale do komentářů! šuci písek', 1208447013),
(32, 10, 36, 4, 'delamancha', 'j.vemola@gmail.com', '', 'Tak tam ode mně všechny pozdravuj, já bohužel budu muset z pracovních důvodů zůstat v Brně.\r\nZa Výpečky ale dorazí Kubin.', 1208453073),
(33, 11, 377, 3, 'cuba', 'jakubmatas@gmail.com', '', 'Hoodně hustýýýý :-D ', 1208480848),
(34, 10, 36, 11, 'BSBVB', 'drimalt@seznam.cz', '', 'Bohužel to musím vzít zpět. Jana se dneska vrací z porocnice, takže by to bylo takové nevhod.. Ale Pavel má stále vůli něco v tom smyslu uspořádat, akorát není jisté kdy.', 1208500789),
(35, 10, 36, 3, 'cuba', 'jakubmatas@gmail.com', '', 'Tak to je "škoda", ale já myslím že pavlík něco vymyslí bude to dobré :-D ', 1208515025),
(36, 10, 36, 3, 'cuba', 'jakubmatas@gmail.com', '', 'Ještě pro ty, kteří netuší o kterého Pavla si jde:-)  tak vězte, že se nám zapsal i do komiksu <a href="http://www.vypecky.info/index.php?category=3&page=4" title="Pavel">Kuba a Kuba</a>', 1208515234),
(37, 10, 33, 11, 'BSBVB', 'drimalt@seznam.cz', '', 'Je v TinyMCE načtený css styl? (to jenom, aby diskuze nestála)', 1208528777),
(38, 10, 37, 3, 'cuba', 'jakubmatas@gmail.com', '', 'Tak hustší je ale odkaz v diskuzi pod zmíněným článkem, To nejlepší ze slovenckého <a href="http://www.nej-videa.cz/vyber-ze-slovenske-verze-nikdo-neni-dokonaly/" title="nikdo není dokonalý">nikdo není dokonalý</a> :-) ', 1208533800),
(39, 10, 33, 3, 'cuba', 'jakubmatas@gmail.com', '', 'Jak to myslíš? jako jestli používá TinyMCE nějaké css?', 1208534081),
(67, 11, 547, 3, 'cuba', 'jakubmatas@gmail.com', '', '\n<p>Svět nerálna! Tohle bych chtěl vidět na vlastní oči! :-o</p>\n\n<!-- by Texy2! -->', 1208855869),
(50, 10, 33, 3, 'cuba', 'jakubmatas@gmail.com', '', '<p>Tákže tu máme další vylepšení systému, a to o podporu rozšíření\r\n<a href="http://www.texy.info">TEXY</a> . Tento systém\r\numožňuje jednoduché vkládání různě upravených komentářů, aniž by\r\njste museli používat WYSIWING editor. Doufám že tuto funkci využijete\r\nnapříklad při vkládání odkazů či tučných textů. Syntaxe je celkem\r\njednoduchá a můžete využít i specielní odkazy vedle smajlíků. Je to\r\nprozatím betaverze, takže popřípadě omluvte případné chyby.</p>\r\n\r\n<!-- by Texy2! -->', 1208564346),
(64, 10, 40, 10, 'jeni013', 'honza.liebel@centrum.cz', '', '\n<p>tywoe, já než taky něco dopíšu…:-|</p>\n\n<!-- by Texy2! -->', 1208821992),
(53, 10, 37, 11, 'BSBVB', 'drimalt@seznam.cz', '', '\n<p>Tak když už sme u toho humoru, tak tady ještě jeden malý tip: Ruský\nLinux <a\nhref="http://halbot.haluze.sk/?id=4120">http://halbot.haluze.sk/?…</a></p>\n\n<!-- by Texy2! -->', 1208591266),
(66, 10, 39, 4, 'delamancha', 'j.vemola@gmail.com', '', '\n<p>Hustý týpek :-) Myšlenková inkontinence na kvadrát. A ta\nzásoba slov.</p>\n\n<!-- by Texy2! -->', 1208855842),
(65, 10, 41, 4, 'delamancha', 'j.vemola@gmail.com', '', '\n<p>Povedené :-) A jak vidět ani se příliš nerozcházíme. Je zajímavé,\njak jme oba zvládli popsat víceméně stejné události aniž bychom se\npředem domluvili.</p>\n\n<!-- by Texy2! -->', 1208855069),
(63, 10, 38, 11, 'BSBVB', 'drimalt@seznam.cz', '', '\n<p>Jo, to sedí. Ale to bych si toho musel vytisknout hodně velký arch, aby mi\nto stačilo aspoň na týden..</p>\n\n<!-- by Texy2! -->', 1208638765),
(68, 10, 40, 11, 'BSBVB', 'drimalt@seznam.cz', '', '\n<p>Aha, tak magické oko… Ti geodeti se asi fakt nebojí.</p>\n\n<!-- by Texy2! -->', 1208864983),
(69, 10, 41, 11, 'BSBVB', 'drimalt@seznam.cz', '', '\n<p>Musím vás kluci oba dva pochválit. U Honzy mi akorát trochu chyběla\nzmínka o magickém oku, ale to bude asi tím, žes řídil. Víkend,\ni pojednání o něm, hdnotím na výbornou.</p>\n\n<!-- by Texy2! -->', 1208865421),
(70, 10, 41, 3, 'cuba', 'jakubmatas@gmail.com', '', '\n<p>No tak to byl určitě vypečený výběr a Barišnikova v podání ondřeje\nbych rád vidě! :-D</p>\n\n<!-- by Texy2! -->', 1208972192),
(71, 11, 369, 0, 'veselaPetula', 'veselaPetula', '', '\n<p>To jsme tehdá ještě věděli co je to sníh…</p>\n\n<!-- by Texy2! -->', 1208973080),
(72, 11, 383, 0, 'veselaPetula', 'veselaPetula@seznam.cz', '', '\n<p>Děduška Moróz přijel k nám a stromečky nám zavál…</p>\n\n<!-- by Texy2! -->', 1208973700),
(73, 10, 41, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>no tak panove, vy nedokazete pochopit ze si jako geodeti pouze uzivame\nzivota. Mam takovy dojem ze z vas jen mluvi ticha zavist ........... PS:\nnazvani „geodeticka zver atd. je velmi hanlive“</p>\n\n<!-- by Texy2! -->', 1209116068),
(74, 10, 39, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>vyklad je natolik presny, ze neni treba se na jakoukoliv vystavu vydat</p>\n\n<!-- by Texy2! -->', 1209116341),
(75, 10, 33, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>neostranuje se obrazek v „AVATARu“ diky</p>\n\n<!-- by Texy2! -->', 1209127988),
(76, 10, 40, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>MAGICKE OKO forever</p>\n\n<!-- by Texy2! -->', 1209128202),
(92, 11, 554, 3, 'cuba', 'jakubmatas@gmail.com', '', '\n<p>Stěhování pod sluníčko?</p>\n\n<!-- by Texy2! -->', 1209388790),
(93, 10, 48, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>jen doufam ze fazolky to prezily, jinak muj zivot bez nich nema cenu</p>\n\n<!-- by Texy2! -->', 1209401387),
(94, 10, 48, 4, 'delamancha', 'j.vemola@gmail.com', '', '\n<p>Neboj, ty jsou v pohodě a bezpečí venku na zahradě :-)</p>\n\n<!-- by Texy2! -->', 1209409382),
(78, 10, 41, 10, 'jeni013', 'honza.liebel@centrum.cz', '', '\n<p>Milý drobku, byl jsem v Jedovnici 2 dny po sobě a musím řict, že jste\nvýpečkům dělali po čertech dobrou reklamu – nejen že jste se zlili jak\nprasata, ale v nekterých případech došlo na sviňačinky a při návratu do\nubikace byl za vámi patrný i vepřový odér…:-P</p>\n\n<!-- by Texy2! -->', 1209139891),
(79, 10, 44, 0, 'Zdenda', 'zdenda.kozak@seznam.cz', '', '\n<p>No, ono bude možná lepší některé věci zanechat v prachu\nzapomnění.:-D Ani si radši nechci představit co za invidua by vyrostla\nz našich budoucích dětí, kdyby zjistili co jejich tatínkové a maminky\nv Jedovnici prováděli. Mohu dodat jen dvě věci. 1) Geodeti jsou prostě\nsebranka z velmi kladným (až nezdravím) vztahem k alkoholu a 2) pokud se\njednou mé dítě zeptá: „Tatínku můžu jít studovat geodézii??“\nOdpovím rázně „NE!!!!“. Ps: Zdravím všechny zůčastněné. Stálo to\nza to.:-P</p>\n\n<!-- by Texy2! -->', 1209147815),
(80, 10, 44, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>vse je lez, sprosta pomluva a bulvar</p>\n\n<!-- by Texy2! -->', 1209150872),
(81, 10, 44, 0, 'Zdenda', 'zdenda.kozak@seznam.cz', '', '\n<p>Pavle…no ták!!!Víme svoje…joko člen komanda taky trochu vím…:-P</p>\n\n<!-- by Texy2! -->', 1209165554),
(82, 10, 46, 0, 'Zdenda', 'zdenda.kozak@seznam.cz', '', '\n<p>:-D No comment…</p>\n\n<!-- by Texy2! -->', 1209165591),
(83, 10, 44, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>ne, me svedomi je ciste jak lilie, kdyz se me nekdy nekdo zepta, nezavdam mu\npriciny aby o mojem slusnem chovani pochyboval</p>\n\n<!-- by Texy2! -->', 1209195652),
(84, 10, 46, 4, 'delamancha', 'j.vemola@gmail.com', '', '\n<p>Rybeczky, ty demagogu, překrucovači pravd, ničiteli národního odkazu a\ntak podobně. Toho erotomana-alkoholika si ještě vypiješ !!!</p>\n\n<!-- by Texy2! -->', 1209205878),
(85, 10, 46, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>jen jsem považoval za nutné říct světu pravdu. a co se tyce\n„erotomana-alkoholika“..­........potre­fena husa se vzdycky ozve</p>\n\n<!-- by Texy2! -->', 1209206712),
(86, 10, 44, 10, 'jeni013', 'honza.liebel@centrum.cz', '', '\n<p>Až se vás Vaše děti zeptají, co že jste to v těch Jedovnicích\nprováděli, tak jim nejspíš odpovíte: Di si hrát a buď rád, že nejsi\nponík:-D</p>\n\n<!-- by Texy2! -->', 1209251226),
(87, 10, 46, 10, 'jeni013', 'honza.liebel@centrum.cz', '', '\n<p>Pravdu znáš jen díky našim příspěvkům a neomylné a naprosto\nnestranné audiovizuální technice (viz předchozí blog) tam je jasné, nejen\nže Ondra nemohl ani chodit (1. video), ale že komanda smrti se nesoustředila\nna vypití restaurace, ale především na její úplné znehodnocení\nupatláním griotkou a pepermintkou čímž bezpečně odradila zákazníky\nlidského rodu…</p>\n\n<!-- by Texy2! -->', 1209251946),
(88, 10, 45, 10, 'jeni013', 'honza.liebel@centrum.cz', '', '\n<p>úžasné, jen mi tady chybí akce fullscreen a download…</p>\n\n<!-- by Texy2! -->', 1209252122),
(90, 10, 45, 4, 'delamancha', 'j.vemola@gmail.com', '', '\n<p>Je to flash vlastní výroby, takže fullscreen nehrozí, download\npřidám.</p>\n\n<!-- by Texy2! -->', 1209285073),
(91, 10, 44, 3, 'cuba', 'jakubmatas@gmail.com', '', '\n<p>Ona to musela být celkem smažba v Jedovnici. Podle výše zmíněných\nzáběrů si dokážu představit že o zábavu nebyla nouze! A tech rán, kdy\nčlověka bolí hlava jako střep muselo být požehnaně!! :-D</p>\n\n<!-- by Texy2! -->', 1209388680),
(95, 10, 48, 11, 'BSBVB', 'drimalt@seznam.cz', '', '\n<p>No, tak to trochu cítím, že na tom mám taky trochu viny. Kdybych tady <a\nhref="http://halbot.haluze.sk/">tenhle</a> odkaz dal k dispozici dřív, tak\nbyste se na úklid asi nedostali..</p>\n\n<!-- by Texy2! -->', 1209634909),
(96, 10, 49, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>¨tos kuwa, jsi borec, musim k tobe na radu, jak se delaji holky. :-) Kdyz\nsi predstavim tebe jako tatu, tak vidim pruser :-) mej se fajn a preju vse nej a\npevne nervy :-)</p>\n\n<!-- by Texy2! -->', 1209688751),
(97, 10, 47, 0, 'drábin', 'pavel.drabek@gmail.com', '', '\n<p>Díky za podporu. Máme za sebou včerejší zahřívací kolo v Ústí a\nvyrážíme na Brno. Můžu slíbit, že všechny tři kapely jsou připraveny\nnaklepat posluchače na skleněnce jako řízek. Hudbě zdar! Pavel</p>\n\n<!-- by Texy2! -->', 1209732274),
(122, 11, 772, 3, 'cuba', 'jakubmatas@gmail.com', '', '\n<p>Copak se mu asi zdá?:-D</p>\n\n<!-- by Texy2! -->', 1211384074),
(123, 10, 61, 9, 'drobek', 'ppavelrybecky@tiscali.cz', '', '\n<p>kdybys se naučil hodně rychle kreslit, nemusel bys fotit\nvůbec........­.............­.....natož si tak ještě stěžovat</p>\n\n<!-- by Texy2! -->', 1211442512),
(120, 10, 54, 10, 'jeni013', 'honza.liebel@centrum.cz', '', '\n<p>Zážitek sice ostrý, ale buď rád, že jsi nejel s těma Banikpičovcema,\nbo … <a\nhref="http://katastrofy.com/scripts/index.php?id_nad=11443:-P">http://katastrofy.com/…ts/index.php?…</a></p>\n\n<!-- by Texy2! -->', 1210886087),
(121, 10, 57, 15, 'Šajtr', 'PavSch@seznam.cz', '', '\n<p>Není se za co stydět. Já už třetím rokem bydlím opravdu kousek od toho\nmuzea a taky jsem v něm ještě nebyl :-D</p>\n\n<!-- by Texy2! -->', 1211383749),
(117, 10, 52, 0, 'veselaPetula', 'veselaPetula@seznam.cz', '', '\n<p>Napsáno opravdu jako rozloučení, krása. Sice neznám tu hospůdku,ale\npodle popisu se mi málem taky zaleskla slza v oku, takové dojetí mě\nopojilo, smutné dojetí nad ztrátou milé hospůdky, která již nebude…\nNebude stát pro vás ani sama pro sebe, ani ji nikdy neuvidim. Ale aspon bude\nráda a v hospodském nebíčku bude vzpominat jak krásné rozloučení jste\njí připravili a všechny hospůdky, hospody, krčmy a další podniky jí\nbudou jen závidět… Tak at se Ambro máš v tom Hospodském nebi krásně,\npřeji hodně hostů a kamarádů v nebi. Nebo se narodíš jako jiná\nhospůdka a budeš žít další svůj život…:-)</p>\n\n<!-- by Texy2! -->', 1210462010),
(116, 10, 51, 11, 'BSBVB', 'drimalt@seznam.cz', '', '\n<p>Rudolfa, kterého sice neznám, bych navrhl na cenu Sebevražedný počin\nroku 2008. Myslím, že jej jenom tak někdo nepřekoná… :-D</p>\n\n<!-- by Texy2! -->', 1210165463),
(114, 10, 50, 11, 'BSBVB', 'drimalt@seznam.cz', '', '\n<p>Hola, zapíjel, zapíjel. Nezapoměli jsme se stavit. A dokonce se nám\ni podařilo některým se z tama dostat tak, že se už teďka trefuju do\ndútíbmývj lůsúrl ms lůábrdnivi (trefuju do správných klapek na\nklávesnici)…</p>\n\n<!-- by Texy2! -->', 1209804706),
(115, 10, 50, 0, '', '', '', '\n<p>Jakožto znalec židenických reálií a přímý účastník zmiňovaného\nposezení v Pod Kaštanech si dovolím jen malé upřesnění ohledně té\nprocházky zakončené jakoby náhodně v řečené zahradní restauraci. Ona\n„procházka“ byla dlouhá přesně 1.1 km, což je nejkratší možná\nvzdálenost mezi bodem A (tj. Balbínova 20) a bodem B (tj. Pod Kaštany).</p>\n\n<!-- by Texy2! -->', 1209830441),
(125, 13, 13, 3, 'cuba', 'jakubmatas@gmail.com', '', '\n<p>Sám jsem byl s nimi na Jonesovi a mohu vřele doporučit, u filmu se velmi\npobavíte! :-)</p>\n\n<!-- by Texy2! -->', 1212056521),
(135, 10, 61, 3, 'cuba', 'jakubmatas@gmail.com', '', '\n<p>Re: paľo Už sem to opravil šlo o špatný odkaz v článku</p>\n\n<!-- by Texy2! -->', 1213097726),
(136, 13, 16, 3, 'cuba', 'jakubmatas@gmail.com', '', '\n<p>Ještě bych poznamenal že ten rekord se ukládá pouze na počítač, kde\nse hraje, takže ve výsledcích nejsou žádné rekordy. To je ale blbost co\nsem napsal, hmm co už :-D</p>\n\n<!-- by Texy2! -->', 1213098333),
(133, 13, 16, 11, 'BSBVB', 'drimalt@seznam.cz', '', '\n<p>Cha chá, už tam mám rekorďáka :-D</p>\n\n<!-- by Texy2! -->', 1212674000),
(134, 10, 61, 0, 'paľo', '', '', '\n<p>Erory od 107 do 109 tu enem naskakujů, když chcu zmrknout ten váš\nalkoholickoge­odetický počin</p>\n\n<!-- by Texy2! -->', 1212870473),
(132, 13, 16, 3, 'cuba', 'jakubmatas@gmail.com', '', '\n<p>Jj je to super! :-D tohle znám.</p>\n\n<!-- by Texy2! -->', 1212578558),
(131, 13, 13, 17, 'usual_moron', '', '', '\n<p>Já jsem tam byl s nimi taky a pro velice maximální pobavení vřele\ndoporučuji před návštěvou kina s novým starým Jonesem přelouskat tři\nstarší filmy s mladším Jonesem.</p>\n\n<!-- by Texy2! -->', 1212178122);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_errors`
--

CREATE TABLE IF NOT EXISTS `vypecky_errors` (
  `id_error` smallint(6) NOT NULL auto_increment,
  `key` varchar(70) NOT NULL,
  `id_user_error` smallint(6) NOT NULL,
  `time_error` int(11) NOT NULL,
  `label_error` varchar(50) NOT NULL,
  `text_error` varchar(500) NOT NULL,
  `fixed` enum('true','false','inprogress') NOT NULL default 'false',
  `time_fixed` int(11) default NULL,
  `text_fixed` varchar(500) default NULL,
  `id_user_fixed` smallint(6) default NULL,
  `file_fixed` varchar(50) default NULL,
  `module_fixed` varchar(50) default NULL,
  PRIMARY KEY  (`id_error`),
  UNIQUE KEY `key` (`key`),
  KEY `id_user` (`id_user_error`),
  KEY `id_user_fixed` (`id_user_fixed`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Vypisuji data pro tabulku `vypecky_errors`
--

INSERT INTO `vypecky_errors` (`id_error`, `key`, `id_user_error`, `time_error`, `label_error`, `text_error`, `fixed`, `time_fixed`, `text_fixed`, `id_user_fixed`, `file_fixed`, `module_fixed`) VALUES
(1, 'editace-komiksu-1211734010', 3, 1211734011, 'Editace textu ke komiksům', '<p>Zobrazení editačního tlačítka u textu ke komiksům</p>\r\n\r\n<!-- by Texy2! -->', 'true', 1211734011, 'Upravení šablony se zobrazením komiksu', 3, 'show.htpl', 'comics'),
(2, 'odsazeni-v-panelu-ucet-1211794138', 4, 1211794138, 'odsazení v panelu účet', '\n<p>padding tlačítka „přihlásit“</p>\n\n<!-- by Texy2! -->', 'false', NULL, NULL, NULL, NULL, NULL),
(3, 'komiksy-1211794380', 4, 1211794380, 'komiksy', '\n<p>zbytečně velká mezera mezi textem a panelem s komixy, navrhuji\nstandartní padding (jako v tlačence). Stálo by možná za úvahu přidat ke\nkomiksu stejnou funkci jako je ve fotogalerce – to automatické schovávání\nhlavičky – aby člověk po zobrazení každého komiksu nemusel skrolovat\ndolu na jejich seznam</p>\n\n<!-- by Texy2! -->', 'false', NULL, NULL, NULL, NULL, NULL),
(4, 'klice-spatne-generovani-1212055023', 3, 1212055023, 'Klíče špatné generování', '\n<p>Při generování klíčů ke článkům se neodstraňují specielní znaky,\nnapř.: ? (otazník)</p>\n\n<!-- by Texy2! -->', '', 1212055023, '', 3, '', ''),
(5, 'jitrnicky-1212503087', 4, 1212503087, 'Jitrničky', '\n<p>Nelze otevřít příspěvek v jitrničkách s názvem Sittin at a…</p>\n\n<!-- by Texy2! -->', 'false', NULL, NULL, NULL, NULL, NULL),
(6, 'odkazy-1212943415', 4, 1212943415, 'odkazy', '<p>nefungují hypertextové odkazy na fotogalerie. I po přepisu odkazu do\r\nsoučasné podoby to vyhazuje další dvě chyby 108 109 a mele to něco\r\no jazykové mutaci</p>\r\n\r\n<!-- by Texy2! -->', 'true', 1213098058, '\n<p>Opraven soubor pro rewriteRules (.htaccess) v kořenu webu. Špatné\npřesměrování.</p>\n\n<!-- by Texy2! -->', 3, '.htaccess', '');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_flash_animation`
--

CREATE TABLE IF NOT EXISTS `vypecky_flash_animation` (
  `id_flash_animation` smallint(3) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `file` varchar(50) default NULL,
  PRIMARY KEY  (`id_flash_animation`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Vypisuji data pro tabulku `vypecky_flash_animation`
--

INSERT INTO `vypecky_flash_animation` (`id_flash_animation`, `id_item`, `width`, `height`, `file`) VALUES
(1, 2, 550, 400, 'vypecky.swf'),
(2, 0, 729, 324, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_groups`
--

CREATE TABLE IF NOT EXISTS `vypecky_groups` (
  `id_group` smallint(3) unsigned NOT NULL auto_increment COMMENT 'ID skupiny',
  `name` varchar(15) default NULL COMMENT 'Nazev skupiny',
  `label` varchar(20) default NULL,
  `used` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `vypecky_groups`
--

INSERT INTO `vypecky_groups` (`id_group`, `name`, `label`, `used`) VALUES
(1, 'admin', 'Administrátor', 1),
(2, 'guest', 'Host', 1),
(3, 'user', 'Uživatel', 1),
(4, 'poweruser', 'uživatel s většími p', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_guestbook`
--

CREATE TABLE IF NOT EXISTS `vypecky_guestbook` (
  `id` smallint(3) NOT NULL auto_increment,
  `id_user` smallint(6) default NULL,
  `ip_address` varchar(15) NOT NULL,
  `time` int(10) NOT NULL,
  `nick` varchar(30) NOT NULL,
  `mail` varchar(30) NOT NULL,
  `article` varchar(50) NOT NULL,
  `text` varchar(600) NOT NULL,
  `disable` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=61 ;

--
-- Vypisuji data pro tabulku `vypecky_guestbook`
--

INSERT INTO `vypecky_guestbook` (`id`, `id_user`, `ip_address`, `time`, `nick`, `mail`, `article`, `text`, `disable`) VALUES
(11, 3, '89.103.48.51', 1205250213, 'cuba', 'jakubmatas@gmail.com', 'Nová kniha návštěv', 'Tak jsme připravili knihu návštěv! takže nám zde můžete zanéchávat vzkazy. Jesli však něco budete potřebovat řešit, Prosím pište do fóra! A prosím chovejte se tu slušně!!!B-) ', 0),
(15, 4, '147.251.103.165', 1207062043, 'delamancha', 'j.vemola@gmail.com', 'upload', 'Hoj Pavliku. Posli to mne, ja to nahodim.', 0),
(14, NULL, '90.177.21.200', 1206965247, 'pavel', 'ppavelrybecky@tiscali.cz', 'blog', 'komu muzu poslat prispevek do blogu? Jack ho chce jeste revidovat', 0),
(16, 3, '89.103.48.51', 1207213088, 'cuba', 'jakubmatas@gmail.com', 'upload blogu', 'Ahoj pavlíku, klidně ho pošli jackovi, ale taky se můžeš zaregistrovat v účtu a pak si  jej vložíš sám. Zaregistruj se rychle, protože tato možnost bude později vypnuta. Hodně zdaru!:-D ', 0),
(17, NULL, '85.160.71.162', 1207766823, 'Šalvěj', 'paveldanek@seznam.cz', 'Usnesení', 'Pratele,kamaradi,\r\nnova tvar vypecku jako celku se mi velice zamlouva. Takze jo.\r\nVyborne jsou nove dily Kuby a Kuby, bohuzel majitele pomaleho pripojeni si prohlizeju Kubu a Kubu pekne dluho, neb nez sa to, do riti, otevre, stihnu 4 piva 8 malych a pet startek. A pak vidim jeden usmudlany, rozmazan', 0),
(18, 4, '212.111.22.89', 1207862838, 'delamancha', 'j.vemola@gmail.com', 'Re: Šalvěj', 'Tož Pavle su rád, že sa ti to líbí. Snad už to brzy nahodíme oficiálně. Zatím je to jenom na http://www.dev.vypecky.info/newwebvypecky . S tím připojením bych ti safra rád pomoh, ale víš jak to je.', 0),
(19, 3, '89.103.48.51', 1208104143, 'cuba', 'jakubmatas@gmail.com', 'Připojení', 'Tak s tím připojením, je to trochu horší, ona konektivita Blahoša do sítě internet není velká (pouze 512kbit/s), takže je klidně možné, že se ti to pomály načítalo, protože jsi nebyl připojen sám, nebo někdo něco stahoval. Pokusím se konektivitu zlepšit, ale bude to chvíli trvat. Navíc Rychlost načítání stránek výpeček je také ovlivněna vývojem, pro který je vypnuto ukládání do mezipaměti, takže každá stránka se vždy přepočítává. I tento neduh bude po finálním spuštění odtraněn a odezva by se tedy měla zlepšit.', 0),
(20, NULL, '85.71.4.176', 1208567273, 'Slavka', 'iva.korgerova@centrum.cz', 'Prispevek', 'Ahoj decka, asi su lama, ale nevim, jak mam hodit prispevek do tlacenky. Taky mam k tomu h audio soubor, slo by to nejak?', 0),
(21, 3, '82.202.44.192', 1208617795, 'cuba', 'j.vemola@gmail.com', 'účet', 'Abys mohla přidat článek do tlačenky, musíš být zaregistrovaná. Ragistraci provedeš v pravém rohu v účtu. A tím se pak i přihlásíš. U registrace se ti vygenerované heslo přepošle na mail, kde si ho můžeš přečíst. V učtu si můžeš poté heslo změnit. Bohužel hudební soubory zatím přidávat nejdou, ale pošli mi ho a já to tam dám manuálně a pošlu ti zpátky link na něj.', 0),
(60, 0, '193.85.207.74', 1211127635, 'meninas da moravia', '', 'pochvala', '\n<p>Ahoooj ogaři, jste úúúúžasnííí, moc se nám líbí váš humor, snad\nse brzo potkáme ve valmezu… lucie a míša:-D :-D :-D</p>\n\n<!-- by Texy2! -->', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_iframe_targets`
--

CREATE TABLE IF NOT EXISTS `vypecky_iframe_targets` (
  `id_iframe_target` smallint(3) NOT NULL auto_increment,
  `url` varchar(100) NOT NULL,
  `label` varchar(200) default NULL,
  PRIMARY KEY  (`id_iframe_target`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `vypecky_iframe_targets`
--

INSERT INTO `vypecky_iframe_targets` (`id_iframe_target`, `url`, `label`) VALUES
(1, 'http://forum.vypecky.info/', 'Fórum Výpeček'),
(2, 'http://data.vypecky.info/', 'Nasdílená data'),
(3, 'http://webmail.vypecky.info', 'Webmail výpeček'),
(4, 'http://stav.vypecky.info/mrtg', 'Stav serveru Blahos');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_images`
--

CREATE TABLE IF NOT EXISTS `vypecky_images` (
  `id_file` int(4) NOT NULL auto_increment,
  `id_category` smallint(3) NOT NULL,
  `id_article` smallint(3) NOT NULL,
  `file` varchar(50) NOT NULL,
  `width` smallint(6) default NULL,
  `height` smallint(6) default NULL,
  `size` int(11) default NULL,
  PRIMARY KEY  (`id_file`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

--
-- Vypisuji data pro tabulku `vypecky_images`
--

INSERT INTO `vypecky_images` (`id_file`, `id_category`, `id_article`, `file`, `width`, `height`, `size`) VALUES
(23, 10, 38, 'cigara2.jpg', NULL, NULL, NULL),
(18, 10, 29, 'dsc-6270.JPG', NULL, NULL, NULL),
(20, 10, 32, 'flyer-creativity-night-369.jpg', NULL, NULL, NULL),
(13, 10, 18, '20080329-01.jpg', NULL, NULL, NULL),
(24, 10, 33, 'kaspar-fajfka.png', NULL, NULL, NULL),
(26, 10, 33, 'not.jpg', NULL, NULL, NULL),
(28, 0, 0, 'gentoo-transparent.png', 370, 492, 106087),
(29, 0, 0, 'gentoo-transparent1.png', 370, 492, 106087),
(30, 0, 0, 'not.jpg', 14, 14, 675),
(35, 10, 58, '1197105631-cover.jpg', 500, 500, 55792),
(36, 10, 1, 'crn1.jpg', 798, 502, 58515),
(37, 10, 1, 'crn1.jpg', 798, 502, 58515),
(38, 10, 1, 'crn2.jpg', 807, 639, 72307),
(39, 10, 1, 'crn7.jpg', 1440, 900, 198467),
(40, 10, 1, 'crn5.jpg', 1440, 900, 306714);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_items`
--

CREATE TABLE IF NOT EXISTS `vypecky_items` (
  `id_item` smallint(6) NOT NULL auto_increment,
  `label` varchar(30) default NULL,
  `alt` varchar(100) default NULL,
  `group_admin` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `group_user` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rw-',
  `group_guest` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'r--',
  `group_poweruser` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `scroll` smallint(5) unsigned default '0',
  `comments` tinyint(1) default '0',
  `ratings` tinyint(1) default '0',
  `priority` smallint(6) NOT NULL default '0',
  `id_category` smallint(6) NOT NULL,
  `id_module` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id_item`),
  KEY `id_category` (`id_category`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Vypisuji data pro tabulku `vypecky_items`
--

INSERT INTO `vypecky_items` (`id_item`, `label`, `alt`, `group_admin`, `group_user`, `group_guest`, `group_poweruser`, `scroll`, `comments`, `ratings`, `priority`, `id_category`, `id_module`) VALUES
(1, 'Tlačenka', 'Tlačenka aneb blog na Výpečkách', 'rwc', 'rw-', 'r--', 'rwc', 10, 1, 1, 0, 10, 11),
(2, 'Výpečky', 'Naše logo', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 5, 1, 12),
(3, 'Portfolio', 'Portfolio našeho grafika', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 2, 12),
(4, 'Fotogalerie', 'Fotografie z akcí', 'rwc', 'rw-', 'r--', 'rwc', 10, 1, 0, 0, 11, 8),
(5, 'Jitrničky', 'Jitrničky aneb novinky na výpečkách', 'rwc', 'rw-', 'r--', 'rwc', 4, 1, 0, 0, 13, 2),
(6, 'Komiks', 'Komiks Kuba a Kuba', 'rwc', 'r--', 'r--', 'rwc', 1, 0, 1, 0, 3, 13),
(7, 'Úvodní proslov', 'Úvodní slovo ke komiksům', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 5, 3, 1),
(8, 'Odkazy', 'Odkazy', 'rwc', 'rw-', 'r--', 'rwc', 0, 0, 0, 0, 12, 14),
(9, 'Odkazy', NULL, 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 12, 1),
(10, 'Jak žije Blahoš', 'Jak si žije Server Blahoš u nás ve sklepě', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 8, 10),
(11, 'O Blahošovi', 'Úvodní text o Blahošovi', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 5, 8, 1),
(12, 'WebMail Výpeček', 'Přístup k Webmailovému klientu Výpeček', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 7, 10),
(13, 'Sdílená data', 'Sdílená data pro potřeby Výpeček', 'rwc', 'r--', 'r--', 'rwc', 0, 0, 0, 0, 6, 10),
(14, 'Naše Data', 'Úvodní slovo ke zdíleným datů', 'rwc', 'rw-', 'r--', 'rwc', 0, 0, 0, 0, 6, 1),
(15, 'Kniha návštěv', 'Kniha návštěv - nebojte se a napište nám', 'rwc', 'rw-', 'r--', 'rwc', 10, 0, 0, 0, 9, 9),
(16, 'Chyby enginu', 'Chyby a opravy v Enginu Výpeček', 'rwc', 'rw-', '---', 'rwc', 0, 0, 0, 0, 14, 15),
(17, 'Můj účet', 'Nastavení mého účtu', 'rwc', 'rw-', 'r--', 'rwc', 0, 0, 0, 0, 4, 4),
(18, 'Sponzoři', 'Naši sponzoři', 'rwc', 'rw-', 'r--', 'rwc', 0, 0, 0, 0, 15, 16),
(19, 'Uživatelé', 'Uživatelé na portálu', 'rwc', 'r--', '---', 'rwc', 20, 0, 0, 0, 16, 17);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_links`
--

CREATE TABLE IF NOT EXISTS `vypecky_links` (
  `id_link` smallint(6) NOT NULL auto_increment,
  `id_link_section` smallint(6) NOT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(200) NOT NULL,
  `target` enum('blank','this','popup') NOT NULL default 'blank',
  `image_file` varchar(50) default NULL,
  PRIMARY KEY  (`id_link`),
  KEY `id_link_section` (`id_link_section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Vypisuji data pro tabulku `vypecky_links`
--

INSERT INTO `vypecky_links` (`id_link`, `id_link_section`, `label`, `url`, `target`, `image_file`) VALUES
(1, 1, 'Gentoo linux', 'http://www.gentoo.org', 'blank', NULL),
(2, 1, 'KDE - K Desktop Enrvitionde', 'http://www.kde.org', 'blank', NULL),
(4, 0, 'Debian Linux', 'http://www.debian.org', 'blank', NULL),
(10, 13, 'Nejlepší vyhledávač', 'http://www.google.cz', 'blank', NULL),
(6, 0, 'Stránky portálu zaměřeného na dění okolo Open-Source a linuxu', 'http://www.root.cz', 'blank', NULL),
(7, 1, 'Portál zaměřený na dění okolo Open-Source a Linuxu', 'http://www.root.cz', 'blank', NULL),
(9, 3, 'Kapela Agáve 9', 'http://www.agave9.com/', 'blank', NULL),
(11, 14, 'youngprimitive - mladí a primitivní', 'http://www.youngprimitive.cz/', 'blank', NULL),
(12, 14, 'beerborec', 'http://www.beerborec.cz', 'blank', NULL),
(13, 14, 'lastfm', 'http://www.last.fm', 'blank', NULL),
(14, 14, 'picasaweb', 'http://www.picasaweb.google.com/j.vemola', 'blank', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_links_section`
--

CREATE TABLE IF NOT EXISTS `vypecky_links_section` (
  `id_link_section` smallint(6) NOT NULL auto_increment,
  `key` varchar(50) NOT NULL,
  `label` varchar(100) NOT NULL,
  `prioryty` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id_link_section`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Vypisuji data pro tabulku `vypecky_links_section`
--

INSERT INTO `vypecky_links_section` (`id_link_section`, `key`, `label`, `prioryty`) VALUES
(1, 'stranky-o-linuxu', 'Stránky o linuxu', 0),
(3, 'kapely-a-kapelky', 'Kapely a kapelky', 0),
(14, 'oblibene-stranky', 'Oblíbené stránky', 0),
(13, 'vyhledavace', 'Vyhledávače', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_modules`
--

CREATE TABLE IF NOT EXISTS `vypecky_modules` (
  `id_module` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `params` varchar(100) default NULL,
  `datadir` varchar(100) default NULL,
  `dbtable1` varchar(50) default NULL,
  `dbtable2` varchar(50) default NULL,
  `dbtable3` varchar(50) default NULL,
  PRIMARY KEY  (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Vypisuji data pro tabulku `vypecky_modules`
--

INSERT INTO `vypecky_modules` (`id_module`, `name`, `params`, `datadir`, `dbtable1`, `dbtable2`, `dbtable3`) VALUES
(1, 'text', NULL, NULL, 'texts', NULL, NULL),
(2, 'news', NULL, NULL, 'news', NULL, NULL),
(3, 'dwfiles', NULL, 'dwfiles', 'dwfiles', NULL, NULL),
(4, 'login', NULL, NULL, 'users', NULL, NULL),
(5, 'minigalery', NULL, 'minigalery', 'minigalery', NULL, NULL),
(6, 'workers', NULL, 'workers', 'workers', NULL, NULL),
(7, 'sendmail', NULL, 'sendmail', 'sendmails', NULL, NULL),
(8, 'photogalery', 'photosingalerylist=3', 'photogalery', 'photos', 'photo_galeries', 'photo_sections'),
(9, 'guestbook', NULL, '', 'guestbook', NULL, NULL),
(10, 'iframe', NULL, NULL, 'iframe_targets', NULL, NULL),
(11, 'blog', NULL, NULL, 'blogs', NULL, NULL),
(12, 'flashpage', '', 'flashpages', NULL, NULL, NULL),
(13, 'comics', NULL, 'comics', 'comics', NULL, NULL),
(14, 'links', NULL, 'links', 'link_sections', NULL, NULL),
(15, 'errors', NULL, NULL, 'errors', NULL, NULL),
(16, 'sponsors', NULL, 'sponsors', 'sponsors', NULL, NULL),
(17, 'users', NULL, 'users', 'users', 'groups', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_news`
--

CREATE TABLE IF NOT EXISTS `vypecky_news` (
  `id_new` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `urlkey` varchar(50) NOT NULL,
  `id_user` smallint(6) NOT NULL,
  `label_cs` varchar(50) NOT NULL,
  `text_cs` varchar(500) NOT NULL,
  `label_en` varchar(50) default NULL,
  `text_en` varchar(500) default NULL,
  `label_de` varchar(50) default NULL,
  `text_de` varchar(500) default NULL,
  `time` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_new`),
  KEY `id_user` (`id_user`),
  KEY `key` (`urlkey`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Vypisuji data pro tabulku `vypecky_news`
--

INSERT INTO `vypecky_news` (`id_new`, `id_item`, `urlkey`, `id_user`, `label_cs`, `text_cs`, `label_en`, `text_en`, `label_de`, `text_de`, `time`, `deleted`) VALUES
(7, 5, 'novinky-jitrnicky-na-vypeckach', 3, 'Novinky (Jitrničky) na Výpečkách', '<p>Tak první novinka na <a href="http://www.vypecky.info">Výpečkách</a> je\r\nvlasně zavedení <strong>novinek</strong>, kde můžete psát krátké novinky.\r\nTak hodně zdaru! :-D (těch novinek je tu až moc :-D )</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1210695435, 0),
(8, 5, 'pjekne', 10, 'Pjekne', '<p>Holahola… Zahradní restaurace u Valentů v Hrachovci zahajuje zítra\r\nv 17:00 svůj letní provoz slavnostním naražením sudu Radegast 10°.\r\nNásledovat budou sudy Zubr 11° (17:05), Radegast 12° (17:07) a Kofola (17:10\r\nesli bude). Po slavnostním naražení se koná slavnostní vypití. Bronik\r\nletos připravil 10 vylepšení oproti loňsku a kdo je všechny objeví,\r\ndostane pivečko zadarmo:-)</p>\r\n', NULL, NULL, NULL, NULL, 1210887011, 0),
(9, 5, 'karamazovi', 4, 'Karamazovi', '\n<p>Žijte kulturně a zajděte si na nový Zelenkův film Karamazovi. Po dlouhé\ndobě zase jeden dobrý český film. Zelenka je holt pašák :-)</p>\n\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211016870, 0),
(10, 5, 'upraveny-layout', 3, 'Upravený layout', '<p>Konečně jsem si našel trochu času a upravil layout výpeček. Teď by se\r\nměl korektně zobrazovat v FF a Opeře, jenom v IE zůstává pár chybiček.\r\nCelý mám (částečně) měnitelnou šířku takže potěším lidi co\r\nnepoužívají velké rozlišení.</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211050696, 0),
(11, 5, 'indiana-jones', 4, 'Indiana Jones', '<p>S výpečky do kina. Zítra jdeme s Míšou a Synkem na nového Jonese, tak\r\nneváhejte a připojte se – the more the merrier :-)</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211467174, 0),
(12, 5, 'indiana-jones2', 4, 'Indiana Jones2', '<p>Tak na jonese se nakonec jde až v neděli. Včera byla noc kejklířů,\r\ndneska je ta muzejní. Obě dvě akce mají samozřejmě vyšší prioritu.\r\nDnes na pryglu začnou soutěžní ohňostroje, nenechte si ujit – see ya there.</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211620130, 0),
(13, 5, 'indiana-jones-potreti', 4, 'Indiana Jones potřetí', '\n<p>Tak na Jonese se nakonec šlo až v pondělí. Kdo byl v něděli, tomu se\nonlouvám. Jinak to samozřejmě stálo za to. Smáli jsem se, báli i plakali.\nV závěru se nám sice Jones oženil, ale i to se dalo přežít. Tak kdo\nnebyl, šup šup do kina.</p>\n\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1211980229, 0),
(14, 5, 'garden-party', 4, 'garden party', '<p>pro všechny, kdo jsou ve Valmezu: V Hrachovci na zahrádce u Broni se\r\nkoná party na oslavu narozenin jeho zeny (me sestrenky) Svatavy. Kdo pujdete kolem,\r\nnezapomente se stavit – ma tam byt i sele, ci co.</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1212250241, 0),
(16, 5, 'sittin-at-a-bar', 10, 'Sittin&#039; at a bar', '<p>Kdo by neměl chuť se tu a tam ožrat do němoty…? Zde je takový malý\r\ntrenažer pro výpitky jak dosáhnout dobré opice a nenadělat příliš\r\nostudy… Vysoce návykový shledávám především song… <a\r\nhref="http://www.tinymania.com/play/sittinatabar/">Odkaz</a></p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1212482056, 0),
(17, 5, 'cs-label', 3, 'cs label', 'cs text', 'en label', 'en text', NULL, NULL, 0, 0),
(18, 5, 'novinka-v-cestine', 3, 'Novinka v češtině', 'Text novinky v češtině. Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky, Česky.', 'English news', 'News in English language. English, English, English, English, English, English, English, English, English, English, English, English, English, English, English, English, English, English, English, English, English, English.', NULL, NULL, 0, 0),
(19, 5, 'novinka-v-cestine', 3, 'Novinka v češtině s popisem', 'česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky ', 'English news with label', 'english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english ', NULL, NULL, 1218921753, 0),
(20, 5, '-label--', 3, 'label', '&lt;b&gt;text&lt;/b&gt;', NULL, NULL, NULL, NULL, 1218975043, 1),
(21, 5, 'cs-label-novy-pekny', 3, 'cs label nový pěkný', 'fdsafsdafasfd', NULL, NULL, NULL, NULL, 1218979456, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_panels`
--

CREATE TABLE IF NOT EXISTS `vypecky_panels` (
  `id_panel` smallint(3) NOT NULL auto_increment,
  `priority` smallint(2) NOT NULL default '0',
  `label` varchar(30) NOT NULL,
  `id_item` smallint(5) unsigned default NULL,
  `position` enum('left','right') NOT NULL default 'left',
  `enable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_panel`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Vypisuji data pro tabulku `vypecky_panels`
--

INSERT INTO `vypecky_panels` (`id_panel`, `priority`, `label`, `id_item`, `position`, `enable`) VALUES
(1, 0, 'Nejnovější tlačenka', 1, 'right', 0),
(2, 4, 'Aktuální komiks', 6, 'right', 0),
(3, 5, 'Fotogalerie', 4, 'right', 0),
(4, 10, 'Výpečky', 2, 'left', 0),
(5, 5, 'Účet', 17, 'left', 0),
(6, 0, 'Jitrničky', 5, 'left', 1),
(7, 0, 'Sponzoři', 18, 'right', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photos`
--

CREATE TABLE IF NOT EXISTS `vypecky_photos` (
  `id_photo` smallint(5) unsigned NOT NULL auto_increment,
  `label_cs` varchar(40) default NULL,
  `label_en` varchar(40) default NULL,
  `label_de` varchar(40) default NULL,
  `file` varchar(40) NOT NULL,
  `time` int(10) unsigned default NULL,
  `text_cs` varchar(400) default NULL,
  `text_en` varchar(400) NOT NULL,
  `text_de` varchar(400) NOT NULL,
  `height` smallint(5) unsigned default '0',
  `width` smallint(5) unsigned default '0',
  `id_galery` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id_photo`),
  KEY `id_galery` (`id_galery`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=815 ;

--
-- Vypisuji data pro tabulku `vypecky_photos`
--

INSERT INTO `vypecky_photos` (`id_photo`, `label_cs`, `label_en`, `label_de`, `file`, `time`, `text_cs`, `text_en`, `text_de`, `height`, `width`, `id_galery`, `id_user`) VALUES
(237, 'dscf5551.jpg', NULL, NULL, 'dscf5551.jpg', 1207753780, NULL, '', '', 0, 0, 28, 1),
(236, 'dscf5546.jpg', NULL, NULL, 'dscf5546.jpg', 1207753778, NULL, '', '', 0, 0, 28, 1),
(235, 'dscf5545.jpg', NULL, NULL, 'dscf5545.jpg', 1207753777, NULL, '', '', 0, 0, 28, 1),
(234, 'dscf5539.jpg', NULL, NULL, 'dscf5539.jpg', 1207753775, NULL, '', '', 0, 0, 28, 1),
(233, 'dscf5527.jpg', NULL, NULL, 'dscf5527.jpg', 1207753773, NULL, '', '', 0, 0, 28, 1),
(232, 'dscf5521.jpg', NULL, NULL, 'dscf5521.jpg', 1207753772, NULL, '', '', 0, 0, 28, 1),
(231, 'dscf5519.jpg', NULL, NULL, 'dscf5519.jpg', 1207753771, NULL, '', '', 0, 0, 28, 1),
(230, 'dscf5513.jpg', NULL, NULL, 'dscf5513.jpg', 1207753769, NULL, '', '', 0, 0, 28, 1),
(229, 'dscf5507.jpg', NULL, NULL, 'dscf5507.jpg', 1207753767, NULL, '', '', 0, 0, 28, 1),
(228, 'dscf5502.jpg', NULL, NULL, 'dscf5502.jpg', 1207753766, NULL, '', '', 0, 0, 28, 1),
(20, 'dsc-60111.jpg', NULL, NULL, 'dsc-60111.jpg', 1207599575, NULL, '', '', 0, 0, 6, 1),
(21, 'dsc-60131.jpg', NULL, NULL, 'dsc-60131.jpg', 1207599579, NULL, '', '', 0, 0, 6, 1),
(22, 'dsc-60241.jpg', NULL, NULL, 'dsc-60241.jpg', 1207599582, NULL, '', '', 0, 0, 6, 1),
(23, 'dsc-60251.jpg', NULL, NULL, 'dsc-60251.jpg', 1207599584, NULL, '', '', 0, 0, 6, 1),
(24, 'dsc-60281.jpg', NULL, NULL, 'dsc-60281.jpg', 1207599586, NULL, '', '', 0, 0, 6, 1),
(25, 'dsc-60301.jpg', NULL, NULL, 'dsc-60301.jpg', 1207599589, NULL, '', '', 0, 0, 6, 1),
(26, 'dsc-60311.jpg', NULL, NULL, 'dsc-60311.jpg', 1207599591, NULL, '', '', 0, 0, 6, 1),
(27, 'dsc-60391.jpg', NULL, NULL, 'dsc-60391.jpg', 1207599593, NULL, '', '', 0, 0, 6, 1),
(28, 'dsc-60461.jpg', NULL, NULL, 'dsc-60461.jpg', 1207599594, NULL, '', '', 0, 0, 6, 1),
(29, 'dsc-60501.jpg', NULL, NULL, 'dsc-60501.jpg', 1207599596, NULL, '', '', 0, 0, 6, 1),
(30, 'dsc-60551.jpg', NULL, NULL, 'dsc-60551.jpg', 1207599598, NULL, '', '', 0, 0, 6, 1),
(31, 'dsc-60691.jpg', NULL, NULL, 'dsc-60691.jpg', 1207599602, NULL, '', '', 0, 0, 6, 1),
(226, 'dscf5490.jpg', NULL, NULL, 'dscf5490.jpg', 1207753763, NULL, '', '', 0, 0, 28, 1),
(205, 'dscf5397.jpg', NULL, NULL, 'dscf5397.jpg', 1207753726, NULL, '', '', 0, 0, 28, 1),
(204, 'dscf5396.jpg', NULL, NULL, 'dscf5396.jpg', 1207753725, NULL, '', '', 0, 0, 28, 1),
(203, 'dscf5395.jpg', NULL, NULL, 'dscf5395.jpg', 1207753723, NULL, '', '', 0, 0, 28, 1),
(202, 'dscf5387.jpg', NULL, NULL, 'dscf5387.jpg', 1207753721, NULL, '', '', 0, 0, 28, 1),
(201, 'dscf5382.jpg', NULL, NULL, 'dscf5382.jpg', 1207753717, NULL, '', '', 0, 0, 28, 1),
(200, 'dscf5379.jpg', NULL, NULL, 'dscf5379.jpg', 1207753716, NULL, '', '', 0, 0, 28, 1),
(199, 'dscf5375.jpg', NULL, NULL, 'dscf5375.jpg', 1207753715, NULL, '', '', 0, 0, 28, 1),
(198, 'dscf5373.jpg', NULL, NULL, 'dscf5373.jpg', 1207753713, NULL, '', '', 0, 0, 28, 1),
(197, 'dscf5366.jpg', NULL, NULL, 'dscf5366.jpg', 1207753712, NULL, '', '', 0, 0, 28, 1),
(196, 'dscf5352.jpg', NULL, NULL, 'dscf5352.jpg', 1207753710, NULL, '', '', 0, 0, 28, 1),
(194, 'dscf5341.jpg', NULL, NULL, 'dscf5341.jpg', 1207753707, NULL, '', '', 0, 0, 28, 1),
(195, 'dscf5348.jpg', NULL, NULL, 'dscf5348.jpg', 1207753708, NULL, '', '', 0, 0, 28, 1),
(46, 'dsc-57181.jpg', NULL, NULL, 'dsc-57181.jpg', 1207600052, NULL, '', '', 0, 0, 7, 1),
(47, 'dsc-57201.jpg', NULL, NULL, 'dsc-57201.jpg', 1207600053, NULL, '', '', 0, 0, 7, 1),
(192, '', NULL, NULL, 'dsc-5722.jpg', 1207727719, NULL, '', '', 0, 0, 7, 1),
(49, 'dsc-57301.jpg', NULL, NULL, 'dsc-57301.jpg', 1207600058, NULL, '', '', 0, 0, 7, 1),
(50, 'dsc-57351.jpg', NULL, NULL, 'dsc-57351.jpg', 1207600060, NULL, '', '', 0, 0, 7, 1),
(51, 'dsc-57411.jpg', NULL, NULL, 'dsc-57411.jpg', 1207600062, NULL, '', '', 0, 0, 7, 1),
(52, 'dsc-57441.jpg', NULL, NULL, 'dsc-57441.jpg', 1207600063, NULL, '', '', 0, 0, 7, 1),
(53, 'dsc-57521.jpg', NULL, NULL, 'dsc-57521.jpg', 1207600065, NULL, '', '', 0, 0, 7, 1),
(54, 'dsc-57531.jpg', NULL, NULL, 'dsc-57531.jpg', 1207600066, NULL, '', '', 0, 0, 7, 1),
(55, 'dsc-57671.jpg', NULL, NULL, 'dsc-57671.jpg', 1207600068, NULL, '', '', 0, 0, 7, 1),
(193, 'dscf5338.jpg', NULL, NULL, 'dscf5338.jpg', 1207753705, NULL, '', '', 0, 0, 28, 1),
(57, 'dsc-57811.jpg', NULL, NULL, 'dsc-57811.jpg', 1207600071, NULL, '', '', 0, 0, 7, 1),
(58, 'dsc-57821.jpg', NULL, NULL, 'dsc-57821.jpg', 1207600073, NULL, '', '', 0, 0, 7, 1),
(59, 'dsc-57921.jpg', NULL, NULL, 'dsc-57921.jpg', 1207600074, NULL, '', '', 0, 0, 7, 1),
(60, 'dsc-57941.jpg', NULL, NULL, 'dsc-57941.jpg', 1207600076, NULL, '', '', 0, 0, 7, 1),
(61, 'dsc-57991.jpg', NULL, NULL, 'dsc-57991.jpg', 1207600077, NULL, '', '', 0, 0, 7, 1),
(62, 'dsc-58181.jpg', NULL, NULL, 'dsc-58181.jpg', 1207600269, NULL, '', '', 0, 0, 8, 1),
(63, 'dsc-58241.jpg', NULL, NULL, 'dsc-58241.jpg', 1207600273, NULL, '', '', 0, 0, 8, 1),
(64, 'dsc-58261.jpg', NULL, NULL, 'dsc-58261.jpg', 1207600274, NULL, '', '', 0, 0, 8, 1),
(65, 'dsc-58281.jpg', NULL, NULL, 'dsc-58281.jpg', 1207600278, NULL, '', '', 0, 0, 8, 1),
(66, 'dsc-58331.jpg', NULL, NULL, 'dsc-58331.jpg', 1207600281, NULL, '', '', 0, 0, 8, 1),
(67, 'dsc-58371.jpg', NULL, NULL, 'dsc-58371.jpg', 1207600282, NULL, '', '', 0, 0, 8, 1),
(68, 'dsc-58411.jpg', NULL, NULL, 'dsc-58411.jpg', 1207600286, NULL, '', '', 0, 0, 8, 1),
(69, 'dsc-58441.jpg', NULL, NULL, 'dsc-58441.jpg', 1207600287, NULL, '', '', 0, 0, 8, 1),
(70, 'dsc-58461.jpg', NULL, NULL, 'dsc-58461.jpg', 1207600291, NULL, '', '', 0, 0, 8, 1),
(71, 'dsc-58561.jpg', NULL, NULL, 'dsc-58561.jpg', 1207600294, NULL, '', '', 0, 0, 8, 1),
(72, 'dsc-58611.jpg', NULL, NULL, 'dsc-58611.jpg', 1207600296, NULL, '', '', 0, 0, 8, 1),
(73, 'dsc-58661.jpg', NULL, NULL, 'dsc-58661.jpg', 1207600297, NULL, '', '', 0, 0, 8, 1),
(74, 'dsc-58671.jpg', NULL, NULL, 'dsc-58671.jpg', 1207600299, NULL, '', '', 0, 0, 8, 1),
(75, 'dsc-58721.jpg', NULL, NULL, 'dsc-58721.jpg', 1207600300, NULL, '', '', 0, 0, 8, 1),
(76, 'dsc-58741.jpg', NULL, NULL, 'dsc-58741.jpg', 1207600302, NULL, '', '', 0, 0, 8, 1),
(77, 'dsc-58751.jpg', NULL, NULL, 'dsc-58751.jpg', 1207600303, NULL, '', '', 0, 0, 8, 1),
(78, 'dsc-58801.jpg', NULL, NULL, 'dsc-58801.jpg', 1207600305, NULL, '', '', 0, 0, 8, 1),
(79, 'dsc-58901.jpg', NULL, NULL, 'dsc-58901.jpg', 1207600307, NULL, '', '', 0, 0, 8, 1),
(80, 'dsc-58911.jpg', NULL, NULL, 'dsc-58911.jpg', 1207600308, NULL, '', '', 0, 0, 8, 1),
(81, 'dsc-58961.jpg', NULL, NULL, 'dsc-58961.jpg', 1207600310, NULL, '', '', 0, 0, 8, 1),
(82, 'dsc-58971.jpg', NULL, NULL, 'dsc-58971.jpg', 1207600311, NULL, '', '', 0, 0, 8, 1),
(83, 'dsc-59001.jpg', NULL, NULL, 'dsc-59001.jpg', 1207600313, NULL, '', '', 0, 0, 8, 1),
(84, 'dsc-59171.jpg', NULL, NULL, 'dsc-59171.jpg', 1207600314, NULL, '', '', 0, 0, 8, 1),
(85, 'dsc-59261.jpg', NULL, NULL, 'dsc-59261.jpg', 1207600316, NULL, '', '', 0, 0, 8, 1),
(86, 'dsc-59281.jpg', NULL, NULL, 'dsc-59281.jpg', 1207600317, NULL, '', '', 0, 0, 8, 1),
(87, 'dsc-59341.jpg', NULL, NULL, 'dsc-59341.jpg', 1207600319, NULL, '', '', 0, 0, 8, 1),
(88, 'dsc-59361.jpg', NULL, NULL, 'dsc-59361.jpg', 1207600320, NULL, '', '', 0, 0, 8, 1),
(89, 'dsc-59371.jpg', NULL, NULL, 'dsc-59371.jpg', 1207600324, NULL, '', '', 0, 0, 8, 1),
(90, 'dsc-59391.jpg', NULL, NULL, 'dsc-59391.jpg', 1207600327, NULL, '', '', 0, 0, 8, 1),
(91, 'dsc-59531.jpg', NULL, NULL, 'dsc-59531.jpg', 1207600329, NULL, '', '', 0, 0, 8, 1),
(227, 'dscf5499.jpg', NULL, NULL, 'dscf5499.jpg', 1207753764, NULL, '', '', 0, 0, 28, 1),
(225, 'dscf5482.jpg', NULL, NULL, 'dscf5482.jpg', 1207753761, NULL, '', '', 0, 0, 28, 1),
(224, 'dscf5481.jpg', NULL, NULL, 'dscf5481.jpg', 1207753760, NULL, '', '', 0, 0, 28, 1),
(223, 'dscf5480.jpg', NULL, NULL, 'dscf5480.jpg', 1207753759, NULL, '', '', 0, 0, 28, 1),
(222, 'dscf5479.jpg', NULL, NULL, 'dscf5479.jpg', 1207753757, NULL, '', '', 0, 0, 28, 1),
(221, 'dscf5473.jpg', NULL, NULL, 'dscf5473.jpg', 1207753756, NULL, '', '', 0, 0, 28, 1),
(220, 'dscf5468.jpg', NULL, NULL, 'dscf5468.jpg', 1207753754, NULL, '', '', 0, 0, 28, 1),
(219, 'dscf5460.jpg', NULL, NULL, 'dscf5460.jpg', 1207753753, NULL, '', '', 0, 0, 28, 1),
(218, 'dscf5456.jpg', NULL, NULL, 'dscf5456.jpg', 1207753751, NULL, '', '', 0, 0, 28, 1),
(217, 'dscf5450.jpg', NULL, NULL, 'dscf5450.jpg', 1207753749, NULL, '', '', 0, 0, 28, 1),
(216, 'dscf5445.jpg', NULL, NULL, 'dscf5445.jpg', 1207753746, NULL, '', '', 0, 0, 28, 1),
(215, 'dscf5439.jpg', NULL, NULL, 'dscf5439.jpg', 1207753744, NULL, '', '', 0, 0, 28, 1),
(214, 'dscf5430.jpg', NULL, NULL, 'dscf5430.jpg', 1207753743, NULL, '', '', 0, 0, 28, 1),
(213, 'dscf5424.jpg', NULL, NULL, 'dscf5424.jpg', 1207753741, NULL, '', '', 0, 0, 28, 1),
(212, 'dscf5423.jpg', NULL, NULL, 'dscf5423.jpg', 1207753740, NULL, '', '', 0, 0, 28, 1),
(211, 'dscf5421.jpg', NULL, NULL, 'dscf5421.jpg', 1207753738, NULL, '', '', 0, 0, 28, 1),
(210, 'dscf5418.jpg', NULL, NULL, 'dscf5418.jpg', 1207753736, NULL, '', '', 0, 0, 28, 1),
(209, 'dscf5417.jpg', NULL, NULL, 'dscf5417.jpg', 1207753735, NULL, '', '', 0, 0, 28, 1),
(208, 'dscf5406.jpg', NULL, NULL, 'dscf5406.jpg', 1207753733, NULL, '', '', 0, 0, 28, 1),
(207, 'dscf5405.jpg', NULL, NULL, 'dscf5405.jpg', 1207753731, NULL, '', '', 0, 0, 28, 1),
(206, 'dscf5401.jpg', NULL, NULL, 'dscf5401.jpg', 1207753728, NULL, '', '', 0, 0, 28, 1),
(238, 'dscf5552.jpg', NULL, NULL, 'dscf5552.jpg', 1207753781, NULL, '', '', 0, 0, 28, 1),
(239, 'p10.jpg', NULL, NULL, 'p10.jpg', 1207753833, NULL, '', '', 0, 0, 29, 1),
(240, 'p11.jpg', NULL, NULL, 'p11.jpg', 1207753834, NULL, '', '', 0, 0, 29, 1),
(241, 'p12.jpg', NULL, NULL, 'p12.jpg', 1207753835, NULL, '', '', 0, 0, 29, 1),
(242, 'p15.jpg', NULL, NULL, 'p15.jpg', 1207753836, NULL, '', '', 0, 0, 29, 1),
(243, 'p16.jpg', NULL, NULL, 'p16.jpg', 1207753837, NULL, '', '', 0, 0, 29, 1),
(244, 'p18.jpg', NULL, NULL, 'p18.jpg', 1207753838, NULL, '', '', 0, 0, 29, 1),
(245, 'dscf5795u.jpg', NULL, NULL, 'dscf5795u.jpg', 1207753840, NULL, '', '', 0, 0, 29, 1),
(246, 'p2.jpg', NULL, NULL, 'p2.jpg', 1207753841, NULL, '', '', 0, 0, 29, 1),
(247, 'p4.jpg', NULL, NULL, 'p4.jpg', 1207753842, NULL, '', '', 0, 0, 29, 1),
(248, 'p7.jpg', NULL, NULL, 'p7.jpg', 1207753843, NULL, '', '', 0, 0, 29, 1),
(249, 'p8.jpg', NULL, NULL, 'p8.jpg', 1207753844, NULL, '', '', 0, 0, 29, 1),
(250, 'p9.jpg', NULL, NULL, 'p9.jpg', 1207753845, NULL, '', '', 0, 0, 29, 1),
(251, 'dscf5562.jpg', NULL, NULL, 'dscf5562.jpg', 1207753846, NULL, '', '', 0, 0, 29, 1),
(252, 'dscf5564.jpg', NULL, NULL, 'dscf5564.jpg', 1207753848, NULL, '', '', 0, 0, 29, 1),
(253, 'dscf5566.jpg', NULL, NULL, 'dscf5566.jpg', 1207753850, NULL, '', '', 0, 0, 29, 1),
(254, 'dscf5567.jpg', NULL, NULL, 'dscf5567.jpg', 1207753851, NULL, '', '', 0, 0, 29, 1),
(255, 'dscf5578.jpg', NULL, NULL, 'dscf5578.jpg', 1207753853, NULL, '', '', 0, 0, 29, 1),
(256, 'dscf5579.jpg', NULL, NULL, 'dscf5579.jpg', 1207753854, NULL, '', '', 0, 0, 29, 1),
(257, 'dscf5582.jpg', NULL, NULL, 'dscf5582.jpg', 1207753856, NULL, '', '', 0, 0, 29, 1),
(258, 'dscf5589.jpg', NULL, NULL, 'dscf5589.jpg', 1207753858, NULL, '', '', 0, 0, 29, 1),
(259, 'dscf5600.jpg', NULL, NULL, 'dscf5600.jpg', 1207753859, NULL, '', '', 0, 0, 29, 1),
(260, 'dscf5605.jpg', NULL, NULL, 'dscf5605.jpg', 1207753861, NULL, '', '', 0, 0, 29, 1),
(261, 'dscf5608.jpg', NULL, NULL, 'dscf5608.jpg', 1207753864, NULL, '', '', 0, 0, 29, 1),
(262, 'dscf5610.jpg', NULL, NULL, 'dscf5610.jpg', 1207753866, NULL, '', '', 0, 0, 29, 1),
(263, 'dscf5624.jpg', NULL, NULL, 'dscf5624.jpg', 1207753869, NULL, '', '', 0, 0, 29, 1),
(264, 'dscf5629.jpg', NULL, NULL, 'dscf5629.jpg', 1207753871, NULL, '', '', 0, 0, 29, 1),
(265, 'dscf5632.jpg', NULL, NULL, 'dscf5632.jpg', 1207753872, NULL, '', '', 0, 0, 29, 1),
(266, 'dscf5652.jpg', NULL, NULL, 'dscf5652.jpg', 1207753874, NULL, '', '', 0, 0, 29, 1),
(267, 'dscf5664.jpg', NULL, NULL, 'dscf5664.jpg', 1207753875, NULL, '', '', 0, 0, 29, 1),
(268, 'dscf5685.jpg', NULL, NULL, 'dscf5685.jpg', 1207753877, NULL, '', '', 0, 0, 29, 1),
(269, 'dscf5688.jpg', NULL, NULL, 'dscf5688.jpg', 1207753879, NULL, '', '', 0, 0, 29, 1),
(270, 'dscf5689.jpg', NULL, NULL, 'dscf5689.jpg', 1207753880, NULL, '', '', 0, 0, 29, 1),
(271, 'dscf5705.jpg', NULL, NULL, 'dscf5705.jpg', 1207753882, NULL, '', '', 0, 0, 29, 1),
(272, 'dscf5723.jpg', NULL, NULL, 'dscf5723.jpg', 1207753883, NULL, '', '', 0, 0, 29, 1),
(273, 'dscf5724.jpg', NULL, NULL, 'dscf5724.jpg', 1207753885, NULL, '', '', 0, 0, 29, 1),
(274, 'dscf5734.jpg', NULL, NULL, 'dscf5734.jpg', 1207753887, NULL, '', '', 0, 0, 29, 1),
(275, 'dscf5738.jpg', NULL, NULL, 'dscf5738.jpg', 1207753888, NULL, '', '', 0, 0, 29, 1),
(276, 'dscf5764.jpg', NULL, NULL, 'dscf5764.jpg', 1207753890, NULL, '', '', 0, 0, 29, 1),
(277, 'dscf5767.jpg', NULL, NULL, 'dscf5767.jpg', 1207753892, NULL, '', '', 0, 0, 29, 1),
(278, 'dscf5774.jpg', NULL, NULL, 'dscf5774.jpg', 1207753893, NULL, '', '', 0, 0, 29, 1),
(279, 'dscf5776.jpg', NULL, NULL, 'dscf5776.jpg', 1207753895, NULL, '', '', 0, 0, 29, 1),
(280, 'dscf5780.jpg', NULL, NULL, 'dscf5780.jpg', 1207753896, NULL, '', '', 0, 0, 29, 1),
(281, 'dscf5786.jpg', NULL, NULL, 'dscf5786.jpg', 1207753898, NULL, '', '', 0, 0, 29, 1),
(282, 'dscf5788.jpg', NULL, NULL, 'dscf5788.jpg', 1207753899, NULL, '', '', 0, 0, 29, 1),
(283, 'dscf5792.jpg', NULL, NULL, 'dscf5792.jpg', 1207753901, NULL, '', '', 0, 0, 29, 1),
(284, 'dscf5802.jpg', NULL, NULL, 'dscf5802.jpg', 1207753903, NULL, '', '', 0, 0, 29, 1),
(285, 'dscf5813.jpg', NULL, NULL, 'dscf5813.jpg', 1207753904, NULL, '', '', 0, 0, 29, 1),
(286, 'dscf5815.jpg', NULL, NULL, 'dscf5815.jpg', 1207753906, NULL, '', '', 0, 0, 29, 1),
(287, 'dscf5819.jpg', NULL, NULL, 'dscf5819.jpg', 1207753909, NULL, '', '', 0, 0, 29, 1),
(288, 'dscf2008.jpg', NULL, NULL, 'dscf2008.jpg', 1207753981, NULL, '', '', 0, 0, 30, 1),
(289, 'dscf2018.jpg', NULL, NULL, 'dscf2018.jpg', 1207753982, NULL, '', '', 0, 0, 30, 1),
(290, 'dscf2022.jpg', NULL, NULL, 'dscf2022.jpg', 1207753984, NULL, '', '', 0, 0, 30, 1),
(291, 'dscf2025.jpg', NULL, NULL, 'dscf2025.jpg', 1207753986, NULL, '', '', 0, 0, 30, 1),
(292, 'dscf2028.jpg', NULL, NULL, 'dscf2028.jpg', 1207753987, NULL, '', '', 0, 0, 30, 1),
(293, 'dscf2034.jpg', NULL, NULL, 'dscf2034.jpg', 1207753989, NULL, '', '', 0, 0, 30, 1),
(294, 'dscf2037.jpg', NULL, NULL, 'dscf2037.jpg', 1207753990, NULL, '', '', 0, 0, 30, 1),
(295, 'dscf2095.jpg', NULL, NULL, 'dscf2095.jpg', 1207753992, NULL, '', '', 0, 0, 30, 1),
(296, 'dscf2096.jpg', NULL, NULL, 'dscf2096.jpg', 1207753994, NULL, '', '', 0, 0, 30, 1),
(297, 'dscf2101.jpg', NULL, NULL, 'dscf2101.jpg', 1207753997, NULL, '', '', 0, 0, 30, 1),
(298, 'dscf2104.jpg', NULL, NULL, 'dscf2104.jpg', 1207754000, NULL, '', '', 0, 0, 30, 1),
(299, 'dscf2123.jpg', NULL, NULL, 'dscf2123.jpg', 1207754002, NULL, '', '', 0, 0, 30, 1),
(300, 'dscf2132.jpg', NULL, NULL, 'dscf2132.jpg', 1207754005, NULL, '', '', 0, 0, 30, 1),
(301, 'dscf2140.jpg', NULL, NULL, 'dscf2140.jpg', 1207754007, NULL, '', '', 0, 0, 30, 1),
(302, 'dscf2141.jpg', NULL, NULL, 'dscf2141.jpg', 1207754010, NULL, '', '', 0, 0, 30, 1),
(303, 'dscf2142.jpg', NULL, NULL, 'dscf2142.jpg', 1207754012, NULL, '', '', 0, 0, 30, 1),
(304, 'dscf2146.jpg', NULL, NULL, 'dscf2146.jpg', 1207754015, NULL, '', '', 0, 0, 30, 1),
(305, 'dscf2156.jpg', NULL, NULL, 'dscf2156.jpg', 1207754016, NULL, '', '', 0, 0, 30, 1),
(306, 'dscf2163.jpg', NULL, NULL, 'dscf2163.jpg', 1207754018, NULL, '', '', 0, 0, 30, 1),
(307, 'dscf2175.jpg', NULL, NULL, 'dscf2175.jpg', 1207754019, NULL, '', '', 0, 0, 30, 1),
(308, 'dscf2179.jpg', NULL, NULL, 'dscf2179.jpg', 1207754022, NULL, '', '', 0, 0, 30, 1),
(309, 'dscf2180.jpg', NULL, NULL, 'dscf2180.jpg', 1207754024, NULL, '', '', 0, 0, 30, 1),
(310, 'dscf2185.jpg', NULL, NULL, 'dscf2185.jpg', 1207754027, NULL, '', '', 0, 0, 30, 1),
(311, 'dscf2187.jpg', NULL, NULL, 'dscf2187.jpg', 1207754030, NULL, '', '', 0, 0, 30, 1),
(312, 'dscf2191.jpg', NULL, NULL, 'dscf2191.jpg', 1207754032, NULL, '', '', 0, 0, 30, 1),
(313, 'dscf2192.jpg', NULL, NULL, 'dscf2192.jpg', 1207754033, NULL, '', '', 0, 0, 30, 1),
(314, 'dscf2195.jpg', NULL, NULL, 'dscf2195.jpg', 1207754035, NULL, '', '', 0, 0, 30, 1),
(315, 'dscf2205.jpg', NULL, NULL, 'dscf2205.jpg', 1207754037, NULL, '', '', 0, 0, 30, 1),
(316, 'dscf2207.jpg', NULL, NULL, 'dscf2207.jpg', 1207754038, NULL, '', '', 0, 0, 30, 1),
(317, 'dscf2208.jpg', NULL, NULL, 'dscf2208.jpg', 1207754040, NULL, '', '', 0, 0, 30, 1),
(318, 'dscf2209.jpg', NULL, NULL, 'dscf2209.jpg', 1207754041, NULL, '', '', 0, 0, 30, 1),
(319, 'dscf2212.jpg', NULL, NULL, 'dscf2212.jpg', 1207754044, NULL, '', '', 0, 0, 30, 1),
(320, 'dscf2213.jpg', NULL, NULL, 'dscf2213.jpg', 1207754046, NULL, '', '', 0, 0, 30, 1),
(321, 'dscf2216.jpg', NULL, NULL, 'dscf2216.jpg', 1207754048, NULL, '', '', 0, 0, 30, 1),
(322, 'dscf2217.jpg', NULL, NULL, 'dscf2217.jpg', 1207754049, NULL, '', '', 0, 0, 30, 1),
(323, 'dscf2223.jpg', NULL, NULL, 'dscf2223.jpg', 1207754051, NULL, '', '', 0, 0, 30, 1),
(324, 'dscf2224.jpg', NULL, NULL, 'dscf2224.jpg', 1207754052, NULL, '', '', 0, 0, 30, 1),
(325, 'dscf2231.jpg', NULL, NULL, 'dscf2231.jpg', 1207754054, NULL, '', '', 0, 0, 30, 1),
(326, 'dscf2236.jpg', NULL, NULL, 'dscf2236.jpg', 1207754056, NULL, '', '', 0, 0, 30, 1),
(327, 'dscf2238.jpg', NULL, NULL, 'dscf2238.jpg', 1207754057, NULL, '', '', 0, 0, 30, 1),
(328, 'dscf2239.jpg', NULL, NULL, 'dscf2239.jpg', 1207754059, NULL, '', '', 0, 0, 30, 1),
(329, 'dscf1956.jpg', NULL, NULL, 'dscf1956.jpg', 1207754060, NULL, '', '', 0, 0, 30, 1),
(330, 'dscf1970.jpg', NULL, NULL, 'dscf1970.jpg', 1207754062, NULL, '', '', 0, 0, 30, 1),
(331, 'dscf1980.jpg', NULL, NULL, 'dscf1980.jpg', 1207754064, NULL, '', '', 0, 0, 30, 1),
(332, 'dscf1985.jpg', NULL, NULL, 'dscf1985.jpg', 1207754065, NULL, '', '', 0, 0, 30, 1),
(333, 'dscf1995.jpg', NULL, NULL, 'dscf1995.jpg', 1207754067, NULL, '', '', 0, 0, 30, 1),
(334, 'dscf1998.jpg', NULL, NULL, 'dscf1998.jpg', 1207754070, NULL, '', '', 0, 0, 30, 1),
(335, 'dscf2138bw.jpg', NULL, NULL, 'dscf2138bw.jpg', 1207754072, NULL, '', '', 0, 0, 30, 1),
(336, 'panora.jpg', NULL, NULL, 'panora.jpg', 1207754190, NULL, '', '', 0, 0, 31, 1),
(337, 'dscf3172b.jpg', NULL, NULL, 'dscf3172b.jpg', 1207754192, NULL, '', '', 0, 0, 31, 1),
(338, 'dscf3041.jpg', NULL, NULL, 'dscf3041.jpg', 1207754195, NULL, '', '', 0, 0, 31, 1),
(339, 'dscf3047.jpg', NULL, NULL, 'dscf3047.jpg', 1207754196, NULL, '', '', 0, 0, 31, 1),
(340, 'dscf3050.jpg', NULL, NULL, 'dscf3050.jpg', 1207754198, NULL, '', '', 0, 0, 31, 1),
(341, 'dscf3052.jpg', NULL, NULL, 'dscf3052.jpg', 1207754200, NULL, '', '', 0, 0, 31, 1),
(342, 'dscf3055.jpg', NULL, NULL, 'dscf3055.jpg', 1207754201, NULL, '', '', 0, 0, 31, 1),
(343, 'dscf3057.jpg', NULL, NULL, 'dscf3057.jpg', 1207754203, NULL, '', '', 0, 0, 31, 1),
(344, 'dscf3125b.jpg', NULL, NULL, 'dscf3125b.jpg', 1207754206, NULL, '', '', 0, 0, 31, 1),
(345, 'dscf3063.jpg', NULL, NULL, 'dscf3063.jpg', 1207754208, NULL, '', '', 0, 0, 31, 1),
(346, 'dscf3065.jpg', NULL, NULL, 'dscf3065.jpg', 1207754209, NULL, '', '', 0, 0, 31, 1),
(347, 'dscf3073.jpg', NULL, NULL, 'dscf3073.jpg', 1207754212, NULL, '', '', 0, 0, 31, 1),
(348, 'dscf3082.jpg', NULL, NULL, 'dscf3082.jpg', 1207754215, NULL, '', '', 0, 0, 31, 1),
(349, 'dscf3085.jpg', NULL, NULL, 'dscf3085.jpg', 1207754217, NULL, '', '', 0, 0, 31, 1),
(350, 'dscf3090.jpg', NULL, NULL, 'dscf3090.jpg', 1207754218, NULL, '', '', 0, 0, 31, 1),
(351, 'dscf3091.jpg', NULL, NULL, 'dscf3091.jpg', 1207754220, NULL, '', '', 0, 0, 31, 1),
(352, 'dscf3099.jpg', NULL, NULL, 'dscf3099.jpg', 1207754221, NULL, '', '', 0, 0, 31, 1),
(353, 'dscf3103.jpg', NULL, NULL, 'dscf3103.jpg', 1207754223, NULL, '', '', 0, 0, 31, 1),
(354, 'dscf3104.jpg', NULL, NULL, 'dscf3104.jpg', 1207754225, NULL, '', '', 0, 0, 31, 1),
(355, 'dscf3108.jpg', NULL, NULL, 'dscf3108.jpg', 1207754228, NULL, '', '', 0, 0, 31, 1),
(356, 'dscf3122.jpg', NULL, NULL, 'dscf3122.jpg', 1207754229, NULL, '', '', 0, 0, 31, 1),
(357, 'dscf3150.jpg', NULL, NULL, 'dscf3150.jpg', 1207754231, NULL, '', '', 0, 0, 31, 1),
(358, 'dscf3151.jpg', NULL, NULL, 'dscf3151.jpg', 1207754232, NULL, '', '', 0, 0, 31, 1),
(359, 'dscf3157.jpg', NULL, NULL, 'dscf3157.jpg', 1207754235, NULL, '', '', 0, 0, 31, 1),
(360, 'dscf3160.jpg', NULL, NULL, 'dscf3160.jpg', 1207754237, NULL, '', '', 0, 0, 31, 1),
(361, 'dscf3165.jpg', NULL, NULL, 'dscf3165.jpg', 1207754238, NULL, '', '', 0, 0, 31, 1),
(362, 'dscf3087b.jpg', NULL, NULL, 'dscf3087b.jpg', 1207754240, NULL, '', '', 0, 0, 31, 1),
(363, 'dscf3088b.jpg', NULL, NULL, 'dscf3088b.jpg', 1207754242, NULL, '', '', 0, 0, 31, 1),
(364, 'dsc-4363.jpg', NULL, NULL, 'dsc-4363.jpg', 1207841728, NULL, '', '', 0, 0, 32, 1),
(365, 'dsc-4366.jpg', NULL, NULL, 'dsc-4366.jpg', 1207841730, NULL, '', '', 0, 0, 32, 1),
(366, 'dsc-4369.jpg', NULL, NULL, 'dsc-4369.jpg', 1207841731, NULL, '', '', 0, 0, 32, 1),
(367, 'dsc-4370.jpg', NULL, NULL, 'dsc-4370.jpg', 1207841733, NULL, '', '', 0, 0, 32, 1),
(368, 'dsc-4372.jpg', NULL, NULL, 'dsc-4372.jpg', 1207841734, NULL, '', '', 0, 0, 32, 1),
(369, 'dsc-4373.jpg', NULL, NULL, 'dsc-4373.jpg', 1207841736, NULL, '', '', 0, 0, 32, 1),
(370, 'dsc-4374.jpg', NULL, NULL, 'dsc-4374.jpg', 1207841738, NULL, '', '', 0, 0, 32, 1),
(371, 'dsc-4377.jpg', NULL, NULL, 'dsc-4377.jpg', 1207841739, NULL, '', '', 0, 0, 32, 1),
(372, 'dsc-4379.jpg', NULL, NULL, 'dsc-4379.jpg', 1207841741, NULL, '', '', 0, 0, 32, 1),
(373, 'dsc-4039.jpg', NULL, NULL, 'dsc-4039.jpg', 1207841742, NULL, '', '', 0, 0, 32, 1),
(374, 'dsc-4053.jpg', NULL, NULL, 'dsc-4053.jpg', 1207841744, NULL, '', '', 0, 0, 32, 1),
(375, 'dsc-4055.jpg', NULL, NULL, 'dsc-4055.jpg', 1207841745, NULL, '', '', 0, 0, 32, 1),
(376, 'dsc-4057.jpg', NULL, NULL, 'dsc-4057.jpg', 1207841747, NULL, '', '', 0, 0, 32, 1),
(377, 'dsc-4101.jpg', NULL, NULL, 'dsc-4101.jpg', 1207841749, NULL, '', '', 0, 0, 32, 1),
(378, 'dsc-4104.jpg', NULL, NULL, 'dsc-4104.jpg', 1207841751, NULL, '', '', 0, 0, 32, 1),
(379, 'dsc-4109.jpg', NULL, NULL, 'dsc-4109.jpg', 1207841752, NULL, '', '', 0, 0, 32, 1),
(380, 'dsc-4123.jpg', NULL, NULL, 'dsc-4123.jpg', 1207841754, NULL, '', '', 0, 0, 32, 1),
(381, 'dsc-4125.jpg', NULL, NULL, 'dsc-4125.jpg', 1207841755, NULL, '', '', 0, 0, 32, 1),
(382, 'dsc-4147.jpg', NULL, NULL, 'dsc-4147.jpg', 1207841757, NULL, '', '', 0, 0, 32, 1),
(383, 'dsc-4151.jpg', NULL, NULL, 'dsc-4151.jpg', 1207841760, NULL, '', '', 0, 0, 32, 1),
(384, 'dsc-4152.jpg', NULL, NULL, 'dsc-4152.jpg', 1207841762, NULL, '', '', 0, 0, 32, 1),
(385, 'dsc-4162.jpg', NULL, NULL, 'dsc-4162.jpg', 1207841764, NULL, '', '', 0, 0, 32, 1),
(386, 'dsc-4168.jpg', NULL, NULL, 'dsc-4168.jpg', 1207841765, NULL, '', '', 0, 0, 32, 1),
(387, 'dsc-4170.jpg', NULL, NULL, 'dsc-4170.jpg', 1207841767, NULL, '', '', 0, 0, 32, 1),
(388, 'dsc-5018.jpg', NULL, NULL, 'dsc-5018.jpg', 1207842649, NULL, '', '', 0, 0, 33, 1),
(389, 'dsc-5025.jpg', NULL, NULL, 'dsc-5025.jpg', 1207842653, NULL, '', '', 0, 0, 33, 1),
(390, 'dsc-5027.jpg', NULL, NULL, 'dsc-5027.jpg', 1207842654, NULL, '', '', 0, 0, 33, 1),
(391, 'dsc-5038.jpg', NULL, NULL, 'dsc-5038.jpg', 1207842656, NULL, '', '', 0, 0, 33, 1),
(392, 'dsc-5042.jpg', NULL, NULL, 'dsc-5042.jpg', 1207842660, NULL, '', '', 0, 0, 33, 1),
(393, 'dsc-5053.jpg', NULL, NULL, 'dsc-5053.jpg', 1207842661, NULL, '', '', 0, 0, 33, 1),
(394, 'dsc-5062.jpg', NULL, NULL, 'dsc-5062.jpg', 1207842663, NULL, '', '', 0, 0, 33, 1),
(395, 'dsc-5065.jpg', NULL, NULL, 'dsc-5065.jpg', 1207842664, NULL, '', '', 0, 0, 33, 1),
(396, 'dsc-50061.jpg', NULL, NULL, 'dsc-50061.jpg', 1207842666, NULL, '', '', 0, 0, 33, 1),
(464, 'dsc-5088.jpg', NULL, NULL, 'dsc-5088.jpg', 1208079517, NULL, '', '', 0, 0, 36, 1),
(398, 'dsc-4975.jpg', NULL, NULL, 'dsc-4975.jpg', 1207842669, NULL, '', '', 0, 0, 33, 1),
(399, 'dsc-4981.jpg', NULL, NULL, 'dsc-4981.jpg', 1207842670, NULL, '', '', 0, 0, 33, 1),
(400, 'dsc-4999.jpg', NULL, NULL, 'dsc-4999.jpg', 1207842672, NULL, '', '', 0, 0, 33, 1),
(401, 'dsc-3919.jpg', NULL, NULL, 'dsc-3919.jpg', 1207842981, NULL, '', '', 0, 0, 34, 1),
(402, 'dsc-3923.jpg', NULL, NULL, 'dsc-3923.jpg', 1207842983, NULL, '', '', 0, 0, 34, 1),
(403, 'dsc-3928.jpg', NULL, NULL, 'dsc-3928.jpg', 1207842984, NULL, '', '', 0, 0, 34, 1),
(404, 'dsc-3931.jpg', NULL, NULL, 'dsc-3931.jpg', 1207842986, NULL, '', '', 0, 0, 34, 1),
(405, 'dsc-3932.jpg', NULL, NULL, 'dsc-3932.jpg', 1207842988, NULL, '', '', 0, 0, 34, 1),
(406, 'dsc-3946.jpg', NULL, NULL, 'dsc-3946.jpg', 1207842989, NULL, '', '', 0, 0, 34, 1),
(407, 'dsc-3950.jpg', NULL, NULL, 'dsc-3950.jpg', 1207842991, NULL, '', '', 0, 0, 34, 1),
(408, 'dsc-3959.jpg', NULL, NULL, 'dsc-3959.jpg', 1207842992, NULL, '', '', 0, 0, 34, 1),
(409, 'dsc-3963.jpg', NULL, NULL, 'dsc-3963.jpg', 1207842994, NULL, '', '', 0, 0, 34, 1),
(410, 'dsc-3971.jpg', NULL, NULL, 'dsc-3971.jpg', 1207842995, NULL, '', '', 0, 0, 34, 1),
(411, 'dsc-3977.jpg', NULL, NULL, 'dsc-3977.jpg', 1207842997, NULL, '', '', 0, 0, 34, 1),
(412, 'dsc-3986.jpg', NULL, NULL, 'dsc-3986.jpg', 1207842998, NULL, '', '', 0, 0, 34, 1),
(413, 'dsc-3991.jpg', NULL, NULL, 'dsc-3991.jpg', 1207843000, NULL, '', '', 0, 0, 34, 1),
(414, 'dsc-3998.jpg', NULL, NULL, 'dsc-3998.jpg', 1207843002, NULL, '', '', 0, 0, 34, 1),
(415, 'dsc-3076.jpg', NULL, NULL, 'dsc-3076.jpg', 1207843740, NULL, '', '', 0, 0, 35, 1),
(416, 'dsc-3096.jpg', NULL, NULL, 'dsc-3096.jpg', 1207843741, NULL, '', '', 0, 0, 35, 1),
(417, 'dsc-3110.jpg', NULL, NULL, 'dsc-3110.jpg', 1207843743, NULL, '', '', 0, 0, 35, 1),
(418, 'dsc-2523.jpg', NULL, NULL, 'dsc-2523.jpg', 1207843744, NULL, '', '', 0, 0, 35, 1),
(419, 'dsc-3111.jpg', NULL, NULL, 'dsc-3111.jpg', 1207843746, NULL, '', '', 0, 0, 35, 1),
(420, 'dsc-4398.jpg', NULL, NULL, 'dsc-4398.jpg', 1207843747, NULL, '', '', 0, 0, 35, 1),
(421, 'dsc-4403.jpg', NULL, NULL, 'dsc-4403.jpg', 1207843749, NULL, '', '', 0, 0, 35, 1),
(422, 'dsc-4405.jpg', NULL, NULL, 'dsc-4405.jpg', 1207843750, NULL, '', '', 0, 0, 35, 1),
(423, 'dsc-4406.jpg', NULL, NULL, 'dsc-4406.jpg', 1207843752, NULL, '', '', 0, 0, 35, 1),
(424, 'dsc-3838.jpg', NULL, NULL, 'dsc-3838.jpg', 1207843754, NULL, '', '', 0, 0, 35, 1),
(425, 'dsc-3861.jpg', NULL, NULL, 'dsc-3861.jpg', 1207843755, NULL, '', '', 0, 0, 35, 1),
(426, 'dsc-4486.jpg', NULL, NULL, 'dsc-4486.jpg', 1207843759, NULL, '', '', 0, 0, 35, 1),
(427, 'dsc-3899.jpg', NULL, NULL, 'dsc-3899.jpg', 1207843760, NULL, '', '', 0, 0, 35, 1),
(428, 'dsc-3306.jpg', NULL, NULL, 'dsc-3306.jpg', 1207843762, NULL, '', '', 0, 0, 35, 1),
(429, 'dsc-3314.jpg', NULL, NULL, 'dsc-3314.jpg', 1207843763, NULL, '', '', 0, 0, 35, 1),
(430, 'dsc-3328.jpg', NULL, NULL, 'dsc-3328.jpg', 1207843765, NULL, '', '', 0, 0, 35, 1),
(431, 'dsc-3344.jpg', NULL, NULL, 'dsc-3344.jpg', 1207843766, NULL, '', '', 0, 0, 35, 1),
(432, 'dsc-4658.jpg', NULL, NULL, 'dsc-4658.jpg', 1207843768, NULL, '', '', 0, 0, 35, 1),
(433, 'dsc-3393.jpg', NULL, NULL, 'dsc-3393.jpg', 1207843770, NULL, '', '', 0, 0, 35, 1),
(434, 'dsc-4669.jpg', NULL, NULL, 'dsc-4669.jpg', 1207843771, NULL, '', '', 0, 0, 35, 1),
(435, 'dsc-2806.jpg', NULL, NULL, 'dsc-2806.jpg', 1207843773, NULL, '', '', 0, 0, 35, 1),
(436, 'dsc-3405.jpg', NULL, NULL, 'dsc-3405.jpg', 1207843774, NULL, '', '', 0, 0, 35, 1),
(437, 'dsc-4011.jpg', NULL, NULL, 'dsc-4011.jpg', 1207843778, NULL, '', '', 0, 0, 35, 1),
(438, 'dsc-2846.jpg', NULL, NULL, 'dsc-2846.jpg', 1207843779, NULL, '', '', 0, 0, 35, 1),
(439, 'dsc-4026.jpg', NULL, NULL, 'dsc-4026.jpg', 1207843781, NULL, '', '', 0, 0, 35, 1),
(440, 'dsc-3450.jpg', NULL, NULL, 'dsc-3450.jpg', 1207843782, NULL, '', '', 0, 0, 35, 1),
(441, 'dsc-2866.jpg', NULL, NULL, 'dsc-2866.jpg', 1207843784, NULL, '', '', 0, 0, 35, 1),
(442, 'dsc-3487.jpg', NULL, NULL, 'dsc-3487.jpg', 1207843786, NULL, '', '', 0, 0, 35, 1),
(443, 'dsc-3497.jpg', NULL, NULL, 'dsc-3497.jpg', 1207843787, NULL, '', '', 0, 0, 35, 1),
(444, 'dsc-3498.jpg', NULL, NULL, 'dsc-3498.jpg', 1207843789, NULL, '', '', 0, 0, 35, 1),
(445, 'dsc-2912.jpg', NULL, NULL, 'dsc-2912.jpg', 1207843790, NULL, '', '', 0, 0, 35, 1),
(446, 'dsc-3501.jpg', NULL, NULL, 'dsc-3501.jpg', 1207843792, NULL, '', '', 0, 0, 35, 1),
(447, 'dsc-3503.jpg', NULL, NULL, 'dsc-3503.jpg', 1207843794, NULL, '', '', 0, 0, 35, 1),
(448, 'dsc-3508.jpg', NULL, NULL, 'dsc-3508.jpg', 1207843795, NULL, '', '', 0, 0, 35, 1),
(449, 'dsc-3523.jpg', NULL, NULL, 'dsc-3523.jpg', 1207843797, NULL, '', '', 0, 0, 35, 1),
(450, 'dsc-3554.jpg', NULL, NULL, 'dsc-3554.jpg', 1207843798, NULL, '', '', 0, 0, 35, 1),
(451, 'dsc-3563.jpg', NULL, NULL, 'dsc-3563.jpg', 1207843800, NULL, '', '', 0, 0, 35, 1),
(452, 'dsc-2985.jpg', NULL, NULL, 'dsc-2985.jpg', 1207843801, NULL, '', '', 0, 0, 35, 1),
(453, 'dsc-3604.jpg', NULL, NULL, 'dsc-3604.jpg', 1207843805, NULL, '', '', 0, 0, 35, 1),
(454, 'dsc-3606.jpg', NULL, NULL, 'dsc-3606.jpg', 1207843808, NULL, '', '', 0, 0, 35, 1),
(455, 'dsc-3620.jpg', NULL, NULL, 'dsc-3620.jpg', 1207843811, NULL, '', '', 0, 0, 35, 1),
(456, 'dsc-3635.jpg', NULL, NULL, 'dsc-3635.jpg', 1207843814, NULL, '', '', 0, 0, 35, 1),
(463, 'dsc-5086.jpg', NULL, NULL, 'dsc-5086.jpg', 1208079515, NULL, '', '', 0, 0, 36, 1),
(458, 'dsc-3004.jpg', NULL, NULL, 'dsc-3004.jpg', 1207843819, NULL, '', '', 0, 0, 35, 1),
(459, 'dsc-3048.jpg', NULL, NULL, 'dsc-3048.jpg', 1207843821, NULL, '', '', 0, 0, 35, 1),
(460, 'dsc-3052.jpg', NULL, NULL, 'dsc-3052.jpg', 1207843824, NULL, '', '', 0, 0, 35, 1),
(461, 'dsc-3059.jpg', NULL, NULL, 'dsc-3059.jpg', 1207843826, NULL, '', '', 0, 0, 35, 1),
(462, 'dsc-3064.jpg', NULL, NULL, 'dsc-3064.jpg', 1207843827, NULL, '', '', 0, 0, 35, 1),
(465, 'dsc-5091.jpg', NULL, NULL, 'dsc-5091.jpg', 1208079520, NULL, '', '', 0, 0, 36, 1),
(466, 'dsc-5113.jpg', NULL, NULL, 'dsc-5113.jpg', 1208079522, NULL, '', '', 0, 0, 36, 1),
(467, 'dsc-5121.jpg', NULL, NULL, 'dsc-5121.jpg', 1208079524, NULL, '', '', 0, 0, 36, 1),
(468, 'dsc-5134.jpg', NULL, NULL, 'dsc-5134.jpg', 1208079525, NULL, '', '', 0, 0, 36, 1),
(469, 'dsc-5136.jpg', NULL, NULL, 'dsc-5136.jpg', 1208079527, NULL, '', '', 0, 0, 36, 1),
(470, 'dsc-5191.jpg', NULL, NULL, 'dsc-5191.jpg', 1208079531, NULL, '', '', 0, 0, 36, 1),
(471, 'dsc-5196.jpg', NULL, NULL, 'dsc-5196.jpg', 1208079533, NULL, '', '', 0, 0, 36, 1),
(472, 'dsc-5200.jpg', NULL, NULL, 'dsc-5200.jpg', 1208079534, NULL, '', '', 0, 0, 36, 1),
(473, 'dsc-5213.jpg', NULL, NULL, 'dsc-5213.jpg', 1208079536, NULL, '', '', 0, 0, 36, 1),
(474, '', NULL, NULL, 'untitled-1-copy.jpg', 1208080067, NULL, '', '', 0, 0, 36, 1),
(475, 'dsc-4351.jpg', NULL, NULL, 'dsc-4351.jpg', 1208080354, NULL, '', '', 0, 0, 37, 1),
(476, 'dsc-4353.jpg', NULL, NULL, 'dsc-4353.jpg', 1208080356, NULL, '', '', 0, 0, 37, 1),
(477, 'pano3-copy.jpg', NULL, NULL, 'pano3-copy.jpg', 1208080356, NULL, '', '', 0, 0, 37, 1),
(478, 'pano5.jpg', NULL, NULL, 'pano5.jpg', 1208080358, NULL, '', '', 0, 0, 37, 1),
(479, 'dsc-4205.jpg', NULL, NULL, 'dsc-4205.jpg', 1208080359, NULL, '', '', 0, 0, 37, 1),
(480, 'dsc-4207.jpg', NULL, NULL, 'dsc-4207.jpg', 1208080361, NULL, '', '', 0, 0, 37, 1),
(481, 'dsc-4249.jpg', NULL, NULL, 'dsc-4249.jpg', 1208080362, NULL, '', '', 0, 0, 37, 1),
(482, 'dsc-4253.jpg', NULL, NULL, 'dsc-4253.jpg', 1208080364, NULL, '', '', 0, 0, 37, 1),
(496, 'dsc-6474.jpg', NULL, NULL, 'dsc-6474.jpg', 1208352295, NULL, '', '', 0, 0, 38, 1),
(484, 'dsc-4269.jpg', NULL, NULL, 'dsc-4269.jpg', 1208080367, NULL, '', '', 0, 0, 37, 1),
(485, 'dsc-4272.jpg', NULL, NULL, 'dsc-4272.jpg', 1208080369, NULL, '', '', 0, 0, 37, 1),
(486, 'dsc-4296.jpg', NULL, NULL, 'dsc-4296.jpg', 1208080371, NULL, '', '', 0, 0, 37, 1),
(495, 'dsc-6471.jpg', NULL, NULL, 'dsc-6471.jpg', 1208352294, NULL, '', '', 0, 0, 38, 1),
(488, 'dsc-4303.jpg', NULL, NULL, 'dsc-4303.jpg', 1208080373, NULL, '', '', 0, 0, 37, 1),
(489, 'dsc-4328.jpg', NULL, NULL, 'dsc-4328.jpg', 1208080375, NULL, '', '', 0, 0, 37, 1),
(490, 'dsc-4331.jpg', NULL, NULL, 'dsc-4331.jpg', 1208080377, NULL, '', '', 0, 0, 37, 1),
(491, 'dsc-4337.jpg', NULL, NULL, 'dsc-4337.jpg', 1208080378, NULL, '', '', 0, 0, 37, 1),
(492, 'dsc-4342.jpg', NULL, NULL, 'dsc-4342.jpg', 1208080380, NULL, '', '', 0, 0, 37, 1),
(493, 'dsc-4343.jpg', NULL, NULL, 'dsc-4343.jpg', 1208080382, NULL, '', '', 0, 0, 37, 1),
(494, 'dsc-4346.jpg', NULL, NULL, 'dsc-4346.jpg', 1208080384, NULL, '', '', 0, 0, 37, 1),
(497, 'dsc-6479.jpg', NULL, NULL, 'dsc-6479.jpg', 1208352299, NULL, '', '', 0, 0, 38, 1),
(498, 'dsc-6482.jpg', NULL, NULL, 'dsc-6482.jpg', 1208352302, NULL, '', '', 0, 0, 38, 1),
(499, 'dsc-6483.jpg', NULL, NULL, 'dsc-6483.jpg', 1208352305, NULL, '', '', 0, 0, 38, 1),
(500, 'dsc-6494.jpg', NULL, NULL, 'dsc-6494.jpg', 1208352308, NULL, '', '', 0, 0, 38, 1),
(501, 'dsc-6497.jpg', NULL, NULL, 'dsc-6497.jpg', 1208352311, NULL, '', '', 0, 0, 38, 1),
(502, 'dsc-6503.jpg', NULL, NULL, 'dsc-6503.jpg', 1208352317, NULL, '', '', 0, 0, 38, 1),
(503, 'dsc-6507.jpg', NULL, NULL, 'dsc-6507.jpg', 1208352320, NULL, '', '', 0, 0, 38, 1),
(504, 'dsc-6511.jpg', NULL, NULL, 'dsc-6511.jpg', 1208352323, NULL, '', '', 0, 0, 38, 1),
(505, 'dsc-6517.jpg', NULL, NULL, 'dsc-6517.jpg', 1208352326, NULL, '', '', 0, 0, 38, 1),
(506, 'dsc-6518.jpg', NULL, NULL, 'dsc-6518.jpg', 1208352330, NULL, '', '', 0, 0, 38, 1),
(507, 'dsc-6523.jpg', NULL, NULL, 'dsc-6523.jpg', 1208352337, NULL, '', '', 0, 0, 38, 1),
(508, 'dsc-6527.jpg', NULL, NULL, 'dsc-6527.jpg', 1208352339, NULL, '', '', 0, 0, 38, 1),
(509, 'dsc-6540.jpg', NULL, NULL, 'dsc-6540.jpg', 1208352340, NULL, '', '', 0, 0, 38, 1),
(510, 'dsc-6547.jpg', NULL, NULL, 'dsc-6547.jpg', 1208352344, NULL, '', '', 0, 0, 38, 1),
(511, 'dsc-6548.jpg', NULL, NULL, 'dsc-6548.jpg', 1208352345, NULL, '', '', 0, 0, 38, 1),
(512, 'dsc-6551.jpg', NULL, NULL, 'dsc-6551.jpg', 1208352347, NULL, '', '', 0, 0, 38, 1),
(513, 'dsc-6554.jpg', NULL, NULL, 'dsc-6554.jpg', 1208352350, NULL, '', '', 0, 0, 38, 1),
(535, 'dsc-6675.jpg', NULL, NULL, 'dsc-6675.jpg', 1208813207, NULL, '', '', 0, 0, 39, 1),
(515, 'dsc-6559.jpg', NULL, NULL, 'dsc-6559.jpg', 1208352355, NULL, '', '', 0, 0, 38, 1),
(516, 'dsc-6564.jpg', NULL, NULL, 'dsc-6564.jpg', 1208352357, NULL, '', '', 0, 0, 38, 1),
(517, 'dsc-6568.jpg', NULL, NULL, 'dsc-6568.jpg', 1208352358, NULL, '', '', 0, 0, 38, 1),
(518, 'dsc-6572.jpg', NULL, NULL, 'dsc-6572.jpg', 1208352360, NULL, '', '', 0, 0, 38, 1),
(519, 'dsc-6584.jpg', NULL, NULL, 'dsc-6584.jpg', 1208352361, NULL, '', '', 0, 0, 38, 1),
(520, 'dsc-6590.jpg', NULL, NULL, 'dsc-6590.jpg', 1208352365, NULL, '', '', 0, 0, 38, 1),
(521, 'dsc-6600.jpg', NULL, NULL, 'dsc-6600.jpg', 1208352367, NULL, '', '', 0, 0, 38, 1),
(522, 'dsc-6604.jpg', NULL, NULL, 'dsc-6604.jpg', 1208352369, NULL, '', '', 0, 0, 38, 1),
(534, 'dsc-6671.jpg', NULL, NULL, 'dsc-6671.jpg', 1208813205, NULL, '', '', 0, 0, 39, 1),
(524, 'dsc-6611.jpg', NULL, NULL, 'dsc-6611.jpg', 1208352377, NULL, '', '', 0, 0, 38, 1),
(525, 'dsc-6619.jpg', NULL, NULL, 'dsc-6619.jpg', 1208352379, NULL, '', '', 0, 0, 38, 1),
(526, 'dsc-6628.jpg', NULL, NULL, 'dsc-6628.jpg', 1208352380, NULL, '', '', 0, 0, 38, 1),
(527, 'dsc-6629.jpg', NULL, NULL, 'dsc-6629.jpg', 1208352382, NULL, '', '', 0, 0, 38, 1),
(528, 'dsc-6633.jpg', NULL, NULL, 'dsc-6633.jpg', 1208352383, NULL, '', '', 0, 0, 38, 1),
(529, 'dsc-6644.jpg', NULL, NULL, 'dsc-6644.jpg', 1208352387, NULL, '', '', 0, 0, 38, 1),
(530, 'dsc-6648.jpg', NULL, NULL, 'dsc-6648.jpg', 1208352388, NULL, '', '', 0, 0, 38, 1),
(531, 'dsc-6656.jpg', NULL, NULL, 'dsc-6656.jpg', 1208352391, NULL, '', '', 0, 0, 38, 1),
(532, 'dsc-6657.jpg', NULL, NULL, 'dsc-6657.jpg', 1208352393, NULL, '', '', 0, 0, 38, 1),
(536, 'dsc-6678.jpg', NULL, NULL, 'dsc-6678.jpg', 1208813210, NULL, '', '', 0, 0, 39, 1),
(537, 'dsc-6679.jpg', NULL, NULL, 'dsc-6679.jpg', 1208813213, NULL, '', '', 0, 0, 39, 1),
(538, 'dsc-6683.jpg', NULL, NULL, 'dsc-6683.jpg', 1208813217, NULL, '', '', 0, 0, 39, 1),
(539, 'dsc-6686.jpg', NULL, NULL, 'dsc-6686.jpg', 1208813218, NULL, '', '', 0, 0, 39, 1),
(540, 'dsc-6689.jpg', NULL, NULL, 'dsc-6689.jpg', 1208813220, NULL, '', '', 0, 0, 39, 1),
(541, 'dsc-6692.jpg', NULL, NULL, 'dsc-6692.jpg', 1208813221, NULL, '', '', 0, 0, 39, 1),
(542, 'dsc-6694.jpg', NULL, NULL, 'dsc-6694.jpg', 1208813223, NULL, '', '', 0, 0, 39, 1),
(543, 'dsc-6695.jpg', NULL, NULL, 'dsc-6695.jpg', 1208813225, NULL, '', '', 0, 0, 39, 1),
(544, 'dsc-6702.jpg', NULL, NULL, 'dsc-6702.jpg', 1208813226, NULL, '', '', 0, 0, 39, 1),
(545, 'dsc-6706.jpg', NULL, NULL, 'dsc-6706.jpg', 1208813228, NULL, '', '', 0, 0, 39, 1),
(546, 'dsc-6709.jpg', NULL, NULL, 'dsc-6709.jpg', 1208813229, NULL, '', '', 0, 0, 39, 1),
(547, 'dsc-6716.jpg', NULL, NULL, 'dsc-6716.jpg', 1208813231, NULL, '', '', 0, 0, 39, 1),
(548, 'dsc-6723.jpg', NULL, NULL, 'dsc-6723.jpg', 1208813232, NULL, '', '', 0, 0, 39, 1),
(549, 'dsc-6725.jpg', NULL, NULL, 'dsc-6725.jpg', 1208813234, NULL, '', '', 0, 0, 39, 1),
(550, 'dsc-6732.jpg', NULL, NULL, 'dsc-6732.jpg', 1208813235, NULL, '', '', 0, 0, 39, 1),
(551, 'dsc-6739.jpg', NULL, NULL, 'dsc-6739.jpg', 1208813237, NULL, '', '', 0, 0, 39, 1),
(552, 'dsc-6744.jpg', NULL, NULL, 'dsc-6744.jpg', 1208813239, NULL, '', '', 0, 0, 39, 1),
(553, 'dsc-6748.jpg', NULL, NULL, 'dsc-6748.jpg', 1208813240, NULL, '', '', 0, 0, 39, 1),
(554, 'dsc-6751.jpg', NULL, NULL, 'dsc-6751.jpg', 1208813242, NULL, '', '', 0, 0, 39, 1),
(555, 'dsc-6754.jpg', NULL, NULL, 'dsc-6754.jpg', 1208813243, NULL, '', '', 0, 0, 39, 1),
(556, 'dsc-6775.jpg', NULL, NULL, 'dsc-6775.jpg', 1209317936, NULL, '', '', 0, 0, 40, 1),
(557, 'dsc-6777.jpg', NULL, NULL, 'dsc-6777.jpg', 1209317940, NULL, '', '', 0, 0, 40, 1),
(558, 'dsc-6779.jpg', NULL, NULL, 'dsc-6779.jpg', 1209317941, NULL, '', '', 0, 0, 40, 1),
(559, 'dsc-6781.jpg', NULL, NULL, 'dsc-6781.jpg', 1209317943, NULL, '', '', 0, 0, 40, 1),
(560, 'dsc-6784.jpg', NULL, NULL, 'dsc-6784.jpg', 1209317944, NULL, '', '', 0, 0, 40, 1),
(561, 'dsc-6785.jpg', NULL, NULL, 'dsc-6785.jpg', 1209317946, NULL, '', '', 0, 0, 40, 1),
(562, 'dsc-6792.jpg', NULL, NULL, 'dsc-6792.jpg', 1209317948, NULL, '', '', 0, 0, 40, 1),
(563, 'dsc-6793.jpg', NULL, NULL, 'dsc-6793.jpg', 1209317949, NULL, '', '', 0, 0, 40, 1),
(564, 'dsc-6796.jpg', NULL, NULL, 'dsc-6796.jpg', 1209317951, NULL, '', '', 0, 0, 40, 1),
(565, 'dsc-6797.jpg', NULL, NULL, 'dsc-6797.jpg', 1209317952, NULL, '', '', 0, 0, 40, 1),
(566, 'dsc-6798.jpg', NULL, NULL, 'dsc-6798.jpg', 1209317954, NULL, '', '', 0, 0, 40, 1),
(567, 'dsc-6799.jpg', NULL, NULL, 'dsc-6799.jpg', 1209317956, NULL, '', '', 0, 0, 40, 1),
(568, 'dsc-6800.jpg', NULL, NULL, 'dsc-6800.jpg', 1209317957, NULL, '', '', 0, 0, 40, 1),
(569, 'dsc-6801.jpg', NULL, NULL, 'dsc-6801.jpg', 1209317961, NULL, '', '', 0, 0, 40, 1),
(570, 'dsc-6807.jpg', NULL, NULL, 'dsc-6807.jpg', 1209317962, NULL, '', '', 0, 0, 40, 1),
(571, 'dsc-6808.jpg', NULL, NULL, 'dsc-6808.jpg', 1209317964, NULL, '', '', 0, 0, 40, 1),
(572, 'dsc-6809.jpg', NULL, NULL, 'dsc-6809.jpg', 1209317965, NULL, '', '', 0, 0, 40, 1),
(573, 'dsc-6811.jpg', NULL, NULL, 'dsc-6811.jpg', 1209317967, NULL, '', '', 0, 0, 40, 1),
(574, 'dsc-6814.jpg', NULL, NULL, 'dsc-6814.jpg', 1209317969, NULL, '', '', 0, 0, 40, 1),
(575, 'dsc-6817.jpg', NULL, NULL, 'dsc-6817.jpg', 1209317972, NULL, '', '', 0, 0, 40, 1),
(576, 'dsc-6819.jpg', NULL, NULL, 'dsc-6819.jpg', 1209739539, 'Kubina zaměstnáme vývojem algoritmu na výpočet poměru přísad a nakrájíme kořenovou zeleninu (mrkev, petržel, celer v poměru 8:5:5) a cibuli.', '', '', 0, 0, 41, 1),
(577, 'dsc-6820.jpg', NULL, NULL, 'dsc-6820.jpg', 1209739541, 'Svíčkovou nasolíme a opepříme…', '', '', 0, 0, 41, 1),
(594, 'dsc-6911.jpg', NULL, NULL, 'dsc-6911.jpg', 1209973835, NULL, '', '', 0, 0, 42, 1),
(579, 'dsc-6826.jpg', NULL, NULL, 'dsc-6826.jpg', 1209739544, '…a zabalíme do plátků anglické slaniny.', '', '', 0, 0, 41, 1),
(580, 'dsc-6827.jpg', NULL, NULL, 'dsc-6827.jpg', 1209739546, 'Na másle zpěníme něco cibulky…', '', '', 0, 0, 41, 1),
(581, 'dsc-6830.jpg', NULL, NULL, 'dsc-6830.jpg', 1209739548, 'Samozřejmě nesmíme zapomenout, že na vaření je nejdůležitější podlévání…', '', '', 0, 0, 41, 1),
(582, 'dsc-6831.jpg', NULL, NULL, 'dsc-6831.jpg', 1209739549, 'K cibulce přidáme nakrájenou zeleninu a krátce osmahneme.', '', '', 0, 0, 41, 1),
(593, 'dsc-6900.jpg', NULL, NULL, 'dsc-6900.jpg', 1209973833, '', '', '', 0, 0, 42, 1),
(584, 'dsc-6838.jpg', NULL, NULL, 'dsc-6838.jpg', 1209739554, 'Celou směs podlijeme připraveným bujónem, vložíme svíčkovou, pár zrnek celého pepře, dva ks vavřínu (bobkový list), 2 zrnka nového koření, přikryjeme pokličkou…', '', '', 0, 0, 41, 1),
(585, 'dsc-6839.jpg', NULL, NULL, 'dsc-6839.jpg', 1209739556, '… a vložíme do rozehřáté trouby.', '', '', 0, 0, 41, 1),
(586, 'dsc-6842.jpg', NULL, NULL, 'dsc-6842.jpg', 1209739558, 'Pak jdeme na zahradu, pijeme kávu, kouříme…', '', '', 0, 0, 41, 1),
(587, 'dsc-6843.jpg', NULL, NULL, 'dsc-6843.jpg', 1209739559, '…a fotíme pampelišky :-)', '', '', 0, 0, 41, 1),
(588, 'dsc-6845.jpg', NULL, NULL, 'dsc-6845.jpg', 1209739561, 'Po zhruba 1,5 hodině vyjmeme upečenou svíčkovou z hrnce, přemístíme ji do pekáče a dáme prohřát do již vypnuté trouby.', '', '', 0, 0, 41, 1),
(589, 'dsc-6850.jpg', NULL, NULL, 'dsc-6850.jpg', 1209739563, 'Upečenou zeleninu s bujónem propasírujeme přes síto…', '', '', 0, 0, 41, 1),
(590, 'dsc-6851.jpg', NULL, NULL, 'dsc-6851.jpg', 1209739564, 'zahustíme zakysanou smetanou (200ml, nebo podle chuti) a prohřejeme. Ohřejeme houskový knedlík, svíčkovou nakrájíme na plátky, přelejem teplou omáčkou…', '', '', 0, 0, 41, 1),
(591, 'dsc-6853.jpg', NULL, NULL, 'dsc-6853.jpg', 1209739566, 'a podáváme.', '', '', 0, 0, 41, 1),
(592, 'dsc-6854.jpg', NULL, NULL, 'dsc-6854.jpg', 1209739569, 'A samozřejmě vychutnáváme.', '', '', 0, 0, 41, 1),
(595, 'dsc-6914.jpg', NULL, NULL, 'dsc-6914.jpg', 1209973837, NULL, '', '', 0, 0, 42, 1),
(596, 'dsc-6915.jpg', NULL, NULL, 'dsc-6915.jpg', 1209973838, NULL, '', '', 0, 0, 42, 1),
(597, 'dsc-6917.jpg', NULL, NULL, 'dsc-6917.jpg', 1209973840, NULL, '', '', 0, 0, 42, 1),
(598, 'dsc-6919.jpg', NULL, NULL, 'dsc-6919.jpg', 1209973842, NULL, '', '', 0, 0, 42, 1),
(599, 'dsc-6925.jpg', NULL, NULL, 'dsc-6925.jpg', 1209973843, NULL, '', '', 0, 0, 42, 1),
(600, 'dsc-6936.jpg', NULL, NULL, 'dsc-6936.jpg', 1209973845, NULL, '', '', 0, 0, 42, 1),
(601, 'dsc-6938.jpg', NULL, NULL, 'dsc-6938.jpg', 1209973846, NULL, '', '', 0, 0, 42, 1),
(602, 'dsc-6942.jpg', NULL, NULL, 'dsc-6942.jpg', 1209973848, NULL, '', '', 0, 0, 42, 1),
(603, 'dsc-6947.jpg', NULL, NULL, 'dsc-6947.jpg', 1209973850, NULL, '', '', 0, 0, 42, 1),
(604, 'dsc-6953.jpg', NULL, NULL, 'dsc-6953.jpg', 1209973851, NULL, '', '', 0, 0, 42, 1),
(605, 'dsc-6956.jpg', NULL, NULL, 'dsc-6956.jpg', 1209973853, NULL, '', '', 0, 0, 42, 1),
(606, 'dsc-6963.jpg', NULL, NULL, 'dsc-6963.jpg', 1209973854, NULL, '', '', 0, 0, 42, 1),
(607, 'dsc-6964.jpg', NULL, NULL, 'dsc-6964.jpg', 1209973856, NULL, '', '', 0, 0, 42, 1),
(608, 'dsc-6972.jpg', NULL, NULL, 'dsc-6972.jpg', 1209973858, NULL, '', '', 0, 0, 42, 1),
(609, 'dsc-6974.jpg', NULL, NULL, 'dsc-6974.jpg', 1209973859, NULL, '', '', 0, 0, 42, 1),
(610, 'dsc-6980.jpg', NULL, NULL, 'dsc-6980.jpg', 1209973861, NULL, '', '', 0, 0, 42, 1),
(611, 'dsc-6862.jpg', NULL, NULL, 'dsc-6862.jpg', 1209973864, NULL, '', '', 0, 0, 42, 1),
(612, 'dsc-6866.jpg', NULL, NULL, 'dsc-6866.jpg', 1209973866, NULL, '', '', 0, 0, 42, 1),
(613, 'dsc-6874.jpg', NULL, NULL, 'dsc-6874.jpg', 1209973867, NULL, '', '', 0, 0, 42, 1),
(614, 'dsc-6877.jpg', NULL, NULL, 'dsc-6877.jpg', 1209973871, NULL, '', '', 0, 0, 42, 1),
(615, 'dsc-6879.jpg', NULL, NULL, 'dsc-6879.jpg', 1209973873, NULL, '', '', 0, 0, 42, 1),
(616, 'dsc-6881.jpg', NULL, NULL, 'dsc-6881.jpg', 1209973874, NULL, '', '', 0, 0, 42, 1),
(617, 'dsc-6889.jpg', NULL, NULL, 'dsc-6889.jpg', 1209973876, NULL, '', '', 0, 0, 42, 1),
(618, 'dsc-6891.jpg', NULL, NULL, 'dsc-6891.jpg', 1209973877, NULL, '', '', 0, 0, 42, 1),
(619, 'dsc-6898.jpg', NULL, NULL, 'dsc-6898.jpg', 1209973879, NULL, '', '', 0, 0, 42, 1),
(620, 'dscf2892.jpg', NULL, NULL, 'dscf2892.jpg', 1209987917, NULL, '', '', 0, 0, 43, 1),
(621, 'dscf2897.jpg', NULL, NULL, 'dscf2897.jpg', 1209987920, NULL, '', '', 0, 0, 43, 1),
(622, 'dscf2900.jpg', NULL, NULL, 'dscf2900.jpg', 1209987923, NULL, '', '', 0, 0, 43, 1),
(623, 'dscf2903.jpg', NULL, NULL, 'dscf2903.jpg', 1209987925, NULL, '', '', 0, 0, 43, 1),
(624, 'dscf2928.jpg', NULL, NULL, 'dscf2928.jpg', 1209987926, NULL, '', '', 0, 0, 43, 1),
(625, 'dscf2931.jpg', NULL, NULL, 'dscf2931.jpg', 1209987928, NULL, '', '', 0, 0, 43, 1),
(626, 'dscf2932.jpg', NULL, NULL, 'dscf2932.jpg', 1209987932, NULL, '', '', 0, 0, 43, 1),
(627, 'dscf2933.jpg', NULL, NULL, 'dscf2933.jpg', 1209987934, NULL, '', '', 0, 0, 43, 1),
(628, 'dscf2938.jpg', NULL, NULL, 'dscf2938.jpg', 1209987937, NULL, '', '', 0, 0, 43, 1),
(629, 'dscf2939.jpg', NULL, NULL, 'dscf2939.jpg', 1209987941, NULL, '', '', 0, 0, 43, 1),
(630, 'dscf2943.jpg', NULL, NULL, 'dscf2943.jpg', 1209987943, NULL, '', '', 0, 0, 43, 1),
(631, 'dscf2947.jpg', NULL, NULL, 'dscf2947.jpg', 1209987946, NULL, '', '', 0, 0, 43, 1),
(632, 'dscf2948.jpg', NULL, NULL, 'dscf2948.jpg', 1209987948, NULL, '', '', 0, 0, 43, 1),
(633, 'dscf2949.jpg', NULL, NULL, 'dscf2949.jpg', 1209987950, NULL, '', '', 0, 0, 43, 1),
(634, 'dscf2952.jpg', NULL, NULL, 'dscf2952.jpg', 1209987951, NULL, '', '', 0, 0, 43, 1),
(635, 'dscf2953.jpg', NULL, NULL, 'dscf2953.jpg', 1209987955, NULL, '', '', 0, 0, 43, 1),
(636, 'dscf2955.jpg', NULL, NULL, 'dscf2955.jpg', 1209987956, NULL, '', '', 0, 0, 43, 1),
(637, 'dscf2960.jpg', NULL, NULL, 'dscf2960.jpg', 1209987958, NULL, '', '', 0, 0, 43, 1),
(638, 'dscf2961.jpg', NULL, NULL, 'dscf2961.jpg', 1209987959, NULL, '', '', 0, 0, 43, 1),
(639, 'dscf2963.jpg', NULL, NULL, 'dscf2963.jpg', 1209987961, NULL, '', '', 0, 0, 43, 1),
(640, 'dscf2965.jpg', NULL, NULL, 'dscf2965.jpg', 1209987963, NULL, '', '', 0, 0, 43, 1),
(641, 'dscf2970.jpg', NULL, NULL, 'dscf2970.jpg', 1209987964, NULL, '', '', 0, 0, 43, 1),
(642, 'dscf2973.jpg', NULL, NULL, 'dscf2973.jpg', 1209987966, NULL, '', '', 0, 0, 43, 1),
(643, 'dscf2974.jpg', NULL, NULL, 'dscf2974.jpg', 1209987968, NULL, '', '', 0, 0, 43, 1),
(644, 'dscf2975.jpg', NULL, NULL, 'dscf2975.jpg', 1209987969, NULL, '', '', 0, 0, 43, 1),
(645, 'dscf2976.jpg', NULL, NULL, 'dscf2976.jpg', 1209987971, NULL, '', '', 0, 0, 43, 1),
(646, 'dscf2979.jpg', NULL, NULL, 'dscf2979.jpg', 1209987972, NULL, '', '', 0, 0, 43, 1),
(647, 'dscf2980.jpg', NULL, NULL, 'dscf2980.jpg', 1209987974, NULL, '', '', 0, 0, 43, 1),
(648, 'dscf2982.jpg', NULL, NULL, 'dscf2982.jpg', 1209987976, NULL, '', '', 0, 0, 43, 1),
(649, 'dscf2987.jpg', NULL, NULL, 'dscf2987.jpg', 1209987977, NULL, '', '', 0, 0, 43, 1),
(650, 'dscf2998.jpg', NULL, NULL, 'dscf2998.jpg', 1209987979, NULL, '', '', 0, 0, 43, 1),
(651, 'dscf2999.jpg', NULL, NULL, 'dscf2999.jpg', 1209987981, NULL, '', '', 0, 0, 43, 1),
(652, 'panorama.jpg', NULL, NULL, 'panorama.jpg', 1209987981, NULL, '', '', 0, 0, 43, 1),
(653, 'dscf29781.jpg', NULL, NULL, 'dscf29781.jpg', 1209987984, NULL, '', '', 0, 0, 43, 1),
(654, 'dscf3000.jpg', NULL, NULL, 'dscf3000.jpg', 1209987986, NULL, '', '', 0, 0, 43, 1),
(655, 'dscf3001.jpg', NULL, NULL, 'dscf3001.jpg', 1209987988, NULL, '', '', 0, 0, 43, 1),
(656, 'dscf3009.jpg', NULL, NULL, 'dscf3009.jpg', 1209987989, NULL, '', '', 0, 0, 43, 1),
(657, 'dscf3010.jpg', NULL, NULL, 'dscf3010.jpg', 1209987991, NULL, '', '', 0, 0, 43, 1),
(658, 'dscf3011.jpg', NULL, NULL, 'dscf3011.jpg', 1209987993, NULL, '', '', 0, 0, 43, 1),
(659, 'dscf3014.jpg', NULL, NULL, 'dscf3014.jpg', 1209987994, NULL, '', '', 0, 0, 43, 1),
(660, 'dscf3016.jpg', NULL, NULL, 'dscf3016.jpg', 1209987996, NULL, '', '', 0, 0, 43, 1),
(661, 'dscf3023.jpg', NULL, NULL, 'dscf3023.jpg', 1209987999, NULL, '', '', 0, 0, 43, 1),
(662, 'dscf3026.jpg', NULL, NULL, 'dscf3026.jpg', 1209988000, NULL, '', '', 0, 0, 43, 1),
(663, 'pan2-copy.jpg', NULL, NULL, 'pan2-copy.jpg', 1209988002, NULL, '', '', 0, 0, 43, 1),
(664, 'pan1.jpg', NULL, NULL, 'pan1.jpg', 1209988003, NULL, '', '', 0, 0, 43, 1),
(665, 'dsc-5224.jpg', NULL, NULL, 'dsc-5224.jpg', 1209988742, NULL, '', '', 0, 0, 44, 1),
(666, 'dsc-5227.jpg', NULL, NULL, 'dsc-5227.jpg', 1209988743, NULL, '', '', 0, 0, 44, 1),
(667, 'dsc-5230.jpg', NULL, NULL, 'dsc-5230.jpg', 1209988745, NULL, '', '', 0, 0, 44, 1),
(668, 'dsc-5234.jpg', NULL, NULL, 'dsc-5234.jpg', 1209988746, NULL, '', '', 0, 0, 44, 1),
(669, 'dsc-5238.jpg', NULL, NULL, 'dsc-5238.jpg', 1209988748, NULL, '', '', 0, 0, 44, 1),
(670, 'dsc-5242.jpg', NULL, NULL, 'dsc-5242.jpg', 1209988750, NULL, '', '', 0, 0, 44, 1),
(671, 'dsc-5246.jpg', NULL, NULL, 'dsc-5246.jpg', 1209988751, NULL, '', '', 0, 0, 44, 1),
(672, 'dsc-5247.jpg', NULL, NULL, 'dsc-5247.jpg', 1209988753, NULL, '', '', 0, 0, 44, 1),
(673, 'dsc-5248.jpg', NULL, NULL, 'dsc-5248.jpg', 1209988756, NULL, '', '', 0, 0, 44, 1),
(674, 'dsc-5250.jpg', NULL, NULL, 'dsc-5250.jpg', 1209988758, NULL, '', '', 0, 0, 44, 1),
(675, 'dsc-5253.jpg', NULL, NULL, 'dsc-5253.jpg', 1209988759, NULL, '', '', 0, 0, 44, 1),
(676, 'dsc-5266.jpg', NULL, NULL, 'dsc-5266.jpg', 1209988761, NULL, '', '', 0, 0, 44, 1),
(677, 'dsc-5268.jpg', NULL, NULL, 'dsc-5268.jpg', 1209988762, NULL, '', '', 0, 0, 44, 1),
(678, 'dsc-5277.jpg', NULL, NULL, 'dsc-5277.jpg', 1209988766, NULL, '', '', 0, 0, 44, 1),
(679, 'dsc-5281.jpg', NULL, NULL, 'dsc-5281.jpg', 1209988767, NULL, '', '', 0, 0, 44, 1),
(680, 'dsc-5303.jpg', NULL, NULL, 'dsc-5303.jpg', 1209988769, NULL, '', '', 0, 0, 44, 1),
(681, 'dsc-5311.jpg', NULL, NULL, 'dsc-5311.jpg', 1209988771, NULL, '', '', 0, 0, 44, 1),
(682, 'dsc-5329.jpg', NULL, NULL, 'dsc-5329.jpg', 1209988774, NULL, '', '', 0, 0, 44, 1),
(683, 'dscf2786.jpg', NULL, NULL, 'dscf2786.jpg', 1209989364, NULL, '', '', 0, 0, 45, 1),
(684, 'dscf2789.jpg', NULL, NULL, 'dscf2789.jpg', 1209989365, NULL, '', '', 0, 0, 45, 1),
(685, 'dscf2792.jpg', NULL, NULL, 'dscf2792.jpg', 1209989367, NULL, '', '', 0, 0, 45, 1),
(686, 'dscf2793.jpg', NULL, NULL, 'dscf2793.jpg', 1209989370, NULL, '', '', 0, 0, 45, 1),
(687, 'dscf2801.jpg', NULL, NULL, 'dscf2801.jpg', 1209989372, NULL, '', '', 0, 0, 45, 1),
(688, 'dscf2806.jpg', NULL, NULL, 'dscf2806.jpg', 1209989373, NULL, '', '', 0, 0, 45, 1),
(689, 'dscf2809.jpg', NULL, NULL, 'dscf2809.jpg', 1209989375, NULL, '', '', 0, 0, 45, 1),
(690, 'dscf2814.jpg', NULL, NULL, 'dscf2814.jpg', 1209989377, NULL, '', '', 0, 0, 45, 1),
(691, 'dscf2815.jpg', NULL, NULL, 'dscf2815.jpg', 1209989378, NULL, '', '', 0, 0, 45, 1),
(692, 'dscf2818.jpg', NULL, NULL, 'dscf2818.jpg', 1209989380, NULL, '', '', 0, 0, 45, 1),
(693, 'dscf2820.jpg', NULL, NULL, 'dscf2820.jpg', 1209989381, NULL, '', '', 0, 0, 45, 1),
(694, 'dscf2821.jpg', NULL, NULL, 'dscf2821.jpg', 1209989383, NULL, '', '', 0, 0, 45, 1),
(695, 'dscf2824.jpg', NULL, NULL, 'dscf2824.jpg', 1209989385, NULL, '', '', 0, 0, 45, 1),
(696, 'dscf2826.jpg', NULL, NULL, 'dscf2826.jpg', 1209989386, NULL, '', '', 0, 0, 45, 1),
(697, 'dscf2828.jpg', NULL, NULL, 'dscf2828.jpg', 1209989388, NULL, '', '', 0, 0, 45, 1),
(698, 'dscf2829.jpg', NULL, NULL, 'dscf2829.jpg', 1209989389, NULL, '', '', 0, 0, 45, 1),
(699, 'dscf2831.jpg', NULL, NULL, 'dscf2831.jpg', 1209989391, NULL, '', '', 0, 0, 45, 1);
INSERT INTO `vypecky_photos` (`id_photo`, `label_cs`, `label_en`, `label_de`, `file`, `time`, `text_cs`, `text_en`, `text_de`, `height`, `width`, `id_galery`, `id_user`) VALUES
(700, 'dscf2834.jpg', NULL, NULL, 'dscf2834.jpg', 1209989392, NULL, '', '', 0, 0, 45, 1),
(701, 'dscf2836.jpg', NULL, NULL, 'dscf2836.jpg', 1209989396, NULL, '', '', 0, 0, 45, 1),
(702, 'dscf2838.jpg', NULL, NULL, 'dscf2838.jpg', 1209989397, NULL, '', '', 0, 0, 45, 1),
(703, 'dscf2840.jpg', NULL, NULL, 'dscf2840.jpg', 1209989399, NULL, '', '', 0, 0, 45, 1),
(704, 'dscf2843.jpg', NULL, NULL, 'dscf2843.jpg', 1209989400, NULL, '', '', 0, 0, 45, 1),
(705, 'dscf2852.jpg', NULL, NULL, 'dscf2852.jpg', 1209989402, NULL, '', '', 0, 0, 45, 1),
(706, 'dscf2857.jpg', NULL, NULL, 'dscf2857.jpg', 1209989405, NULL, '', '', 0, 0, 45, 1),
(707, 'dscf2858.jpg', NULL, NULL, 'dscf2858.jpg', 1209989408, NULL, '', '', 0, 0, 45, 1),
(708, 'dscf2864.jpg', NULL, NULL, 'dscf2864.jpg', 1209989411, NULL, '', '', 0, 0, 45, 1),
(709, 'dscf2865.jpg', NULL, NULL, 'dscf2865.jpg', 1209989414, NULL, '', '', 0, 0, 45, 1),
(710, 'dscf2866.jpg', NULL, NULL, 'dscf2866.jpg', 1209989418, NULL, '', '', 0, 0, 45, 1),
(711, 'dscf2869.jpg', NULL, NULL, 'dscf2869.jpg', 1209989422, NULL, '', '', 0, 0, 45, 1),
(712, 'dscf2870.jpg', NULL, NULL, 'dscf2870.jpg', 1209989423, NULL, '', '', 0, 0, 45, 1),
(758, 'napis.jpg', NULL, NULL, 'napis.jpg', 1211129253, '', '', '', 0, 0, 46, 1),
(717, 'plavba.jpg', NULL, NULL, 'plavba.jpg', 1211018098, '', '', '', 0, 0, 46, 1),
(718, 'dscf4223.jpg', NULL, NULL, 'dscf4223.jpg', 1211018099, '', '', '', 0, 0, 46, 1),
(760, 'klaster.jpg', NULL, NULL, 'klaster.jpg', 1211129256, '', '', '', 0, 0, 46, 1),
(720, 'dscf4229.jpg', NULL, NULL, 'dscf4229.jpg', 1211018103, '', '', '', 0, 0, 46, 1),
(721, 'dscf4244.jpg', NULL, NULL, 'dscf4244.jpg', 1211018109, '', '', '', 0, 0, 46, 1),
(723, 'dscf4273.jpg', NULL, NULL, 'dscf4273.jpg', 1211018114, '', '', '', 0, 0, 46, 1),
(759, 'zatoka-2.jpg', NULL, NULL, 'zatoka-2.jpg', 1211129255, '', '', '', 0, 0, 46, 1),
(726, 'dscf4277.jpg', NULL, NULL, 'dscf4277.jpg', 1211018121, '', '', '', 0, 0, 46, 1),
(727, 'dscf4278.jpg', NULL, NULL, 'dscf4278.jpg', 1211018123, '', '', '', 0, 0, 46, 1),
(728, 'dscf4285.jpg', NULL, NULL, 'dscf4285.jpg', 1211018124, '', '', '', 0, 0, 46, 1),
(729, 'dscf4293.jpg', NULL, NULL, 'dscf4293.jpg', 1211018126, '', '', '', 0, 0, 46, 1),
(730, 'dscf4298.jpg', NULL, NULL, 'dscf4298.jpg', 1211018131, '', '', '', 0, 0, 46, 1),
(765, '2008-04-18-04.jpg', NULL, NULL, '2008-04-18-04.jpg', 1211382083, 'Geodetic Hero', '', '', 0, 0, 47, 1),
(732, 'dscf4306.jpg', NULL, NULL, 'dscf4306.jpg', 1211018140, '', '', '', 0, 0, 46, 1),
(734, 'dscf4309.jpg', NULL, NULL, 'dscf4309.jpg', 1211018143, '', '', '', 0, 0, 46, 1),
(735, 'dscf4312.jpg', NULL, NULL, 'dscf4312.jpg', 1211018145, '', '', '', 0, 0, 46, 1),
(764, '2008-04-17-08.jpg', NULL, NULL, '2008-04-17-08.jpg', 1211382052, 'Jak nám sladce spinká (čtvrtek, 12:30)', '', '', 0, 0, 47, 1),
(737, 'dscf4315.jpg', NULL, NULL, 'dscf4315.jpg', 1211018148, '', '', '', 0, 0, 46, 1),
(738, 'dscf4331.jpg', NULL, NULL, 'dscf4331.jpg', 1211018150, '', '', '', 0, 0, 46, 1),
(739, 'dscf4336.jpg', NULL, NULL, 'dscf4336.jpg', 1211018151, '', '', '', 0, 0, 46, 1),
(740, 'dscf4337.jpg', NULL, NULL, 'dscf4337.jpg', 1211018153, '', '', '', 0, 0, 46, 1),
(763, '2008-04-14-09.jpg', NULL, NULL, '2008-04-14-09.jpg', 1211382015, 'Létající muž', '', '', 0, 0, 47, 1),
(742, 'dscf4339.jpg', NULL, NULL, 'dscf4339.jpg', 1211018156, '', '', '', 0, 0, 46, 1),
(743, 'dscf4341.jpg', NULL, NULL, 'dscf4341.jpg', 1211018159, '', '', '', 0, 0, 46, 1),
(744, 'dscf4346.jpg', NULL, NULL, 'dscf4346.jpg', 1211018161, '', '', '', 0, 0, 46, 1),
(746, 'dscf4354.jpg', NULL, NULL, 'dscf4354.jpg', 1211018166, '', '', '', 0, 0, 46, 1),
(747, 'dscf4362.jpg', NULL, NULL, 'dscf4362.jpg', 1211018169, '', '', '', 0, 0, 46, 1),
(748, 'dscf4369.jpg', NULL, NULL, 'dscf4369.jpg', 1211018170, '', '', '', 0, 0, 46, 1),
(749, 'dscf4370.jpg', NULL, NULL, 'dscf4370.jpg', 1211018172, '', '', '', 0, 0, 46, 1),
(762, '2008-04-14-07.jpg', NULL, NULL, '2008-04-14-07.jpg', 1211381976, 'Jak elegantně Ondřej padá :-)', '', '', 0, 0, 47, 1),
(751, 'dscf4396.jpg', NULL, NULL, 'dscf4396.jpg', 1211018177, '', '', '', 0, 0, 46, 1),
(752, 'dscf4400.jpg', NULL, NULL, 'dscf4400.jpg', 1211018180, '', '', '', 0, 0, 46, 1),
(753, 'dscf4407.jpg', NULL, NULL, 'dscf4407.jpg', 1211018182, '', '', '', 0, 0, 46, 1),
(754, 'dscf4409.jpg', NULL, NULL, 'dscf4409.jpg', 1211018185, '', '', '', 0, 0, 46, 1),
(755, 'dscf4419.jpg', NULL, NULL, 'dscf4419.jpg', 1211018186, '', '', '', 0, 0, 46, 1),
(756, 'dscf4421.jpg', NULL, NULL, 'dscf4421.jpg', 1211018188, '', '', '', 0, 0, 46, 1),
(766, '2008-04-19-06.jpg', NULL, NULL, '2008-04-19-06.jpg', 1211382122, '... že prý kamarádi', '', '', 0, 0, 47, 1),
(771, '2008-04-22-17.jpg', NULL, NULL, '2008-04-22-17.jpg', 1211382351, 'Za svítání na střeše, prostě romatika', '', '', 0, 0, 47, 1),
(768, '2008-04-21-031.jpg', NULL, NULL, '2008-04-21-031.jpg', 1211382188, 'Zdravotní procházka na Rudické propadání', '', '', 0, 0, 47, 1),
(770, '2008-04-21-141.jpg', NULL, NULL, '2008-04-21-141.jpg', 1211382257, 'Kytarista...', '', '', 0, 0, 47, 1),
(772, '2008-04-23-01.jpg', NULL, NULL, '2008-04-23-01.jpg', 1211382407, 'půl druhé odpoledne...', '', '', 0, 0, 47, 1),
(773, '2008-04-23-03.jpg', NULL, NULL, '2008-04-23-03.jpg', 1211382481, 'Pracovní skupinka č. 1, tihle lidi dohromady opravdu pracovali!!! (=vyvíjeli naprosto zbytečnou geodetickou činnost)', '', '', 0, 0, 47, 1),
(775, 'dsc-7062.jpg', NULL, NULL, 'dsc-7062.jpg', 1211469140, 'Jenyk s mapkou - výlet na Petřkovickou hůrku', '', '', 0, 0, 50, 1),
(776, 'dsc-7065.jpg', NULL, NULL, 'dsc-7065.jpg', 1211469141, '', '', '', 0, 0, 50, 1),
(777, 'dsc-7070.jpg', NULL, NULL, 'dsc-7070.jpg', 1211469143, '', '', '', 0, 0, 50, 1),
(778, 'pano1.jpg', NULL, NULL, 'pano1.jpg', 1211469144, 'Pod Petřkovickou hůrkou', '', '', 0, 0, 50, 1),
(779, 'dsc-7073.jpg', NULL, NULL, 'dsc-7073.jpg', 1211469146, '', '', '', 0, 0, 50, 1),
(780, 'dsc-7076.jpg', NULL, NULL, 'dsc-7076.jpg', 1211469149, '', '', '', 0, 0, 50, 1),
(799, 'dsc-7012.jpg', NULL, NULL, 'dsc-7012.jpg', 1211795865, '', '', '', 0, 0, 50, 1),
(782, 'dsc-7103.jpg', NULL, NULL, 'dsc-7103.jpg', 1211469152, '', '', '', 0, 0, 50, 1),
(783, 'dsc-7107.jpg', NULL, NULL, 'dsc-7107.jpg', 1211469154, 'nová dimenze podnikatelského baroka :-D', '', '', 0, 0, 50, 1),
(784, 'dsc-7118.jpg', NULL, NULL, 'dsc-7118.jpg', 1211469156, 'studující Pavlik', '', '', 0, 0, 50, 1),
(785, 'dsc-7120.jpg', NULL, NULL, 'dsc-7120.jpg', 1211469157, 'U sestry na privátě', '', '', 0, 0, 50, 1),
(786, 'dsc-7122.jpg', NULL, NULL, 'dsc-7122.jpg', 1211469159, '', '', '', 0, 0, 50, 1),
(787, 'dsc-7131.jpg', NULL, NULL, 'dsc-7131.jpg', 1211469162, '', '', '', 0, 0, 50, 1),
(789, 'dsc-7139.jpg', NULL, NULL, 'dsc-7139.jpg', 1211469165, 'Hooverphonic v Konviktu', '', '', 0, 0, 50, 1),
(790, 'dsc-7144.jpg', NULL, NULL, 'dsc-7144.jpg', 1211469168, 'Zázraky lidové tvořivosti', '', '', 0, 0, 50, 1),
(791, 'dsc-7152.jpg', NULL, NULL, 'dsc-7152.jpg', 1211469170, '', '', '', 0, 0, 50, 1),
(792, 'dsc-7153.jpg', NULL, NULL, 'dsc-7153.jpg', 1211469172, '', '', '', 0, 0, 50, 1),
(798, 'dsc-7004.jpg', NULL, NULL, 'dsc-7004.jpg', 1211795863, '', '', '', 0, 0, 50, 1),
(794, 'dsc-7162.jpg', NULL, NULL, 'dsc-7162.jpg', 1211469177, '', '', '', 0, 0, 50, 1),
(795, 'dsc-7168.jpg', NULL, NULL, 'dsc-7168.jpg', 1211469178, '', '', '', 0, 0, 50, 1),
(796, 'dsc-7172.jpg', NULL, NULL, 'dsc-7172.jpg', 1211469180, '', '', '', 0, 0, 50, 1),
(797, 'dsc-7176.jpg', NULL, NULL, 'dsc-7176.jpg', 1211469181, '', '', '', 0, 0, 50, 1),
(800, 'dsc-7022.jpg', NULL, NULL, 'dsc-7022.jpg', 1211795867, '', '', '', 0, 0, 50, 1),
(801, 'dsc-7028.jpg', NULL, NULL, 'dsc-7028.jpg', 1211795868, '', '', '', 0, 0, 50, 1),
(802, 'dsc-7036.jpg', NULL, NULL, 'dsc-7036.jpg', 1211795870, '', '', '', 0, 0, 50, 1),
(803, 'dsc-7045.jpg', NULL, NULL, 'dsc-7045.jpg', 1211795871, '', '', '', 0, 0, 50, 1),
(809, 'dsc-7049.jpg', NULL, NULL, 'dsc-7049.jpg', 1211796126, '', '', '', 0, 0, 50, 1),
(808, 'dsc-7054.jpg', NULL, NULL, 'dsc-7054.jpg', 1211796106, '', '', '', 0, 0, 50, 1),
(807, 'dsc-7057.jpg', NULL, NULL, 'dsc-7057.jpg', 1211796085, '', '', '', 0, 0, 50, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photo_galeries`
--

CREATE TABLE IF NOT EXISTS `vypecky_photo_galeries` (
  `id_galery` smallint(5) unsigned NOT NULL auto_increment,
  `urlkey` varchar(50) NOT NULL,
  `label_cs` varchar(40) default NULL,
  `text_cs` varchar(400) default NULL,
  `label_en` varchar(40) default NULL,
  `text_en` varchar(400) default NULL,
  `label_de` varchar(40) default NULL,
  `text_de` varchar(400) default NULL,
  `time` int(10) default NULL,
  `id_section` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL default '1',
  PRIMARY KEY  (`id_galery`),
  KEY `id_section` (`id_section`),
  KEY `key` (`urlkey`),
  KEY `id_user` (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

--
-- Vypisuji data pro tabulku `vypecky_photo_galeries`
--

INSERT INTO `vypecky_photo_galeries` (`id_galery`, `urlkey`, `label_cs`, `text_cs`, `label_en`, `text_en`, `label_de`, `text_de`, `time`, `id_section`, `id_user`) VALUES
(29, 'recko-07', 'Řecko 07', 'Nostalgická výprava na místa známá i neznámá. Cesta za ztraceným sluncem. Vyjížďka s orangutany, křenem a jednookým medvídětem.', NULL, NULL, NULL, NULL, 1189350628, 13, 1),
(28, 'ukrajina-07', 'Ukrajina 07', 'Putovní dovolená Sádla a jeho přátel po ukrainských poloninách.', NULL, NULL, NULL, NULL, 1186758501, 13, 1),
(6, 'prvni-jarni-grilovacka', 'První jarní grilovačka', 'Vypečená grilovačka s dobrým vínem a ještě lepšími špízy.', NULL, NULL, NULL, NULL, 1207655381, 11, 1),
(7, 'velikonocni-vychazka', 'Velikonoční vycházka', 'Krátká návštěvá kolegy Krátkého na Valachách.', NULL, NULL, NULL, NULL, 1200655394, 12, 1),
(8, 'viden-=', 'Vídeň', 'Výlet do Vídeňského Flexu na Laurenta Garniera', NULL, NULL, NULL, NULL, 1207655363, 13, 1),
(30, 'skotsko-06', 'Skotsko 06', 'Do třetice všeho dobrého i zlého. Opět ve Skotsku (hromadně jsme se zařekli, že na farmě jsme jistojistě naposled :-) a opět s věrným finepixem. Tentokrát jsme pobyli jen něco málo přes měsíc. Zvládli jsme několik kratších výletů a vandr v Grampianech.', NULL, NULL, NULL, NULL, 1152285178, 13, 1),
(31, 'thessaloniki-1', 'Thessaloniki 1', 'Výběr fotograafií z půlročního pobytu v Soluni.', NULL, NULL, NULL, NULL, 1192029389, 13, 1),
(32, 'valasska-zima', 'Valašská zima', 'Směs fotek z různých zimních vycházek po Beskydech.', NULL, NULL, NULL, NULL, 1199291725, 15, 1),
(33, 'babi-lom', 'Babí lom', 'Nedělní vycházka na poněkud přelidněný Babí lom.', NULL, NULL, NULL, NULL, 1203785447, 12, 1),
(34, 'pardubice', 'Pardubice', 'Předvánoční punková polízanice pod patronátem pardubického přítelíčka Pavla proběhla prostě parádně. Půllitry poletovaly povětřím, pankáči pěkně pogovali, pach pardubického perníku pronikal putykou. Prostě pohoda.', NULL, NULL, NULL, NULL, 1198342579, 11, 1),
(35, 'mischmasch-1', 'Mischmasch 1', 'Směs fotek z různých zeměpisných šířek, délek, časových období a pásem, zobrazující okolní předměty, lidi, domy, faunu, flóru, přírodní útvary atp. První zkušenosti s poměrně plnotučnou digitální zrcadlovkou Nikon D70.', NULL, NULL, NULL, NULL, 1200848937, 15, 1),
(36, 'namest-nad-oslavou', 'Námešť nad Oslavou', 'Sobotní bloudění okolím Brna.', NULL, NULL, NULL, NULL, 1204367911, 12, 1),
(37, 'velky-choc', 'Velký Choč', 'Vánoční výšlap na Choč v kratochvilné společnosti bratří Kratochvílů a mladého pana Žilinského.', NULL, NULL, NULL, NULL, 1198839151, 12, 1),
(38, 'creativity-night-08', 'Creativity Night 08', 'Úterní anglistická kalba na Flédě.', NULL, NULL, NULL, NULL, 1208265890, 11, 1),
(39, 'jedovnice-08', 'Jedovnice 08', 'Jedovatý víkend na geodetické výuce v terénu', NULL, NULL, NULL, NULL, 1208640401, 11, 1),
(40, 'trolejbusy-v-komine', 'Trolejbusy v Komíně', 'Nedělní vycházka na den otevřených dveří ve vozovně Komín.', NULL, NULL, NULL, NULL, 1209317933, 12, 1),
(41, 'svickova', 'Svíčková', 'Stručný fotorecept na vypečenou svíčkovou - s námi to zvládne každý.', NULL, NULL, NULL, NULL, 1209739537, 9, 1),
(42, 'jihlava', 'Jihlava', 'Anglistická spářka v malebném kraji Vysočiny + několik kýčovitých fotografií z mé nedělní vycházky v okolí Jihlavy.', NULL, NULL, NULL, NULL, 1209973830, 12, 1),
(43, 'bulharsko-rila', 'Bulharsko - Rila', 'Blageovgrad, Rilski Monastyr, Kobylino Branishte, Ribne Ezera, Pavlev Vrah, Makedonia Bodrost a zpět do Blageovgradu', NULL, NULL, NULL, NULL, 1163249110, 13, 1),
(44, 'katciny-narozky', 'Katčiny narozky', 'Narozeninová oslava na Výpečkách', NULL, NULL, NULL, NULL, 1204376338, 11, 1),
(45, 'olymp-=', 'Olymp', 'Podzimní výšlap na Olymp - 2917 metrů nad mořem a vycházelo se od moře :-)', NULL, NULL, NULL, NULL, 1145621359, 13, 1),
(46, 'thassos', 'Thassos', '', NULL, NULL, NULL, NULL, 1170500088, 13, 1),
(47, 'jedovnice-08-vol-2', 'Jedovnice 08, vol. 2', '', NULL, NULL, NULL, NULL, 1211381227, 11, 1),
(51, 'sraz-barevnych-2008', 'Sraz barevných 2008', '', NULL, NULL, NULL, NULL, 1213101827, 12, 1),
(50, 'mischmasch-2', 'Mischmasch 2', '', NULL, NULL, NULL, NULL, 1211469137, 15, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photo_sections`
--

CREATE TABLE IF NOT EXISTS `vypecky_photo_sections` (
  `id_section` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL default '1',
  `urlkey` varchar(50) NOT NULL,
  `label_cs` varchar(40) default NULL,
  `label_en` varchar(40) default NULL,
  `label_de` varchar(40) default NULL,
  `time` int(10) default NULL,
  PRIMARY KEY  (`id_section`),
  KEY `id_item` (`id_item`,`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Vypisuji data pro tabulku `vypecky_photo_sections`
--

INSERT INTO `vypecky_photo_sections` (`id_section`, `id_item`, `id_user`, `urlkey`, `label_cs`, `label_en`, `label_de`, `time`) VALUES
(12, 4, 1, 'vychazky', 'Vycházky', NULL, NULL, 1207599835),
(11, 4, 1, 'vecirky-', 'Večírky', NULL, NULL, 1207590364),
(9, 4, 1, 'vypecenosti', 'Vypečenosti', NULL, NULL, 1207308314),
(13, 4, 1, 'vyjizdky', 'Vyjížďky', NULL, NULL, 1207599848),
(15, 4, 1, 'vybery--', 'Výběry', NULL, NULL, 1207599880);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_rating`
--

CREATE TABLE IF NOT EXISTS `vypecky_rating` (
  `id_rating` smallint(4) NOT NULL auto_increment,
  `id_category` smallint(3) NOT NULL,
  `id_article` smallint(3) NOT NULL,
  `mark_1` mediumint(4) NOT NULL default '0',
  `mark_2` mediumint(4) NOT NULL default '0',
  `mark_3` smallint(4) NOT NULL default '0',
  `mark_4` smallint(4) NOT NULL default '0',
  `mark_5` smallint(4) NOT NULL default '0',
  PRIMARY KEY  (`id_rating`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=154 ;

--
-- Vypisuji data pro tabulku `vypecky_rating`
--

INSERT INTO `vypecky_rating` (`id_rating`, `id_category`, `id_article`, `mark_1`, `mark_2`, `mark_3`, `mark_4`, `mark_5`) VALUES
(1, 10, 1, 26, 38, 0, 0, 0),
(75, 10, 29, 0, 0, 0, 0, 0),
(110, 3, 43, 0, 0, 0, 0, 1),
(5, 3, 3, 1, 0, 0, 0, 0),
(6, 3, 4, 0, 1, 0, 1, 0),
(7, 3, 5, 0, 0, 0, 0, 0),
(8, 3, 7, 0, 0, 0, 0, 0),
(111, 3, 44, 1, 0, 0, 0, 0),
(10, 3, 9, 1, 0, 1, 0, 0),
(11, 3, 10, 0, 0, 0, 0, 0),
(12, 3, 11, 1, 0, 0, 0, 0),
(13, 3, 12, 1, 0, 0, 0, 0),
(14, 3, 13, 0, 0, 0, 0, 0),
(15, 3, 14, 6, 1, 0, 0, 0),
(16, 3, 15, 0, 0, 0, 0, 0),
(17, 3, 16, 0, 0, 0, 0, 0),
(18, 3, 17, 1, 0, 0, 1, 0),
(19, 3, 18, 2, 0, 0, 0, 0),
(20, 3, 19, 0, 0, 0, 0, 0),
(21, 3, 20, 0, 0, 0, 0, 1),
(22, 3, 21, 0, 0, 0, 0, 0),
(23, 3, 23, 1, 0, 0, 0, 0),
(24, 3, 24, 1, 2, 0, 0, 0),
(25, 3, 25, 0, 0, 0, 0, 0),
(26, 3, 26, 0, 0, 0, 0, 1),
(27, 3, 27, 0, 0, 0, 0, 0),
(28, 3, 28, 2, 11, 0, 0, 0),
(29, 3, 29, 0, 0, 0, 0, 1),
(67, 10, 21, 0, 0, 0, 0, 0),
(66, 10, 20, 0, 0, 1, 0, 0),
(65, 3, 34, 0, 0, 0, 0, 0),
(74, 10, 28, 0, 0, 0, 0, 0),
(63, 3, 32, 1, 0, 0, 0, 0),
(62, 3, 31, 0, 0, 0, 0, 0),
(61, 3, 30, 1, 0, 0, 0, 0),
(59, 10, 18, 0, 0, 0, 0, 0),
(58, 10, 17, 1, 1, 0, 0, 0),
(57, 10, 16, 0, 0, 0, 0, 0),
(68, 10, 22, 0, 0, 0, 0, 0),
(70, 10, 24, 0, 0, 0, 0, 0),
(71, 10, 25, 0, 0, 0, 0, 0),
(72, 10, 26, 1, 0, 0, 0, 0),
(73, 10, 27, 0, 0, 0, 0, 0),
(76, 10, 30, 0, 0, 0, 0, 0),
(77, 10, 31, 0, 0, 0, 0, 0),
(78, 10, 32, 0, 0, 0, 0, 0),
(79, 3, 37, 0, 0, 0, 0, 0),
(80, 3, 38, 0, 0, 0, 0, 0),
(81, 10, 33, 0, 0, 0, 0, 0),
(83, 3, 40, 0, 0, 0, 0, 0),
(84, 10, 34, 0, 0, 0, 0, 0),
(85, 10, 35, 0, 0, 0, 0, 0),
(86, 10, 36, 2, 0, 0, 0, 0),
(87, 10, 37, 0, 0, 0, 0, 0),
(88, 10, 38, 0, 0, 0, 0, 0),
(89, 10, 39, 0, 0, 0, 0, 0),
(90, 3, 41, 7, 1, 1, 1, 0),
(91, 3, 42, 108, 2, 1, 0, 2),
(92, 10, 40, 0, 1, 0, 0, 1),
(93, 10, 41, 0, 0, 0, 0, 0),
(96, 10, 44, 1, 0, 0, 0, 0),
(97, 10, 45, 0, 1, 0, 0, 0),
(98, 10, 46, 0, 0, 0, 0, 0),
(99, 10, 47, 0, 0, 0, 0, 0),
(100, 10, 48, 0, 0, 0, 0, 0),
(101, 10, 49, 0, 0, 0, 0, 0),
(102, 10, 50, 0, 0, 0, 0, 0),
(103, 10, 51, 0, 0, 0, 0, 0),
(104, 10, 52, 0, 0, 0, 0, 0),
(105, 10, 57, 0, 0, 0, 0, 0),
(106, 10, 58, 0, 0, 0, 0, 0),
(109, 10, 61, 0, 0, 0, 0, 0),
(112, 3, 45, 0, 0, 0, 0, 0),
(113, 3, 46, 0, 0, 0, 0, 0),
(114, 3, 47, 0, 0, 0, 0, 0),
(115, 3, 48, 1, 0, 0, 0, 0),
(116, 3, 49, 0, 0, 0, 0, 0),
(117, 3, 50, 0, 0, 0, 0, 0),
(118, 3, 51, 0, 0, 0, 0, 0),
(119, 3, 52, 0, 0, 0, 0, 0),
(120, 3, 53, 0, 0, 0, 0, 0),
(121, 3, 54, 0, 0, 0, 0, 0),
(122, 3, 55, 0, 0, 0, 0, 0),
(123, 3, 56, 1, 0, 0, 0, 0),
(124, 3, 57, 0, 0, 0, 0, 0),
(125, 3, 58, 0, 0, 0, 0, 0),
(126, 3, 59, 0, 0, 0, 0, 0),
(127, 3, 60, 0, 0, 0, 0, 0),
(128, 3, 61, 0, 0, 0, 0, 0),
(129, 3, 62, 0, 0, 0, 0, 0),
(130, 3, 63, 0, 0, 0, 0, 0),
(131, 3, 64, 0, 0, 0, 0, 0),
(132, 3, 65, 0, 0, 0, 0, 0),
(133, 3, 66, 0, 0, 0, 0, 0),
(134, 3, 67, 0, 0, 0, 0, 0),
(135, 3, 68, 0, 0, 0, 0, 0),
(136, 3, 69, 0, 0, 0, 0, 0),
(137, 3, 70, 0, 0, 0, 0, 0),
(138, 3, 71, 0, 0, 0, 0, 0),
(139, 3, 72, 0, 0, 0, 0, 0),
(140, 3, 73, 0, 0, 0, 0, 0),
(141, 3, 74, 0, 0, 0, 0, 0),
(142, 3, 75, 0, 0, 0, 0, 0),
(143, 3, 76, 0, 0, 0, 0, 0),
(144, 3, 77, 0, 0, 0, 0, 0),
(145, 3, 78, 0, 0, 0, 0, 0),
(146, 3, 79, 1, 0, 0, 0, 0),
(147, 3, 80, 8, 1, 0, 1, 7),
(148, 10, 62, 0, 0, 0, 0, 0),
(149, 10, 63, 0, 0, 0, 0, 0),
(151, 3, 81, 8, 0, 0, 0, 1),
(152, 10, 65, 1, 0, 0, 0, 0),
(153, 10, 66, 3, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_sections`
--

CREATE TABLE IF NOT EXISTS `vypecky_sections` (
  `id_section` smallint(3) NOT NULL auto_increment,
  `label_cs` varchar(50) default NULL,
  `alt_cs` varchar(200) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `priority` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id_section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Vypisuji data pro tabulku `vypecky_sections`
--

INSERT INTO `vypecky_sections` (`id_section`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `priority`) VALUES
(1, 'Tlačenka', NULL, NULL, NULL, NULL, NULL, 100),
(2, 'Výpečky', NULL, NULL, NULL, NULL, NULL, 90),
(3, 'Fotogalerie', NULL, NULL, NULL, NULL, NULL, 80),
(4, 'Jitrničky', NULL, NULL, NULL, NULL, NULL, 70),
(5, 'Odkazy', NULL, NULL, NULL, NULL, NULL, 50),
(6, 'Blahoš', NULL, NULL, NULL, NULL, NULL, 40),
(7, 'Ke stažení', NULL, NULL, NULL, NULL, NULL, 30),
(8, 'Kontakt', NULL, NULL, NULL, NULL, NULL, 20),
(9, 'Chyby', NULL, NULL, NULL, NULL, NULL, 10),
(10, 'Účet', NULL, NULL, NULL, NULL, NULL, 0),
(11, 'Reklama', NULL, NULL, NULL, NULL, NULL, 5),
(12, 'Komiks', 'Komiks Kuba a Kuba', NULL, NULL, NULL, NULL, 60),
(13, 'Správa', 'Správa portálu', 'Settings', 'Portal settings', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_sponsors`
--

CREATE TABLE IF NOT EXISTS `vypecky_sponsors` (
  `id_sponsor` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(6) NOT NULL,
  `urlkey` varchar(50) NOT NULL,
  `name_cs` varchar(50) NOT NULL,
  `label_cs` varchar(500) default NULL,
  `name_en` varchar(50) default NULL,
  `label_en` varchar(500) default NULL,
  `name_de` varchar(50) default NULL,
  `label_de` varchar(500) default NULL,
  `url` varchar(100) default NULL,
  `logo_image` varchar(100) default NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_sponsor`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=61 ;

--
-- Vypisuji data pro tabulku `vypecky_sponsors`
--

INSERT INTO `vypecky_sponsors` (`id_sponsor`, `id_item`, `urlkey`, `name_cs`, `label_cs`, `name_en`, `label_en`, `name_de`, `label_de`, `url`, `logo_image`, `deleted`) VALUES
(1, 18, 'deza-as', 'Deza a.s.', '<p>dehtové závody ve Valašském Meziříčí. Tato firma patří mezi osm největších chemiček v České Republice a je naším hlavním sponzorem</p>', 'Deza a.s.', '<p>Corporation</p>', NULL, NULL, 'http://www.deza.cz', '65275b44-b-6-deza-212.jpg', 0),
(2, 18, 'albert-as', 'Albert a.s.', '<p>Malovelkoobchod s potravinami. <span style="font-size: medium;"><strong>Náš sponzor pro stravu!!</strong></span></p>', 'Alber a.s.', NULL, NULL, NULL, 'http://www.albert.cz', 'albert4.gif', 0),
(58, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, 'www.deza.cz', '65275b44-b-6-deza-27.jpg', 1),
(60, 18, 'tak-serri', 'Tak šeřří', '<p><span style="color: #ffcc00;"><strong>Albert a.s.</strong></span></p>', NULL, '<p>In english</p>', NULL, NULL, 'http://www.albert.cz', '65275b44-b-6-deza-211.jpg', 1),
(59, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, 'http://www.deza.cz', '65275b44-b-6-deza-28.jpg', 1),
(41, 18, 'albert-as', 'Albert a.s.', NULL, NULL, NULL, NULL, NULL, NULL, 'albert.gif', 1),
(57, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, 'www.deza.cz', '65275b44-b-6-deza-26.jpg', 1),
(56, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, 'www.deza.cz', '65275b44-b-6-deza-25.jpg', 1),
(52, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-21.jpg', 1),
(51, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-2.jpg', 1),
(55, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-24.jpg', 1),
(54, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-23.jpg', 1),
(53, 18, 'deza-as', 'Deza a.s.', NULL, NULL, NULL, NULL, NULL, NULL, '65275b44-b-6-deza-22.jpg', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_texts`
--

CREATE TABLE IF NOT EXISTS `vypecky_texts` (
  `id_text` smallint(4) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `text` mediumtext,
  `changed_time` int(11) default NULL,
  PRIMARY KEY  (`id_text`),
  UNIQUE KEY `id_article` (`id_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Vypisuji data pro tabulku `vypecky_texts`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_userfiles`
--

CREATE TABLE IF NOT EXISTS `vypecky_userfiles` (
  `id_file` smallint(6) NOT NULL auto_increment,
  `id_category` smallint(6) NOT NULL,
  `id_article` smallint(6) NOT NULL,
  `file` varchar(50) NOT NULL,
  `size` int(11) default NULL,
  PRIMARY KEY  (`id_file`),
  KEY `id_category` (`id_category`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Vypisuji data pro tabulku `vypecky_userfiles`
--

INSERT INTO `vypecky_userfiles` (`id_file`, `id_category`, `id_article`, `file`, `size`) VALUES
(22, 10, 1, 'unit1.obj', 18946),
(21, 10, 1, 'doc.pdf', 1122),
(20, 10, 45, 'moucha21.swf', 0),
(19, 10, 45, 'moucha2.swf', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_users`
--

CREATE TABLE IF NOT EXISTS `vypecky_users` (
  `id_user` smallint(5) unsigned NOT NULL auto_increment COMMENT 'ID uzivatele',
  `username` varchar(20) NOT NULL COMMENT 'Uzivatelske jmeno',
  `password` varchar(40) default NULL COMMENT 'Heslo',
  `id_group` smallint(3) unsigned default '3',
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `note` varchar(500) default NULL,
  `blocked` tinyint(1) NOT NULL default '0',
  `foto_file` varchar(30) default NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_user`,`username`),
  KEY `id_group` (`id_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Vypisuji data pro tabulku `vypecky_users`
--

INSERT INTO `vypecky_users` (`id_user`, `username`, `password`, `id_group`, `name`, `surname`, `mail`, `note`, `blocked`, `foto_file`, `deleted`) VALUES
(1, 'admin', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'administrátor', 0, NULL, 0),
(2, 'guest', 'guest', 2, 'host', 'host', '', 'host systému', 0, NULL, 0),
(3, 'cuba', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0),
(4, 'delamancha', 'delamancha', 4, 'Jakub', 'Vémola', 'j.vemola@gmail.com', 'Jack', 0, 'jack.jpg', 0),
(13, 'slávka', 'SK1Tl7jq', 3, 'Iva', 'Korgerová', 'j.vemola@gmail.com', '', 0, NULL, 0),
(9, 'drobek', 'drobek', 3, 'Pavlík', 'Rybecký', 'ppavelrybecky@tiscali.cz', '', 0, '33-kubaakuba-dvojnik.jpg', 0),
(10, 'jeni013', 'jenicek8', 3, 'Honza', 'Liebel', 'honza.liebel@centrum.cz', '', 0, 'krtecek.jpg', 0),
(11, 'BSBVB', 'oligo', 3, 'Johnie', 'BSBVB', 'drimalt@seznam.cz', 'drimalt@sezman.cz\r\n - když já už sem si zvykl ten mejl dávat dycky dvakrát..', 0, NULL, 0),
(12, 'arivederci', 'h3d27GYQ', 3, 'Kateřina', 'Pardubová', 'katerina.pardubova@gmail.com', '', 0, NULL, 0),
(14, 'Šalvěj', 'dwXgzUFs', 3, 'Pavel', 'Daněk', 'paveldanek@seznam.cz', '', 0, NULL, 0),
(15, 'Šajtr', 'ville', 3, 'Pavel', 'Schreier', 'PavSch@seznam.cz', '', 0, NULL, 0),
(16, 'Zdenda benda', 'cepaj8or', 3, 'Zdenda', 'Kozák', 'zdenda.kozak@seznam.cz', '', 0, NULL, 0),
(17, 'usual_moron', 'usual_moron', 3, 'Michal', 'Čarnický', '', NULL, 0, 'images1.jpg', 0),
(19, 'Mikimyš', 'q4SMvs2f', 3, 'Kateřina', 'Novotná', 'katerinati@seznam.cz', 'Zdravím Výpečky! Lužánecká rulez!', 0, NULL, 0);
