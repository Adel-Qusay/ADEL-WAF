<?php
/*
 * AdelWAF is a lightweight PHP in-app Web Application Firewall.
 * https://github.com/Adel-Qusay/ADEL-WAF
 */
namespace App\Libraries;

class AdelWAF {

	public $PRODUCT = 'ADEL WAF';
	public $VERSION = '1.0'; 
	public $SITE = 'https://github.com/Adel-Qusay/ADEL-WAF'; 
	
	/* ------ USER CONFIGURATION VARIABLES -------- */	
	public $ENABLE_WAF = true;
	public $EXCLUDE_DOMAINS = array("zerowaf.test");
	public $ENABLE_EMAIL_NOTIFICATIONS = false;
	public $EMAIL = 'email@email.com'; 
	public $ENABLE_LOGS = false;
	public $LOGS_FILE = 'awlogs.txt';
	/* ------ END USER CONFIGURATION ------ */

	private $xssRules = array("<img", "img>", "<image", "</scr", "<script", "onabort=","onauxclick=","oncancel=","oncanplay=","oncanplaythrough=","onchange=","onclick=","onclose=","oncontextmenu=","oncuechange=","ondblclick=","ondrag=","ondragend=","ondragenter=","ondragexit=","ondragleave=","ondragover=","ondragstart=","ondrop=","ondurationchange=","onemptied=","onended=","onformdata=","oninput=","oninvalid=","onkeydown=","onkeypress=","onkeyup=","onloadeddata=","onloadedmetadata=","onloadstart=","onmousedown=","onmouseenter=","onmouseleave=","onmousemove=","onmouseout=","onmouseover=","onmouseup=","onpause=","onplay=","onplaying=","onprogress=","onratechange=","onreset=","onsecuritypolicyviolation=","onseeked=","onseeking=","onselect=","onslotchange=","onstalled=","onsubmit=","onsuspend=","ontimeupdate=","ontoggle=","onvolumechange=","onwaiting=","onwebkitanimationend=","onwebkitanimationiteration=","onwebkitanimationstart=","onwebkittransitionend=","onwheel=","onblur=","onerror=","onfocus=","onload=","onresize=","onscroll=","onafterprint=","onbeforeprint=","onbeforeunload=","onhashchange=","onlanguagechange=","onmessage=","onmessageerror=","onoffline=","ononline=","onpagehide=","onpageshow=","onpopstate=","onrejectionhandled=","onstorage=","onunhandledrejection=","onunload=","oncut=","oncopy=","onpaste=","onreadystatechange=","<iframe", "javascript:","<frame","<embed","<object","href=","src=");
	
	private $sqliRules = array("table_schema", "or '1", "unhex(hex(concat(", "select*from", ";--","and(select", "or(select", "count(", "information_schema", "schema_name", "extractvalue", "concat(", "json_keys(", "droptable", "selectif","'select","unionall","'and'","'or'", "unionselect","orderby","groupby","insertinto","intooutfile","benchmark(","waitfordelay","waitfortime","sleep(");
	
	private $lfiRules = array("..\\","../","..\\/");
	
	private $rfiRules = array("file://","ftp://","ftps://","http://","https://");
	
    private $rceRules = array("bin/", "cmd/", "&&", ">/", "system(","exec(", "<?", "?>");

