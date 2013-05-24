<?php
/*
 * EXAMPLE USAGE:
 * 1) Upload a media file
 * 2) Request a transcript
 * 3) Save transcript
 *   (can also be done separately using the Transcript object) 
 * 4) Publish a transcript
 */

$username = 'testuser@koemei.com'; // replace by username
$password = 'pwd4test'; // replace by password

function __autoload($class_name) {
    include 'classes/'.$class_name . '.php';
}

$request = new RestRequest('','','text/xml','','',$username,$password);

$files = array('http://www.youtube.com/watch?v=LPl0i5_l3X0','https://s3.amazonaws.com/static.koemei.com/videos/koemei_techcrunch_2012.flv');
/*
// 1) Upload the media file 
$media_item = new Media($request);
$media_item->create($files[0]);

if ($media_item->rest_request->getStatus()==200){
	$xml = simplexml_load_string( $media_item->rest_request->getResponseBody());
	$media_item->uuid=$xml->id;
	echo "Uploaded new media with uuid ".$media_item->uuid."\n";
}
else {
	echo "An error occurred creating the Media : ";
	$media_item->rest_request->printResult();
	exit("An error occurred creating the Media");	
}
$media_item->rest_request->flush();


// 2) Request a transcript of the just uploaded media item
$transcription_success_callback_url = 'https://www.koemei.com/REST';
$media_item->transcribe($transcription_success_callback_url);
if ($media_item->rest_request->getStatus()==202){
	//$xml = simplexml_load_string( $media_item->rest_request->getResponseBody());
	echo "Launched transcription of new media : ".$media_item->uuid."\n";
}
else {
	echo "An error occurred requesting Media transcription : ";
	$media_item->rest_request->printResult();
	exit("An error occurred creating the Media");
}
$media_item->rest_request->flush();
*/

// 3) Get the transcript for a media (once the transcript is available, success_callback_url provided to the transcribe method above will be called.)

// 3.1) Get current transcript uuid
$media_uuid = '1a4ddf51-d2d2-43e0-aa05-3fc44284cc27';
$media_item = new Media($request);
$media_item->get($media_uuid);
$xml = simplexml_load_string( $media_item->rest_request->getResponseBody());
$transcript_path= $xml->currentTranscript->link['href'];
echo $transcript_path;
$media_item->rest_request->flush();

preg_match("/[^\/]+$/", $transcript_path, $matches);
$file_path = explode('.',$matches[0]);
$transcript_uuid = $file_path[0];

// 3.2) Get current transcript content (vtt in this case)
$request = new RestRequest('','','text/vtt','','',$username,$password);
$transcript = new Transcript($request);
$transcript->get($transcript_uuid);
echo $transcript->rest_request->getResponseBody();

/*
// 4) Publish a media (this will make the captions available to the outside world)
$media_uuid = '<CHANGEME>';
$media_item = new Media($request);
$media_item->uuid=$media_uuid;
$media_item->publish();
$xml = simplexml_load_string( $media_item->rest_request->getResponseBody());
echo $xml;
$media_item->rest_request->flush();*/

?>
