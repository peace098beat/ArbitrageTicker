<?php
/*
    使い方

    $.get("get_wrapper.php",
          {url:"https://api.bitfinex.com/v1/pubticker/btcusd"},
          function(data){
            console.log(data);
           }
    );
*/
$url = $_GET["url"];
$data = file_get_contents($url);
header("Access-Control-Allow-Origin: *");
print $data;