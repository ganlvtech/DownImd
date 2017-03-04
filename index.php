<?php
/***********************************************************
 **
 **                 节奏大师官方谱面下载
 **
 **                      by Ganlv
 **
 **　　　　 LICENSE: Apache License, Version 2.0
 **
 ***********************************************************
 **
 ** Version history:
 **
 ** v 1.1.1 -- 2016.06.08
 ** v 1.1.0 -- 2016.06.05
 ** v 1.0.0 -- 2015
 **
 **********************************************************/
define('URL', 'http://game.ds.qq.com/Com_TableCom_Android/mrock_song_client_android.xml');
define('CACHE_FILE', 'cache.php');
define('CACHE_EXPIRE', 86400);
if (extension_loaded('zlib') && (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')))
	ini_set('zlib.output_compression', 'On');
header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set('Asia/Shanghai');
$cache_ok = false;
$cache_exists = file_exists(CACHE_FILE);
if ($cache_exists) {
	require CACHE_FILE;
	$cache_ok = ($_SERVER['REQUEST_TIME'] - $cache[0] < CACHE_EXPIRE);
	$time = date('Y-m-d H:i:s', $cache[0]);
	$state = "歌曲列表时间：{$time}";
}
if (!$cache_ok) {
	function &getClientXML() {
		if (extension_loaded('curl')) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, URL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			$xml = curl_exec($ch);
			curl_close($ch);
		} else {
			$xml = file_get_contents(URL);
		}
		return $xml;
	}
	if (($xml = getClientXML()) && preg_match_all('/<m_szSongName>(.+?)<\/m_szSongName>\\s+?<m_szPath>(.+?)<\/m_szPath>/', $xml, $matches)) {
		$cache = array($_SERVER['REQUEST_TIME'], json_encode($matches[1]), json_encode($matches[2]));
		file_put_contents(CACHE_FILE, '<?php $cache = ' . var_export($cache, true) . ';');
		$time = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$state = "歌曲列表时间：{$time}";
	} elseif ($cache_exists) {
		$state = "歌曲列表时间：{$time}";
	} else {
		$state = "在线获取失败，且没有缓存";
	}
}
function html5_support(){
	if (empty($_SERVER['HTTP_USER_AGENT']))
		return false;
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (false !== strpos($ua, 'Edge') || false !== strpos($ua, 'rv:11.0'))
		return true;
	elseif (preg_match('/MSIE\s(\d+)\..*/i', $ua, $matches))
		return ((int)$matches[1] >= 9);
	elseif (preg_match('/FireFox\/(\d+)\..*/i', $ua, $matches))
		return ((int)$matches[1] >= 4);
	elseif (preg_match('/Opera[\s|\/](\d+)\..*/i', $ua, $matches))
		return ((int)$matches[1] >= 11);
	elseif (preg_match('/Chrome\/(\d+)\..*/i', $ua, $matches))
		return ((int)$matches[1] >= 5);
	elseif ((strpos($ua, 'Chrome') === false) && preg_match('/Safari\/(\d+)\..*$/i', $ua, $matches))
		return ((int)$matches[1] >= 525);
	else
		return false;
}
$html5 = html5_support() && !isset($_GET['compatible']);
if ($html5) { ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<style>
		* {
			padding: 0;
			margin: 0;
		}
		header, section, footer, aside, nav, main {
			display: block;
		}
		body {
			background-color: #e6e6e6;
			font-size: 14px;
			font-family: Arial, Helvetica, sans-serif;
		}
		@media screen and (min-width: 321px) {
			body{font-size: 16px;}
		}
		@media screen and (min-width: 414px) {
			body{font-size: 17px;}
		}
		@media screen and (min-width: 640px) {
			body{font-size: 18px;}
		}
		aside {
			position: fixed;
			width: 100%;
			height: 100%;
			z-index: 2;
			color: #eee;
			background-color: rgba(0, 0, 0, 0.9);
		}
		aside h1 {
			margin-top: 2em;
			font-size: 2em;
			text-align: center;
		}
		aside > section {
			font-size: 1.5em;
			margin: auto;
		}
		aside > section > p {
			margin: 0.5em;
			text-indent: 1em;
		}
		header {
			position: fixed;
			width: 100%;
			z-index: 1;
		}
		.search-text {
			padding: 0.5em 2em 0.5em 0.5em;
			width: 100%;
			box-sizing: border-box;
			color: #333;
			background-color: #69c;
			border: none;
			font-size: 1em;
		}
		::-webkit-input-placeholder {
			color: #ccc;
		}
		:-moz-placeholder {
			color: #ccc;
		}
		::-moz-placeholder {
			color: #ccc;
		}
		:-ms-input-placeholder {
			color: #ccc;
		}
		.search-text:focus {
			border: 2px solid #999;
			background-color: #fcfcfc;
			border-radius: 0.3em;
		}
		header > a {
			margin-left: -1em;
			font-size: 2em;
			color: #999;
			text-decoration: none;
			vertical-align: top;
		}
		main {
			padding-top: 2.5em;
		}
		.list {
			margin-bottom: 0.5em;
			padding: 0.5em;
			background-color: #fcfcfc;
		}
		.list > h1 {
			font-size: 1em;
		}
		.list > a {
			display: inline-block;
			width: 100%;
			border-top: 1px solid #999;
			padding: 0.3em;
			color: #333;
			box-sizing: border-box;
			text-decoration: none;
			vertical-align: top;
		}
		@media screen and (min-width: 321px) {
			.list > a {width: 50%;}
		}
		@media screen and (min-width: 640px) {
			.list > a {width: 33.3%;}
		}
		@media screen and (min-width: 960px) {
			.list > a {width: 25%;}
		}
		.result {
			margin: 0 auto 0.5em auto;
			padding: 1em;
			width: 98%;
			background-color: #fcfcfc;
			box-sizing: border-box;
			border: 2px solid #999;
			border-radius: 5px;
		}
		@media screen and (min-width: 640px) {
			.result > section {
				display: inline-block;
				width: 50%;
				vertical-align: top;
			}
		}
		.preview img, .preview audio {
			width: 100%;
		}
		.link > a {
			display: block;
			padding: 0.3em;
			width: 100%;
			color: #333;
			box-sizing: border-box;
			border-radius: 5px;
		}
		footer {
			padding: 1em 0;
			color: #eee;
			background-color: #333;
			text-align: center;
		}
		footer h1 {
			margin: 1em 0;
			font-size: 1em;
		}
		footer nav a {
			color: #eee;
			text-decoration: none;
		}
		footer nav a:hover {
			text-decoration: underline;
		}
		footer address {
			margin: 1em 0;
			color: #999;
		}
	</style>
	<title>节奏大师官方谱面下载 - 睡梦中吧</title>
</head>
<body>
	<aside onclick="this.style.display='none'">
		<h1>免责声明</h1>
		<section>
			<p>本页面仅供学习交流使用，请勿用于商业用途。</p>
			<p>请使用者自觉遵守<a href="//game.qq.com/contract.shtml" target="_blank">腾讯游戏许可及服务协议</a>。</p>
			<p>使用者应当从正规途径下载数据，否则后果自负。</p>
		</section>
	</aside>
	<header>
		<input class="search-text" type="text" placeholder="搜索歌曲" onkeyup="search(this.value, event)" onclick="search(this.value)"><a href="javascript:clear();void 0;">×</a>
	</header>
	<main>
		<section class="list"></section>
		<section class="result"></section>
	</main>
	<footer>
		<h1>相关链接</h1>
		<nav>
			<a href="https://github.com/ganlvtech/DownImd" target="_blank">Fork me on GitHub</a>
			<a href="http://www.dreamimd.com/" target="_blank">睡梦中IMD下载站</a>
			<a href="http://tieba.baidu.com/f?kw=%CB%AF%C3%CE%D6%D0" target="_blank">睡梦中吧</a>
		</nav>
		<address>作者：Ganlv</address>
	</footer>
	<script>
		var names = <?php echo $cache[1], ', paths = ', $cache[2], ', state = ', json_encode($state); ?>, suffixes = [".mp3",".jpg","_ipad.jpg", "_4k_ez.imd","_4k_nm.imd","_4k_hd.imd", "_5k_ez.imd","_5k_nm.imd","_5k_hd.imd", "_6k_ez.imd","_6k_nm.imd","_6k_hd.imd", "_Papa_Easy.mde","_Papa_Normal.mde","_Papa_Hard.mde"];
		function $(className) {
			return document.getElementsByClassName(className)[0];
		}
		function clear() {
			$("search-text").value="";
			$("list").style.display = "none";
		}
		function search(str, event) {
			var html = '<h1>' + state + '</h1>', count = 0, tmpPath;
			if (str != '')
				html += '<a href="javascript:show(\'' + str + '\', true);void 0;">直接下载' + str + '</a>';
			for (var i in paths) {
				var regexp = new RegExp(str, "i");
				if ((names[i].search(regexp) >= 0) || (paths[i].search(regexp) >= 0)) {
					html += '<a href="javascript:show(\'' + paths[i] + '\', true);void 0;">' + names[i] + '</a>';
					tmpPath = paths[i];
					++count;
				}
			}
			if (0 === count) {
				show(str, (13 === event.keyCode));
			} else if (1 === count) {
				show(tmpPath, true);
			}
			$("list").innerHTML = html;
			$("list").style.display = "";
			document.body.scrollTop = 0;
		}
		function show(path, isLoadRes) {
			var html = '<section class="link">', baseUrl = "http://game.ds.qq.com/Com_SongRes/song/" + path + "/" + path;
			for (var i in suffixes) {
				html += "<a href='" + baseUrl + suffixes[i] + "' target='_blank'>" + path + suffixes[i] + '</a>';
			}
			html += '</section>';
			if (isLoadRes) {
				html = '<section class="preview"><img src="' + baseUrl + '_title_ipad.jpg"><audio src="' + baseUrl + '.mp3" preload="none" loop="loop" controls>你的浏览器不支持直接播放歌曲</audio></section>' + html;
			}
			$("list").style.display = "none";
			$("result").innerHTML = html;
			$("result").style.display = "";
		}
		search("");
		$("result").style.display = "none";
	</script>
</body>
</html>
<?php } else { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="Content-Language" content="zh-cn" />
	<style>
		h1, h2 {
			margin: 0.3em 0;
		}
		p {
			margin: 0.2em 1em;
		}
		div {
			margin-left: 1em;
		}
		body > div {
			margin-left: 0;
		}
		h1 {
			font-size: 1.3em;
		}
		h2 {
			font-size: 1.1em;
		}
	</style>
	<title>节奏大师官方谱面下载 - 睡梦中吧</title>
</head>
<body>
	<div>
		<h1>免责声明：</h1>
		<div>
			<p>本页面仅供学习交流使用，请勿用于商业用途。</p>
			<p>请使用者自觉遵守<a href="//game.qq.com/contract.shtml" target="_blank">腾讯游戏许可及服务协议</a>。</p>
			<p>使用者应当从正规途径下载数据，否则后果自负。</p>
		</div>
	</div>
	<div>
		<h1>下载：</h1>
		<p><?php echo $state; ?></p>
		<p><input id="option-audio" type="checkbox" />试听mp3音乐</p>
		<div>
			<h2>输入path显示链接</h2>
			<p><input type="text" onkeyup="show(this.value, (13 === event.keyCode))"/></p>
		</div>
		<div>
			<h2>选择歌名显示链接</h2>
			<p><select id="select" onclick="show(this.value, true)" onchange="show(this.value, true)"></select></p>
		</div>
		<div>
			<h2>搜索歌曲名或path</h2>
			<p><input type="text" onkeyup="search(this.value, event)"/></p>
			<div id="list"></div>
		</div>
	</div>
	<div id="result"></div>
	<div>
		<h1>相关链接</h1>
		<div>
			<p><a href="https://github.com/ganlvtech/DownImd" target="_blank">Fork me on GitHub</a></p>
			<p><a href="http://www.dreamimd.com/" target="_blank">睡梦中IMD下载站</a></p>
			<p><a href="http://tieba.baidu.com/f?kw=%CB%AF%C3%CE%D6%D0" target="_blank">睡梦中吧</a></p>
		</div>
		<p>作者：Ganlv</p>
	</div>
	<script type="text/javascript">
		var names = <?php echo $cache[1], ', paths = ', $cache[2]; ?>, suffixes = [".mp3",".jpg","_ipad.jpg", "_4k_ez.imd","_4k_nm.imd","_4k_hd.imd", "_5k_ez.imd","_5k_nm.imd","_5k_hd.imd", "_6k_ez.imd","_6k_nm.imd","_6k_hd.imd", "_Papa_Easy.mde","_Papa_Normal.mde","_Papa_Hard.mde"];
		function $(id) {
			return document.getElementById(id);
		}
		function search(str, event) {
			var html = '', count = 0, tmpPath;
			if (str != '')
				html += '<p><a href="javascript:show(\'' + str + '\', true);void 0;">直接下载' + str + '</a></p>';
			for (var i in paths) {
				var regexp = new RegExp(str, "i");
				if ((names[i].search(regexp) >= 0) || (paths[i].search(regexp) >= 0)) {
					html += '<p><a href="javascript:show(\'' + paths[i] + '\', true);void 0;">' + names[i] + '</a></p>';
					tmpPath = paths[i];
					++count;
				}
			}
			if (0 === count) {
				show(str, (13 === event.keyCode));
			} else if (1 === count) {
				show(tmpPath, true);
			}
			$("list").innerHTML = html;
			document.body.scrollTop = 0;
		}
		function show(path, isLoadRes) {
			var html = '<div class="link">', baseUrl = "http://game.ds.qq.com/Com_SongRes/song/" + path + "/" + path;
			for (var i in suffixes) {
				html += "<p><a href='" + baseUrl + suffixes[i] + "' target='_blank'>" + path + suffixes[i] + '</a></p>';
			}
			html += '</div>';
			if (isLoadRes) {
				var preview_html = '<div class="preview"><p><img src="' + baseUrl + '_title_ipad.jpg"></p>';
				if ($("option-audio").checked)
					preview_html += '<p><audio src="' + baseUrl + '.mp3" preload="none" loop="loop" controls>你的浏览器不支持直接播放歌曲</audio></p>'
				html = preview_html + '</div>' +  html;
			}
			$("result").innerHTML = '<h1>链接：</h1>' + html;
		}
		+function () {
			var select = $("select");
			for (var i in paths) {
				var option = document.createElement("option");
				option.innerHTML = names[i];
				option.value = paths[i];
				select.appendChild(option);
			}
		}();
	</script>
</body>
</html>
<?php }