	private $webShellRules = array("c99shell", "<h1 style=\"margin-bottom: 0\">webadmin.php</h1>", "b374k m1n1", "Shell I</title>", "SmEvK_PaThAn Shell", "by C-W-M</title>", "<title>azrail ", "<title>punkholicshell</title>", "<title>g00nshell", "<title>PhpSpy Ver", "Cr@zy_King</title>", "<title>s72 Shell v", "<title>Ru24PostWebShell -", "<font size=\"1\">Input command :</font>", "<title>PHP Web Shell</title>", "<title>lostDC - ", "<title>lama's'hell v", "<title>Web Shell</title>", "<title>SimAttacker - ", "<small>NGHshell", "<title>GRP WebShell ", "<title>CasuS ", "by MafiABoY</title>", "<title>Symlink_Sa", "Ashiyane V", "Developed By LameHacker", "<title>Mini Shell</title>", "- WSO","B4TM4N SH3LL</title>", "<meta name='author' content='k4mpr3t'/>", "<title>=[ 1n73ct10n privat shell ]=</title>", ">Ajax/PHP Command Shell<", ".:: :[ AK-74 Security Team Web-shell ]: ::.", "~ ALFA TEaM Shell -", "<title>--==[[ Andela Yuwono Priv8 Shell ]]==--</title>", "<title>Ani-Shell | India</title>", "<input type='submit' value='file' /></form>AnonymousFox", "- Antichat Shell</title>", "Ayyildiz Tim  | AYT", "<link rel='SHORTCUT ICON' href='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MkRFNDY2MDQ4MDgyMTFFM0FDRDdBN0MzOTAxNzZFQUYiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MkRFNDY2MDU4MDgyMTFFM0FDRDdBN0MzOTAxNzZFQUYiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyREU0NjYwMjgwODIxMUUzQUNEN0E3QzM5MDE3NkVBRiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyREU0NjYwMzgwODIxMUUzQUNEN0E3QzM5MDE3NkVBRiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pu6UWJYAAAKySURBVHjafFNdSJNhFH7e/fhDkrm2i03QphsxhYSgMIUgIeiiK6/SCAKTKNlFoEtBRfEvXYhM+0GQMtMUL7qSgqS0QCNKTDS6cJWGi6n577Zv3/e+b+934ZgxPfDBd3jP85xznnOOzufz4SCr7R7knKOg4eaVd9WPBgsZY/3NZcWJ0TGaaKeuZzgz2ueMgFF+p6WnL0OAjzMK+f8k+wg4xXxN91D5ns8ok8CRH5S2GogS8HBKk1xud+uBBIwpm5zyRvW/+sHAJuM8nsrMIElHi0/aHAmFl/OI2WRyOevrK/YwJFoD0ecFkfWthpDNRH1Cct4ZOzRaglX/DsY+TcNqTUd2phEjo1OiWg5KKUhJTbua6XTT7SKvSlLpGWB6DUjuWQeW/m4iJIWho8DvBT+2tgOwpZsxM/tm/sn9Trsar2OMq6rOV3X19wncJUNSEsnKSsWifx0BKYTgdhDxiENBfjZCuxJejX0W4frZiAZNZUVxVKYfmcyuKTI15ZxKw4IA74aCCIiMeqZDptWIuV8+hAkXOlFo9eaLNyrvOfdp4Gp/FjKlpMSbLMlY2dhCaCcEnUJgt5sF4QqkkIKsDAtGXn9QSThlMmFCg8gUmELpkXg99FoNwgEJ2jBBWpoBP/8sC7AMi/EY/EvLUBQJCpOMT921hDG5JkIglPd8/7EIFpShCQMnrAYsrW0gLERUwTNfv2FyaloddWmvu25NxTzvaG6MELRVXK/SgL8fHZ9AjsMCKUzFqBhSjQZAkrC6viqyy+ILdxU775bH3APVblW3j3POzuc4bGIHNPgyM4dAcFdtslT07OWcvhRVJIvVtg0/9nhJrGMqqWzpFb1eFYuiVfdbACcGOlvzYx0cOewaVStyuiY5U3JFVbahhx3eQ48plr3obDtHqSxTRZ6K9f5PgAEAm/hvADIkGOQAAAAASUVORK5CYII='>", "<title>BloodSecurity Hackers Shell</title>", "<font color='red' size='6px' face='Fredericka the Great'> Bypass Attack Shell </font>", "title='.::[c0derz shell]::.'>", "<font face=Webdings size=6><b>!</b></font>", "<title>Con7ext Shell V.2</title>", "<font face=\"Wingdings 3\" size=\"5\">y</font><b>Crystal shell v.", "~ CWShell ~</font></a>", "&dir&pic=o.b height= width=>", "<b>[ Defacing Tool Pro v", "<title>Dive Shell - Emperor Hacking Team</title>", "<script>document.getElementById(\"cmd\").focus();</script>", "<p align=\"center\" class=\"style4\">FaTaLSheLL v", "<title>G-Security Webshell</title>", "<title>h4ntu shell [powered by tsoi]</title>", "<H1><center>-=[+] IDBTEAM SHELLS", "<title>IndoXploit</title>", "<KAdot Universal Shell>     |", ">LIFKA SHELL</span></big></big></big></a>", "<title>Loader'z WEB shell</title>", "b>--[ x2300 Locus7Shell v.", "<title>Lolipop.php - Edited By KingDefacer -", "<title> Matamu Mat </title>", "<b>MyShell</b> &copy;2001 Digitart Producciones</a>", "<h1>.:NCC:. Shell v", "<font size=3>PHPShell by Macker - Version", "PHPShell by MAX666, Private Exploit, For Server Hacking", "<form action=\"\" METHOD=\"GET\" >Execute Shell Command (safe mode is off): <input type=\"text\" name=\"c\"><input type=\"submit\" value=\"Go\"></form>", "<p align=\"center\"><font face=\"Verdana\" size=\"2\">Rootshell v", "<font color=lime>./rusuh</font>", "<center><h1>Watch Your system Shany was here.</h1></center><center><h1>Linux Shells</h1></center><hr><hr>", "<!-- Simple PHP backdoor by DK", "<title>SimShell - Simorgh Security MGZ</title>", "<title>:: AventGrup ::.. - Sincap", "<title>Small Shell - Edited By KingDefacer</title>", "<title>small web shell by zaco", "<title>SoldiersofAllah Private Shell |", "<title>Sosyete Safe Mode Bypass Shell", "&nbsp;&nbsp;STNC&nbsp;WebShell&nbsp;", "<font face=\"Wingdings 3\" size=\"5\">y</font><b>StresBypass<span lang=\"en-us\">v", "<title>SyRiAn Sh3ll ~", "<head><title>Wardom | Ne Mutlu T", "<hr>to browse go to http://?d=[directory here]", "<font color=\"red\">USTADCAGE_48</font> <font color=\"dodgerblue\">FILE MANAGER</font>", "<div align=\"center\"><span class=\"style6\">By BLaSTER</span><br />", "<title>-:[GreenwooD]:- WinX Shell</title>", "<title>Yourman.sh Mini Shell</title>", "</div><center><br />Zerion Mini Shell <font color=", "<title>0byt3m1n1-V2</title>", "<title>ZEROSHELL | ZEROSTORE</title>", "<input type=submit name=find value='find writeable'>", "<title>r57 shell</title>");
	
