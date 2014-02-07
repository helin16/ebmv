<?php
class BmvComScriptCURL
{
	/**
	 * download the url to a local file
	 *
	 * @param string $url       The url
	 * @param string $localFile The local file path
	 *
	 * @return string The local file path
	 */
	public static function downloadFile($url, $localFile, $timeout = null)
	{
		$timeout = trim($timeout);
		$timeout = (!is_numeric($timeout) ? self::CURL_TIMEOUT : $timeout);
		$fp = fopen($localFile, 'w+');
		$options = array(
				CURLOPT_FILE    => $fp,
				CURLOPT_TIMEOUT => $timeout, // set this to 8 hours so we dont timeout on big files
				CURLOPT_URL     => $url
		);
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		curl_exec($ch);
		fclose($fp);
		curl_close($ch);
		return $localFile;
	}
	/**
	 * read from a url
	 *
	 * @param string  $url             The url
	 * @param int     $timeout         The timeout in seconds
	 * @param array   $data            The data we are POSTING
	 * @param string  $customerRequest The type of the post: DELETE or POST etc...
	 *
	 * @return mixed
	 */
	public static function readUrl($url, $timeout = null, array $data = array(), $customerRequest = '')
	{
		$timeout = trim($timeout);
		$timeout = (!is_numeric($timeout) ? self::CURL_TIMEOUT : $timeout);
		$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => $timeout, // set this to 8 hours so we dont timeout on big files
				CURLOPT_URL     => $url
		);
		if(count($data) > 0)
		{
			if(trim($customerRequest) === '')
				$options[CURLOPT_POST] = true;
			else
				$options[CURLOPT_CUSTOMREQUEST] = $customerRequest;
			$options[CURLOPT_POSTFIELDS] = http_build_query($data);
		}
		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$data =curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}