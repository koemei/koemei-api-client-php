<?php
/*
 * Koemei php API client
 * author : Marina Zimmermann
 * This file gives example on how to use the Koemei API as command line php : 
 * Examples:
 *    upload media:		API.php POST text/xml media test.mp3
 *    transcribe media:	API.php POST text/xml media/{id}/transcribe
 *    transcription status:	API.php GET text/xml media/{id}/transcribe/{process-id}
 *    info about media:	API.php GET text/xml media/{id}
 */

function __autoload($class_name) {
    include 'classes/'.$class_name . '.php';
}

/* remember to set USERNAME and PASSWORD below */

$username = 'testuser@koemei.com'; // replace by username
$password = 'pwd4test'; // replace by password

if ($argc < 2)
	exit("Usage: API.php <method> <accept> <path> [upload] [metadata]\n");

$method = $argv[1];
$accept = $argv[2];
$path = $argv[3];

$audioFilename = ($argc > 4) ? $argv[4] : null;
$metadataFilename = ($argc > 5) ? $argv[5] : null;
// for the moment:
$metadataFilename = null;

$request = new RestRequest($method, $path, $accept, $audioFilename, $metadataFilename, $username, $password);

$request->execute();

echo "------request header------\n";
echo print_r($request->getHeader(), true) . "\n";
echo "------response body------\n";
echo $request->getResponseBody() . "\n";
echo "------response info------\n";
echo print_r($request->getResponseInfo(), true) . "\n";
?>