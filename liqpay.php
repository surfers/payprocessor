<?

	interface IICChip {
	
		const LiqPayServer						= "https://www.liqpay.com/?do=api_xml";
		const LiqPayUrl							= "/?do=api_xml";
		const LiqPayUserName					= "";
		const LiqPayPassword					= "";
	
	}
	
	class LiqPay implements IICChip {
		
		private $server 	= "";
		private $url	 	= "";
		private $UserName 	= "";
		private $Password 	= "";
		
		public function __construct()
		{
		
			$this->server = IICChip::LiqPayServer;
			$this->url = IICChip::LiqPayUrl;
			$this->UserName = IICChip::LiqPayUserName;
			$this->Password = IICChip::LiqPayPassword;
			$this->dataToSend = '';

		}
		public function GetBalance($currency)
		{
			$str = '<request>
						<version>1.2</version>
						<action>view_balance</action>
						<merchant_id>'.$this->UserName.'</merchant_id>
					</request>';
			$operation_xml = base64_encode($str);
			$signature = base64_encode(sha1($this->Password.$str.$this->Password, 1));
			$operation_envelop = '<operation_envelope>
                              <operation_xml>'.$operation_xml.'</operation_xml>
                              <signature>'.$signature.'</signature>
                         </operation_envelope>';
			$post = '<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                              <request>
                                   <liqpay>'.$operation_envelop.'</liqpay>
                              </request>';

			$headers = array("POST ".$this->url." HTTP/1.0",
                         "Content-type: text/xml;charset=\"utf-8\"",
                         "Accept: text/xml",
                         "Content-length: ".strlen($post));
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->server);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$result = curl_exec($ch);
			curl_close($ch);
			return simplexml_load_string(base64_decode(simplexml_load_string($result)->liqpay->operation_envelope->operation_xml))->balances->$currency;		
		
		}
		
	}

	$accountLiqPay = new LiqPay();
	echo $accountLiqPay->GetBalance("USD");
	
?>