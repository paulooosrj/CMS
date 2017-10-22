<?
class session{
	private $type;
	private $preStr;
	private $maxCookie;
	private $cookieLenght;
	private $stringone;
	private $duratacookie;
	private $secret;
	public function __construct ($name="ws-session") {
		$this->type 		= 	"session"; //cookie
		$this->secury 		= 	1;
		$this->prefix 		= 	"ws-"; 
		$this->preStr 		= 	ID_SESS; 
		$this->secret 		=	LOGGED_IN_SALT;		
		$this->maxCookie	=	20;
		$this->CoockieIdSess=	md5('ws-idsess');
		$this->cookieLenght	=	3096;	
		$this->duratacookie	=	(time() + ( 24 * 3600));	
		$this->newName 		= 	strtolower($this->prefix.substr(str_replace(array("_","-","==","=","."," "),"",base64_encode(md5($this->preStr))),0,256));
		$this->start();
	}

	public function start() {
		if($this->verifyLogin()==false){
			if ($this->type=="cookie") {
				if($this->secury==1){
					$this->stringone = $this->prelevaStringaTotale();
				}
			} else {
				if($this->secury==0){
					####################################################################
					# ALGUNS SERVIDORES VEM COM O DIRETÓRIO /TMP SEM PERMISSÃO PRA LEITURA OU ESCRITA
					# ENTÃO PARA PREVINIR ISSO JÁ JOGAMOS A PERMISSÃO 0700
					####################################################################
					chmod(session_save_path().'/sess_'.session_id(), 0700);
					
					####################################################################
					ini_set("session.gc_maxlifetime","432000");
					ini_set("url_rewriter.tags","");
					ini_set("session.use_trans_sid", false);
					if(empty($_SESSION) && session_id()!=$this->preStr){
						session_id($this->preStr);
						session_name($this->preStr);
						$status = session_status();
						if($status == PHP_SESSION_NONE){
							session_start();
						}
					}
				}else{
					@ini_set("session.cookie_secure",true);
					@ini_set("session.cookie_httponly",true);
					@ini_set("session.use_trans_sid", false);
					 if(
						 	(empty($_COOKIE[$this->CoockieIdSess])) ||
						 	(isset($_COOKIE[$this->CoockieIdSess]) && $_COOKIE[$this->CoockieIdSess]!=$this->newName) 
					 ){
						$status = session_status();
						session_id($this->newName);
						session_name($this->newName);
						if($status == PHP_SESSION_NONE){session_start();}
						setcookie($this->CoockieIdSess, $this->newName,$this->duratacookie,'/');	
					}else{
						$status = session_status();
						session_id($_COOKIE[$this->CoockieIdSess]);
						session_name($_COOKIE[$this->CoockieIdSess]);
						@session_start();

					}
				}
			}
		}
	}

	public function verifyLogin() {
		 if ($this->get("ws_log")==1) {
		 	return true;
		 }else{
		 	return false;
		 }
	}
 	private function build_str($ar) {
		$qs = array();
		foreach ($ar as $k => $v) { $qs[] = $k.'='.$v; }
		return join('&', $qs);
	}
	private function prelevaStringaTotale() {
		$cookiesSet = array_keys($_COOKIE);
		$out 		= "";
		for ($x=0;$x<count($cookiesSet);$x++) {
			if (strpos(" ".$cookiesSet[$x],$this->preStr)==1)
				$out.=$_COOKIE[$cookiesSet[$x]];
		}
		return $this->decrypta($out,$this->secret);
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
					$str = $this->crypta($this->build_str($vars),$this->secret);
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
				$_SESSION[$var]=$this->crypta($value,$this->secret);
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
					if(!isset($vars[$var])) {return "";}
					return $vars[$var];

				}else{
					return $_COOKIE[$var];
				}
		} else {
			if($this->secury==1){
				return $this->decrypta(@$_SESSION[$var],$this->secret);
			}else{
				return @$_SESSION[$var];
			}
		}
	}
 	public function finish() {
			if($this->secury==1){
				$cookiesSet = array_keys($_COOKIE);
				for ($x=0;$x<count($cookiesSet);$x++) {
					if (strpos(" ".$cookiesSet[$x],$this->preStr)==1){
						setcookie($cookiesSet[$x],"",time()-3600*24,"/",$_SERVER['HTTP_HOST']);
						$this->stringone="";
					}
				}
			}else{
				$cookiesSet = array_keys($_COOKIE);
				for ($x=0;$x<count($cookiesSet);$x++) {
					setcookie($cookiesSet[$x],"",time()-3600*24,"/", $_SERVER['HTTP_HOST'] );
				}
			}
			session_id($_COOKIE[$this->CoockieIdSess]);
			session_name($_COOKIE[$this->CoockieIdSess]);
			$_SESSION=array();
			unset($_SESSION);
			session_unset();
			session_destroy();
			session_write_close();
			flush();
	}
	private function crypta($t,$secret){
		if ($t=="") return $t;
		return _encripta($t,$secret);
	}
	private function decrypta($t,$secret) {
		if ($t=="") return $t;
		return _decripta($t,$secret);
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
	public function verify() {
		
		  if(isset($_SESSION) && session_id()==$this->newName){
		 	 return true;
		  }else{
		 	 return false;
		 }
	}


}
?>