	function isDA()
	{
		if (realpath(__FILE__) == realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'])) {
			header("Location: " . $this->SITE, true, 302);
			die();
		}
	}
	
	function strposa($needles, $str) {
		foreach ($needles as $needle) {
			if (strpos($str, $needle) !== false) {
				return true;
			}			
		}
		return false;
	}
	
	function clean($data){
		return filter_var(rawurldecode(htmlspecialchars($data)), FILTER_SANITIZE_STRING);
	}
		
	function getEnvir($st_var) {
		global $HTTP_SERVER_VARS;
		if(isset($_SERVER[$st_var])) {
			return strip_tags( $_SERVER[$st_var] );
		} elseif(isset($_ENV[$st_var])) {
			return strip_tags( $_ENV[$st_var] );
		} elseif(isset($HTTP_SERVER_VARS[$st_var])) {
			return strip_tags( $HTTP_SERVER_VARS[$st_var] );
		} elseif(getenv($st_var)) {
			return strip_tags(getenv($st_var));
		} elseif(function_exists('apache_getenv') && apache_getenv($st_var, true)) {
			return strip_tags(apache_getenv($st_var, true));
		}
		return 'UNKNOWN';
	}

	function getRef() {
		if( $this->getEnvir('HTTP_REFERER') )
			return $this->getEnvir('HTTP_REFERER');
		return 'UNKNOWN';
	}

