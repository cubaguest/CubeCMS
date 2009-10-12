-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.1
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Pondělí 12. října 2009, 10:20
-- Verze MySQL: 5.0.75
-- Verze PHP: 5.2.6-3ubuntu4.2

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
-- Struktura tabulky `vypecky_actions`
--

DROP TABLE IF EXISTS `vypecky_actions`;
CREATE TABLE IF NOT EXISTS `vypecky_actions` (
  `id_action` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL,
  `label_cs` varchar(50) NOT NULL,
  `text_cs` text NOT NULL,
  `label_en` varchar(50) default NULL,
  `text_en` text,
  `label_de` varchar(50) default NULL,
  `text_de` text,
  `time` int(11) NOT NULL,
  `start_date` int(11) default NULL,
  `stop_date` int(11) default NULL,
  `image` varchar(200) default NULL,
  `disable` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_action`),
  KEY `id_user` (`id_user`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Vypisuji data pro tabulku `vypecky_actions`
--

INSERT INTO `vypecky_actions` (`id_action`, `id_item`, `id_user`, `label_cs`, `text_cs`, `label_en`, `text_en`, `label_de`, `text_de`, `time`, `start_date`, `stop_date`, `image`, `disable`) VALUES
(20, 11, 1, 'AKCE (VRATA A DVEŘE) za neuvěřitelnou cenu', '<p>Speciální akce - vrata Hörmann za 20.900,- Kč a hliníkové vchodové dveře za 26.900,- Kč.</p>', NULL, NULL, NULL, NULL, 1240065330, 1240005600, 1241128800, 'akce-jaro-0320.jpg', 0),
(22, 11, 1, 'ZDARMA DOPLŇKY DLE VÝBĚRU ZÁKAZNÍKA', '<p><span class="texty">Jako bonus nabízíme svým zákazníkům doplňky dle vlastního výběru. Zákazník si může vybrat jednu ze čtyř možností:<br /> 1.<strong> <a class="text_underline" href="http://www.okna-sevcik.cz/okna/prislusenstvi/" target="_parent">Žaluzie</a></strong> - ke každému oknu s čirým sklem dodáme na přání zákazníka žaluzie zdarma. Jedná se o žaluzie ve standardním provedení (pouze lakované, bez imitace dřeva nebo proužku)<br /> 2.	<strong><a class="text_underline" href="http://www.okna-sevcik.cz/okna/prislusenstvi/" target="_parent">Sítě proti hmyzu</a></strong> - ke každému oknu se sklopným křídlem dodáme na přání zákazníka sítě zdarma. Jedná se o sítě ve standardním provedení (pouze lakované, bez imitace dřeva, ne dveřní). <br /> 3.	<strong><a class="text_underline" href="http://www.okna-sevcik.cz/okna/parapety/" target="_parent">Venkovní parapety</a></strong> - ke každému oknu dodáme parapet dle naší nabídky</span></p>\r\n<p><span class="texty"><br /></span></p>\r\n<p><span class="texty">55<br /></span></p>\r\n<p><span class="texty"> 4.	<strong><a class="text_underline" href="http://www.okna-sevcik.cz/okna/parapety/" target="_parent">Vnitřní parapety</a></strong> - ke každému oknu dodáme parapet dle naší nabídky</span></p>', NULL, NULL, NULL, NULL, 1240065513, 1240092000, 1242597600, '', 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_articles`
--

DROP TABLE IF EXISTS `vypecky_articles`;
CREATE TABLE IF NOT EXISTS `vypecky_articles` (
  `id_article` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned default '1',
  `add_time` int(11) NOT NULL,
  `edit_time` int(11) default NULL,
  `label_cs` varchar(400) default NULL,
  `text_cs` text,
  `label_en` varchar(400) default NULL,
  `text_en` text,
  `lebal_de` varchar(400) default NULL,
  `text_de` text,
  PRIMARY KEY  (`id_article`),
  KEY `id_item` (`id_item`,`id_user`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `lebal_de` (`lebal_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vypecky_articles`
--

INSERT INTO `vypecky_articles` (`id_article`, `id_item`, `id_user`, `add_time`, `edit_time`, `label_cs`, `text_cs`, `label_en`, `text_en`, `lebal_de`, `text_de`) VALUES
(1, 7, 1, 1239003796, 1239112581, 'lore ipsum', '<p style="text-align: justify;">Nic takového, žádný román se nekoná a přiznejme si, že ke značnému zklamání mnoha z nás, kteří věřili tomu, že Kundera vydá na sklonku svého života dílo, v němž rozvine témata, kterých se dotkl už ve svých předchozích pracích - emigraci, zradu, odcizení, identitu. Spisovatel je opravdu rozvíjí, opakuje a upřesňuje - avšak pouze formou esejů s názvem Une rencontre (Setkání).</p>\r\n<p><a title="image" rel="lightbox" href="data/userimages/anree.jpg"><img style="float: left;" title="anree.jpg" src="data/userimages/anree.jpg" alt="anree.jpg" width="150" height="200" /></a></p>\r\n<p style="text-align: justify;">V kontextu celé knihy by byl možná příhodnější název Setkání s Milanem Kunderou. V jejím úvodu totiž autor tvrdí, že pokud umělec mluví či píše o někom jiném, mluví, přímo či nepřímo, především sám o sobě a tím je ovlivněn i jeho soud. "Pokud mluví o Beckettovi, co nám Bacon vlastně sděluje o sobě?" ptá se.</p>\r\n<p> </p>\r\n<p>Milan Kundera oslavil osmdesátiny</p>\r\n<p> </p>\r\n<p style="text-align: justify;">Ačkoliv patří mezi osobnosti přijímané kontroverzně prakticky celý život, zejména v posledních měsících jako by nutil zaujímat postoje vůči sobě i ty, kteří od něj nepřečetli jediné slovo.</p>\r\n<p> </p>\r\n<p style="text-align: justify;">Takže co říká Setkání o Kunderovi? V jedné ze svých posledních knih Pomalost přichází s paralelou mezi rychlostí chůze a schopností paměti vybavovat si jisté momenty. Pokud zrychlujeme, snažíme se na některé věci zapomenout, pokud naopak zpomalujeme, pomáháme si je vybavit, tvrdí a lamentuje nad tím, že svět je příliš rychlý, a abychom dokázali ocenit drobné radosti, je potřeba zpomalit. V Setkání učinil Kundera přímo zastávku s ohlédnutím.</p>\r\n<p> </p>', NULL, NULL, NULL, NULL),
(10, 7, 1, 1244914976, 1244915134, 'Iran election protests turn violent', '<p><strong>TEHRAN, Iran (CNN)</strong> -- Angry crowds in Moseni Square in Iran''s capital Saturday night broke into shops, tore down signs and started fires as they protested the re-election of Iranian President Mahmoud Ahmadinejad, according to CNN employees at the scene.</p>\r\n<p> </p>\r\n<!--startclickprintexclude-->\r\n<div id="imageChanger1"><!-- PURGE: /2009/WORLD/meast/06/13/iran.election/art.protest.afp.gi.jpg --><!-- KEEP -->\r\n<div class="cnnStoryPhotoBox">\r\n<div id="cnnImgChngr" class="cnnImgChngr">\r\n<div id="cnnImgChngrNested"><img style="float: left; margin-right: 10px;" title="art.protest.afp.gi.jpg" src="data/userfiles/art.protest.afp.gi.jpg" alt="art.protest.afp.gi.jpg" width="292" height="219" />They were yelling the name of Mir Hossein Moussavi, who the government says lost Friday''s presidential election by a wide margin.</div>\r\n</div>\r\n</div>\r\n<!-- /PURGE: /2009/WORLD/meast/06/13/iran.election/art.protest.afp.gi.jpg --></div>\r\n<p>\r\n<script type="text/javascript"><!--\r\n	var CNN_ArticleChanger = new CNN_imageChanger(''cnnImgChngr'',''/2009/WORLD/meast/06/13/iran.election/imgChng/p1-0.init.exclude.html'',2,1);\r\n\r\n//CNN.imageChanger.load(''cnnImgChngr'',''imgChng/p1-0.exclude.html'');\r\n// --></script>\r\n</p>\r\n<!--endclickprintexclude-->\r\n<p>Protests broke out in Tehran earlier Saturday after Ahmadinejad was declared the winner of the vote.</p>\r\n<p>The announcement brought thousands of Moussavi supporters onto the streets where they were met a strong police presence and the threat of violence.</p>\r\n<p>CNN''s Christiane Amanpour said she saw riot police fighting "running battles" with protesters, who were shouting "death to dictatorship."</p>\r\n<p>The government said on Saturday that Ahmadinejad won Friday''s presidential election with 62.63 percent of the vote and Mir Hossein Moussavi received 33.75 percent of the vote.</p>\r\n<p>Before the vote count ended, Moussavi issued a sharply worded letter urging the counting to stop because of "blatant violations" and lashed out at what he indicated was an unfair process.</p>\r\n<p>Moussavi said the results from "untrustworthy monitors" reflected "the weakening of the pillars that constitute the sacred system" of Iran and "the rule of authoritarianism and tyranny." Independent vote monitors were banned from polling places. <span class="cnnEmbeddedMosLnk"><img src="http://i2.cdn.turner.com/cnn/.element/img/2.0/mosaic/tabs/video.gif" border="0" alt="Video" width="16" height="14" /> <a onclick="CNN_changeMosaicTab(''cnnVideoCmpnt'',''videos.html'',true,''/video/#/video/world/2009/06/13/amanpour.election.protests.cnn'');" href="http://edition.cnn.com/2009/WORLD/meast/06/13/iran.election/index.html#cnnSTCVideo">Watch as riot police disperse protesters »</a></span></p>\r\n<p>"The results announced for the 10th presidential elections are astonishing. People who stood in long lines and knew well who they voted for were utterly surprised by the magicians working at the television and radio broadcasting," Moussavi said in his statement.</p>\r\n<p>Iran, he said, "belongs to the people and not cheaters." <span class="cnnEmbeddedMosLnk"><img src="http://i2.cdn.turner.com/cnn/.element/img/2.0/mosaic/tabs/video.gif" border="0" alt="Video" width="16" height="14" /> <a onclick="CNN_changeMosaicTab(''cnnVideoCmpnt'',''videos.html'',true,''/video/#/video/world/2009/06/13/iran.election.results.irinn'');" href="http://edition.cnn.com/2009/WORLD/meast/06/13/iran.election/index.html#cnnSTCVideo">Watch as Ahmadinejad is declared the winner »</a></span></p>\r\n<p>Analysts expected <a class="cnnInlineTopic" href="http://topics.edition.cnn.com/topics/mir_hossein_moussavi">Moussavi</a>, widely regarded as a reformist, to do well.</p>\r\n<p>After a presidential debate between Moussavi and <a class="cnnInlineTopic" href="http://topics.edition.cnn.com/topics/Mahmoud_Ahmadinejad">Ahmadinejad</a> riveted the nation, Moussavi''s campaign caught fire in recent days, triggering massive street rallies in Tehran.</p>\r\n<p>What officials have called an unprecedented voter turnout at the polls Friday had been expected to boost Moussavi''s chances of winning the presidency. But Ahmadinejad -- despite being blamed for Iran''s economic turmoil over the past four years -- maintains staunch support in rural areas.</p>\r\n<!--startclickprintexclude-->\r\n<div class="cnnStoryElementBox">\r\n<h4>Don''t Miss</h4>\r\n<ul class="cnnRelated">\r\n<li> <a href="http://edition.cnn.com/2009/WORLD/meast/06/11/iran.election.women/index.html">Election could be test for women''s rights</a> </li>\r\n<li> <a href="http://edition.cnn.com/2009/WORLD/meast/06/09/iran.election.debate/index.html">Political drama unfolds as election nears</a> </li>\r\n<li> <a href="http://edition.cnn.com/2009/WORLD/meast/06/08/ballen.iran/index.html">Commentary: Iranians favor U.S. peace deal</a> </li>\r\n<li> CNN.com Arabic: <a href="http://arabic.cnn.com/iran.2009/" target="new">Iran votes</a> </li>\r\n</ul>\r\n</div>\r\n<!--endclickprintexclude-->\r\n<p>The voter turnout has surpassed 80 percent, at least two officials said on Saturday.</p>\r\n<p>Iran''s Interior Minister Seyed Sadeq Mahsouli said 85 percent of the country''s 46 million eligible voters had gone to the polls Friday. Iran''s supreme leader, <a class="cnnInlineTopic" href="http://topics.edition.cnn.com/topics/ayatollah_ali_khamenei">Ayatollah Ali Khamenei</a>, lauded the "epic" event.</p>\r\n<p>"The 12 June election was an artistic expression of the nation, which created a new advancement in the history of elections in the country," Khamenei said.</p>\r\n<p>"The over 80 percent participation of the people and the 24 million votes cast for the president-elect is a real celebration which with the power of almighty God can guarantee the development, progress, national security, and the joy and excitement of the nation."</p>\r\n<!--startclickprintexclude-->\r\n<div class="cnnStoryElementBox">\r\n<h4>Candidate profiles</h4>\r\n<ul class="cnnRelated">\r\n<li> <a href="http://edition.cnn.com/2009/WORLD/meast/06/10/iran.ahmadinejad.profile/index.html">Mahmoud Ahmadinejad </a> </li>\r\n<li> <a href="http://edition.cnn.com/2009/WORLD/meast/06/11/iran.moussavi.profile/index.html">Mir Hossein Moussavi</a> </li>\r\n<li> <a href="http://edition.cnn.com/2009/WORLD/meast/06/11/iran.karrubi.profile/index.html">Mehdi Karrubi</a> </li>\r\n<li> <a href="http://edition.cnn.com/2009/WORLD/meast/06/11/iran.rezaie.profile/index.html">Mohsen Rezaie </a> </li>\r\n</ul>\r\n</div>\r\n<!--endclickprintexclude-->\r\n<p>Moussavi was the main challenger among three candidates who had been vying to replace Ahmadinejad.</p>\r\n<p>The others were former parliament speaker and reformist Mehdi Karrubi, and hard-liner Mohsen Rezaie, the former head of Iran''s Revolutionary Guards.</p>\r\n<p>Technology has been a key tactic in politically mobilizing young people in Iran, but text messaging has not been working in Iran over recent days. However, Iranian protesters still arrived en masse at meeting places around Tehran on Saturday morning. <span class="cnnEmbeddedMosLnk"><img src="http://i2.cdn.turner.com/cnn/.element/img/2.0/mosaic/tabs/video.gif" border="0" alt="Video" width="16" height="14" /> <a onclick="CNN_changeMosaicTab(''cnnVideoCmpnt'',''videos.html'',true,''/video/#/video/world/2009/06/10/barnett.iran.elections.online.cnn'');" href="http://edition.cnn.com/2009/WORLD/meast/06/13/iran.election/index.html#cnnSTCVideo">Watch CNN review the unprecedented online presence of candidates »</a></span></p>', NULL, NULL, NULL, NULL),
(7, 7, 1, 1244644568, 1244644568, 'Testovací článek na novém enginu', '<p>At the top of the main JavaScript file I defined variables for the total number of headlines, the interval between headlines, the current headline, and the "old" headline:</p>\r\n<pre> var headline_count;<br /> var headline_interval;<br /> var current_headline = 0;<br /> var old_headline = 0;<br /></pre>\r\n<p>Then I set the <code>headline_count</code> variable to be the number of <code>div</code> elements that have a class of "headline." But that number can''t be computed until the DOM is loaded, so I wrapped the variable in jQuery''s <code>$(document).ready()</code> function. I also wanted to set the first headline''s "top" property so that it would be immediately visible, while the others would remain hidden — at least initially — below the box (as set in the CSS):</p>\r\n<pre> $(document).ready(function(){<br />   headline_count = $("div.headline").size();<br />   $("div.headline:eq(" + current_headline + ")").css(''top'', ''5px'');<br /> });<br /></pre>\r\n<p>Before we move on, I''d like to point out a few things about the above code:</p>\r\n<ol>\r\n<li> jQuery has a <code>.size()</code> function that is similar to JavaScript''s <code>length</code> property in that it returns the number of jQuery objects defined by the <code>$()</code> that comes before it. </li>\r\n<li> The second line is only fired once, when the DOM is first loaded. Instead of setting <em>every</em> headline''s top to 5px, it uses a special jQuery DOM selector, <code>:eq()</code>, to set only the current headline''s top. Typically, <code>:eq()</code> would take a number, like so: <code>$("div.headline:eq(0)")</code>. </li>\r\n<li> I chose to pass in the "current_headline" variable instead of a number to allow for a little flexibility. If I later decide I want to start with the fourth headline, for example, I''d just have to change <code>var current_headline = 0</code> up at the top of the script to <code>var current_headline = 3</code>. In order to get the variable in there, though, I had to concatenate it with the rest of the selector before and after it.</li>\r\n</ol>\r\n<p style="text-align: center;"><a rel="lightbox" href="data/userfiles/0070.jpg"><img title="0070.jpg" src="data/userfiles/0070.jpg" alt="0070.jpg" width="237" height="162" /></a></p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Everybody Rotate!" href="http://docs.jquery.com/action/edit/Tutorials:Scroll_Up_Headline_Reader?section=4">edit</a>]</div>\r\n<p><a name="Everybody_Rotate.21"></a></p>\r\n<h2>Everybody Rotate!</h2>\r\n<p>Now that everything was in place, I could write my <code>headline_rotate()</code> function. I first needed to increment each headline by one until I reached the last one and then start over, creating a loop. To do so, I used what my friend Jonathan Chaffer told me is called "clock arithmetic." Here is what that looks like (at least, the way I did it):</p>\r\n<pre> function headline_rotate() {<br />   current_headline = (old_headline + 1) % headline_count;<br /> }<br /></pre>\r\n<p>Line 2 sets a new value for <code>current_headline</code> by first adding 1 to old_headline and then using the modulus operator (%) to get the remainder of <code>old_headline + 1</code> (our new headline) divided by <code>headline_count</code> (total number of headlines). Jonathan explained it this way: "the remainder will always equal <code>old_headline + 1</code> until it reaches <code>headline_count</code>, at which point the remainder becomes zero." The only thing better than having a genius working next to me is having a genius who is great at explaining things to mere mortals like me.</p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Add the Animation" href="http://docs.jquery.com/action/edit/Tutorials:Scroll_Up_Headline_Reader?section=5">edit</a>]</div>\r\n<p><a name="Add_the_Animation"></a></p>\r\n<h2>Add the Animation</h2>\r\n<p>Next I added the animation into the <code>headline_rotate()</code> function — moving the old headline up and out of sight while moving the next headline (now called <code>current_headline</code>) into view.</p>\r\n<p>The old headline movement actually has two parts: (a) scrolling up and out of sight and (b) moving instantly back down underneath the box so that it''s ready to slide up into the box again the next time. This is where jQuery''s "callbacks" come in very handy. I could queue the second effect by putting it in the callback of the first. Compare just the first effect...</p>\r\n<pre> $("div.headline:eq(" + old_headline + ")").animate({top: -205},"slow")<br /></pre>\r\n<p>...to the first, plus the second in the callback...</p>\r\n<pre> $("div.headline:eq(" + old_headline + ")").animate({top: -205},"slow", function(){<br />   $(this).css(''top'', ''210px'');<br /> });<br /></pre>\r\n<p>By the way, the -205 in <code>.animate({top: -205})</code> means that the top of the headline moves to 205 pixels above the top of its containing element (because the containing element had its position set to relative) so that we''re sure the entire headline clears the box.</p>\r\n<p>For the current headline, I simply moved its top up to 5 pixels below the top of the scrollup box so that it would be visible. And after that, I made <code>old_headline</code> equal <code>current_headline</code>:</p>\r\n<pre> $("div.headline:eq(" + current_headline + ")").animate({top: 5},"slow"); <br /> old_headline = current_headline;<br /></pre>\r\n<p>To get the function to run when the page loaded, I simply dropped <code>headline_rotate()</code> inside my <code>$(document).ready()</code>. Unfortunately, that only made the animation fire once. I wanted it to repeat.</p>', NULL, NULL, NULL, NULL),
(8, 7, 1, 1244646218, 1244795768, 'Testovací článek na novém enginu', '<h1>Part 1</h1>\r\n<p>I''m a bit funny when it comes to semantic markup. I really don''t like putting visual cues into the markup that I may want to change later. You know those little symbols that we put at the end or beginning of a sentence, that we sometimes no longer use as literary symbols.</p>\r\n<p>As an example, when I''m creating a form it pains me that I have to embed a colon into the markup at the end of the <code>label</code> text. We know why we do this, so that our <code>input</code> box has some kind of “stop” before it, making the view to the user just that little bit cleaner and easier on the eye. Then of course the client asks us if we can change the colon to a hyphen or a comma and we huff and puff because we''ve embedded these visual cues into the mark-up of all of our pages and doing a search and replace on a colon in an entire site can be a bit hit and miss at the best of times.</p>\r\n<p style="text-align: center;"><a rel="lightbox" href="data/userfiles/00701.jpg"><img title="00701.jpg" src="data/userfiles/00701.jpg" alt="00701.jpg" width="150" height="103" /></a></p>\r\n<p> </p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: CSS to the rescue?" href="http://docs.jquery.com/action/edit/Tutorials:jQuery_For_Designers?section=2">edit</a>]</div>\r\n<p><a name="CSS_to_the_rescue.3F"></a></p>\r\n<h3>CSS to the rescue?</h3>\r\n<p>Those thoughtful folks from the W3C have given us the lovely pseudo classes of “<code>:before</code>” and “<code>:after</code>” that we can use to append or prepend content before or after an element. But as you may know these really useful tools are not featured in Internet Explorer. So what next?</p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Try JavaScript?" href="http://docs.jquery.com/action/edit/Tutorials:jQuery_For_Designers?section=3">edit</a>]</div>\r\n<p><a name="Try_JavaScript.3F"></a></p>\r\n<h3>Try JavaScript?</h3>\r\n<p>Nope, I''m a designer. I don''t get it, it''s ugly, it''s not accessible, it''s bad for usability and it''ll bloat my page. Hmm.... OK.</p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Mythical widget" href="http://docs.jquery.com/action/edit/Tutorials:jQuery_For_Designers?section=4">edit</a>]</div>\r\n<p><a name="Mythical_widget"></a></p>\r\n<h3>Mythical widget</h3>\r\n<p>Let''s pretend for a minute that there''s this mythical widget thingy that we can put into our markup. It will do all sorts of clever things in a very easy manner without our having to be a programming wiz, and it won''t damage our nice clean markup. Let''s also assume that our mythical widget thingy looks like this:</p>\r\n<pre> <script title="http://jquery.com/src/jquery-latest.js" src="&lt;a class="><!--mce:0--></script><br /> <script type="text/javascript"><!--mce:1--></script><br /></pre>\r\n<p>OK, there''s no fooling you. It''s not mythical. <a class="external text" title="http://ok-cool.com/tutorials/jquery/test01.html" href="http://ok-cool.com/tutorials/jquery/test01.html">See the example here</a>. If you look at the source of the page you can see that there is no colon in the markup.</p>\r\n<p>Firstly we include the jQuery JavaScript remotely purely for convenience. For speed reasons, I wouldn''t recommend doing this in a runtime environment (it does mean that we are always using the latest version, though). Next we set up a function that looks for every <code>label</code> within every <code>dd</code> in the page and adds a colon inside the label at the end. This function doesn''t do anything until the page is “ready”, which is decided by the last bit of JavaScript before we close the <code>script</code> tag.</p>\r\n<p>What if we want to add a colon to the beginning of the element? We just use “prepend” instead of “append”, like so:</p>\r\n<pre> $("dd label").prepend(":");<br /></pre>\r\n<p>It couldn''t really be any simpler, and we haven''t even touched our beautiful markup.</p>\r\n<p>Of course we want this to be sitewide, so we just put this JavaScript into a separate file and include this in every page.</p>\r\n<pre> <script title="http://jquery.com/src/jquery-latest.js" src="&lt;a class="><!--mce:2--></script><br /> <script src="scripts/myjavascript.js"><!--mce:3--></script><br /></pre>\r\n<p>Now every page that has a <code>dd</code> element with a <code>label</code> will get a colon stuck on the end. When our client asks to change this we can do a very minor edit and the whole site is updated, just as it should be.</p>\r\n<p>The downside to this technique is that the user needs to have JavaScript enabled, as the majority of users do. It''s a risk that I''m prepared to take, and in this case it degrades quite nicely too.</p>\r\n<p>As you can see, the power of jQuery is pretty phenomenal, and we haven''t even scratched the surface. For more information on jQuery <a class="external text" title="http://jquery.com/" href="http://jquery.com/">take a look at the jQuery homepage</a>.</p>\r\n<p>In Part 2 we''ll look at a few more examples based on this technique. How about using an image instead of a colon? Or replacing the actual label text with an image? Or...?</p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Part 2" href="http://docs.jquery.com/action/edit/Tutorials:jQuery_For_Designers?section=5">edit</a>]</div>\r\n<p><a name="Part_2"></a></p>\r\n<h2>Part 2</h2>\r\n<p>In our first look at <a class="external text" title="http://jquery.com/" href="http://jquery.com/">jQuery</a> we established that we could use jQuery to “fix” some of the discrepancies of certain browsers’ handling of CSS. We used jQuery to simply append (or prepend) a little bit of information inside a tag, enabling us to make sitewide changes to certain visual cues very quickly and simply.</p>\r\n<p>With part 2 we are going in a little deeper. Most web sites these days tend to have “news” pages where they display a “title”, a “summary” and the main “body” of the article. To my mind this can sometimes lead to overwhelming amounts of text in your face. I don''t know about you, but if I''m presented with masses of text my brain shuts down and orders my fingers to click my mouse button away.</p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Wouldn''t it be nice…" href="http://docs.jquery.com/action/edit/Tutorials:jQuery_For_Designers?section=6">edit</a>]</div>\r\n<p><a name="Wouldn.27t_it_be_nice.E2.80.A6"></a></p>\r\n<h3>Wouldn''t it be nice…</h3>\r\n<p>Wouldn''t it be nice to be able to have a little button underneath each article that displayed the “body” when it was needed and allowed just the summaries for each article to be shown in the meantime? Of course it would, and using jQuery we can do this very quickly and simply — <a class="external text" title="http://www.ok-cool.com/tutorials/jquery/test02.html" href="http://www.ok-cool.com/tutorials/jquery/test02.html">a bit like this.</a></p>\r\n<div class="editsection" style="float: right; margin-left: 5px;">[<a title="Edit section: Diving in" href="http://docs.jquery.com/action/edit/Tutorials:jQuery_For_Designers?section=7">edit</a>]</div>\r\n<p><a name="Diving_in"></a></p>\r\n<h3>Diving in</h3>\r\n<p>Before we dive straight in, let''s think about our lovely, accessible, semantically strict XHTML code for a few minutes. Our page already displays the “title”, “summary” and “body”, so we need to <em>hide</em> the body and place a “button” under the body. If we just placed a “button” directly into the page and the user has JavaScript turned off we would have a button that did nothing — bad usability. Hmm.</p>', NULL, NULL, NULL, NULL),
(9, 7, 1, 1244795724, 1244913759, 'Testovací článek na novém enginu', '<p>“This is a bill not for a one-year or two-year splash, but for a long-term impact,” said Matthew L. Myers, president of the Campaign for Tobacco-Free Kids, a Washington advocacy group that took a lead in coordinating support for the legislation. The <a title="More information about Altria Group" href="http://topics.nytimes.com/top/news/business/companies/altria_group_inc/index.html?inline=nyt-org">Altria Group</a>, the parent company of Philip Morris, whose Marlboro brand helps make it the nation’s leading tobacco seller, endorsed the F.D.A. legislation and negotiated some of its crucial provisions with Congress.</p>\r\n<p>The <a title="More articles about Congressional Budget Office, U.S." href="http://topics.nytimes.com/top/reference/timestopics/organizations/c/congressional_budget_office/index.html?inline=nyt-org">Congressional Budget Office</a> had estimated that the F.D.A. legislation would reduce youth smoking by 11 percent and adult smoking by 2 percent over the next decade beyond the declines that had already resulted from public education, higher taxes and smoke-free indoor space laws.</p>\r\n<p>At least partly because of   such other efforts, <a title="In-depth reference and news articles about Smoking and smokeless tobacco." href="http://health.nytimes.com/health/guides/specialtopic/smoking-and-smokeless-tobacco/overview.html?inline=nyt-classifier">cigarette smoking</a> has declined measurably over the last decade: in 2005, about 21 percent of adults in the United States were smokers, compared with about 25 percent in 1995.</p>\r\n<p>Reynolds America and Lorillard, the second- and third-largest companies, opposed the legislation and criticized it as being intended to protect Philip Morris’s market dominance by restricting advertising and new products.</p>\r\n<p>But Brendan McCormick, a spokesman for Philip Morris’s parent, Altria, argued that previous marketing restrictions, like the television advertising ban imposed in 1971, had not frozen companies’ market shares. He said his company supported “federal regulation and the benefits it will bring to tobacco consumers and the greater predictability and stability we think it will bring to the tobacco industry.”</p>\r\n<p><img style="float: left; margin-right: 10px;" title="fotka-novinky.jpg_[eh7874].jpeg" src="data/userfiles/fotka-novinky.jpg_[eh7874].jpeg" alt="fotka-novinky.jpg_[eh7874].jpeg" width="241" height="58" />There are only minor differences between the Senate bill and the one the House passed in April — the main one involving the size of the graphic warnings on cigarette packs, which would be bigger under the Senate version.</p>\r\n<p><a title="More articles about Henry A. Waxman." href="http://topics.nytimes.com/top/reference/timestopics/people/w/henry_a_waxman/index.html?inline=nyt-per">Henry A. Waxman</a>, the California Democratic who was chief sponsor of the House bill, said in an interview that he hoped the House could simply pass the Senate version of the bill next week to send quickly to the President.</p>\r\n<p>“I would prefer we do that,” Mr. Waxman said, adding that it was still possible to call a conference committee instead to negotiate the minor differences. But that process, he said, could delay action and risk another Senate <a title="More articles about filibusters and debate curbs." href="http://topics.nytimes.com/top/reference/timestopics/subjects/f/filibusters_and_debate_curbs/index.html?inline=nyt-classifier">filibuster</a> of the type that was broken Monday in a crucial vote of 61 Senators, two more than needed to proceed to final action. That filibuster had been mounted by Richard M. Burr, Republican of North Carolina, the nation’s leading tobacco-growing state. Only one Democrat — <a title="More articles about Kay R. Hagan." href="http://topics.nytimes.com/top/reference/timestopics/people/h/kay_hagan/index.html?inline=nyt-per">Kay Hagan</a>, also of North Carolina — had voted to uphold the filibuster.</p>\r\n<p>On Tuesday, the Senate voted 60 to 36 against a substitute bill by Mr. Burr and Ms. Hagan to promote smokeless and other “reduced risk” products rather than strictly regulate all new tobacco products.</p>', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_categories`
--

DROP TABLE IF EXISTS `vypecky_categories`;
CREATE TABLE IF NOT EXISTS `vypecky_categories` (
  `id_category` smallint(3) NOT NULL auto_increment,
  `module` varchar(20) default NULL,
  `urlkey_cs` varchar(100) character set utf8 collate utf8_czech_ci NOT NULL,
  `label_cs` varchar(50) character set utf8 collate utf8_czech_ci default NULL,
  `alt_cs` varchar(200) character set utf8 collate utf8_czech_ci default NULL,
  `urlkey_en` varchar(100) default NULL,
  `label_en` varchar(50) default NULL,
  `alt_en` varchar(200) default NULL,
  `urlkey_de` varchar(100) default NULL,
  `label_de` varchar(50) default NULL,
  `alt_de` varchar(200) default NULL,
  `keywords_cs` varchar(200) character set utf8 collate utf8_czech_ci default NULL,
  `description_cs` varchar(500) character set utf8 collate utf8_czech_ci default NULL,
  `keywords_en` varchar(200) default NULL,
  `description_en` varchar(500) default NULL,
  `keywords_de` varchar(200) default NULL,
  `description_de` varchar(500) default NULL,
  `params` varchar(200) default NULL,
  `protected` tinyint(1) NOT NULL default '0',
  `priority` smallint(2) NOT NULL default '0',
  `active` tinyint(1) NOT NULL default '1' COMMENT 'je-li kategorie aktivní',
  `group_guest` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') NOT NULL,
  `group_user` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') NOT NULL,
  `group_admin` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') NOT NULL,
  `group_poweruser` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') NOT NULL,
  `left_panel` tinyint(1) NOT NULL default '1' COMMENT 'Je li zobrazen levý panel',
  `right_panel` tinyint(1) NOT NULL default '1' COMMENT 'Ja li zobrazen pravý panel',
  `sitemap_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL default 'yearly',
  `sitemap_priority` float NOT NULL default '0.1',
  `show_in_menu` tinyint(1) NOT NULL default '1' COMMENT 'Má li se položka zobrazit v menu',
  `show_when_login_only` tinyint(1) NOT NULL default '0' COMMENT 'Jstli má bát položka zobrazena po přihlášení',
  PRIMARY KEY  (`id_category`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

--
-- Vypisuji data pro tabulku `vypecky_categories`
--

INSERT INTO `vypecky_categories` (`id_category`, `module`, `urlkey_cs`, `label_cs`, `alt_cs`, `urlkey_en`, `label_en`, `alt_en`, `urlkey_de`, `label_de`, `alt_de`, `keywords_cs`, `description_cs`, `keywords_en`, `description_en`, `keywords_de`, `description_de`, `params`, `protected`, `priority`, `active`, `group_guest`, `group_user`, `group_admin`, `group_poweruser`, `left_panel`, `right_panel`, `sitemap_changefreq`, `sitemap_priority`, `show_in_menu`, `show_when_login_only`) VALUES
(1, 'text', 'text-s-obrazky-a-soubory', 'text s obrázky a soubory', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 10, 1, 'r--', 'r--', 'rwc', 'rw-', 1, 1, 'monthly', 0.8, 1, 0),
(12, 'text', 'text-druhy', 'Text Druhý', NULL, 'text-second', 'Text Second', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 'r--', 'r--', 'rwc', 'rw-', 1, 1, 'yearly', 0.1, 1, 0),
(13, 'pokus', 'pokus', 'testovací kategorie', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 1, 'r--', 'rw-', 'rwc', 'rwc', 1, 1, 'yearly', 0.1, 1, 0),
(14, 'categories', 'kategorie', 'kategorie', NULL, 'categories', 'categories', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, '---', '---', 'rwc', '---', 1, 1, 'never', 0.1, 1, 0),
(50, 'text', 'cesko-recke-behy', 'česko  řecké běhy', '', '', '', '', '', '', '', 'běhy slalom sport', 'Stránka s informacemi o běhání v česku a řecku', '', '', '', '', NULL, 0, 0, 1, 'r--', 'r--', 'rwc', 'r--', 1, 1, 'yearly', 0, 1, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_config`
--

DROP TABLE IF EXISTS `vypecky_config`;
CREATE TABLE IF NOT EXISTS `vypecky_config` (
  `id_config` smallint(5) unsigned NOT NULL auto_increment,
  `key` varchar(50) character set utf8 NOT NULL,
  `value` text character set utf8,
  `values` varchar(200) character set utf8 default NULL,
  PRIMARY KEY  (`id_config`),
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Vypisuji data pro tabulku `vypecky_config`
--

INSERT INTO `vypecky_config` (`id_config`, `key`, `value`, `values`) VALUES
(1, 'DEFAULT_ID_GROUP', '2', NULL),
(2, 'DEFAULT_GROUP_NAME', 'guest', NULL),
(3, 'DEFAULT_USER_NAME', 'anonym', NULL),
(4, 'APP_LANGS', 'cs;en;de', NULL),
(5, 'DEFAULT_APP_LANG', 'cs', NULL),
(6, 'IMAGES_DIR', 'images', NULL),
(7, 'IMAGES_LANGS_DIR', 'langs', NULL),
(8, 'DEBUG_LEVEL', '2', NULL),
(9, 'TEMPLATE_FACE', 'default', NULL),
(10, 'SITEMAP_PERIODE', 'weekly', NULL),
(11, 'SEARCH_RESULT_LENGHT', '300', NULL),
(12, 'SEARCH_HIGHLIGHT_TAG', 'strong', NULL),
(13, 'SESSION_NAME', 'vypecky_cookie', NULL),
(14, 'WEB_NAME', 'Vepřové Výpečky', NULL),
(15, 'CATEGORIES_STRUCTURE', 'O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:13:"Hlavní sekce";s:2:"en";N;s:2:"de";N;}s:20:"\0Menu_Sections\0level";i:0;s:17:"\0Menu_Sections\0id";i:51246421;s:23:"\0Menu_Sections\0idParent";N;s:24:"\0Menu_Sections\0childrens";a:4:{i:0;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:7:"Hlavní";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:1;s:17:"\0Menu_Sections\0id";i:523857685;s:23:"\0Menu_Sections\0idParent";i:51246421;s:24:"\0Menu_Sections\0childrens";a:0:{}}i:1;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:8:"Doplňky";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:1;s:17:"\0Menu_Sections\0id";i:2026422696;s:23:"\0Menu_Sections\0idParent";i:51246421;s:24:"\0Menu_Sections\0childrens";a:2:{i:0;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:9:"JsPluginy";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:2;s:17:"\0Menu_Sections\0id";i:1276501734;s:23:"\0Menu_Sections\0idParent";i:2026422696;s:24:"\0Menu_Sections\0childrens";a:0:{}}i:1;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:10:"Komponenty";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:2;s:17:"\0Menu_Sections\0id";i:2056888460;s:23:"\0Menu_Sections\0idParent";i:2026422696;s:24:"\0Menu_Sections\0childrens";a:0:{}}}}i:2;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:6:"Moduly";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:1;s:17:"\0Menu_Sections\0id";i:1378333988;s:23:"\0Menu_Sections\0idParent";i:51246421;s:24:"\0Menu_Sections\0childrens";a:3:{i:0;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:10:"Základní";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:2;s:17:"\0Menu_Sections\0id";i:144463462;s:23:"\0Menu_Sections\0idParent";i:1378333988;s:24:"\0Menu_Sections\0childrens";a:2:{i:0;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:19:"Textové a Znakové";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:3;s:17:"\0Menu_Sections\0id";i:553342086;s:23:"\0Menu_Sections\0idParent";i:144463462;s:24:"\0Menu_Sections\0childrens";a:1:{i:0;s:1:"1";}}i:1;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:9:"Grafické";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:3;s:17:"\0Menu_Sections\0id";i:198504839;s:23:"\0Menu_Sections\0idParent";i:144463462;s:24:"\0Menu_Sections\0childrens";a:0:{}}}}i:1;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:11:"Pokročilé";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:2;s:17:"\0Menu_Sections\0id";i:720806407;s:23:"\0Menu_Sections\0idParent";i:1378333988;s:24:"\0Menu_Sections\0childrens";a:1:{i:0;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:8:"Textové";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:3;s:17:"\0Menu_Sections\0id";i:1669142342;s:23:"\0Menu_Sections\0idParent";i:720806407;s:24:"\0Menu_Sections\0childrens";a:0:{}}}}i:2;O:13:"Menu_Sections":5:{s:21:"\0Menu_Sections\0labels";a:3:{s:2:"cs";s:14:"Konfigurační";s:2:"en";s:0:"";s:2:"de";s:0:"";}s:20:"\0Menu_Sections\0level";i:2;s:17:"\0Menu_Sections\0id";i:780317735;s:23:"\0Menu_Sections\0idParent";i:1378333988;s:24:"\0Menu_Sections\0childrens";a:0:{}}}}i:3;s:2:"12";}}', NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_contacts`
--

DROP TABLE IF EXISTS `vypecky_contacts`;
CREATE TABLE IF NOT EXISTS `vypecky_contacts` (
  `id_contact` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_city` smallint(5) unsigned NOT NULL,
  `name_cs` varchar(300) default NULL,
  `text_cs` text,
  `name_en` varchar(300) default NULL,
  `text_en` text,
  `name_de` varchar(300) default NULL,
  `text_de` text,
  `file` varchar(200) default NULL,
  `changed_time` int(11) default NULL,
  PRIMARY KEY  (`id_contact`),
  KEY `id_item` (`id_item`),
  FULLTEXT KEY `name_cs` (`name_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `name_en` (`name_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `name_de` (`name_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_contacts`
--

INSERT INTO `vypecky_contacts` (`id_contact`, `id_item`, `id_city`, `name_cs`, `text_cs`, `name_en`, `text_en`, `name_de`, `text_de`, `file`, `changed_time`) VALUES
(2, 8, 203, 'Prodejna a sklad, centrála společnosti', '<p>Telefon: 571 611 801, 571 618 970<br />Mobil:739 619 605<br /> Fax: 571 611 801</p>\r\n<p> </p>\r\n<p>E-mail: <a href="mailto:belocky@moravaokno.cz">belocky@moravaokno.cz</a><br /> WWW: <a href="http://www.moravaokno.cz/" target="_blank">www.moravaokno.cz</a></p>\r\n<p> </p>\r\n<p>Ulice: Kolaříkova 1438 (areál bývalých kasáren)<br /> Město: Valašské Meziříčí<br /> PSČ: 757 01</p>', NULL, NULL, NULL, NULL, 'budova-milenium-center-s-parkovacim-domem.jpg', 1239209498),
(4, 8, 21, 'Prodejna', '<p>Telefon: 571 611 801, 571 618 970,Mobil:739 619 605<br /> Fax: 571 611 801<br /> E-mail: <a href="mailto:belocky@moravaokno.cz">belocky@moravaokno.cz</a><br /> WWW: <a href="http://www.moravaokno.cz/" target="_blank">www.moravaokno.cz</a><br /> Ulice: Kolaříkova 1438 (areál bývalých kasáren)<br /> Město: Valašské Meziříčí<br /> PSČ: 757 01</p>', NULL, NULL, NULL, NULL, 'budova-milenium-center-s-parkovacim-domem2.jpg', 1239207032),
(5, 8, 100, 'Prodejna a sklad', '<p>Telefon: 571 611 801, 571 618 970,Mobil:739 619 605<br /> Fax: 571 611 801<br /> E-mail: <a href="mailto:belocky@moravaokno.cz">belocky@moravaokno.cz</a><br /> WWW: <a href="http://www.moravaokno.cz/" target="_blank">www.moravaokno.cz</a><br /> Ulice: Kolaříkova 1438 (areál bývalých kasáren)<br /> Město: Valašské Meziříčí<br /> PSČ: 757 01</p>', NULL, NULL, NULL, NULL, 'budova-milenium-center-s-parkovacim-domem3.jpg', 1239207080);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_contacts_areas`
--

DROP TABLE IF EXISTS `vypecky_contacts_areas`;
CREATE TABLE IF NOT EXISTS `vypecky_contacts_areas` (
  `id_area` int(11) NOT NULL auto_increment,
  `area_name` varchar(200) NOT NULL,
  PRIMARY KEY  (`id_area`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65536 ;

--
-- Vypisuji data pro tabulku `vypecky_contacts_areas`
--

INSERT INTO `vypecky_contacts_areas` (`id_area`, `area_name`) VALUES
(1, 'Hlavní město Praha'),
(2, 'Jihočeský kraj'),
(3, 'Jihomoravský kraj'),
(4, 'Karlovarský kraj'),
(5, 'Královéhradecký kraj'),
(6, 'Liberecký kraj'),
(7, 'Moravskoslezský kraj'),
(8, 'Olomoucký kraj'),
(9, 'Pardubický kraj'),
(10, 'Plzeňský kraj'),
(11, 'Středočeský kraj'),
(12, 'Ústecký kraj'),
(13, 'Vysočina'),
(14, 'Zlínský kraj'),
(65535, 'Nezařazeno');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_contacts_cities`
--

DROP TABLE IF EXISTS `vypecky_contacts_cities`;
CREATE TABLE IF NOT EXISTS `vypecky_contacts_cities` (
  `id_city` int(11) NOT NULL auto_increment,
  `id_area` int(11) NOT NULL,
  `city_name` varchar(200) NOT NULL,
  PRIMARY KEY  (`id_city`),
  KEY `id_area` (`id_area`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65536 ;

--
-- Vypisuji data pro tabulku `vypecky_contacts_cities`
--

INSERT INTO `vypecky_contacts_cities` (`id_city`, `id_area`, `city_name`) VALUES
(1, 1, 'Praha'),
(2, 2, 'Blatná'),
(3, 2, 'České Budějovice'),
(4, 2, 'Český Krumlov'),
(5, 2, 'Dačice'),
(6, 2, 'Jindřichův Hradec'),
(7, 2, 'Kaplice'),
(8, 2, 'Milevsko'),
(9, 2, 'Písek'),
(10, 2, 'Prachatice'),
(11, 2, 'Soběslav'),
(12, 2, 'Strakonice'),
(13, 2, 'Tábor'),
(14, 2, 'Trhové Sviny'),
(15, 2, 'Třeboň'),
(16, 2, 'Týn nad Vltavou'),
(17, 2, 'Vimperk'),
(18, 2, 'Vodňany'),
(19, 3, 'Blansko'),
(20, 3, 'Boskovice'),
(21, 3, 'Brno'),
(22, 3, 'Břeclav'),
(23, 3, 'Bučovice'),
(24, 3, 'Hodonín'),
(25, 3, 'Hustopeče'),
(26, 3, 'Ivančice'),
(27, 3, 'Kuřim'),
(28, 3, 'Kyjov'),
(29, 3, 'Mikulov'),
(30, 3, 'Moravský Krumlov'),
(31, 3, 'Pohořelice'),
(32, 3, 'Rosice'),
(33, 3, 'Slavkov u Brna'),
(34, 3, 'Šlapanice'),
(35, 3, 'Tišnov'),
(36, 3, 'Veselí nad Moravou'),
(37, 3, 'Vyškov'),
(38, 3, 'Znojmo'),
(39, 3, 'Židlochovice'),
(40, 4, 'Aš'),
(41, 4, 'Cheb'),
(42, 4, 'Karlovy Vary'),
(43, 4, 'Kraslice'),
(44, 4, 'Mariánské Lázně'),
(45, 4, 'Ostrov'),
(46, 4, 'Sokolov'),
(47, 5, 'Broumov'),
(48, 5, 'Dobruška'),
(49, 5, 'Dvůr Králové nad Labem'),
(50, 5, 'Hořice'),
(51, 5, 'Hradec Králové'),
(52, 5, 'Jaroměř'),
(53, 5, 'Jičín'),
(54, 5, 'Kostelec nad Orlicí'),
(55, 5, 'Náchod'),
(56, 5, 'Nová Paka'),
(57, 5, 'Nové Město nad Metují'),
(58, 5, 'Nový Bydžov'),
(59, 5, 'Rychnov nad Kněžnou'),
(60, 5, 'Trutnov'),
(61, 5, 'Vrchlabí'),
(62, 6, 'Česká Lípa'),
(63, 6, 'Frýdlant'),
(64, 6, 'Jablonec nad Nisou'),
(65, 6, 'Jilemnice'),
(66, 6, 'Liberec'),
(67, 6, 'Nový Bor'),
(68, 6, 'Semily'),
(69, 6, 'Tanvald'),
(70, 6, 'Turnov'),
(71, 6, 'Železný Brod'),
(72, 7, 'Bílovec'),
(73, 7, 'Bohumín'),
(74, 7, 'Bruntál'),
(75, 7, 'Český Těšín'),
(76, 7, 'Frenštát pod Radhoštěm'),
(77, 7, 'Frýdek-Místek'),
(78, 7, 'Frýdlant nad Ostravicí'),
(79, 7, 'Havířov'),
(80, 7, 'Hlučín'),
(81, 7, 'Jablunkov'),
(82, 7, 'Karviná'),
(83, 7, 'Kopřivnice'),
(84, 7, 'Kravaře'),
(85, 7, 'Krnov'),
(86, 7, 'Nový Jičín'),
(87, 7, 'Odry'),
(88, 7, 'Opava'),
(89, 7, 'Orlová'),
(90, 7, 'Ostrava'),
(91, 7, 'Rýmařov'),
(92, 7, 'Třinec'),
(93, 7, 'Vítkov'),
(94, 8, 'Hranice'),
(95, 8, 'Jeseník'),
(96, 8, 'Konice'),
(97, 8, 'Lipník nad Bečvou'),
(98, 8, 'Litovel'),
(99, 8, 'Mohelnice'),
(100, 8, 'Olomouc'),
(101, 8, 'Prostějov'),
(102, 8, 'Přerov'),
(103, 8, 'Šternberk'),
(104, 8, 'Šumperk'),
(105, 8, 'Uničov'),
(106, 8, 'Zábřeh'),
(107, 9, 'Česká Třebová'),
(108, 9, 'Hlinsko'),
(109, 9, 'Holice'),
(110, 9, 'Chrudim'),
(111, 9, 'Králíky'),
(112, 9, 'Lanškroun'),
(113, 9, 'Litomyšl'),
(114, 9, 'Moravská Třebová'),
(115, 9, 'Pardubice'),
(116, 9, 'Polička'),
(117, 9, 'Přelouč'),
(118, 9, 'Svitavy'),
(119, 9, 'Ústí nad Orlicí'),
(120, 9, 'Vysoké Mýto'),
(121, 9, 'Žamberk'),
(122, 10, 'Blovice'),
(123, 10, 'Domažlice'),
(124, 10, 'Horažďovice'),
(125, 10, 'Horšovský Týn'),
(126, 10, 'Klatovy'),
(127, 10, 'Kralovice'),
(128, 10, 'Nepomuk'),
(129, 10, 'Nýřany'),
(130, 10, 'Plzeň'),
(131, 10, 'Přeštice'),
(132, 10, 'Rokycany'),
(133, 10, 'Stod'),
(134, 10, 'Stříbro'),
(135, 10, 'Sušice'),
(136, 10, 'Tachov'),
(137, 11, 'Benešov'),
(138, 11, 'Beroun'),
(139, 11, 'Brandýs nad Labem-Stará Boleslav'),
(140, 11, 'Čáslav'),
(141, 11, 'Černošice'),
(142, 11, 'Český Brod'),
(143, 11, 'Dobříš'),
(144, 11, 'Hořovice'),
(145, 11, 'Kladno'),
(146, 11, 'Kolín'),
(147, 11, 'Kralupy nad Vltavou'),
(148, 11, 'Kutná Hora'),
(149, 11, 'Lysá nad Labem'),
(150, 11, 'Mělník'),
(151, 11, 'Mladá Boleslav'),
(152, 11, 'Mnichovo Hradiště'),
(153, 11, 'Neratovice'),
(154, 11, 'Nymburk'),
(155, 11, 'Poděbrady'),
(156, 11, 'Příbram'),
(157, 11, 'Rakovník'),
(158, 11, 'Říčany'),
(159, 11, 'Sedlčany'),
(160, 11, 'Slaný'),
(161, 11, 'Vlašim'),
(162, 11, 'Votice'),
(163, 12, 'Bílina'),
(164, 12, 'Děčín'),
(165, 12, 'Chomutov'),
(166, 12, 'Kadaň'),
(167, 12, 'Litoměřice'),
(168, 12, 'Litvínov'),
(169, 12, 'Louny'),
(170, 12, 'Lovosice'),
(171, 12, 'Most'),
(172, 12, 'Podbořany'),
(173, 12, 'Roudnice nad Labem'),
(174, 12, 'Rumburk'),
(175, 12, 'Teplice'),
(176, 12, 'Ústí nad Labem'),
(177, 12, 'Varnsdorf'),
(178, 12, 'Žatec'),
(179, 13, 'Bystřice nad Pernštejnem'),
(180, 13, 'Havlíčkův Brod'),
(181, 13, 'Humpolec'),
(182, 13, 'Chotěboř'),
(183, 13, 'Jihlava'),
(184, 13, 'Moravské Budějovice'),
(185, 13, 'Náměšť nad Oslavou'),
(186, 13, 'Nové Město na Moravě'),
(187, 13, 'Pacov'),
(188, 13, 'Pelhřimov'),
(189, 13, 'Světlá nad Sázavou'),
(190, 13, 'Telč'),
(191, 13, 'Třebíč'),
(192, 13, 'Velké Meziříčí'),
(193, 13, 'Žďár nad Sázavou'),
(194, 14, 'Bystřice pod Hostýnem'),
(195, 14, 'Holešov'),
(196, 14, 'Kroměříž'),
(197, 14, 'Luhačovice'),
(198, 14, 'Otrokovice'),
(199, 14, 'Rožnov pod Radhoštěm'),
(200, 14, 'Uherské Hradiště'),
(201, 14, 'Uherský Brod'),
(202, 14, 'Valašské Klobouky'),
(203, 14, 'Valašské Meziříčí'),
(204, 14, 'Vizovice'),
(205, 14, 'Vsetín'),
(206, 14, 'Zlín'),
(65535, 65535, 'Nezařazeno');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_eplugin_sendmails`
--

DROP TABLE IF EXISTS `vypecky_eplugin_sendmails`;
CREATE TABLE IF NOT EXISTS `vypecky_eplugin_sendmails` (
  `id_mail` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(5) unsigned default NULL,
  `mail` varchar(200) NOT NULL,
  PRIMARY KEY  (`id_mail`),
  KEY `id_item` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Vypisuji data pro tabulku `vypecky_eplugin_sendmails`
--

INSERT INTO `vypecky_eplugin_sendmails` (`id_mail`, `id_item`, `id_article`, `mail`) VALUES
(1, 9, 0, 'jakubmatas@gmail.com'),
(2, 9, 0, 'cuba@vypecky.info');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_eplugin_sendmailstexts`
--

DROP TABLE IF EXISTS `vypecky_eplugin_sendmailstexts`;
CREATE TABLE IF NOT EXISTS `vypecky_eplugin_sendmailstexts` (
  `id_text` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(5) unsigned default NULL,
  `subject` varchar(500) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY  (`id_text`),
  KEY `id_item` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Vypisuji data pro tabulku `vypecky_eplugin_sendmailstexts`
--

INSERT INTO `vypecky_eplugin_sendmailstexts` (`id_text`, `id_item`, `id_article`, `subject`, `text`) VALUES
(1, 9, NULL, 'Předmět emalu', 'Text mailu %pokus%.\r\n\r\npočet znaků je: %pocet%/%sudy[sudý/lichý]%\r\n\r\npočet znaků je: %pocet%/%sudy[sudý/lichý]%');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_groups`
--

DROP TABLE IF EXISTS `vypecky_groups`;
CREATE TABLE IF NOT EXISTS `vypecky_groups` (
  `id_group` smallint(3) unsigned NOT NULL auto_increment COMMENT 'ID skupiny',
  `name` varchar(15) default NULL COMMENT 'Nazev skupiny',
  `label` varchar(100) default NULL,
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
(4, 'poweruser', 'uživatel s většími právy', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_items`
--

DROP TABLE IF EXISTS `vypecky_items`;
CREATE TABLE IF NOT EXISTS `vypecky_items` (
  `id_item` smallint(6) NOT NULL auto_increment,
  `label_cs` varchar(100) default NULL,
  `alt_cs` varchar(500) default NULL,
  `label_en` varchar(100) default NULL,
  `alt_en` varchar(500) default NULL,
  `label_de` varchar(100) default NULL,
  `alt_de` varchar(500) default NULL,
  `group_admin` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `group_user` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rw-',
  `group_guest` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'r--',
  `group_poweruser` enum('r--','rw-','rwc','r-c','-wc','--c','-w-','---') default 'rwc',
  `params` varchar(500) default NULL COMMENT 'parametry pro daný modul itemu - jsouv popsány v docs',
  `priority` smallint(6) NOT NULL default '0',
  `id_category` smallint(6) NOT NULL,
  `id_module` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id_item`),
  KEY `id_category` (`id_category`),
  KEY `id_module` (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Vypisuji data pro tabulku `vypecky_items`
--

INSERT INTO `vypecky_items` (`id_item`, `label_cs`, `alt_cs`, `label_en`, `alt_en`, `label_de`, `alt_de`, `group_admin`, `group_user`, `group_guest`, `group_poweruser`, `params`, `priority`, `id_category`, `id_module`) VALUES
(1, 'text s obrázky a soubory', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=true;theme=advanced', 0, 1, 1),
(2, 'text pouze s obrázky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'files=true;images=false;theme=simple', 0, 2, 1),
(3, 'Novinky', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10;scrollpanel=2', 0, 3, 2),
(4, 'Login', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 4, 4),
(5, 'text s obrázky a soubory - FULL', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 5, 1),
(6, 'Reference', NULL, 'References', NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'width=800;height=600;smallwidth=200;smallheight=150', 0, 6, 19),
(7, 'Články', NULL, 'Articles', NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=2', 0, 7, 20),
(8, 'Kontakty', NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 8, 21),
(9, NULL, NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', NULL, 0, 9, 7),
(10, NULL, NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10', 0, 10, 22),
(11, NULL, NULL, NULL, NULL, NULL, NULL, 'rwc', 'rw-', 'r--', 'rwc', 'scroll=10;scrollpanel=1', 0, 11, 24);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_modules`
--

DROP TABLE IF EXISTS `vypecky_modules`;
CREATE TABLE IF NOT EXISTS `vypecky_modules` (
  `id_module` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `mparams` varchar(100) default NULL,
  `datadir` varchar(100) default NULL,
  `dbtable1` varchar(50) default NULL,
  `dbtable2` varchar(50) default NULL,
  `dbtable3` varchar(50) default NULL,
  PRIMARY KEY  (`id_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Vypisuji data pro tabulku `vypecky_modules`
--

INSERT INTO `vypecky_modules` (`id_module`, `name`, `mparams`, `datadir`, `dbtable1`, `dbtable2`, `dbtable3`) VALUES
(1, 'text', NULL, NULL, 'texts', NULL, NULL),
(2, 'news', NULL, NULL, 'news', NULL, NULL),
(3, 'categories', NULL, NULL, NULL, NULL, NULL),
(4, 'login', NULL, NULL, 'users', NULL, NULL),
(5, 'minigalery', NULL, 'minigalery', 'minigalery', NULL, NULL),
(6, 'workers', NULL, 'workers', 'workers', NULL, NULL),
(7, 'pokus', NULL, '', '', NULL, NULL),
(8, 'photogalerymax', 'photosingalerylist=3', 'photogalery', 'photos', 'photo_galeries', 'photo_sections'),
(9, 'guestbook', NULL, '', 'guestbook', NULL, NULL),
(10, 'iframe', NULL, NULL, 'iframe_targets', NULL, NULL),
(11, 'blog', NULL, NULL, 'blogs', 'blogs_sections', NULL),
(12, 'flashpage', '', 'flashpages', NULL, NULL, NULL),
(13, 'comics', NULL, 'comics', 'comics', NULL, NULL),
(14, 'links', NULL, 'links', 'link_sections', NULL, NULL),
(15, 'errors', NULL, NULL, 'errors', NULL, NULL),
(16, 'partners', NULL, 'partners', 'partners', NULL, NULL),
(17, 'users', NULL, 'users', 'users', 'groups', NULL),
(18, 'photogalery', NULL, 'photogalery', 'photogalery_galeries', 'photogalery_photos', NULL),
(19, 'references', NULL, 'references', 'references', 'texts', NULL),
(20, 'articles', NULL, NULL, 'articles', NULL, NULL),
(21, 'contacts', NULL, 'contacts', 'contacts', 'contacts_areas', 'contacts_cities'),
(22, 'products', NULL, 'products', 'products', 'products_documents', 'products_photos'),
(24, 'actions', NULL, 'actions', 'actions', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_news`
--

DROP TABLE IF EXISTS `vypecky_news`;
CREATE TABLE IF NOT EXISTS `vypecky_news` (
  `id_new` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
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
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `label_de` (`label_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Vypisuji data pro tabulku `vypecky_news`
--

INSERT INTO `vypecky_news` (`id_new`, `id_item`, `id_user`, `label_cs`, `text_cs`, `label_en`, `text_en`, `label_de`, `text_de`, `time`, `deleted`) VALUES
(7, 3, 3, 'Novinky (Jitrničky) na Výpečkách', '<p>Tak první novinka na <a href="http://www.vypecky.info">Výpečkách</a> je\r\nvlasně zavedení <strong>novinek</strong>, kde můžete psát krátké novinky.\r\nTak hodně zdaru! :-D (těch novinek je tu až moc :-D )</p>\r\n\r\n<!-- by Texy2! -->', NULL, NULL, NULL, NULL, 1210695435, 0),
(10, 3, 3, 'Upravený layout a je ještě LEPŠÍ!', 'Konečně jsem si našel trochu času a upravil layout výpeček. Teď by se\r\nměl korektně zobrazovat v FF a Opeře, jenom v IE zůstává pár chybiček.\r\nCelý mám (částečně) měnitelnou šířku takže potěším lidi co\r\nnepoužívají velké rozlišení.\r\n', 'Better layout', NULL, NULL, NULL, 1211050696, 0),
(33, 3, 3, 'Další novinka', 'ggfd gdfg fdg df', NULL, NULL, NULL, NULL, 1237116725, 1),
(19, 3, 3, 'Novinka v češtině s popisem', 'česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky česky ', 'English news with label', 'english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english english ', NULL, NULL, 1218921753, 0),
(20, 3, 3, 'label', '&lt;b&gt;text&lt;/b&gt;', NULL, NULL, NULL, NULL, 1218975043, 1),
(21, 3, 3, 'cs label nový pěkný', 'fdsafsdafasfd', NULL, NULL, NULL, NULL, 1218979456, 1),
(22, 3, 3, 'Popis novinky', 'pokusný text', 'dsadasda', 'English news', NULL, NULL, 1222705858, 0),
(23, 3, 3, 'Zcela nová novinka a uprava', 'Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky Text nové novinky ', 'Realy new News', 'News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text News text ', NULL, NULL, 1222705915, 0),
(24, 3, 3, 'Nová novinka', '&lt;b&gt;tučně&lt;/b&gt;', NULL, NULL, NULL, NULL, 1222705983, 1),
(25, 3, 3, 'pes', 'tak tohle je novinka o psu', NULL, NULL, NULL, NULL, 1222706254, 1),
(26, 3, 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232204814, 1),
(27, 3, 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232204880, 1),
(28, 3, 3, '&lt;input /&gt;jakubmatas@gmail.com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232205108, 1),
(29, 3, 3, 'jakubmatas_gmail_com', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus eu ligula. Maecenas tristique, turpis ac interdum feugiat, ligula nibh mattis erat, eu mattis felis lacus vel leo. Morbi vehicula sapien vitae lectus. Aliquam sit amet ipsum. Quisque sit amet neque. Sed ornare orci eget orci. Aenean scelerisque. Vivamus mauris magna, adipiscing eget, imperdiet eget, placerat non, quam. Praesent eget enim vitae pede rutrum auctor. Maecenas dictum purus. Nunc convallis, nulla id consectetur lacinia', NULL, NULL, NULL, NULL, 1232205120, 0),
(30, 3, 3, 'Upravený popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232206214, 0),
(31, 3, 3, 'Nový popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232220144, 0),
(32, 3, 3, 'Nový popis novinky', 'Lorem Ipsum je demonstrativní výplňový text používaný v tiskařském a knihařském průmyslu. Lorem Ipsum je považováno za standard v této oblasti už od začátku 16. století, kdy dnes neznámý tiskař vzal kusy textu a na jejich základě vytvořil speciální vzorovou knihu. Jeho odkaz nevydržel pouze pět století, on přežil i nástup elektronické sazby v podstatě beze změny. Nejvíce popularizováno bylo Lorem Ipsum v šedesátých letech 20. století, kdy byly vydávány speciální vzorníky s jeho pasážemi a pozděj', NULL, NULL, NULL, NULL, 1232220169, 0),
(34, 3, 1, 'Zcela nová novinka pro publikum', '&amp;lt;p&amp;gt;&amp;lt;strong&amp;gt;ROME, Italy (CNN) &amp;lt;/strong&amp;gt; -- American student Amanda Knox was on the stand Saturday for a second day, this time facing questions from the public prosecutor in her trial on charges of murdering her housemate about two years ago.&amp;lt;/p&amp;gt;', NULL, NULL, NULL, NULL, 1244917651, 0),
(35, 3, 1, 'Zcela nová novinka pro publikum', '&amp;lt;p&amp;gt;&amp;lt;strong&amp;gt;ROME, Italy (CNN) &amp;lt;/strong&amp;gt; -- American student Amanda Knox was on the stand Saturday for a second day, this time facing questions from the public prosecutor in her trial on charges of murdering her housemate about two years ago.&amp;lt;/p&amp;gt;', NULL, NULL, NULL, NULL, 1244918038, 0),
(36, 3, 1, 'Zcela nová novinka pro publikum', '<p><strong>ROME, Italy (CNN)</strong> -- American student Amanda Knox was on the stand Saturday for a second day, this time facing questions from the public prosecutor in her trial on charges of murdering her housemate about two years ago.</p>', NULL, NULL, NULL, NULL, 1244918107, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_panels`
--

DROP TABLE IF EXISTS `vypecky_panels`;
CREATE TABLE IF NOT EXISTS `vypecky_panels` (
  `id_panel` smallint(3) NOT NULL auto_increment,
  `priority` smallint(2) NOT NULL default '0',
  `label` varchar(30) NOT NULL,
  `id_item` smallint(5) unsigned default NULL,
  `position` enum('left','right') NOT NULL default 'left',
  `enable` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_panel`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Vypisuji data pro tabulku `vypecky_panels`
--

INSERT INTO `vypecky_panels` (`id_panel`, `priority`, `label`, `id_item`, `position`, `enable`) VALUES
(8, 60, 'NovinkyKyy', 3, 'left', 1),
(9, 0, 'dsadsadas', 3, 'right', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_galeries`
--

DROP TABLE IF EXISTS `vypecky_photogalery_galeries`;
CREATE TABLE IF NOT EXISTS `vypecky_photogalery_galeries` (
  `id_galery` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(6) NOT NULL,
  `label_cs` varchar(200) default NULL,
  `text_cs` varchar(1000) default NULL,
  `label_en` varchar(200) default NULL,
  `text_en` varchar(1000) default NULL,
  `label_de` varchar(200) default NULL,
  `text_de` varchar(1000) default NULL,
  `time_add` int(11) default NULL,
  `time_edit` int(11) default NULL,
  PRIMARY KEY  (`id_galery`),
  KEY `id_item` (`id_item`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

--
-- Vypisuji data pro tabulku `vypecky_photogalery_galeries`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_photogalery_photos`
--

DROP TABLE IF EXISTS `vypecky_photogalery_photos`;
CREATE TABLE IF NOT EXISTS `vypecky_photogalery_photos` (
  `id_photo` smallint(5) unsigned NOT NULL auto_increment,
  `id_galery` smallint(5) unsigned NOT NULL,
  `label_cs` varchar(500) default NULL,
  `label_en` varchar(500) default NULL,
  `label_de` varchar(500) default NULL,
  `file` varchar(200) NOT NULL,
  `time_add` int(11) default NULL,
  PRIMARY KEY  (`id_photo`),
  KEY `id_galery` (`id_galery`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;

--
-- Vypisuji data pro tabulku `vypecky_photogalery_photos`
--


-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_products`
--

DROP TABLE IF EXISTS `vypecky_products`;
CREATE TABLE IF NOT EXISTS `vypecky_products` (
  `id_product` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_user` smallint(5) unsigned default '1',
  `add_time` int(11) NOT NULL,
  `edit_time` int(11) default NULL,
  `label_cs` varchar(400) default NULL,
  `text_cs` text,
  `label_en` varchar(400) default NULL,
  `text_en` text,
  `lebal_de` varchar(400) default NULL,
  `text_de` text,
  `main_image` varchar(200) default NULL,
  PRIMARY KEY  (`id_product`),
  KEY `id_item` (`id_item`,`id_user`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `lebal_de` (`lebal_de`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Vypisuji data pro tabulku `vypecky_products`
--

INSERT INTO `vypecky_products` (`id_product`, `id_item`, `id_user`, `add_time`, `edit_time`, `label_cs`, `text_cs`, `label_en`, `text_en`, `lebal_de`, `text_de`, `main_image`) VALUES
(2, 10, 1, 1239816255, 1239964857, 'pokus', '<p>Pokusný text produktu</p>\r\n<p> </p>\r\n<p class="para">Instead of lots of commands to output HTML (as seen in C or Perl),     PHP pages contain HTML with embedded code that does     "something" (in this case, output "Hi, I\\\\\\\\\\\\\\''m a PHP script!").     The PHP code is enclosed in special <a class="link" href="file:////home/cuba/Docs/PHP/html/language.basic-syntax.phpmode.html">start and end processing     instructions <code class="code"> and <code class="code">?&gt;</code></code></a> that allow you to jump into and out of "PHP mode."</p>\r\n<p class="para">What distinguishes PHP from something like client-side JavaScript     is that the code is executed on the server, generating HTML which     is then sent to the client. The client would receive     the results of running that script, but would not know     what the underlying code was. You can even configure your web server     to process all your HTML files with PHP, and then there\\\\\\\\\\\\\\''s really no     way that users can tell what you have up your sleeve.</p>\r\n<p class="para">The best things in using PHP are that it is extremely simple     for a newcomer, but offers many advanced features for     a professional programmer. Don\\\\\\\\\\\\\\''t be afraid reading the long     list of PHP\\\\\\\\\\\\\\''s features. You can jump in, in a short time, and     start writing simple scripts in a few hours.</p>\r\n<p class="para">Although PHP\\\\\\\\\\\\\\''s development is focused on server-side scripting,     you can do much more with it. Read on, and see more in the     <a class="link" href="file:////home/cuba/Docs/PHP/html/intro-whatcando.html">What can PHP do?</a> section,     or go right to the <a class="link" href="file:////home/cuba/Docs/PHP/html/tutorial.html">introductory     tutorial</a> if you are only interested in web programming.</p>', NULL, NULL, NULL, NULL, 'okno-titul-stitulkem.jpg'),
(3, 10, 1, 1239817269, 1239817269, 'Dveře k oknům', '<p class="para">Instead of lots of commands to output HTML (as seen in C or Perl),     PHP pages contain HTML with embedded code that does     "something" (in this case, output "Hi, I\\''m a PHP script!").     The PHP code is enclosed in special <a class="link" href="file:////home/cuba/Docs/PHP/html/language.basic-syntax.phpmode.html">start and end processing     instructions <code class="code">&lt;?php</code> and <code class="code">?&gt;</code></a> that allow you to jump into and out of "PHP mode."</p>\r\n<p class="para">What distinguishes PHP from something like client-side JavaScript     is that the code is executed on the server, generating HTML which     is then sent to the client. The client would receive     the results of running that script, but would not know     what the underlying code was. You can even configure your web server     to process all your HTML files with PHP, and then there\\''s really no     way that users can tell what you have up your sleeve.</p>\r\n<p class="para">The best things in using PHP are that it is extremely simple     for a newcomer, but offers many advanced features for     a professional programmer. Don\\''t be afraid reading the long     list of PHP\\''s features. You can jump in, in a short time, and     start writing simple scripts in a few hours.</p>\r\n<p class="para">Although PHP\\''s development is focused on server-side scripting,     you can do much more with it. Read on, and see more in the     <a class="link" href="file:////home/cuba/Docs/PHP/html/intro-whatcando.html">What can PHP do?</a> section,     or go right to the <a class="link" href="file:////home/cuba/Docs/PHP/html/tutorial.html">introductory     tutorial</a> if you are only interested in web programming.</p>', NULL, NULL, NULL, NULL, 'madagaskar-2-1226738485.jpg'),
(4, 10, 1, 1239875036, 1239965458, 'Dveře k okýnkům', '<p>Málokterý element má při návrhu fasády takovou důležitost, jako vchodové dveře. Dveře by měly být vizitkou každého domu a zároveň zárukou maximální bezpečnosti a trvanlivosti. <br /> <br />Široká nabídka dveřních systémů TROCAL sahá od balkonových dveří, přes vedlejší vstupní dveře s hliníkovým prahem, až po vchodové dveře tuhostí srovnatelné s hliníkovými. Pro sladění designu s okenními systémy nabízíme samozřejmě i provedení elegance. I vchodové dveře mohou být opatřeny osvědčenou barevnou technologií AcrylProtect, DecoStyle s designem dřeva, nebo TROCAL AluClip, skýtajícím takřka neomezenou barevnou volbu dle vzorníku RAL. Standardem je otvírání dovnitř i ven. Všechny dveřní systémy jsou koncipovány na nejvyšší tuhost. Rohy jsou zesíleny rohovými spojkami.</p>\r\n<p> </p>\r\n<p><a rel="lightbox" href="data/userfiles/budova-milenium-center-s-parkovacim-domem.jpg"><img title="budova-milenium-center-s-parkovacim-domem.jpg" src="data/userfiles/budova-milenium-center-s-parkovacim-domem.jpg" alt="budova-milenium-center-s-parkovacim-domem.jpg" width="300" height="225" /></a></p>\r\n<p><br />Všechny dveře TROCAL odpovídají požadavkům normy DIN 18103. <br /> <br />Skutečný "vzhled" vašim dveřím propůjčí kromě různých kombinací ze sloupků vyráběných přímo v produkci oken i dveřní výplně. Informujte se u svého dodavatele oken TROCAL. <br /> <br />Kombinace tvarů, skel, barev a rozměrů jdou do tisíců. Naši specialisté jsou připraveni Vám zpracovat a prezentovat tu neefektivnější variantu.</p>', NULL, NULL, NULL, NULL, 'okna.jpg'),
(5, 10, 1, 1239963926, 1239963926, 'Plastová okna innoNova', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vulputate nibh. Etiam ut odio. Donec vel mauris. Nullam ut urna. Morbi sapien lectus, rutrum ac, malesuada in, tincidunt at, ligula. Proin non ipsum. Nunc nulla lectus, varius non, facilisis id, eleifend sed, justo. Duis ac nulla non eros rutrum condimentum. Etiam nunc velit, feugiat ac, vestibulum id, blandit quis, orci. Phasellus ultricies, mauris semper fringilla commodo, enim arcu porta orci, et auctor ligula ante non est. In faucibus, libero vitae eleifend porta, purus sem elementum quam, et dapibus mi urna sit amet nisl. Nullam sodales. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nam libero dolor, porta nec, sodales eget, sodales sed, ipsum. Proin a arcu non mi adipiscing ultricies. In gravida, nisi id ornare cursus, eros felis dapibus justo, vitae convallis massa lorem laoreet ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Ut eu libero quis purus condimentum suscipit. Proin eu nisl quis eros suscipit tempor. Curabitur sed metus vitae metus molestie feugiat.</p>\r\n<p>Nunc tempus. Mauris risus. Praesent porttitor, risus vel euismod feugiat, justo ligula ornare sem, eu ullamcorper ante justo sit amet erat. Etiam bibendum. Donec pellentesque. Pellentesque pellentesque lectus at nibh. Duis sollicitudin, leo non dapibus congue, erat orci placerat ipsum, nec semper lectus mauris ut risus. Nam auctor ullamcorper mauris. Nulla non augue. Suspendisse luctus convallis ipsum. Cras a leo sed felis faucibus auctor. Maecenas nec erat eget elit cursus commodo.</p>\r\n<p>Aliquam ut nulla. Suspendisse sodales libero fringilla odio. Vivamus turpis nisi, aliquam in, vehicula vel, tristique fermentum, nisl. Mauris urna justo, placerat vel, sodales cursus, mollis a, turpis. Quisque metus. Maecenas et magna eget urna ullamcorper fringilla. Vivamus ut nisi. Praesent quis sapien sit amet urna faucibus interdum. Vestibulum posuere, quam eleifend egestas tincidunt, nisi sapien viverra mauris, eget luctus dui nisl eu augue. Sed dictum, eros vitae mattis mattis, nunc augue lacinia eros, vel cursus urna ligula quis leo. Nam tempor. Nulla facilisi. Nunc risus. Pellentesque tortor. Donec purus. Morbi suscipit.</p>', NULL, NULL, NULL, NULL, 'okno-titul.jpg');

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_references`
--

DROP TABLE IF EXISTS `vypecky_references`;
CREATE TABLE IF NOT EXISTS `vypecky_references` (
  `id_reference` smallint(5) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `name_cs` varchar(300) default NULL,
  `label_cs` text,
  `name_en` varchar(300) default NULL,
  `label_en` text,
  `name_de` varchar(300) default NULL,
  `label_de` text,
  `file` varchar(200) default NULL,
  `changed_time` int(11) default NULL,
  PRIMARY KEY  (`id_reference`),
  KEY `id_item` (`id_item`),
  FULLTEXT KEY `name_cs` (`name_cs`),
  FULLTEXT KEY `label_cs` (`label_cs`),
  FULLTEXT KEY `name_en` (`name_en`),
  FULLTEXT KEY `label_en` (`label_en`),
  FULLTEXT KEY `name_de` (`name_de`),
  FULLTEXT KEY `label_de` (`label_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Vypisuji data pro tabulku `vypecky_references`
--

INSERT INTO `vypecky_references` (`id_reference`, `id_item`, `name_cs`, `label_cs`, `name_en`, `label_en`, `name_de`, `label_de`, `file`, `changed_time`) VALUES
(7, 6, 'Stránky hudba valmez 2009', '<p>tránky k projektu valašského CD, které vychází každých 5 let. obsahují různé kapely od známých, jako mňága a žďorp až po úplné neznámé.</p>', 'english label', NULL, NULL, NULL, 'madagaskar-2-1226738485.jpg', 1238325126);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_sections`
--

DROP TABLE IF EXISTS `vypecky_sections`;
CREATE TABLE IF NOT EXISTS `vypecky_sections` (
  `id_section` smallint(3) NOT NULL auto_increment,
  `id_parent` int(11) NOT NULL,
  `slabel_cs` varchar(50) default NULL,
  `salt_cs` varchar(200) default NULL,
  `slabel_en` varchar(50) default NULL,
  `salt_en` varchar(200) default NULL,
  `slabel_de` varchar(50) default NULL,
  `salt_de` varchar(200) default NULL,
  `priority` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id_section`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Vypisuji data pro tabulku `vypecky_sections`
--

INSERT INTO `vypecky_sections` (`id_section`, `id_parent`, `slabel_cs`, `salt_cs`, `slabel_en`, `salt_en`, `slabel_de`, `salt_de`, `priority`) VALUES
(1, 0, 'section 1', NULL, NULL, NULL, NULL, NULL, 0),
(6, 0, 'sekce 2', 'druhá sekce s dalšími nástroji', NULL, NULL, NULL, NULL, 5),
(7, 1, 'pod sekce 1', NULL, NULL, NULL, NULL, NULL, 0),
(9, 1, 'podsekce 2', NULL, NULL, NULL, NULL, NULL, 0),
(10, 7, 'podpodsekce 1', NULL, NULL, NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_texts`
--

DROP TABLE IF EXISTS `vypecky_texts`;
CREATE TABLE IF NOT EXISTS `vypecky_texts` (
  `id_text` smallint(4) unsigned NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `text_cs` mediumtext,
  `changed_time` int(11) default NULL,
  `text_en` mediumtext,
  `text_de` mediumtext,
  PRIMARY KEY  (`id_text`),
  UNIQUE KEY `id_article` (`id_item`),
  FULLTEXT KEY `text_cs` (`text_cs`),
  FULLTEXT KEY `text_en` (`text_en`),
  FULLTEXT KEY `text_de` (`text_de`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Vypisuji data pro tabulku `vypecky_texts`
--

INSERT INTO `vypecky_texts` (`id_text`, `id_item`, `text_cs`, `changed_time`, `text_en`, `text_de`) VALUES
(9, 2, '<div id="lipsum">\r\n<p><strong>Lorem ipsum dolor sit </strong>amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p><img title="tango-feet.png" src="data/userfiles/tango-feet.png" alt="tango-feet.png" width="556" height="593" /></p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p> </p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n</div>', 1244631636, NULL, NULL),
(8, 1, '<p><a rel="lightbox" href="#"><img style="float: left; margin-right: 10px;" title="imag0001.jpg" src="data/userimages/imag0001.jpg" alt="imag0001.jpg" width="201" height="149" /></a><strong><span style="font-size: xx-large;">Od 29.3. méme na skladě nové druhy <a href="text-pouze-s-obrazky-2">parapetů</a> v barvách duhy</span></strong></p>\r\n<p> </p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>\r\n<p> </p>\r\n<p>Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p> </p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. <img style="float: right; margin-left: 10px; margin-top: 10px; margin-bottom: 10px;" title="00703.jpg" src="data/userfiles/00703.jpg" alt="00703.jpg" width="200" height="137" />Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p> </p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<p> </p>', 1244997562, NULL, NULL),
(10, 5, '<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n<div id="lipsum">\r\n<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse urna dui, imperdiet a, pellentesque sed, malesuada a, nibh. Maecenas adipiscing lacus id nisi. Integer purus orci, consectetur vitae, rhoncus eget, dapibus ornare, lectus. Aliquam et purus at risus vulputate mattis. Vestibulum consequat urna in ligula. Fusce arcu nunc, tincidunt ac, sagittis eget, laoreet quis, tortor. Maecenas lacinia ante et libero. Curabitur placerat bibendum ipsum. Curabitur congue, orci congue rhoncus semper, dolor nibh condimentum neque, et posuere purus leo sed velit. Ut egestas. Phasellus tristique condimentum massa. Fusce posuere risus et augue. Aliquam erat volutpat. Integer diam nulla, mollis in, varius at, vulputate sed, ligula. Nulla dolor. In convallis, lacus non sollicitudin sodales, nisi nisi varius velit, non semper tellus purus nec lorem.</p>\r\n<p>Duis at sapien. Integer sagittis aliquet massa. Duis ornare nulla at dui. Phasellus consequat, libero ut tristique ultricies, justo orci ornare augue, vel eleifend quam odio non sem. Nam luctus auctor justo. Nulla posuere sollicitudin tellus. Nulla aliquet, nisl in pellentesque euismod, ante est venenatis tortor, in dapibus mauris metus eu ante. Aliquam rhoncus tristique lectus. Proin aliquam. Praesent auctor, leo vel fermentum convallis, tellus odio porta ante, sed luctus turpis enim et nisl. Nulla ante elit, bibendum ut, ultricies vitae, vulputate nec, dolor. Aliquam erat volutpat. Fusce vitae diam. Etiam non nunc.</p>\r\n<p>Maecenas lorem. Nulla est. Nullam porta malesuada quam. Praesent erat. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ornare, sapien id venenatis mattis, enim ipsum sagittis nibh, sed imperdiet orci lacus at leo. Sed nec risus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse aliquam, ligula mattis convallis bibendum, purus nunc ultrices justo, sit amet mattis libero eros a leo. Sed vehicula.</p>\r\n<p>Quisque eget elit. Vivamus dictum dui nec risus. Ut ultrices dui ac neque. Etiam justo mi, rutrum sed, pharetra eu, auctor eleifend, diam. Aliquam ac augue. Quisque mi augue, mollis congue, imperdiet nec, bibendum elementum, odio. Sed elementum, dolor non faucibus semper, ligula libero laoreet enim, eget gravida est tellus et enim. Duis at neque quis nulla mattis congue. Nulla facilisi. Duis dapibus elementum orci. Nullam libero diam, lobortis sit amet, dapibus et, dignissim vitae, est. Nulla eget massa sit amet nibh vulputate tempus. Proin placerat. Mauris a orci vel tellus molestie posuere. Aliquam semper nisi ut arcu. Maecenas in sem et erat iaculis semper. Praesent mattis imperdiet massa.</p>\r\n<p>Morbi accumsan. Duis eros turpis, vulputate et, lobortis quis, rhoncus quis, lorem. Etiam rhoncus enim. Curabitur congue, lectus vitae ornare cursus, felis ipsum congue elit, sed semper ligula risus vitae justo. Maecenas eget tellus eu lacus sodales tempor. Suspendisse potenti. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Duis egestas sollicitudin velit. Phasellus eget augue quis sem blandit eleifend. Pellentesque semper, eros et blandit consequat, orci justo consectetur felis, sed sodales sem lorem in odio. Morbi egestas. Morbi eros metus, porta vitae, scelerisque adipiscing, interdum vitae, felis.</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>', 1237145467, NULL, NULL),
(11, 6, '<ul>\r\n<li>Další reference</li>\r\n<li>dalfvdv</li>\r\n<li>fvfvfdvdf</li>\r\n<li>gvfdfvfdvfdv</li>\r\n<li>fdvfdvdfvdfv</li>\r\n<li>fdvdfvdfvdfvfdv</li>\r\n<li>dfvdfvdfvdfvf</li>\r\n<li>vdfvdfvdvfd</li>\r\n</ul>', 1238325822, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_userfiles`
--

DROP TABLE IF EXISTS `vypecky_userfiles`;
CREATE TABLE IF NOT EXISTS `vypecky_userfiles` (
  `id_file` smallint(6) NOT NULL auto_increment,
  `id_item` smallint(5) unsigned NOT NULL,
  `id_article` smallint(6) NOT NULL,
  `id_user` smallint(5) unsigned NOT NULL default '1',
  `file` varchar(50) NOT NULL,
  `type` enum('file','image','flash') NOT NULL default 'file',
  `width` int(11) default NULL,
  `height` int(11) default NULL,
  `size` int(11) default NULL,
  `time` int(10) unsigned default NULL,
  PRIMARY KEY  (`id_file`),
  KEY `id_category` (`id_item`,`id_article`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=67 ;

--
-- Vypisuji data pro tabulku `vypecky_userfiles`
--

INSERT INTO `vypecky_userfiles` (`id_file`, `id_item`, `id_article`, `id_user`, `file`, `type`, `width`, `height`, `size`, `time`) VALUES
(65, 1, 1, 1, '00703.jpg', 'image', 700, 480, 68306, 1244911573),
(46, 1, 1, 1, 'recoverdatareiserfstrial.tar.gz', 'file', NULL, NULL, 4346556, 1238771976),
(48, 7, 1, 1, 'anree1.jpg', 'image', 1200, 1600, 674756, 1239023394),
(49, 7, 1, 1, 'anree2.jpg', 'image', 1200, 1600, 674756, 1239023578),
(50, 7, 1, 1, 'teo.jpg', 'image', 46, 58, 1320, 1239023663),
(51, 7, 1, 1, 'buttony.swf', 'flash', 120, 120, 2981, 1239029679),
(53, 10, 4, 1, 'budova-milenium-center-s-parkovacim-domem.jpg', 'image', 2048, 1536, 1038239, 1239874962),
(55, 2, 2, 1, 'icon-naming-utils-0.8.90.tar.gz', 'file', 0, 0, 70321, 1244630753),
(56, 2, 2, 1, 'tango-feet.png', 'image', 729, 783, 256459, 1244631590),
(58, 7, 7, 1, '0070.jpg', 'image', 700, 480, 68306, 1244644445),
(59, 7, 7, 1, '1560.jpg', 'image', 414, 414, 74995, 1244645022),
(61, 7, 8, 1, '00701.jpg', 'image', 700, 480, 68306, 1244645753),
(64, 7, 9, 1, 'fotka-novinky.jpg_[eh7874].jpeg', 'image', 241, 58, 13181, 1244795678),
(66, 7, 10, 1, 'art.protest.afp.gi.jpg', 'image', 292, 219, 23057, 1244915033);

-- --------------------------------------------------------

--
-- Struktura tabulky `vypecky_users`
--

DROP TABLE IF EXISTS `vypecky_users`;
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
(3, 'cuba', '084e0343a0486ff05530df6c705c8bb4', 1, 'Jakub', 'Matas', 'jakubmatas@gmail.com', 'Normální uživatel', 0, 'cuba1.jpg', 0);
