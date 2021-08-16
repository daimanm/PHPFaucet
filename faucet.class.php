<?php
class faucet {
	public function __Construct() {
		session_start();
	}
	public function settemplate($name) {
		if (!file_exists("templates/".$name.".tpl")) {
			die($this->errormsg("404", "Template file not found!"));
		}
		else {
			$this->template = file_get_contents("templates/".$name.".tpl");
		}
	}
	public function setdb($host, $username, $password, $database) {
		$this->con = mysqli_connect($host, $username, $password, $database);
		if (mysqli_connect_error($this->con)) {
			die ($this->errormsg(mysqli_connect_errno($this->con), mysqli_connect_error($this->con)));
		}
	}
	function errormsg($errno, $message) {
		echo '<div style="background-color:#d97976; padding:10px; border:1px solid red;width:100%; color:white;"><b>Error code: '.$errno.'</b><br />'.$message.'</div>';
	}
	public function setrpc($host, $port, $username, $password) {
		$this->rpchost = $host;
		$this->rpcport = $port;
		$this->rpcuser = $username;
		$this->rpcpass = $password;
		$this->call_api("getinfo", array());
	}
	public function call_api($method, $params) {
		$message = json_encode(
			array('jsonrpc' => '2.0', 'id' => 1, 'method' => $method, 'params' => $params)
		);
		$requestHeaders = [
			'Content-type: application/json'
		];

		$ch = curl_init("http://".$this->rpcuser.":".$this->rpcpass."@".$this->rpchost.":".$this->rpcport."/");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
		
		$response = curl_exec($ch);
		$response = json_decode($response, true);
		curl_close($ch);
		if (!isset($response["result"]) && $method=="getinfo") {
			die($this->errormsg('400', 'The RPC configuration is incorrect!'));
		}
		else {
			return $response;
		}
	}
	public function balance() {
		$call = $this->call_api("getbalance", array());
		if (isset($call["error"]["code"])) {
			die($this->errormsg($call["error"]["code"], $call["error"]["message"]));
		}
		else {
			return $call["result"];
		}
	}
	public function sendcoins($wallet, $amount) {
		$call = $this->call_api("sendtoaddress", array($wallet, $amount));
		if (isset($call["error"]["code"])) {
			die($this->errormsg($call["error"]["code"], $call["error"]["message"]));
		}
		else {
			return $call["result"];
		}
	}
	public function setreward($min, $max) {
		$this->minreward = $min;
		$this->maxreward = $max;
	}
	public function setwallet($wallet) {
		$this->faucetwallet = $wallet;
	}
	public function settimers($captcha, $rewardtime) {
		$this->captchatime = $captcha;
		$this->rewardtime = $rewardtime;
	}
	function captcha() {
		$captcha = substr(sha1(microtime()),0,8);
		$_SESSION["captcha"] = $captcha;
		$captchahtml = "";
		for($i=0;$i<=(strlen($captcha)-1); $i++) {
			$ran = rand(1,5);
			if ($ran==1) {
				$color = "red";
			}
			elseif ($ran==2) {
				$color = "blue";
			}
			elseif ($ran==3) {
				$color = "green";
			}
			elseif ($ran==4) {
				$color = "yellow";
			}
			elseif ($ran==5) {
				$color = "orange";
			}
			$captchahtml = $captchahtml."<span style='color:".$color."; margin-left:".rand(1,10)."px; font-size:".rand(10,18)."px;'>&nbsp;".$captcha[$i]."&nbsp;&nbsp;&nbsp;</span>";
		}
		return $captchahtml;
	}
	public function ads($code) {
		$this->ads = $code;
	}
	public function config($coin, $ticker, $explorer, $site, $fullurl) {
		$this->coinname = $coin;
		$this->ticker = $ticker;
		$this->explorer = $explorer;
		$this->site = $site;
		$this->fullurl = $fullurl;
	}
	function form() {
		if ($this->balance() < 0.1) {
			return '<span>The faucet wallet is almost empty, The wallets needs to be refilled. To keep the faucet running please send some coins to: <b>'.$this->faucetwallet.'</b>';
		}
		elseif (isset($_POST["address"]) && isset($_POST["captcha"])) {
			$range = $this->maxreward - $this->minreward;
			$num = $this->minreward + $range * (mt_rand() / mt_getrandmax());    
			$num = round($num, 8);
			$amount = (float) $num;
			$amount = sprintf('%.8f', floatval($amount));
			$algehad = mysqli_query($this->con, "SELECT * FROM reward WHERE (address='".mysqli_real_escape_string($this->con, $_POST["address"])."' OR ip='".mysqli_real_escape_string($this->con, $_SERVER["REMOTE_ADDR"])."') AND (datum > (NOW() - INTERVAL ".mysqli_real_escape_string($this->con, $this->rewardtime)." HOUR))");
			if (mysqli_num_rows($algehad)>=1) {
				return "You got already an reward those ".$this->rewardtime." Hour(s)";
			}
			else {
				if ($_POST["captcha"]==$_SESSION["captcha"]) {
					$txid = $this->sendcoins($_POST["address"], $amount);
					mysqli_query($this->con, "INSERT INTO reward(ip, address, useragent, datum, referer, coin, amount, txid)VALUES(
					'".mysqli_real_escape_string($this->con, $_SERVER["REMOTE_ADDR"])."',
					'".mysqli_real_escape_string($this->con, $_POST["address"])."',
					'".mysqli_real_escape_string($this->con, $_SERVER['HTTP_USER_AGENT'])."',
					NOW(),
					'".mysqli_real_escape_string($this->con, $_SERVER['HTTP_REFERER'])."',
					'".mysqli_real_escape_string($this->con, $amount)."',
					'".mysqli_real_escape_string($this->con, $txid)."'
					)");
					unset($_SESSION["captcha"]);
					return '<span><b>'.$amount.'</b> '.$this->ticker.' has been send to your address <b>'.htmlentities($_POST["address"]).'</b> with TXID: <b><a href="'.$this->explorer.$txid.'">'.$txid.'</a></b></span>';
				}
				else {
					return '
					<form action="" method="post">
						<input class="form-control" name="address" type="text" placeholder="'.$this->coinname.' address" aria-label="'.$this->coinname.' address" aria-describedby="button-search" />
						<br />
						<span style="background-color:black; padding:10px; color:white;" id="countdown">
						</span><br /><br />
						<input class="form-control" name="captcha" type="text" placeholder="Enter captcha" aria-label="Enter captcha" aria-describedby="button-search" />
						<input type="submit" class="btn btn-primary" id="button-search" type="button" value="Get coins">
					</form>
					<i>The captcha is invalid!</i>
					';
				}
			}
		}
		else {
			$algehad = mysqli_query($this->con, "SELECT * FROM reward WHERE ip='".mysqli_real_escape_string($this->con, $_SERVER["REMOTE_ADDR"])."' AND datum > (NOW() - INTERVAL ".mysqli_real_escape_string($this->con, $this->rewardtime)." HOUR)");
			if (mysqli_num_rows($algehad)>=1) {
				return "You got already an reward those ".$this->rewardtime." Hour(s)";
			}
			else {
				return '
				<form action="" method="post">
					<input class="form-control" name="address" type="text" placeholder="'.$this->coinname.' address" aria-label="'.$this->coinname.' address" aria-describedby="button-search" />
					<br />
					<span style="background-color:black; padding:10px; color:white;" id="countdown">
					</span><br /><br />
					<input class="form-control" name="captcha" type="text" placeholder="Enter captcha" aria-label="Enter captcha" aria-describedby="button-search" />
					<input type="submit" class="btn btn-primary" id="button-search" type="button" value="Get coins">
				</form>
				';
			}
		}
	}
	function transactions($limit) {
		$m = mysqli_query($this->con, "SELECT * FROM reward WHERE txid!='' ORDER BY id DESC LIMIT ".mysqli_real_escape_string($this->con, $limit));
		$tx = "";
		while($f = mysqli_fetch_array($m)) {
			$tx .= '
			<tr>
				<td><a href="'.$this->explorer.$f["txid"].'">'.$f["address"].'</a></td>
				<td><a href="'.$this->explorer.$f["txid"].'">'.$f["amount"].'</a></td>
				<td><a href="'.$this->explorer.$f["txid"].'">'.$f["datum"].'</a></td>
			</tr>
			';
		}
		return $tx;
	}
	public function makepage() {
		$page = str_replace("{site}", $this->site, $this->template);
		$page = str_replace("{ticker}", $this->ticker, $page);
		$page = str_replace("{coinname}", $this->coinname, $page);
		$page = str_replace("{minreward}", $this->minreward, $page);
		$page = str_replace("{maxreward}", $this->maxreward, $page);
		$page = str_replace("{url}", $this->fullurl, $page);
		$page = str_replace("{ads}", $this->ads, $page);
		$page = str_replace("{explorer}", $this->explorer, $page);
		$page = str_replace("{year}", date("Y"), $page);
		$page = str_replace("{balance}", $this->balance(), $page);
		$page = str_replace("{faucetwallet}", $this->faucetwallet, $page);
		$page = str_replace("{time}", $this->rewardtime, $page);
		$page = str_replace("{captchatimer}", $this->captchatime, $page);
		if (!isset($_POST["captcha"]) && !isset($_POST["address"])) {
			$page = str_replace("{htmlcaptcha}", $this->captcha(), $page);
		}
		$page = str_replace("{transactions}", $this->transactions(15), $page);
		$page = str_replace("{form}", $this->form(), $page);
		return $page;
	}
	
}