	function getIP() {
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			return $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
			return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			return $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			return $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			return $_SERVER['REMOTE_ADDR'];
		else
			return 'UNKNOWN';
	}

	function getUA() {
		if($this->getEnvir('HTTP_USER_AGENT'))
			return $this->getEnvir('HTTP_USER_AGENT');
		return 'UNKNOWN';
	}

	function getFullURL() {
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	function getHost() {
		return $_SERVER['HTTP_HOST'];
	}

	function infoCollect() {
		$info = [];
		$info['time'] = date('d-m-Y H:i:s');		
		$info['ipAddress'] = $this->clean($this->getIP());		
		$info['referer'] =  $this->clean($this->getRef());
		$info['userAgent'] =  $this->clean($this->getUA());
		$info['url'] = htmlspecialchars($this->getFullURL());
		$info['method'] =  $this->clean($_SERVER['REQUEST_METHOD']);		
		return $info;
	}
	
	function logs($info, $msg, $key, $value) {
		if ($this->ENABLE_LOGS) file_put_contents($this->LOGS_FILE, implode(' -> ', $info)."\r\n", FILE_APPEND);
		if ($this->ENABLE_EMAIL_NOTIFICATIONS) mail($this->EMAIL,'An attempted '.$msg.' was detected and blocked',$info['time'] . "\tIP: " . $info['ipAddress'] . "\tMethod: " . $_SERVER['REQUEST_METHOD'] . "\tURL: " . $info['url'] . "\tUser-Agent: " . $info['userAgent'] . "\tParameter: " . $key ."=" . $value . "\tAttack Type: " . $msg . "\tProduct: " . $this->PRODUCT . " " . $this->VERSION);
	}

	function warn($info, $msg, $key= '', $value = '') {
		$this->logs($info, $msg, $key, $value);
		$this->warningHTML($info, $_SERVER['REQUEST_METHOD'], $msg);
	}
	

	function warningHTML($info, $method, $typeVuln) {
		header('HTTP/1.0 403 Forbidden');
		die('<!DOCTYPE html><html lang="en" xmlns="//www.w3.org/1999/xhtml"><head><style>.app-header,body{text-align:center }.btn,button.btn,input.btn{border:0;outline:0;display:inline-block;vertical-align:middle;border-radius:5em;background-color:#609f43;color:#fff;padding:5px 12px;background-repeat:no-repeat;font-size:14px }.btn:hover{background-color:#58913d }.clearfix:after,.clearfix:before,footer,header,section{display:block }.clearfix:after,.clearfix:before,.row:after{clear:both;content:"" }.clearfix:after,.clearfix:before,.logo-neartext:before,.row:after{content:"" }*{margin:0;padding:0 }html{box-sizing:border-box;font-family:"Open Sans",sans-serif }body,html{height:100% }*,:after,:before{box-sizing:inherit }body{background-color:#e8e8e8;font-size:14px;color:#222;line-height:1.6;padding-bottom:60px }h1{font-size:36px;margin-top:0;line-height:1;margin-bottom:30px }h2{font-size:25px;margin-bottom:10px }a{color:#1e7d9d;text-decoration:none }a:hover{text-decoration:underline }.access-denied .btn:hover,.site-link,footer a{text-decoration:none }.color-green{color:#609f43 }.color-gray{color:grey }hr{border:0;margin:20px auto;border-top:1px #e2e2e2 solid }[class*=icon-circle-]{display:inline-block;width:14px;height:14px;border-radius:50%;margin:-5px 8px 0 0;vertical-align:middle }.icon-circle-red{background-color:#db1802 }#main-container{min-height:100%;position:relative }.app-header{background-color:#333;min-height:50px;padding:0 25px }.app-header .logo{display:block;width:100px;height:24px;float:left;background:url(data:image/png;base64,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) center center no-repeat;background-size:100px 24px;position:absolute;left:0;top:12px }.logo-neartext{display:inline-block;margin-top:3px;color:#fff;font-size:25px;font-weight:600 }.site-link{color:#8a8a8a;font-size:11px;position:absolute;top:15px;right:0 }#recaptcha_image,.box,.captcha,.wrap{position:relative }.wrap{max-width:1090px;margin:auto }.app-content{max-width:580px;margin:20px auto 0;text-align:left;text-align:center }.box{border-radius:10px;background-color:#fff;padding:35px;box-shadow:0 1px 0 0 #d4d4d4;margin:0 4% 35px }#block-details{margin-bottom:35px;margin-top:25px }.row:first-child{border-top:0!important }.row:last-child{border-bottom:0!important }.row:nth-child(even){border:1px solid #e2e2e2;border-left:0;border-right:0;background:#fafafa }.row:after{display:block }.row>div{float:left;padding:12px;word-wrap:break-word }.row>div:first-child{width:15%;font-weight:700 }.row>div:last-child{width:85% }.code-snippet{border:1px solid grey;background-color:#f7f7f7;box-shadow:0 1px 4px 0 rgba(0,0,0,.2);border-radius:8px;padding:18px;margin:30px 0 30px }.medium-text{font-size:16px;clear:both }footer{margin-top:50px;margin-bottom:50px;font-size:13px;color:grey }#privacy-policy{padding-left:25px }@media (max-width:979px){h1{font-size:30px }h2{font-size:20px }.row>div{float:none;width:100%!important }}.captcha{background-color:#fff;width:370px;margin:auto;padding:25px 35px 35px;border-radius:10px;box-shadow:0 1px 0 0 #d4d4d4;border:1px solid #bfbfbf }.captcha-title{text-align:left;margin-bottom:15px;font-size:13px;line-height:1 }table.recaptchatable{margin-left:-14px!important }table#recaptcha_table input[type=text]{height:37px;display:block;width:300px!important;padding:10px!important;border-color:#b8b8b8;font-size:14px;margin-top:20px!important }table#recaptcha_table input[type=text]:focus{background-color:#f9f9f9;border-color:#222;outline:0 }table#recaptcha_table td{display:block;background:0!important;padding:0!important;height:auto!important;position:static!important }#recaptcha_image{border:1px solid #b8b8b8!important;padding:5px;height:60px!important;margin-bottom:25px!important;left:-2px;overflow:hidden;-moz-box-sizing:border-box!important;-webkit-box-sizing:border-box!important;box-sizing:border-box!important }#recaptcha_image img{position:absolute;left:0;top:0 }#recaptcha_reload_btn,#recaptcha_switch_audio_btn,#recaptcha_whatsthis_btn{position:absolute;top:25px }#recaptcha_reload_btn{right:78px }#recaptcha_switch_audio_btn{right:52px }#recaptcha_whatsthis_btn{right:28px }.recaptcha_input_area{margin-left:-7px!important }button.ajax-form{width:300px;cursor:pointer;height:37px;padding:0!important }#recaptcha_privacy{position:absolute!important;top:105px!important;display:block;margin:auto;width:300px;text-align:center }#recaptcha_privacy a{color:#1e7d9d!important }.what-is-firewall{width:100%;padding:35px;background-color:#f7f7f7;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box;margin-left:-35px;margin-bottom:-35px;border-radius:0 0 15px 15px }.access-denied .center{display:table;margin-left:auto;margin-right:auto }.width-max-940{max-width:940px }.access-denied{max-width:none;text-align:left }.access-denied h1{font-size:25px }.access-denied .font-size-xtra{font-size:36px }.access-denied table{margin:25px 0 35px;border-spacing:0;box-shadow:0 1px 0 0 #dfdfdf;border:1px solid #b8b8b8;border-radius:8px;width:100%;background-color:#fff }.access-denied table:first-child{margin-top:0 }.access-denied table:last-child{margin-bottom:0 }.access-denied th{background-color:#ededed;text-align:left;white-space:nowrap }.access-denied th:first-child{border-radius:8px 0 0 }.access-denied th:last-child{border-radius:0 8px 0 0 }.access-denied td{border-top:1px #e2e2e2 solid;vertical-align:top;word-break:break-word }.access-denied td,.access-denied th{padding:12px }.access-denied td:first-child{padding-right:0 }.access-denied tbody tr:first-child td{border-color:#c9c9c9;border-top:0 }.access-denied tbody tr:last-child td:first-child{border-bottom-left-radius:8px }.access-denied tbody tr:last-child td:last-child{border-bottom-right-radius:8px }.access-denied tbody tr:nth-child(2n){background-color:#fafafa }table.property-list td:first-child,table.property-table td:first-child{font-weight:700;width:1%;white-space:nowrap }.overflow-break-all{-ms-word-break:break-all;word-break:break-all }</style><section class="center clearfix"><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>'. $this->PRODUCT .'- Access Denied</title></head><body><div id="main-container"><header class="app-header clearfix"><div class="wrap"><span class="logo-neartext">Web Application Firewall</span></div></header><section class="app-content access-denied clearfix"><div class="box center width-max-940"><h1 class="brand-font font-size-xtra no-margin"><i class="icon-circle-red"></i>Access Denied - '. $this->PRODUCT .'</h1><p class="medium-text code-snippet">If you think this block is an error please <a href="mailto:adelbak2014@gmail.com">contact firewall developer</a> and make sure to include the block details (displayed in the box below), so we can assist you in troubleshooting the issue. </p><h2>Block details:</h1><table class="property-table overflow-break-all line-height-16"><tr><td>Your IP:</td><td><span>'. $info['ipAddress'] .'</span></td></tr><tr><td>URL:</td><td><span>'. $info['url'] .'</tr><tr><tr><td>Method:</td><td><span>'. $info['method'] .'</tr><tr><td>Your Browser: </td><td><span>'. $info['userAgent'] . '</tr><tr><td>Referer:</td><td><span>'. $info['referer'] .'</span></td></tr><tr><td>Block ID:</td><td><span>'. md5($info['time']) .'</span></td></tr><tr><td>Block reason:</td><td><span>An attempted ' . $typeVuln . ' was detected and blocked.</span></td></tr><tr><td>Time:</td><td><span>' . $info['time'].'</tr></table></div></section><footer><span>&copy; '.date('Y').' '.$this->PRODUCT.'- Free Open-Source Web Application Firewall.</span><span id="privacy-policy"><a href="https://github.com/Adel-Qusay/ADEL-WAF" target="_blank" rel="nofollow noopener">Github</a></span>');		
	}
	
	function run() {
		if ($this->ENABLE_WAF) {	
			$info = $this->infoCollect();
			if (!$this->strposa($this->EXCLUDE_DOMAINS, $_SERVER['HTTP_HOST'])) {				
				if (count($_REQUEST) > 20) {
					$this->warn($info, 'Denial of service (DOS)', 'count', count($_REQUEST));
				} else {
					foreach ($_REQUEST as $key => $value) {
						$value = html_entity_decode(str_replace(" ", "", strtolower($value)));
						if ($this->strposa($this->webShellRules, $value))
							$this->warn($info, 'Web shell', $key, $value);							
						elseif ($this->strposa($this->xssRules, $value))
							$this->warn($info, 'Cross-site scripting (XSS)', $key, $value);
						elseif ($this->strposa($this->sqliRules, $value))
							$this->warn($info, 'SQL injection (SQLI)', $key, $value);
						elseif ($this->strposa($this->rfiRules, $value))
							$this->warn($info, 'Remote file inclusion (RFI)', $key, $value);
						elseif ($this->strposa($this->rceRules, $value))
							$this->warn($info, 'Remote code execution (RCE)', $key, $value);
						elseif ($this->strposa($this->lfiRules, $value))
							$this->warn($info, 'Local file inclusion (LFI)', $key, $value);					
					}
				}
			}
		}
	}
}

?>
