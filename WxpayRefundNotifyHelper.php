<?php

namespace libs\wxpay;

/**
 * @Author nick
 * @Blog http://www.lampnick.com
 * @Email nick@lampnick.com
 */
class WxpayRefundNotifyHelper
{
	const MCH_KEY = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx';
	const CIPHER = MCRYPT_RIJNDAEL_128;
	const MCRYPT_MODE = MCRYPT_MODE_ECB;
	
	/**
	 * You should implements this method to handle you own business logic.
	 * @param array $decryptedData
	 * @param string $msg this message will return to wechat if something error.
	 * @return bool
	 */
	protected function handelInternal(array $decryptedData, string &$msg)
	{
		//You should implements this method to handle you own business logic.
		return true;
	}
	
	/**
	 * handle wechat pay refund notify
	 */
	public function handle()
	{
		try {
			$xml = file_get_contents("php://input");
			$data = $this->xml2array($xml);
			$encryptData = $data['req_info'];
			$decryptedData = $this->xml2array($this->decryptData($encryptData, self::MCH_KEY));
			$msg = 'OK';
			$result = $this->handelInternal($decryptedData, $msg);
			$returnArray['return_msg'] = $msg;
			if (true === $result) {
				$returnArray['return_code'] = 'SUCCESS';
			} else {
				$returnArray['return_code'] = 'FAIL';
			}
			$this->replyNotify($returnArray);
		} catch (\Exception $e) {
			throw new \Exception($e);
		}
	}
	
	/**
	 * reply to wechat
	 * @param $xml
	 */
	public function replyNotify($xml)
	{
		if (is_array($xml)) {
			$xml = $this->toXml($xml);
		}
		echo $xml;
	}
	
	/**
	 * @param string $xml
	 * @return array
	 * @throws \Exception
	 */
	public function xml2array(string $xml)
	{
		if (empty($xml)) {
			throw new \Exception('Error xml data!');
		}
		$p = xml_parser_create();
		xml_parse_into_struct($p, $xml, $values, $index);
		xml_parser_free($p);
		$result = [];
		foreach ($values as $val) {
			$result[strtolower($val['tag'])] = $val['value'];
		}
		return $result;
	}
	
	/**
	 * output xml
	 * @param array $array
	 * @return string
	 * @throws \Exception
	 */
	public function toXml(array $array)
	{
		if (empty($array)) {
			throw new \Exception("array is empty！");
		}
		$xml = "<xml>";
		foreach ($array as $key => $val) {
			if (is_numeric($val)) {
				$xml .= "<" . $key . ">" . $val . "</" . $key . ">";
			} else {
				$xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
			}
		}
		$xml .= "</xml>";
		return $xml;
	}
	
	/**
	 * decrypt data
	 * @param string $encryptData
	 * @param string $md5LowerKey
	 * @return array
	 */
	public function decryptData(string $encryptData, string $Key = '')
	{
		//1. base64_decode
		// openssl_decrypt only accept base64 input param
		//2. md5 original key
		$md5LowerKey = strtolower(md5($Key));
		//3. decrypt AES ECB
		$decrypted = openssl_decrypt($encryptData, "AES-256-ECB", $md5LowerKey);
		return $decrypted;
	}
}
