<?php

class Compare {

	public function compare($masterFile, $masterSku, $masterPrice, $invoiceFile, $invoiceSku, $invoicePrice)
{

//Read Master list file

$masterList = [];

$handle = fopen("uploads/master/".$masterFile, "r");
if ($handle) {
    $array = [];
    while (($line = fgets($handle)) !== false) {

        //CSV line (sku - price)
        $array = str_getcsv($line);
        $masterList[trim($array[$masterSku])]['price'] = $array[$masterPrice];
    }
    fclose($handle);
} else {
    // error opening the file.
    die("Error 1");
}

//Read Invoice

$invoiceList = [];

$handle = fopen("uploads/invoices/".$invoiceFile, "r");
if ($handle) {
    $array = [];
    while (($line = fgets($handle)) !== false) {

        //CSV line (sku - qty - price)
        $array = str_getcsv($line);
	if(strpos($array[$invoicePrice], "£") !== false){
	        //$invoiceList[trim($array[0])]['total'] = $array[$invoicePrice];
       		//$invoiceList[trim($array[0])]['qty'] = 1; //$array[1];
        	$invoiceList[trim($array[$invoiceSku])]['price'] = $array[$invoicePrice]; //($array[2]/$array[1]);
	}
    }
    fclose($handle);
} else {
    // error opening the file.
    die("Error 2");
}

$itemsInInvoice = count($invoiceList);

//Compare

$invoiceItemsNotSetInMaster = array();
$invoiceItemsPriceMismatch_Cheaper = array();
$invoiceItemsPriceMismatch_Expensive = array();
$invoiceItemsMatch = array();
$invoiceItemsPriceMismatch_Unknown = array();

foreach($invoiceList as $sku => $invoiceItem)
{
        if(!isset($masterList[$sku])) {
                $invoiceItemsNotSetInMaster[$sku] = $invoiceItem;
        }
        else{
                $priceMaster = $masterList[$sku]['price'];
                $pricePaid = $invoiceItem['price'];

                if ($priceMaster < $pricePaid) {
                        $invoiceItemsPriceMismatch_Expensive[$sku] = "Master price at $priceMaster but invoiced at $pricePaid";
                } elseif($priceMaster > $pricePaid) {
                        $invoiceItemsPriceMismatch_Cheaper[$sku] = "Master price at $priceMaster but invoiced at $pricePaid";
                } elseif($priceMaster == $pricePaid) {
                        $invoiceItemsMatch[$sku] = "Price match";
                } else {
                        $invoiceItemsPriceMismatch_Unknown[$sku] = "Unknown error";
                }
        }
}

return array($invoiceItemsNotSetInMaster, $invoiceItemsPriceMismatch_Cheaper, $invoiceItemsPriceMismatch_Expensive, $invoiceItemsMatch, $invoiceItemsPriceMismatch_Unknown);


}


}






