<style>
th	{ white-space: nowrap; vertical-align: top; }
tr:nth-child(even)	{ background-color: #DDD; }
body	{ font-family: sans-serif; }
pre	{ font-family: inherit; }
</style>
<table>
<?php

$COUNT = 100;
$ACCOUNT = "q23p";
$by_id = [];

$next_cursor = "";
do {
	$scheduled = fetch_scheduled($next_cursor);
	foreach ($scheduled["data"] as $tweet)
		if ($tweet["scheduled_status"] !== "SUCCESS")
			add_tweet($by_id, $tweet);
	$next_cursor = $scheduled["next_cursor"];
} while ($next_cursor);

//array_multisort(array_column($by_id, "date"), SORT_ASC, $by_id); clobbers key (id)

foreach ($by_id as $id=>$tweet) {
	$datetime = DateTime::createFromFormat(DateTime::ISO8601, $tweet["date"]);
	echo "<tr>\n";
	echo " <th>{$datetime->format('Y-m-d')}</th>\n";
	echo " <td><pre>{$tweet['text']}</pre></td>\n";
	echo " <td><a href='delete_tweet.php?id={$id}'>Delete</a></td>\n";
	echo "</tr>\n";
}

function fetch_scheduled($cursor = "") {
	global $COUNT, $ACCOUNT;
	if ($cursor !== "")
		$cursor = "&cursor={$cursor}";
	$scheduled = `sudo -u www-data twurl -H "ads-api.twitter.com" "/7/accounts/{$ACCOUNT}/scheduled_tweets?count={$COUNT}{$cursor}"`;
	return json_decode($scheduled, true);
}

function add_tweet(&$by_id, $tweet) {
	$by_id[$tweet["id"]] = [
		"date" => $tweet["scheduled_at"],
		"text" => $tweet["text"]
	];
}

?>
</table>
