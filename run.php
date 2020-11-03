<?php

use Supermetrics\API;
use Supermetrics\SlToken;
use todorowww\APIException;
use todorowww\PostStats;

require "class/autoloader.php";

Autoloader::register();

// The data should not be hardcoded, but instead stored in an encrypted config file, or perhaps environment variable
// To avoid adding complexity to the project, I chose not to include this functionality
$SMAPI = new API("ju16a6m81mhid5ue1z3v2g0uh", "slobodan@todorov.rs", "Slobodan Todorov");

$token = null;

// Check if we have the token in cache
if (file_exists("token.json")) {
    $token = new SlToken("", "", "");
    $token->build(file_get_contents("token.json"));
    if (!$token->isExpired()) {
        $SMAPI->setToken($token);
    }
}

// Check if we have the token in cache, and if we do, is it expired
if ((!$token) || ($token->isExpired())) {
    // Get the token, and store for future use
    $token = $SMAPI->register();
    if ($token) {
        /**
         * If we have successfully registered, store the token for reuse.
         * This is not the safe option, and should be avoided. Token should either be encrypted on disk,
         * or, preferably, be kept in some inmemory storage. This is to prevent potential security breach,
         * should someone gain unauthorized access to the machine this script resides on.
         */
        $fh = fopen("token.json", 'w');
        if ($fh) {
            fwrite($fh, json_encode($token));
            fclose($fh);
        }
    }
}

$PS = new PostStats();

$pages = 10;
for($page = 0; $page < $pages; $page++) {
    $failCounter = 0; $failed = false;
    do {
        try {
            $posts = $SMAPI->fetchPosts($page);
            break;
        } catch (APIException $e) {
            $failed = true;
            $failCounter++;
        } catch (Exception $e) {
            echo "Unhandled exception occurred, aborting [{$e->getMessage()}]\n";
            exit(1);
        }
        // If fetch failed, try to re-register
        if ($failed) {
            $token = $SMAPI->register();
            $failed = $token->isExpired();
        }
        // If we failed 5 times, abort.
        if ($failCounter > 5) {
            echo "Error registering token, aborting [Failed $failCounter times]\n";
            exit(1);
        }
    } while($failed);

    /**
     * Perform post processing, for the current page.
     * Processing each page separately, reduces the memory needed to store all the data prior to processing.
     * Can be useful for big datasets
     */
    $PS->processPosts($posts->data->posts);
}
$stats = [
    'Average character length of posts per month' => $PS->getAveragePostLengthPerMonth(),
    'Total posts by week number' => $PS->getTotalPostsByWeek(),
    'Average number of posts per user per month' => $PS->getAverageNumberOfPostsPerUserPerMonth(),
    'Longest post length per month' => $PS->getLongestPostLengthByMonth(),
    'Longest post per month' => $PS->getLongestPostByMonth(),
];

$fh = fopen("stats.json", 'w');
if ($fh) {
    fwrite($fh, json_encode($stats));
    fclose($fh);
}

echo json_encode($stats) . "\n";
