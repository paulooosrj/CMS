<?
class session{
	private $type;
	private $preStr;
	private $maxCookie;
	private $cookieLenght;
	private $stringone;
	private $duratacookie;
	private $secret;
	public function __construct ($cook="cookie",$secury=1,$salt="classeSegura",$prefix="_WS_") {
		$this->type 		= $cook;
		$this->secury 		= $secury;
		$this->preStr 		= $prefix;
		$this->maxCookie	=20;
		$this->cookieLenght	=3096;	
		$this->duratacookie	=3600*24;	
		$this->secred 		=$salt;		
		if ($this->type=="cookie") {
			if($secury==1){$this->stringone = $this->prelevaStringaTotale();}
		} else {
			if($secury==0){
				ini_set("session.gc_maxlifetime","432000");
				ini_set("url_rewriter.tags","");
				ini_set("session.use_trans_sid", false);
				session_start();
			}else{
				
				ini_set("session.gc_maxlifetime","432000");
				ini_set("url_rewriter.tags","");
				ini_set("session.use_trans_sid", false);
				session_id($this->crypta($this->preStr));
				session_name($this->crypta($this->preStr));
				session_start();
			}
		}
	}
 	private function build_str($ar) {
		$qs = array();
		foreach ($ar as $k => $v) { $qs[] = $k.'='.$v; }
		return join('&', $qs);
	}
	private function prelevaStringaTotale() {
		$cookiesSet = array_keys($_COOKIE);
		$out = "";
		for ($x=0;$x<count($cookiesSet);$x++) {
			if (strpos(" ".$cookiesSet[$x],$this->preStr)==1)
				$out.=$_COOKIE[$cookiesSet[$x]];
		}
		return $this->decrypta($out);
	}
	public function debug() {return $this->prelevaStringaTotale();}
 	private function calcolaCookieLiberi() {
		$cookiesSet = array_keys($_COOKIE);
		$c=0;
		for ($x=0;$x<count($cookiesSet);$x++) {
			if (strpos(" ".$cookiesSet[$x],$this->preStr)==1)
				$c+=1;
		}
		return $this->maxCookie - count($cookiesSet) + $c;
	}
	private function my_str_split($s,$len) {
		$output = array();
		if (strlen($s)<=$len) {
			$output[0] = $s;
			return $output;
		}
		$i = 0;
		while (strlen($s)>0) {
			$s = substr($s,0,$len);
			$output[$i]=$s;
			$s = substr($s,$len);
			$i++;
		}
		return $output;
	}
	public function set($var,$value) {
		if ($this->type=="cookie") {
				if($this->secury==1){
					if ($this->stringone!="") {
						parse_str($this->stringone, $vars);
					} else {
						$vars=array();
					}
					$vars[$var] = $value;
					$str = $this->crypta($this->build_str($vars));
					$arr = $this->my_str_split($str,$this->cookieLenght);
					$cLiberi = $this->calcolaCookieLiberi();
					if (count($arr) < $cLiberi) {
						$this->stringone = $this->build_str($vars);
						for ($i=0;$i<count($arr);$i++) {
							setcookie($this->preStr.$i,$arr[$i],time()+$this->duratacookie,"/", $_SERVER['HTTP_HOST'] );
						}
					} else {
						return "errore cookie overflow";
					}
				}else{
					setcookie($var,$value,time()+$this->duratacookie,"/", $_SERVER['HTTP_HOST'] );
				}
		} else {
			if($this->secury==1){
				$_SESSION[$var]=$this->crypta($value);
			}else{
				$_SESSION[$var]=$value;
			}
		}
	}
	public function get($var) {
		if ($this->type=="cookie") {
				if($this->secury==1){
					if ($this->stringone!="") {
						parse_str($this->stringone, $vars);
					} else {
						return "";
					}
					if(!isset($vars[$var])) {
						return "";
					}
					return $vars[$var];

				}else{
					return $_COOKIE[$var];
				}
		} else {
			if($this->secury==1){
				return $this->decrypta($_SESSION[$var]);
			}else{
				return $_SESSION[$var];
			}
		}
	}
 	public function finish() {
		if ($this->type=="cookie") {
			if($this->secury==1){
				$cookiesSet = array_keys($_COOKIE);
				for ($x=0;$x<count($cookiesSet);$x++) {
					if (strpos(" ".$cookiesSet[$x],$this->preStr)==1)
						setcookie($cookiesSet[$x],"",time()-3600*24,"/",$_SERVER['HTTP_HOST']);
						$this->stringone="";
				}
			}else{
				$cookiesSet = array_keys($_COOKIE);
				for ($x=0;$x<count($cookiesSet);$x++) {
					setcookie($cookiesSet[$x],"",time()-3600*24,"/", $_SERVER['HTTP_HOST'] );
				}
			}
		} else {
			session_destroy();
			$_SESSION = array();
		}
	}
	private function crypta($t){
		if ($t=="") return $t;
		$r = md5(10); $c=0; $v="";
		for ($i=0;$i<strlen($t);$i++){
			if ($c==strlen($r)) $c=0;
			$v.= substr($r,$c,1) . (substr($t,$i,1) ^ substr($r,$c,1));
			$c++;
		}
		return (base64_encode($this->ed($v)));
	}
	private function decrypta($t) {
		if ($t=="") return $t;
		$t = $this->ed(base64_decode(($t)));
		$v = "";
		for ($i=0;$i<strlen($t);$i++){
			$md5 = substr($t,$i,1);
			$i++;
			$v.= (substr($t,$i,1) ^ $md5);
		}
		return $v;
	}
	private function ed($t) {
		$r = md5($this->secret); $c=0; $v="";
		for ($i=0;$i<strlen($t);$i++) {
			if ($c==strlen($r)) $c=0;
			$v.= substr($t,$i,1) ^ substr($r,$c,1);
			$c++;
		}
		return $v;
	}
}
?>
