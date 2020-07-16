<?php

namespace MeysamZnd\IranGateways;

use nusoap_client;

class MellatBank
{

    /**
     * @var integer
     */
    private $terminal = '';

    /**
     * @var string
     */
    private $username = '';

    /**
     * @var string
     */
    private $password = '';


    /**
     * MellatBank constructor.
     * @param string $terminal
     * @param string $username
     * @param string $password
     */
    public function __construct($terminal = '', $username = '', $password = '')
    {
        if (!empty($terminal)) {
            $this->terminal = $terminal;
        }

        if (!empty($username)) {
            $this->username = $username;
        }

        if (!empty($password)) {
            $this->password = $password;
        }
    }


    /**
     * @param $amount
     * @param $orderId
     * @param $callBackUrl
     * @return mixed|string|null
     */
    public function payment($amount, $orderId, $callBackUrl): ?string
    {
        $outPut =null;
        $client = new nusoap_client('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
        $terminalId = $this->terminal;
        $userName = $this->username;
        $userPassword = $this->password;
        $localDate = date('ymj');
        $localTime = date('His');
        $additionalData = '';
        $err = $client->getError();
        if ($err) {
            $outPut = $err;
        }
        $parameters = [
            'terminalId' => $terminalId,
            'userName' => $userName,
            'userPassword' => $userPassword,
            'orderId' => $orderId,
            'amount' => $amount,
            'localDate' => $localDate,
            'localTime' => $localTime,
            'additionalData' => $additionalData,
            'callBackUrl' => $callBackUrl,
            'payerId' => 0
        ];
        $result = $client->call('bpPayRequest', $parameters, 'http://interfaces.core.sw.bps.com/');
        if ($client->fault) {
            $outPut = $result;
        }

        $err = $client->getError();
        if ($err) {
            $outPut = $err;
        }
        $res = explode(',', $result);
        $ResCode = $res[0];
        if ($ResCode === '0') {
            $this->postRefId($res[1]);
        } else {
            $outPut = $this->error($ResCode);
        }
        return $outPut;
    }

    /**
     * @param $refIdValue
     */
    protected function postRefId($refIdValue): void
    {
        echo '<form name="mellat" action="https://bpm.shaparak.ir/pgwchannel/startpay.mellat" method="POST">
				<input type="hidden" id="RefId" name="RefId" value="' . $refIdValue . '">
				</form>
				<script type="text/javascript">window.onload = formSubmit; function formSubmit() { document.forms[0].submit(); }</script>';
        exit;
    }

    /**
     * @param $params
     * @return array|bool|mixed|string|null
     */
    protected function verifyPayment($params)
    {
        $outPut = false;
        $client = new nusoap_client( 'https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl' ) ;
        $orderId = $params['SaleOrderId'];
        $verifySaleOrderId = $params['SaleOrderId'];
        $verifySaleReferenceId = $params['SaleReferenceId'];
        $err = $client->getError();
        if ($err) {
            $outPut = $err;
        }
        $parameters = array(
            'terminalId'=> $this->terminal,
            'userName'=> $this->username,
            'userPassword'=> $this->password,
            'orderId' => $orderId,
            'saleOrderId' => $verifySaleOrderId,
            'saleReferenceId' => $verifySaleReferenceId);
        $result = $client->call('bpVerifyRequest', $parameters, 'http://interfaces.core.sw.bps.com/');
        if ($client->fault) {
            $outPut = $result;
        }
        else {
            $resultStr = $result;
            $err = $client->getError();
            if ($err) {
                $outPut = $err;
            }
            else if( $resultStr === '0' ) {
                $outPut = true;
            }
        }
        return $outPut;
    }

    /**
     * @param $params
     * @return array|bool
     */
    public function controlPayment($params)
    {
        if(($params['ResCode'] === '0') && $this->verifyPayment($params) === true) {
            return [
                'status' => 'success',
                'trans' =>$params['SaleReferenceId']
            ];
        }
        return false;
    }

    /**
     * @param $number
     * @return string
     */
    protected function error($number): string
    {
        return $this->response($number);
    }

    /**
     * @param $number
     * @return string
     */
    protected function response($number): string
    {
        switch ($number) {
            case 31 :
                $err = 'پاسخ نامعتبر است!';
                break;
            case 17 :
                $err = 'کاربر از انجام تراکنش منصرف شده است!';
                break;
            case 21 :
                $err = 'پذیرنده نامعتبر است!';
                break;
            case 25 :
                $err = 'مبلغ نامعتبر است!';
                break;
            case 34 :
                $err = 'خطای سیستمی!';
                break;
            case 41 :
                $err = 'شماره درخواست تکراری است!';
                break;
            case 421 :
                $err = 'ای پی نامعتبر است!';
                break;
            case 412 :
                $err = 'شناسه قبض نادرست است!';
                break;
            case 45 :
                $err = 'تراکنش از قبل ستل شده است';
                break;
            case 46 :
                $err = 'تراکنش ستل شده است';
                break;
            case 35 :
                $err = 'تاریخ نامعتبر است';
                break;
            case 32 :
                $err = 'فرمت اطلاعات وارد شده صحیح نمیباشد';
                break;
            case 43 :
                $err = 'درخواست verify قبلا صادر شده است';
                break;
            default  :
                $err = 'خطای نا مشخص';

        }
        return $err;
    }


}
