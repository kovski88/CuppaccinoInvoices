<?php

require('passwordCheck.php');
require_once dirname(__FILE__) . '/classes/ExcelToCSV.php';

if (isset($_GET['p']) && $_GET['p'] == "masterUpload") {

    $target_dir = "uploads/master/";
    $target_file = $target_dir . basename($_FILES["masterFileToUpload"]["name"]);

    $uploadOk = 1;
    $fileInfo = explode(":", $_POST['fileType'], 2);

    $fileName = pathinfo($target_file, PATHINFO_FILENAME);

    $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
    $fileTypeGiven = $fileInfo[0];

    $fileMime = $_FILES["masterFileToUpload"]['type'];
    $fileMimeGiven = $fileInfo[1];

    $fileOption = $_POST['fileOption'];

    if (isset($_POST["submit"])) {
        //print("$fileType, $fileTypeGiven, $fileMime, $fileMimeGiven");
        if ((strcasecmp($fileType, $fileTypeGiven) === 0) && strcasecmp($fileMime, $fileMimeGiven) === 0) {
            $uploadOk = 1;
        } else {
            echo "File types do not match! \n";
            $uploadOk = 0;
        }
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded. \n";
    } else {

        //1 - remove old files
        //2 - upload new
        //3 - convert (if needed)

        foreach (scandir($target_dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            unlink($target_dir . DIRECTORY_SEPARATOR . $item);
        }

        if (move_uploaded_file($_FILES["masterFileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["masterFileToUpload"]["name"]) . " has been uploaded. \n";
            chmod($target_file, 0666);

            if ($fileTypeGiven != "csv") {
                $tmp = new ExceltoCSV();
                $converter = $tmp->convert($fileName, "master/", null,
                    array("fileExt" => $fileType, 'ExcelFormat' => $fileOption));
                if ($converter[0] === false) {
                    print("Error:" . $converter[1] . "\n");
                } else {
                    print("The file has been converted correctly\n");
                }
            }

        } else {
            echo "Sorry, there was an error uploading your file. \n";
        }
    }
}

if (isset($_GET['p']) && $_GET['p'] == "invoiceUpload") {

    $target_dir = "uploads/invoices/";
    $target_file = $target_dir . basename($_FILES["invoiceFileToUpload"]["name"]);

    $uploadOk = 1;
    $fileInfo = explode(":", $_POST['fileType'], 2);

    $fileName = pathinfo($target_file, PATHINFO_FILENAME);

    $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
    $fileTypeGiven = $fileInfo[0];

    $fileMime = $_FILES["invoiceFileToUpload"]['type'];
    $fileMimeGiven = $fileInfo[1];

    $fileOption = $_POST['fileOption'];

    if (isset($_POST["submit"])) {
        //print("$fileType, $fileTypeGiven, $fileMime, $fileMimeGiven");
        if ((strcasecmp($fileType, $fileTypeGiven) === 0) && strcasecmp($fileMime, $fileMimeGiven) === 0) {
            $uploadOk = 1;
        } else {
            echo "File types do not match! \n";
            $uploadOk = 0;
        }
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded. \n";
    } else {

        //1 - remove old files
        //2 - upload new
        //3 - convert (if needed)

        foreach (scandir($target_dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            unlink($target_dir . DIRECTORY_SEPARATOR . $item);
        }

        if (move_uploaded_file($_FILES["invoiceFileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . basename($_FILES["invoiceFileToUpload"]["name"]) . " has been uploaded. \n";
            chmod($target_file, 0666);

            if ($fileTypeGiven != "csv") {
                $tmp = new ExceltoCSV();
                $converter = $tmp->convert($fileName, "invoices/", null,
                    array("fileExt" => $fileType, 'ExcelFormat' => $fileOption));
                if ($converter[0] === false) {
                    print("Error:" . $converter[1] . "\n");
                } else {
                    print("The file has been converted correctly\n");
                }
            }

        } else {
            echo "Sorry, there was an error uploading your file. \n";
        }
    }
}

//Get master CSV filenames
//Get invoice CSV filenames
//Filename, Col for Sku, Col for Price and Price allowance.

if (isset($_GET['p']) && $_GET['p'] == "compare") {
    require_once dirname(__FILE__) . '/classes/Compare.php';

    print_r($_POST);
    $masterFile = $_POST['masterList'];
    $masterSku = $_POST['masterSku'];
    $masterPrice = $_POST['masterPrice'];

    $invoiceFile = $_POST['invoiceList'];
    $invoiceSku = $_POST['invoiceSku'];
    $invoicePrice = $_POST['invoicePrice'];

    $priceThreshold = $_POST['priceThreshold'];

    $compareClass = new Compare();
    $tryCompare = $compareClass->compareMasterFileToInvoice($masterFile, $masterSku, $masterPrice, true, $invoiceFile, $invoiceSku, $invoicePrice, true, $priceThreshold);
    //print_r($tryCompare);
    //total in invoice, no match, cheaper, expensive, match, unknown, overchargeTotal, underChargeTotal


}

$masterLocation = "uploads/master";
$masterLocationCSV = array();
$invoiceLocation = "uploads/invoices";
$invoiceLocationCSV = array();

$counter = 0;
foreach (scandir($masterLocation) as $item) {
    if ($item == '.' || $item == '..' || strpos($item, ".") === 0 || strpos($item, ".csv") === false) continue;
    $masterLocationCSV["mcsv".$counter] = $item;
    $counter++;
}

$counter = 0;
foreach (scandir($invoiceLocation) as $item) {
    if ($item == '.' || $item == '..' || strpos($item, ".") === 0 || strpos($item, ".csv") === false) continue;
    $invoiceLocationCSV["icsv".$counter] = $item;
    $counter++;
}

?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="assets/invoice.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="/assets/invoice.js"></script>
</head>
<body>

<h2>Step 1</h2>
<h3>Upload a Master File</h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?p=masterUpload" method="post" enctype="multipart/form-data">
    Select Master Price List to upload:
    <input type="file" name="masterFileToUpload" id="masterFileToUpload">
    <br/>
    <div class="advancedOptions">
        <span>Advanced +</span>
    </div>
    <div class="hide">
        <br/>
        Select Master File Extension:
        <select name="fileType">
            <option value="xlsx:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">XLSX</option>
        </select>
        <br/>
        Select Master File Type:
        <select name="fileOption">
            <option value="Excel2007">Excel2007</option>
        </select>
        <br/>
        <br/>
    </div>
    <input type="submit" value="Upload Master File" name="submit">
</form>


<h2>Step 2</h2>
<h3>Upload an Invoice</h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?p=invoiceUpload" method="post" enctype="multipart/form-data">
    Select Invoice Price List to upload:
    <input type="file" name="invoiceFileToUpload" id="invoiceFileToUpload">
    <br/>
    <div class="advancedOptions">
        <span>Advanced +</span>
    </div>
    <div class="hide">
        <br/>
        Select Invoice File Extension:
        <select name="fileType">
            <option value="xlsx:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">XLSX</option>
        </select>
        <br/>
        Select Invoice File Type:
        <select name="fileOption">
            <option value="Excel2007">Excel2007</option>
        </select>
        <br/>
        <br/>
    </div>
    <input type="submit" value="Upload Invoice" name="submit">
</form>

<h2>Step 3</h2>
<h3>Select Comparison</h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?p=compare" method="post" enctype="multipart/form-data">
    Select Master Price List File:
    <?php $point = isset($_POST['masterList']) ? $_POST['masterList'] : null; ?>
    <select name="masterList">
        <?php foreach($masterLocationCSV as $key => $masterCSV) { ?>
            <option value="<?= $masterCSV?>" <?php if($masterCSV == $point){ echo "selected";} ?>><?= $masterCSV ?></option>
        <?php } ?>
    </select>
    <br />

    <div class="advancedOptions">
        <span>Advanced +</span>
    </div>
    <div class="hide">
        <br/>
        SKU COL
	<?php $point = isset($_POST['masterSku']) ? $_POST['masterSku'] : 3; ?>
        <select name="masterSku">
            <?php for ($i=0; $i<=25; $i++) { ?>
                <option value="<?= $i;?>" <?php if($i==$point){ echo "selected";} ?>><?= chr(65 + $i);?></option>
            <?php } ?>
        </select>

        PRICE COL
	<?php $point = isset($_POST['masterPrice']) ? $_POST['masterPrice'] : 9; ?>
        <select name="masterPrice">
            <?php for ($i=0; $i<=25; $i++) { ?>
                <option value="<?= $i;?>" <?php if($i==$point){ echo "selected";} ?>><?= chr(65 + $i);?></option>
            <?php } ?>
        </select>
    </div>

    <br />
    <br />

    Select Invoice File:
    <?php $point = isset($_POST['invoiceList']) ? $_POST['invoiceList'] : null; ?>
    <select name="invoiceList">
        <?php foreach($invoiceLocationCSV as $key => $invoiceCSV) { ?>
            <option value="<?= $invoiceCSV?>" <?php if($invoiceCSV==$point){ echo "selected";} ?>><?= $invoiceCSV ?></option>
        <?php } ?>
    </select>
    <br/>

    <div class="advancedOptions">
        <span>Advanced +</span>
    </div>
    <div class="hide">
        <br/>
        SKU COL
	<?php $point = isset($_POST['invoiceSku']) ? $_POST['invoiceSku'] : 3; ?>
        <select name="invoiceSku">
            <?php for ($i=0; $i<=25; $i++) { ?>
                <option value="<?= $i;?>" <?php if($i==$point){ echo "selected";} ?>><?= chr(65 + $i);?></option>
            <?php } ?>
        </select>

        PRICE COL
	<?php $point = isset($_POST['invoicePrice']) ? $_POST['invoicePrice'] : 9; ?>
        <select name="invoicePrice">
            <?php for ($i=0; $i<=25; $i++) { ?>
                <option value="<?= $i;?>" <?php if($i==$point){ echo "selected";} ?>><?= chr(65 + $i);?></option>
            <?php } ?>
        </select>
    </div>

    <br/>
    <br />

    PRICE THRESHOLD (PENCE)
    <?php $point = isset($_POST['priceThreshold']) ? $_POST['priceThreshold'] : 2; ?>
    <select name="priceThreshold">
        <?php for ($i=0; $i<=99; $i++) { ?>
            <option value="<?= $i;?>" <?php if($i==$point){ echo "selected";} ?>><?= $i;?></option>
        <?php } ?>
    </select>

    <br/>
    <br/>
    <input type="submit" value="Compare" name="submit">
</form>

<!--
//print_r($tryCompare);
//      0		1	2	3	    4	   5		6		7		8
//total in invoice, no match, cheaper, expensive, match, unknown, overchargeTotal, underChargeTotal, notInInvTotal-->
<br/>
<?php
if(!empty($tryCompare)) {
    echo "There are {$tryCompare[0]} items in the Invoice <br/>";

    echo "There are " .count($tryCompare[1]) . " items in the Invoice that are not in the master file";
    if(count($tryCompare[1]) > 0){
        echo " - total extras {$tryCompare[8]}";
    }

    echo "<br />";

    echo "There are " .count($tryCompare[4]) ." items that match prices";
    if($priceThreshold > 0){
        echo " - or are in a range of {$priceThreshold}p";
    }
    echo "<br/>";

    echo "There are " .count($tryCompare[2]). " items that are cheaper in the invoice";
    if(count($tryCompare[2]) > 0){
        echo " - total saving {$tryCompare[7]}";
    }
    echo "<br/>";

    echo "There are " .count($tryCompare[3]). " items that are more expensive in the invoice";
    if(count($tryCompare[3]) > 0){
        echo " - total overcharge {$tryCompare[6]}";
    }
    echo "<br/>";
}
?>

</body>
</html>
