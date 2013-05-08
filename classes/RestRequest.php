<?php

class RestRequest {
	protected $url;
	public $method;
	protected $requestBody;
	protected $requestLength;
	protected $username;
	protected $password;
	protected $acceptType;
	protected $responseBody;
	protected $responseInfo;
	protected $error;
	protected $errorno;
	protected $status;
	protected $boundary;
	protected $fileLength;
	public $success_callback_url;

	public function __construct($method = 'GET', $path = null, $accept = 'text/xml', $audioFilename = null, $metadataFilename = null, $username = null, $password = null, $success_callback_url = null) {
		$this -> url = 'https://www.koemei.com/REST';
		$this -> method = $method;
		$this -> path = $path;
		$this -> requestLength = 0;
		$this -> username = $username;
		$this -> password = $password;
		$this -> acceptType = $accept;
		$this -> audioFilename = $audioFilename;
		$this -> metadataFilename = $metadataFilename;
		$this -> fileLength = 0;
		$this -> header = array('Accept: ' . $this -> acceptType);
		$this -> responseBody = null;
		$this -> responseInfo = null;
		$this -> errorno = null;
		$this -> error = null;
		$this -> status = null;
		$this -> boundary = null;
		$this -> success_callback_url = $success_callback_url;
	}

	public function flush() {
		$this -> requestBody = null;
		$this -> requestLength = 0;
		$this -> method = 'GET';
		$this -> responseBody = null;
		$this -> responseInfo = null;
		$this -> url = 'https://www.koemei.com/REST';
	}

	public function execute() {
		$this -> requestBody = $this -> path;
		$ch = curl_init();
		$this -> setAuth($ch);

		// select method
		try {
			switch (strtoupper($this->method)) {
				case 'GET' :
					$this -> executeGet($ch);
					break;
				case 'POST' :
					$this -> executePost($ch);
					break;
				case 'PUT' :
					$this -> executePut($ch);
					break;
				default :
					throw new InvalidArgumentException('Current method (' . $this -> method . ') is an invalid REST method.');
			}
		} catch (InvalidArgumentException $e) {
			curl_close($ch);
			throw $e;
		} catch (Exception $e) {
			curl_close($ch);
			throw $e;
		}

	}

	// function to setup a GET - get list of media, one media item or transcription status
	protected function executeGet($ch) {
		$this -> url = $this -> url . "/" . $this -> requestBody;
		$this -> doExecute($ch);
		$this -> requestBody = "";
	}

	// function to setup a PUT - e.g. publish a media
	protected function executePut($ch) {
		$this -> url = $this -> url . "/" . $this -> requestBody;
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

		$this -> doExecute($ch);
		$this -> requestBody = "";
	}

	// function to setup a POST - upload media and transcribe media
	protected function executePost($ch) {
		// media upload
		if ($this -> requestBody == 'media') {
			$this -> url .= "/" . $this -> requestBody;
			if (strpos($this -> audioFilename, 'http') !== false) {
				$this -> url .= "?media=" . urlencode($this -> audioFilename);
				$this -> requestBody = "";
			} else {
				// random string as boundary
				$this -> boundary = 'R50hrfBj5JYyfR3vF3wR96GPCC9Fd2q2pVMERvEaOE3D8LZTgLLbRpNwXek3';

				array_push($this -> header, 'Content-Type: multipart/form-data; boundary=' . $this -> boundary);

				// file contents
				$file = fopen($this -> audioFilename, 'rb');
				$file_contents = file_get_contents($this -> audioFilename);
				$this -> fileLength = strlen($file_contents);

				// POST body
				$this -> requestBody = "--" . $this -> boundary . "\r\n";
				$this -> requestBody .= "Content-Disposition: form-data; name=\"media\"; filename=\"" . $this -> audioFilename . "\"\r\n";
				$this -> requestBody .= "Content-Type: application/octet-stream\r\n";
				$this -> requestBody .= "Content-Transfer-Encoding: binary\r\n";
				$this -> requestBody .= "Content-Length: " . "$this->fileLength" . "\r\n";
				$this -> requestBody .= "\r\n";
				$this -> requestBody .= stream_get_contents($file);
				$this -> requestBody .= "\r\n";
				$this -> requestBody .= "--" . $this -> boundary . "--";
			}

		}
		// transcribe media
		elseif (strpos($this -> path, 'transcribe') !== false) {
			$this -> url .= "/" . $this -> requestBody;
			$this -> requestBody = '';
			if ($this -> success_callback_url != null) {
				$this -> requestBody = http_build_query(array('success_callback_url' => $this -> success_callback_url), '', '&');
			}
		}

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this -> requestBody);

