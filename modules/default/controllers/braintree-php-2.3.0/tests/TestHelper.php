<?php

require_once 'PHPUnit/Framework.php';

set_include_path(
  get_include_path() . PATH_SEPARATOR .
  realpath(dirname(__FILE__)) . '/../lib'
);

require_once "Braintree.php";

Braintree_Configuration::environment('development');
Braintree_Configuration::merchantId('integration_merchant_id');
Braintree_Configuration::publicKey('integration_public_key');
Braintree_Configuration::privateKey('integration_private_key');

class Braintree_TestHelper
{
    public static function defaultMerchantAccountId()
    {
        return 'sandbox_credit_card';
    }

    public static function nonDefaultMerchantAccountId()
    {
        return 'sandbox_credit_card_non_default';
    }

    public static function createViaTr($regularParams, $trParams)
    {
        $trData = Braintree_TransparentRedirect::transactionData(
            array_merge($trParams, array("redirectUrl" => "http://www.example.com"))
        );
        return Braintree_TestHelper::submitTrRequest(
            TransparentRedirect::url(),
            $regularParams,
            $trData
        );
    }

    public static function submitTrRequest($url, $regularParams, $trData)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_HEADER, true);
        // curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array_merge($regularParams, array('tr_data' => $trData))));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        preg_match('/Location: .*\?(.*)/', $response, $match);
        return trim($match[1]);
    }

    public static function suppressDeprecationWarnings()
    {
        set_error_handler("Braintree_TestHelper::_errorHandler", E_USER_NOTICE);
    }

    static function _errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (preg_match('/^DEPRECATED/', $errstr) == 0) {
            trigger_error('Unknown error received: ' . $errstr, E_USER_ERROR);
        }
    }

    public static function includes($collection, $targetItem)
    {
        foreach ($collection AS $item) {
            if ($item->id == $targetItem->id) {
                return true;
            }
        }
        return false;
    }
}

?>
