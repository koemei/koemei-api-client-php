Koemei php API client
=====================

* Note : remember to set USERNAME and PASSWORD in the protected variables*

Usage : API.php \<method\> \<accept\> \<path\> [upload] [metadata]

Basic use case for generating captions
---------------------

1. Upload media file :

    API.php POST text/xml media test.mp3

or

    API.php POST text/xml media "http://media_file_path"

2. Start transcription :

    API.php POST text/xml media/{media_uid}/transcribe

3. Get transcript/captions :

    API.php GET text/xml transcripts/{transcript_uuid}


**For a more detailed documentation, please have a look at [the API documentation](https://www.koemei.com/api/)**