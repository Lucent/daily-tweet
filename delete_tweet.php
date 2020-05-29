<?php

$ACCOUNT = "q23p";
$id = $_GET["id"];

echo `twurl -H "ads-api.twitter.com" -X DELETE "/7/accounts/{$ACCOUNT}/scheduled_tweets/{$id}"`;

?>
