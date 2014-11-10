<?

class Payment_payeer 
{
    private $shop = null;
    private $params = array();

    function __construct(&$shop) 
	{
        $this->shop = &$shop;
		
        if (!$shop->OrderID && $_GET["OrderNumber"]) 
		{
            $this->shop->LoadOrder($_GET["OrderNumber"]);
        }
    }

    function create_bill($to_string = false) 
	{
        global $MODULE_VARS;
		
        $shop = $this->shop;
		$m_url = $MODULE_VARS['payeer']['﻿MerchantURL'];
		$m_shop = $MODULE_VARS['payeer']['MerchantID'];
		$m_key = $MODULE_VARS['payeer']['SecretKey'];
		$m_orderid = htmlspecialchars($shop->OrderID);
		$m_amount = str_replace(',', '.', htmlspecialchars($shop->CartSum()));
		$m_curr = $MODULE_VARS['payeer']['Currency'];
		$m_desc = base64_encode($MODULE_VARS['payeer']['OrderDescription']);
		$arHash = array(
			$m_shop,
			$m_orderid,
			$m_amount,
			$m_curr,
			$m_desc,
			$m_key
		);
		$sign = strtoupper(hash('sha256', implode(":", $arHash)));
		
        $form = "
		<img src='https://payeer.com/bitrix/templates/difiz/images/logo.png' />
		<br/>" . MODULE_PAYEER_ENTERPRISETEXT . "
		
		<form action=$m_url method='get'>
			<input type='hidden' name='m_shop' value=$m_shop />
			<input type='hidden' name='m_orderid' value=$m_orderid />
			<input type='hidden' name='m_amount' value=$m_amount />
			<input type='hidden' name='m_curr' value=$m_curr />
			<input type='hidden' name='m_desc' value=$m_desc />
			<input type='hidden' name='m_sign' value=$sign />
			<input type='submit' name='m_process' value='" . MODULE_PAYEER_SUBMIT ."'>
		";
		
        if (!$to_string)
		{
            echo $form;
            echo "<input type=submit value='" . MODULE_PAYEER_PRINT_TICKET . "'></form><br/>";
            return true;
        }
		else
		{
            return $form.'</form><br/>';
        }
    }
}