		$this -> doExecute($ch);
	}

	// execute the request
	protected function doExecute(&$curlHandle) {
		$this -> setCurlOpts($curlHandle);
		$this -> responseBody = curl_exec($curlHandle);
		$this -> errorno = curl_errno($curlHandle);
		$this -> error = curl_error($curlHandle);
		$this -> responseInfo = curl_getinfo($curlHandle);
		$this -> status = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

		curl_close($curlHandle);
	}

	// setting curl options
	protected function setCurlOpts(&$curlHandle) {
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 300);
		curl_setopt($curlHandle, CURLOPT_URL, $this -> url);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this -> header);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curlHandle, CURLOPT_VERBOSE, true);
		curl_setopt($curlHandle, CURLINFO_HEADER_OUT, true);
		curl_setopt($curlHandle, CURLOPT_FRESH_CONNECT, true);
	}

	// set authentication - encoding is done automatically
	protected function setAuth(&$curlHandle) {
		if ($this -> username !== null && $this -> password !== null) {
			$non_encoded_auth = "$this->username:$this->password";
			curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curlHandle, CURLOPT_USERPWD, $non_encoded_auth);
		}
	}

	// print result of the request
	public function printResult() {
		echo "------request header------\n";
		echo print_r($this -> getHeader(), true) . "\n";
		echo "------response body------\n";
		echo $this -> getResponseBody() . "\n";
		echo "------response info------\n";
		echo print_r($this -> getResponseInfo(), true) . "\n";

	}

	// public functions to get/set protected variables
	public function getAcceptType() {
		return $this -> acceptType;
	}

	public function setAcceptType($acceptType) {
		$this -> acceptType = $acceptType;
	}

	public function getAudioFilename() {
		return $this -> audioFilename;
	}

	public function setAudioFilename($audioFilename) {
		$this -> audioFilename = $audioFilename;
	}

	public function getMetadataFilename() {
		return $this -> metadataFilename;
	}

	public function setMetadataFilename($metadataFilename) {
		$this -> metadataFilename = $metadataFilename;
	}

	public function getHeader() {
		return $this -> header;
	}

	public function setHeader($header) {
		$this -> header = $header;
	}

	public function getPassword() {
		return $this -> password;
	}

	public function setPassword($password) {
		$this -> password = $password;
	}

	public function getResponseBody() {
		return $this -> responseBody;
	}

	public function getResponseInfo() {
		return $this -> responseInfo;
	}

	public function getUrl() {
		return $this -> url;
	}

	public function setUrl($url) {
		$this -> url = $url;
	}

	public function getUsername() {
		return $this -> username;
	}

	public function setUsername($username) {
		$this -> username = $username;
	}

	public function getMethod() {
		return $this -> method;
	}

	public function setMethod($method) {
		$this -> method = $method;
	}

	public function getError() {
		return $this -> error;
	}

	public function setError($error) {
		$this -> error = $error;
	}

	public function getErrorno() {
		return $this -> errorno;
	}

	public function setErrorno($errorno) {
		$this -> errorno = $errorno;
	}

	public function getStatus() {
		return $this -> status;
	}

	public function setStatus($status) {
		$this -> status = $status;
	}

	public function getBoundary() {
		return $this -> boundary;
	}

	public function setBoundary($boundary) {
		$this -> boundary = $boundary;
	}

	public function getFileLength() {
		return $this -> fileLength;
	}

	public function setFileLength($fileLength) {
		$this -> fileLength = $fileLength;
	}

}
?>
