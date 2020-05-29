<?php

$COUNT = 30;
$ACCOUNT = "q23p";
$by_id = [];

$next_cursor = "";
do {
	$scheduled = fetch_scheduled($next_cursor);
	foreach ($scheduled["data"] as $tweet)
		if ($tweet["scheduled_status"] === "SCHEDULED")
			add_tweet($by_id, $tweet);
	$next_cursor = $scheduled["next_cursor"];
} while ($next_cursor);

array_multisort(array_column($by_id, "date"), SORT_ASC, $by_id);

print_r($by_id);

function fetch_scheduled($cursor = "") {
	echo $cursor, "\n";
	global $COUNT, $ACCOUNT;
	if ($cursor !== "")
		$cursor = "&cursor={$cursor}";
	$scheduled = `twurl -H "ads-api.twitter.com" "/7/accounts/{$ACCOUNT}/tweets?tweet_type=SCHEDULED&trim_user=true&timeline_type=ALL&count={$COUNT}{$cursor}"`;
	return json_decode($scheduled, true);
}

function add_tweet(&$by_id, $tweet) {
	$by_id[$tweet["id"]] = [
		"date" => $tweet["scheduled_at"],
		"text" => $tweet["full_text"]
	];
}

?>
