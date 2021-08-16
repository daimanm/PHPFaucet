<?php
include 'rpc.api.php';
$faucet = new faucet();
$faucet->settemplate('standard'); // Template
$faucet->setreward(0.001, 0.01); // min reward, max reward
$faucet->setwallet("AsunUCZnwr4ymdxvLXbnw1HNSMQXxw9xRu"); // Faucet donation wallet
$faucet->settimers(15, 1); // Timer in seconds for captcha, Reward each * hour
$faucet->config("Array", "Units", "http://explorer.2array.com/tx/", "faucet.mining.moe", "http://faucet.mining.moe/array"); // Coin name, Ticker, Block explorer TX URL, Site, Faucet URL
$faucet->ads('<iframe data-aa="1720259" src="//acceptable.a-ads.com/1720259" style="border:0px; padding:0; width:100%; height:100%; overflow:hidden; background-color: transparent;" ></iframe>');
$faucet->setdb("", "", "", ""); //Database: host, username, password, database
$faucet->setrpc("127.0.0.1", 3253, "", ""); //RPC: host, port, username, password
?>
