<?php

class Compare
{

    public function compareMasterFileToInvoice($masterFile, $masterSku, $masterPrice, $masterPound, $invoiceFile, $invoiceSku, $invoicePrice, $invoicePound, $priceDifference = 0)
    {

        //Read Master list file
	//Assume can only have one unique entry

        $masterList = array();

        $handle = fopen("uploads/master/" . $masterFile, "r");
        if ($handle) {
            $array = array();
            while (($line = fgets($handle)) !== false) {

                //CSV line (sku - price)
                $array = str_getcsv($line);

                $masterAmount = trim(preg_replace('/[^0-9.]+/', '', $array[$masterPrice]));
                if(is_numeric($masterAmount)){
                    $masterList[trim($array[$masterSku])]['price'] = $masterAmount;
                }
            }
            fclose($handle);
        } else {
            // error opening the file.
            die("Error 1");
        }

        //Read Invoice
	//Can have same SKU on multiple lines!!

        $invoiceList = array();

        $handle = fopen("uploads/invoices/" . $invoiceFile, "r");
        if ($handle) {
            $array = array();
            while (($line = fgets($handle)) !== false) {

                //CSV line (sku - qty - price)
                $array = str_getcsv($line);

                $invoiceAmount = trim(preg_replace('/[^0-9.]+/', '', $array[$invoicePrice]));
                if(is_numeric($invoiceAmount)){
                    $invoiceList[trim($array[$invoiceSku])][]['price'] = $invoiceAmount; //($array[2]/$array[1]);
                }
            }
            fclose($handle);
        } else {
            // error opening the file.
            die("Error 2");
        }

        $itemsInInvoice = count($invoiceList);

	//die(print_r($invoiceList));

        //Compare

        $invoiceItemsNotSetInMaster = array();
        $invoiceItemsPriceMismatch_Cheaper = array();
        $invoiceItemsPriceMismatch_Expensive = array();
        $invoiceItemsMatch = array();
        $invoiceItemsPriceMismatch_Unknown = array();
        $overChargeTotal = 0;
        $underChargeTotal = 0;
	$notInInvoiceTotal = 0;

        foreach ($invoiceList as $sku => $invoiceItemArray) {

		//die(print_r($invoiceItemArray));

		foreach($invoiceItemArray as $invoiceItem) {


            if (!isset($masterList[$sku])) {
                $invoiceItemsNotSetInMaster[][$sku] = $invoiceItem;
		$notInInvoiceTotal += $invoiceItem['price'];
            } else {
                $priceMaster = $masterList[$sku]['price'];
                $pricePaid = $invoiceItem['price'];

                if($priceDifference > 0)
                {
                    //convert to pence
                    $priceDifferencePence = $priceDifference / 100;
                }
                $priceMasterHigh = $priceMaster + $priceDifferencePence;
                $priceMasterLow = $priceMaster - $priceDifferencePence;

                if ($pricePaid <= $priceMasterHigh && $pricePaid >= $priceMasterLow) {
                    if($priceDifference > 0)
                    {
                        $invoiceItemsMatch[][$sku] = "Price match or within threshold of {$priceDifference}p";
                    } else {
                        $invoiceItemsMatch[][$sku] = "Price match";
                    }
                } elseif ($priceMasterHigh < $pricePaid) {
                    //Items in Invoice are more expensive than price master + threshold:
		    $c = count($invoiceItemsPriceMismatch_Expensive);
                    $invoiceItemsPriceMismatch_Expensive[$c][$sku]['text'] = "Master price at $priceMaster but invoiced at $pricePaid";
                    $expensiveDifference = number_format($pricePaid - $priceMaster, 2);
                    $invoiceItemsPriceMismatch_Expensive[$c][$sku]['diff'] = $expensiveDifference;
                    $overChargeTotal += $expensiveDifference;

                } elseif ($priceMasterLow > $pricePaid) {
                    //Item in Invoice are cheaper than price master.
		    $c = count($invoiceItemsPriceMismatch_Cheaper);
                    $invoiceItemsPriceMismatch_Cheaper[$c][$sku]['text'] = "Master price at $priceMaster but invoiced at $pricePaid";
                    $cheaperDifference = number_format($priceMaster - $pricePaid, 2);
                    $invoiceItemsPriceMismatch_Cheaper[$c][$sku]['diff'] = $cheaperDifference;
                    $underChargeTotal += $cheaperDifference;
                } else {
		    $c = count($invoiceItemsPriceMismatch_Unknown);
                    $invoiceItemsPriceMismatch_Unknown[$c][$sku] = "Unknown error";
                }
            }
	}
        }

        return array(
            $itemsInInvoice,
            $invoiceItemsNotSetInMaster,
            $invoiceItemsPriceMismatch_Cheaper,
            $invoiceItemsPriceMismatch_Expensive,
            $invoiceItemsMatch,
            $invoiceItemsPriceMismatch_Unknown,
            $overChargeTotal,
            $underChargeTotal,
	    $notInInvoiceTotal,
        );


    }


}






