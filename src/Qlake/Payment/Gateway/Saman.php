<?php

namespace Qlake\Payment\Gateway;

use SoapClient;

class Saman extends Gateway implements GatewayInterface
{
	protected $wsdlUrl = 'https://sep.shaparak.ir/Payments/InitPayment.asmx?wsdl';


	protected $paymentUrl = 'https://sep.shaparak.ir/Payment.aspx';


	protected $terminalId;


	protected $amount;


	protected $receiptId;


	protected $client;


	protected $requestData;


	protected $requestError;



	public function __construct(array $config)
	{
		$this->client = new SoapClient($this->wsdlUrl, ['exceptions' => false]);

		$this->terminalId   = $config['terminalId'];
		$this->callbackUrl  = $config['callbackUrl'];
	}



	public function purchase($amount, $receiptId)
	{
		$this->amount    = (int)$amount;
		$this->receiptId = $receiptId;

		return $this;
	}



	public function send()
	{
		$params = [
			'TermID'          => $this->terminalId,
			'ResNum'          => $this->receiptId,
			'TotalAmount'     => $this->amount,
			/*
			'SegAmount1'      => null,
			'SegAmount2'      => null,
			'SegAmount3'      => null,
			'SegAmount4'      => null,
			'SegAmount5'      => null,
			'SegAmount6'      => null,
			'AdditionalData1' => null,
			'AdditionalData1' => null,
			'wage'            => null,
			*/
		];

		$result = $this->client->__soapCall('RequestToken', $params);

		if (is_numeric($result) and $result <= 0)
		{
			$this->requestError = $result;

			return false;
		}

		$this->token = $result;

		$this->requestData = $result;

		return true;
	}



	public function getRequestData()
	{
		return $this->requestData;
	}



	public function getRequestError()
	{
		return $this->requestError;
	}



	public function isReady()
	{
		return $this->requestError === null;
	}



	public function redirect()
	{
		$this->redirectByForm($this->paymentUrl, ['Token' => $this->token, 'RedirectURL' => $this->callbackUrl]);
	}



	public function handle()
	{
		// redirect to RedirectURL
		// $_POST['State']
		// $_POST['RefNum']
		// $_POST['ResNum']
		// $_POST['MID']
		// $_POST['TraceNo']
		$state = $_POST['state'];
		if ($state !== 'OK')
		{
			echo "Error: $state";
			exit;
		}
		if ($_POST['RefNum'] /*is not uniqu*/)
		{
			echo 'error';
			exit;
		}
	}



	public function verify()
	{
		$params = [
			'RefNum' => '',
			'MID' => '',
		];

		$res = $this->client->__soapCall('VerifyTransaction', $params);

		if ($res < 0)
		{
			echo "Error: $res";
			exit;
		}
		if ($res != $this->amount)
		{
			$res = $this->client->__soapCall('reverseTransaction', ['RefNum' => $RefNum, 'MID' => $this->username, 'Username' => $this->username, 'Password' => $this->password]);
			if ($res == 1)
			{
				echo 'Transaction reversed';
				exit;
			}
			else
			{
				echo 'error';
				exit;
			}
		}
		return true;
	}
}
