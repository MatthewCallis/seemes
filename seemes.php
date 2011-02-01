<?php
/*
Copyright (c) 2010, Matthew Callis

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

Some parts of this script were refered from Chrome AppSniffer, by Bao Nguyen <contact@nqbao.com>.
*/
class Seemes{
	var $url = '';
	var $connected;
	var $valid_filters = array('Ads', 'Analytics', 'CMS', 'Customer Service', 'Framework', 'Social API', 'Utility');

	# Some basic user agents for testing sites.
	var $user_agents = array(
		'Chrome'	=> 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_6; en-US) AppleWebKit/534.16 (KHTML, like Gecko) Chrome/10.0.648.11 Safari/534.16',
		'GoogleBot'	=> 'Googlebot/2.1 (+http://www.google.com/bot.html)'
	);

	var $test_script_src = array(
		array('Ads',				'adingo',					'/adingo\.jp\/\?/i'),	# http://sh.adingo.jp/?G=1000000002&guid=ON
		array('Ads',				'AdSense', 					'/pagead\/show\_ads\.js/i'),
		array('Ads',				'AdSide', 					'/\.doclix\.com\//i'),	# http://ads.doclix.com/adserver/serve/js/doclix_synd_overlay.js
		array('Ads',				'adzerk',					'/adzerk\.(net|com)/i'),
		array('Ads',				'AOL Advertising',			'/an\.tacoda\.net\//i'),	# http://an.tacoda.net/an/g10007/slf.js
		array('Ads',				'Casale Media',				'/as\.casalemedia\.com\//i'),	# http://as.casalemedia.com/sd?s=95308&f=1
		array('Ads',				'Chango',					'/\.chango\.com\/p\.js/i'),	# http://p.chango.com/p.js
		array('Ads',				'CheckM8',					'/\.checkm8\.com\//i'),	# http://q1digital.checkm8.com/adam/cm8adam_1_call.js
		array('Ads',				'ClickBank',				'/hopfeed\.com\//i'),	# http://thumbsup01.hopfeed.com/script/hopfeed.js
		array('Ads',				'CONTAXE',					'/contaxe\.com\//i'),	# http://www.contaxe.com/go/go.js?atp=isa&amp;c=21206
		array('Ads',				'Crowd Science',			'/\.crowdscience\.com\/start\.js\?/i'),	# http://static.crowdscience.com/start.js?id=5c5c650d27
		array('Ads',				'ContextWeb',				'/contextweb\.com\/TagPublish\//i'),
		array('Ads',				'Google Partner Ads',		'/partner\.googleadservices\.com\/gampad\/google\_ads\.js/i'),
		array('Ads',				'Kontera',					'/kontera\.com\/javascript\/lib\//i'), # http://kona.kontera.com/javascript/lib/KonaLibInline.js, http://konac.kontera.com/javascript/lib/2011_01_24_1/KonaFlashBase.js
		array('Ads',				'RCS MediaGroup',			'/ads\.rcs\.it\//i'),	# http://ads.rcs.it/www/delivery/spcjsCache.php?id=5&amp;target=_blank&amp;block=1
		array('Ads',				'Skimlinks',				'/\.skimresources\.com\/js\//i'),	# http://s.skimresources.com/js/4343X644758.skimlinks.js
		array('Ads',				'ValueClick',				'/media\.fastclick\.net\//i'),	# http://media.fastclick.net/w/get.media?sid=54112&tp=3&d=j&t=n
		array('Ads',				'WhitePixel',				'/\/whitepixel\.com\/backend\//i'),	# http://whitepixel.com/backend/remote/?wp_id=71a86dadd1db00829fdb8c58ef4180af
		array('Analytics',			'AudienceScience',			'/js\.revsci\.net\//i'),	# http://js.revsci.net/gateway/gw.js?csid=B08725
		array('Analytics',			'BLVD Status',				'/blvdstatus\.com\/js\//i'),	# http://www.blvdstatus.com/js/initBlvdJS.php
		array('Analytics',			'Clicky',					'/\.getclicky.com\/(js|in)/i'),	# http://static.getclicky.com/js
		array('Analytics',			'CrazyEgg',					'/\.cetrk\.com\//i'),	# http://s3.amazonaws.com/new.cetrk.com/pages/scripts/0009/5174.js
		array('Analytics',			'Google Analytics',			'/google\-analytics\.com\/(ga|urchin).js/i'),
		array('Analytics',			'Enquisite',				'/\.enquisite\.com\//i'),	# http://log.enquisite.com/log.js?id=seomoz
		array('Analytics',			'Mint',						'/\/mint\/\?js/i'),
		array('Analytics',			'Quantcast', 				'/quantserve\.com\/quant\.js/i'),	# http://edge.quantserve.com/quant.js
		array('Analytics',			'Reinvigorate', 			'/include\.reinvigorate\.net\/re\_\.js/i'),	# http://include.reinvigorate.net/re_.js
		array('Analytics',			'ScorecardResearch',		'/\.scorecardresearch\.com\/beacon\.js/i'),	# http://b.scorecardresearch.com/beacon.js
		array('Analytics',			'Site Meter',				'/\.sitemeter\.com\/js\//i'),	# http://s15.sitemeter.com/js/counter.js?site=s15friedbeef
		array('Analytics',			'StatCounter',				'/statcounter\.com\//i'),	# http://www.statcounter.com/counter/counter_xhtml.js
		array('Analytics',			'Trovus',					'/statistics\.trovus\.co\.uk\//i'),	# http://statistics.trovus.co.uk/splats/111.js
		array('Analytics',			'WordPress Stats',			'/stats\.wordpress\.com\//i'),	# http://stats.wordpress.com/e-201105.js
		array('CMS',				'Big Cartel',				'/\.bigcartel\.com\//i'),
		array('CMS',				'Gallery2',					'/main\.php\?.*g2_.*/i'),
		array('CMS',				'Joomla',					'/\/components\/com_/i'),
		array('CMS',				'MODx',						'/\/min\/b=.*f=.*/i'),
		array('CMS',				'TypePad',					'/typepad\.com\/services\//i'),	# http://profile.typepad.com/services/embed/tpc/6a0120a76d2282970b012876b29710970c/counts_embed.js
		array('CMS',				'Ubercart',					'/uc_cart/i'),
		array('CMS',				'vBulletin',				'/vbulletin\_global\.js/i'),
		array('CMS',				'WordPress',				'/wp\-includes\//i'),
		array('CMS',				'XenForo',					'/js\/xenforo\//i'),
		array('CMS',				'XOOPS',					'/\/xoops\.js/i'),
		array('CMS',				'ZenPhoto',					'/zp-core\/js/i'),
		array('Customer Service',	'GetSatisfaction',			'/getsatisfaction\.com\/feedback/i'),
		array('Customer Service',	'KISSinsights',				'/j\.kissinsights\.com\//i'),
		array('Customer Service',	'zendesk',					'/zendesk\.com\/external\/zenbox\/overlay\.js/i'),	# http://assets0.zendesk.com/external/zenbox/overlay.js
		array('Framework',			'Cappuccino',				'/Frameworks\/Objective-J\/Objective-J\.js/i'),
		array('Framework',			'Closure',					'/\/goog\/base\.js/i'),
		array('Framework',			'DD_belatedPNG',			'/DD_belatedPNG_.*\.js/i'),
		array('Framework',			'Dojo',						'/dojo(\.xd)?\.js/i'),
		array('Framework',			'jQuery',					'/jquery\.js/i'),
		array('Framework',			'jQuery (Google API)',		'/ajax\.googleapis\.com\/ajax\/libs\/jquery\//i'),
		array('Framework',			'jQuery UI (Google API)',	'/ajax\.googleapis\.com\/ajax\/libs\/jqueryui\//i'),
		array('Framework',			'MooTools',					'/mootools/i'),
		array('Framework',			'Prototype',				'/prototype\.js/i'),
		array('Framework',			'script.aculo.us',			'/scriptaculous\.js/i'),
		array('Framework',			'SWFObject',				'/(flash|swf)object\.js/i'),
		array('Framework',			'Typekit',					'/use\.typekit\.com\//i'),
		array('Framework',			'YUI',						'/(yahoo-dom-event|connection-min)\.js/i'),
		array('Social API',			'AddThis',					'/addthis\.com\/js\//i'),	# http://s7.addthis.com/js/addthis_widget.php?v=12
		array('Social API',			'bandcamp',					'/bandcamp\.com\/tmpdata\//i'),
		array('Social API',			'Facebook',					'/connect\.facebook\.net\/.*\/all\.js/i'),
		array('Social API',			'MyBlogLog',				'/mybloglog\.com\//i'),	# http://track.mybloglog.com/js/jsserv.php?mblID=2008072316273799
		array('Social API',			'Sphinn',					'/sphinn\.com\//i'),	# http://sphinn.com/evb/button.php
		array('Social API',			'Tumblr',					'/\.tumblr\.com\//i'),
		array('Social API',			'Twitter',					'/\/platform\.twitter\.com\//i'),
		array('Utility',			'Brightcove',				'/brightcove\.com\//i'),	# http://admin.brightcove.com/js/BrightcoveExperiences_all.js
		array('Utility',			'Cufon',					'/cufon\-yui\./i'),
		array('Utility',			'Disqus',					'/disqus.com\/forums/i'),
		array('Utility',			'E-junkie',					'/\.e\-junkie\.com\//i'),	# http://www.e-junkie.com/ecom/boxec28_enc.js
		array('Utility',			'Mollom',					'/mollom\/mollom\.js/i'),
		array('Utility',			'reCaptcha',				'/(\/wp\-recaptcha\/|api\.recaptcha\.net\/)/i'),
		array('Utility',			'Tyny',						'/tynt\.com\/javascripts\//i'),	# http://tcr.tynt.com/javascripts/Tracer.js?user=dUaMNy73Or3QRoadbiUzgI&amp;s=101
		array('Utility',			'Visual Website Optimizer',	'/visualwebsiteoptimizer\.com\//i'),	# http://dev.visualwebsiteoptimizer.com/deploy/js_visitor_settings.php?a=566&amp;random=0.36674677208065987
		array('Utility',			'Wibiya',					'/wibiya\.com\/Loaders\//i')
	);

	var $test_script_content = array(
		array('Ads',				'AdFox',					'/ads\.adfox\.ru\//i'),	# http://ads.adfox.ru/3732/getCode?p1=cjst
		array('Ads',				'BuySellAds.com',			'/\.buysellads\.com\//i'),
		array('Ads', 				'doubleclick',				'/doubleclick\.net/i'),
		array('Ads', 				'Evidon',					'/(info\.betteradvertising\.com|betrad\.com)/i'),
		array('Ads', 				'expo-Max',					'/expo\-max\.com\/adserver\//i'),
		array('Ads', 				'Google Partner Ads',		'/GA_google(AddSlot|AddAttr|FetchAds|AddAdSenseService|EnableAllServices)/'),
		array('Ads', 				'TradeDoubler',				'/tradedoubler\.com\/imp\?/i'),	# http://imppl.tradedoubler.com/imp?type(js)pool(308935)a(1531296)'
		array('Ads', 				'VigLink',					'/var\svglnk\s?\=\s?\{/i'),
		array('Ads', 				'Yahoo! Ads',				'/window\.yzq_d/i'),
		array('Analytics',			'BTBuckets',				'/btbuckets\.com\/bt\.js/i'),	# btbuckets.com/bt.js
		array('Analytics',			'chartbeat',				'/chartbeat\.com\/js\/chartbeat\.js/i'),
		array('Analytics',			'Clicky',					'/clicky/'),
		array('Analytics',			'Gomez',					'/var\sgomez\s?\=/i'),
		array('Analytics',			'Google Analytics',			'/google-analytics.com\/(ga|urchin).js/i'),
		array('Analytics',			'KISSmetrics',				'/(i\.kissmetrics\.com\/|\_kmq \= \_kmq|\_kiq \= \_kiq)/i'),
		array('Analytics',			'ScorecardResearch',		'/\.scorecardresearch\.com\/beacon\.js/i'),
		array('Analytics',			'Tyxo.bg Counter',			'/(www|cnt)\.tyxo\.bg\//i'),
		array('Analytics',			'SiteCensus',				'/\.imrworldwide\.com\//i'),
		array('Analytics',			'Woopra',					'/woopraTracker/'),
		array('CMS',				'Drupal',					'/Drupal/'),
		array('CMS',				'ErainCart',				'/fn_register_hooks/'),
		array('CMS',				'IPB',						'/IPBoard/'),
		array('CMS',				'MODx',						'/(var el= \$\(\'modxhost\'\);|<script type=["\']text\/javascript["\']>var MODX_MEDIA_PATH = ["\']media["\'];)/'),
		array('CMS',				'MyBB',						'/MyBB/'),
		array('CMS',				'Piwik',					'/Piwik/'),
		array('CMS',				'phpBB',					'/_phpbbprivmsg/'),
		array('Customer Service',	'UserVoice',				'/cdn\.uservoice\.com\//i'),
		array('Framework',			'ExtJS',					'/Ext/'),
		array('Framework',			'jQuery UI',				'/jQuery.ui/'),
		array('Framework',			'jQuery',					'/jQuery/'),
		array('Framework',			'Typekit',					'/Typekit/'),
		array('Framework',			'YUI',						'/YAHOO/'),
		array('Social API',			'bandcamp', 				'/bandcamp\.com\/files\//'),
		array('Social API',			'Blogglisten',				'/blogglisten\.no\/count\?/i'),	# myBloggListenAsynch.src = ('http://www.blogglisten.no/count?id=1962');
		array('Social API',			'Facebook',					'/FB\.(Facebook|api|init)/'),
		array('Social API',			'Flattr',					'/api\.flattr\.com\//i'),	# http://api.flattr.com/js/0.6/load.js?mode=auto
		array('Utility',			'Cufon',					'/Cufon/'),
		array('Utility',			'Modernizr',				'/Modernizr/'),
		array('Utility',			'Raphael',					'/Raphael/'),
		array('Utility',			'sIFR',						'/sIFR/'),
		array('Utility',			'SugarCRM',					'/SUGAR/'),
		array('Utility',			'Xiti',						'/(xtsite|xtpage)/')
	);

	var $test_metatag_types = array('generator', 'openacs', 'copyright', 'elggrelease', 'powered-by');
	var $test_metatags = array(
		array('CMS',				'Amiro.CMS',				'generator',			'/Amiro/i'),
		array('CMS',				'bbPress',					'generator',			'/bbPress/i'),
		array('CMS',				'BIGACE',					'generator',			'/BIGACE/i'),
		array('CMS',				'Blogger',					'generator',			'/blogger/i'),
		array('CMS',				'CMSMadeSimple',			'generator',			'/CMS Made Simple/i'),
		array('CMS',				'DokuWiki',					'generator',			'/dokuWiki/i'),
		array('CMS',				'DotNetNuke',				'generator',			'/DotNetNuke/i'),
		array('CMS',				'Drupal',					'generator',			'/Drupal/i'),
		array('CMS',				'ez Publish',				'generator',			'/eZ\s*Publish/i'),
		array('CMS',				'JaliosJCMS',				'generator',			'/Jalios JCMS/i'),
		array('CMS',				'Joomla',					'generator',			'/joomla/i'),
		array('CMS',				'Koobi',					'generator',			'/koobi/i'),
		array('CMS',				'MediaWiki',				'generator',			'/MediaWiki/i'),
		array('CMS',				'Movable Type',				'generator',			'/Movable Type/i'),
		array('CMS',				'OpenACS',					'generator',			'/OpenACS/i'),
		array('CMS',				'PHP-Nuke',					'generator',			'/PHP-Nuke/i'),
		array('CMS',				'PivotX',					'generator',			'/PivotX/i'),
		array('CMS',				'Plone',					'generator',			'/plone/i'),
		array('CMS',				'PrestaShop',				'generator',			'/PrestaShop/i'),
		array('CMS',				'SharePoint',				'generator',			'/SharePoint/'),
		array('CMS',				'SilverStripe',				'generator',			'/SilverStripe/i'),
		array('CMS',				'Sitefinity',				'generator',			'/Sitefinity/i'),
		array('CMS',				'TypePad',					'generator',			'/typepad\.com/i'),
		array('CMS',				'TYPO3',					'generator',			'/TYPO3/i'),
		array('CMS',				'vBulletin',				'generator',			'/vBulletin/i'),
		array('CMS',				'WebGUI',					'generator',			'/WebGUI/i'),
		array('CMS',				'WordPress', 				'generator',			'/wordPress/i'),	# <meta name="generator" content="WordPress 3.0.4" />
		array('CMS',				'WPML',						'generator',			'/WPML/i'),
		array('CMS',				'XOOPS',					'generator',			'/xoops/i'),
		array('CMS',				'ZenCart',					'generator',			'/zen-cart/i'),
		array('CMS',				'OpenACS',					'openacs', 				'/OpenACS/i'),
		array('CMS',				'phpBB',					'copyright',			'/phpBB/i'),
		array('CMS',				'Elgg',						'elggrelease',			'/.+/i'),
		array('CMS',				'Serendipity',				'powered-by',			'/Serendipity/i'),
		array('Analytics',			'Alexa Rank',				'alexaVerifyID',		'/.+/i'),	# <meta name="alexaVerifyID" content="Bd85OCZl66apE-s3kZ1kHwmHsSA" />
		array('Analytics',			'Bing Webmaster Tools',		'msvalidate.01',		'/.+/i'),	# <meta name="msvalidate.01" content="FA310D24EFEA12737520F4C8C36F67A5" />
		array('Analytics',			'Google Webmaster Tools',	'google-site-verification', '/.+/i'),	# <meta name="google-site-verification" content="jkqweQY8S-UZGcifNuxgeQ493pfF_wo3HBTeH2TVbSw" />
		array('Analytics',			'Google Webmaster Tools',	'verify-v1',			'/.+/i'),	# <meta name="verify-v1" content="5VGqvoC0PBAbwbfT/XlznseDBSLXNw/+SA/B1KJuqXA=" />
		array('Analytics',			'Yahoo! Webmaster Tools',	'y_key',				'/.+/i')	# <META name="y_key" content="1658eb44756e03a0">
	);

	var $fetch_accounts = array(
		array('Ads',				'AdSense',					'/google\_ad\_client\s?\=\s?["\'](.*)["\']\;/i'),	#	google_ad_client = "pub-3632299827451484";
		array('Ads',				'Chango',					'/__changoPartnerId\=["\'](.*)["\']\;/i'),	#	<script type="text/javascript">var __changoPartnerId='suite101';</script>
		array('Ads',				'Google Conversion',		'/google\_conversion\_id\s?\=\s?["\']?(.*)["\']?\;/i'),	#	google_conversion_id = 1031212126;
		array('Ads',				'VigLink',					'/vglnk\s?\=\s?\{\s\?key\:\s?["\'](.*)["\']\s?\}\;/i'),	#	var vglnk = { key: '909e2de4e3686ff0cbf92e12d6b99c58' };
		array('Analytics',			'BTBuckets',				'/\$BTB\=\{s\:(.*)\}\;/i'),	# $BTB={s:10777};
		array('Analytics',			'chartbeat',				'/async_config\=\{uid\:(.*)\,/i'),
		array('Analytics',			'Clicky',					'/clicky\.init\((.*)\)\;/i'),	#	clicky.init(210465);
		array('Analytics',			'Clicky',					'/getclicky\.com\/in\.php\?site\_id\=(.*)\&/i'),	#	http://in.getclicky.com/in.php?site_id=210465&
		array('Analytics',			'Enquisite',				'/\.enquisite\.com\/log\.js\?id\=(.*)/i'),	#	http://log.enquisite.com/log.js?id=seomoz
		array('Analytics',			'Google Analytics (Old)',	'/\_uacct \= ["\'](.*)["\']/i'),
		array('Analytics',			'Google Analytics',			'/\_getTracker\(["\'](.*)["\']\);/i'),	#	_gat._getTracker("UA-705847-4");
		array('Analytics',			'Google Analytics',			'/\_setAccount["\']\, ["\'](.*)["\']\]\, \[["\']\_trackPageview["\']\]/i'),
		array('Analytics',			'Google Analytics',			'/\_setAccount["\']\, ["\']([AUau\-0-9]*)["\']\]\)\;/i'),	#	_setAccount', 'UA-32013-6'], ['_trackPageview'] / _setAccount', 'UA-10841838-1']);
		array('Analytics',			'NedStat',					'/\.nedstatbasic\.net\/cgi-bin\/viewstat\?name\=(.*)["\']/i'), # <a href="http://usa.nedstatbasic.net/cgi-bin/viewstat?name=nesdev">
		array('Analytics',			'Quantcast',				'/\qacct\=["\'](.*)["\'];/i'),	#	_qacct="p-d4P3FpSypJrlA";
		array('Analytics',			'Quantcast',				'/\_qoptions \= \{ qacct\: ["\'](.*)["\']/i'),	#	_qoptions = { qacct: "p-45WWkjSYwI3II" };
		array('Analytics',			'Reinvigorate',				'/reinvigorate\.track\(["\'](.*)["\']\)/i'),	#	reinvigorate.track("54ja7-5p4r2a407o");
		array('Analytics',			'Site Meter',				'/sitemeter\.com\/js\/counter\.js\?site\=(.*)["\']/i'),	#	# http://s15.sitemeter.com/js/counter.js?site=s15friedbeef
		array('Analytics',			'SiteCensus',				'/\?ci\=(.*)&cg\=/i'),	#	"http://secure-us.imrworldwide.com/cgi-bin/m?ci=us-803450h&cg=0&cc=1&si="
		array('Analytics',			'StatCounter',				'/sc\_project\=(.*);/i'), #	var sc_project=4240773;
		array('Analytics',			'TopList.cz',				'/src\=["\']http\:\/\/toplist\.cz\/dot\.asp\?id\=(.*)["\&]/i'),	# http://toplist.cz/dot.asp?id=472987
		array('Analytics',			'Tyxo.bg Counter',			'/cnt\.tyxo\.bg\/(.*)\?rnd/i'),	# http://cnt.tyxo.bg/30428?rnd=
		array('Analytics',			'Tyxo.bg Counter',			'/www\.tyxo\.bg\/\?(.*)["\']/i'),	# http://www.tyxo.bg/?30428
		array('Social API',			'Blogglisten',				'/blogglisten\.no\/count\?id\=(.*)["\']\)/i'),	# myBloggListenAsynch.src = ('http://www.blogglisten.no/count?id=1962');
		array('Social API',			'Facebook AppID',			'/appId\:\s?["\']([0-9])["\']\,/'),
		array('Social API',			'MyBlogLog',				'/mybloglog\.com\/js\/jsserv\.php\?mblID\=(.*)["\']/i'),	# http://track.mybloglog.com/js/jsserv.php?mblID=2008072316273799
	);

	var $test_text = array(
		array('Ads',				'BannerConnect',			'/src=["\']http:\/\/ad\.bannerconnect\.net/i'),
		array('Ads',				'OpenX',					'/(href|src)=["\'].*delivery\/(afr|ajs|avw|ck)\.php[^"\']*/'),
		array('Ads',				'ReTargeter',				'/\/ad\.retargeter\.com\/seg/i'),
		array('Analytics',			'NedStat',					'/\.nedstatbasic\.net\/cgi-bin\/viewstat\?name\=/i'),
		array('Analytics',			'PercentMobile',			'/tracking\.percentmobile\.com\/pixel\//i'),	# http://tracking.percentmobile.com/pixel/ce645c30-75bd-11de-899d-12313900c5b8
		array('Analytics',			'SiteCatalyst',				'/End SiteCatalyst code/i'),
		array('Analytics',			'StatCounter',				'/Start of StatCounter Code/i'),
		array('Analytics',			'TopList.cz',				'/src\=["\']http\:\/\/toplist\.cz\/dot\.asp\?id\=/i'),
		array('CMS',				'Bitrix',					'/<link[^>]*\/bitrix\/.*?>/i'),
		array('CMS',				'Burning Board Lite',		'/Powered by <b><a[^>]+>Burning Board Lite/i'),
		array('CMS',				'Contao',					'/powered by (TYPOlight|Contao)/is'),
		array('CMS',				'Fatwire',					'/\/Satellite\?|\/ContentServer\?/s'),
		array('CMS',				'Liferay',					'/<script[^>]*>.*LifeRay\.currentURL/is'),
		array('CMS',				'Magento',					'/var BLANK_URL = \'[^>]+js\/blank\.html\'/i'),
		array('CMS',				'miniBB',					'/<a href=["\'][^>]+minibb.+\s*<!--End of copyright link/is'),
		array('CMS',				'MODx',						'/<a[^>]+>Powered by MODx<\/a>/i'),
		array('CMS',				'Moodle',					'/<link[^>]*\/theme\/standard\/styles.php".*>/'),
		array('CMS',				'OpenCMS',					'/<link[^>]*\.opencms\..*?>/i'),
		array('CMS',				'osCommerce',				'/Powered by <a[^>]+>osCommerce<\/a>/i'),
		array('CMS',				'PHP-Fusion',				'/(href|src)=["\']?infusions\//i'),
		array('CMS',				'phpBB',					'/Powered by <a[^>]+>phpBB<\/a>/i'),
		array('CMS',				'SMF',						'/<script .+\s+var smf_/i'),
		array('CMS',				'vBulletin',				'/vbmenu_control/i'),
		array('CMS',				'WordPress',				'/<link rel=["\']stylesheet["\'] [^>]+wp-content/i'),
		array('CMS',				'XOOPS',					'/xoops(_login|_redirect|poll)/i'),
		array('Customer Service',	'GetSatisfaction',			'/asset_host\s*\+\s*"javascripts\/feedback.*\.js/im'),
		array('Framework',			'Google Font API',			'/ref=["\']?http:\/\/fonts.googleapis.com\//i'),
		array('Framework',			'YUI',						'/(yui-overlay|yui-panel-container|_yuiResizeMonitor)/'),
		array('Social API',			'Tumblr',					'/<iframe src=["\']http:\/\/www\.tumblr\.com/i'),
		array('Social API',			'Twitter',					'/\.twitter\.com\/flash\/widgets\//i'),
		array('Utility',			'Closure',					'/<script[^>]*>.*goog\.require/is'),
		array('Utility',			'Gravatar',					'/\/secure\.gravatar\.com\/avatar\//i'),
		array('*Error*',			'PHP Error',				'/\<b\>Warning\<\/b\>\:/')
	);

	# Constructor
	function Seemes($url = ''){
		$this->url = $url;
		$this->connected = $this->testConnection();
	}

	# Check to see if we are online so we don't waste time polling data.
	function testConnection(){
		$connected = true;
		if(!$socket = @fsockopen('google.com', 80, $number, $error, 5)){
			$connected = false;
		}
		return $connected;
	}

	# Create a nice string for a file name.
	function sanitize($string){
		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ".", ">", "/", "?");
		$clean = trim(str_replace($strip, "", strip_tags($string)));
		$clean = preg_replace("/[^a-zA-Z0-9]/", "", $clean);
		return (function_exists('mb_strtolower')) ? mb_strtolower($clean, 'UTF-8') : strtolower($clean);
	}

	# Return the contents of a page as a string, either by spoofing or not.
	function fetchUrl($url, $spoof = false, $user_agent = '', $cache = true){
		$url = trim($url);
		$data = null;
		$name = $this->sanitize($url);
		$file = getcwd() . '/' . $name . '.html';
		if(file_exists($file)){
			# Remove file if its older than a day
			$data = file_get_contents($file);
			$file_last_modified = filemtime($file);
			if(($file_last_modified - time()) > 24 * 3600){
				unlink($file);
			}
		}
		else if($this->connected){
			$ch = curl_init();
			if($spoof){
				if($user_agent == ''){
					$user_agent = $this->user_agents['Chrome'];
				}
				curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
			}
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
			$data = curl_exec($ch);
			curl_close($ch);
			if($cache){
				file_put_contents($file, $data);
			}
		}
		else{
			$data = 'offline';
		}
		return $data;
	}

	# Check meta-tags for known values.
	function checkMetaTags($page, $found = array(), $filter = array()){
		$found = is_array($found) ? $found : array();
		$filter = ((!empty($filter) && is_array($filter)) ? $filter : $this->valid_filters);
		preg_match_all('/<[\s]*meta[\s]*name[\s]*=[\s]*["\']?([^>"\']*)["\']?[\s]*content[\s]*=[\s]*["\']?([^>"\']*)["\']?[\s]*[\/]?[\s]*>/si', $page, $metas);
		if(empty($metas[0])) return array('');
		$meta_names = $this->test_metatag_types;
		$no_results = true;
		foreach($metas[1] as $key => $value){
			if(!in_array($value, $meta_names)) continue;
			foreach($this->test_metatags as $test){
				if(in_array($test[1], $found)) continue;
				if(!in_array($test[0], $filter)) continue;
				if($value == $test[2] && preg_match($test[3], $metas[2][$key])){
					$no_results = false;
					array_push($found, $test[1]);
				}
			}
		}
		return $found;
	}

	# Check script tags src values and contents.
	function checkScriptTags($page, $found = array(), $filter = array()){
		$found = is_array($found) ? $found : array();
		$filter = ((!empty($filter) && is_array($filter)) ? $filter : $this->valid_filters);
		preg_match_all('/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i', $page, $script_tags);
		if(empty($script_tags)) return array('');
		$scripts = $script_tags[2];
		$script_contents = $script_tags[3];
		foreach($scripts as $script){
			foreach($this->test_script_src as $test){
				if(in_array($test[1], $found)) continue;
				if(!in_array($test[0], $filter)) continue;
				if(preg_match($test[2], $script)){
					array_push($found, $test[1]);
				}
			}
		}
		foreach($script_contents as $script){
			foreach($this->test_script_content as $test){
				if(in_array($test[1], $found)) continue;
				if(!in_array($test[0], $filter)) continue;
				if(preg_match($test[2], $script)){
					array_push($found, $test[1]);
				}
			}
		}
		return $found;
	}

	# Check whole page.
	function checkPageText($page, $found = array(), $filter = array()){
		$filter = ((!empty($filter) && is_array($filter)) ? $filter : $this->valid_filters);
		$found = is_array($found) ? $found : array();
		foreach($this->test_text as $test){
			if(in_array($test[1], $found)) continue;
			if(!in_array($test[0], $filter)) continue;
			if(preg_match($test[2], $page)){
				array_push($found, $test[1]);
			}
		}
		return $found;
	}

	# Read the Analytic IDs used on the page.
	function getAccounts($page, $found = array()){
		$found = is_array($found) ? $found : array();
		preg_match_all('/((<[\s\/]*script\b[^>]*>)([^>]*)(<\/script>))/i', $page, $script_tags);
		$script_contents = $script_tags[3];
		foreach($script_contents as $script){
			foreach($this->fetch_accounts as $test){
				if(preg_match($test[2], $script, $matches)){
					$result = $test[1].': '.$matches[1];
					if(in_array($result, $found)) continue;
					array_push($found, $result);
				}
			}
		}
		return $found;
	}
}