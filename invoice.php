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

    //print_r($_POST);
    $masterFile = $_POST['masterList'];
    $masterSku = $_POST['masterSku'];
    $masterPrice = $_POST['masterPrice'];

    $invoiceFile = $_POST['invoiceList'];
    $invoiceSku = $_POST['invoiceSku'];
    $invoicePrice = $_POST['invoicePrice'];

    $priceThreshold = $_POST['priceThreshold'];

    $compareClass = new Compare();
    $tryCompare = $compareClass->compare($masterFile, $masterSku, $masterPrice, true, $invoiceFile, $invoiceSku, $invoicePrice, true, $priceThreshold);
    print_r($tryCompare);
    //total in invoice, no match, cheaper, expensive, match, unknown

    
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
	<span>Advanced Options +</span>
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
	<span>Advanced Options +</span>
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
    <select name="masterList">
        <?php foreach($masterLocationCSV as $key => $masterCSV) { ?>
            <option value="<?= $masterCSV?>"><?= $masterCSV ?></option>
        <?php } ?>
    </select>
    <br />

    <div class="advancedOptions">
	<span>Advanced Options +</span>
    </div>
    <div class="hide">
    <br/>
    SKU COL
    <select name="masterSku">
        <?php for ($i=0; $i<=25; $i++) { ?>
            <option value="<?= $i;?>" <?php if($i==3){ echo "selected";} ?>><?= chr(65 + $i);?></option>
        <?php } ?>
    </select>

    PRICE COL
    <select name="masterPrice">
        <?php for ($i=0; $i<=25; $i++) { ?>
            <option value="<?= $i;?>" <?php if($i==9){ echo "selected";} ?>><?= chr(65 + $i);?></option>
        <?php } ?>
    </select>
    </div>

    <br />
    <br />

    Select Invoice File:
    <select name="invoiceList">
        <?php foreach($invoiceLocationCSV as $key => $invoiceCSV) { ?>
            <option value="<?= $invoiceCSV?>"><?= $invoiceCSV ?></option>
        <?php } ?>
    </select>
    <br/>

    <div class="advancedOptions">
	<span>Advanced Options +</span>
    </div>
    <div class="hide">
    <br/>
    SKU COL
    <select name="invoiceSku">
        <?php for ($i=0; $i<=25; $i++) { ?>
            <option value="<?= $i;?>" <?php if($i==0){ echo "selected";} ?>><?= chr(65 + $i);?></option>
        <?php } ?>
    </select>

    PRICE COL
    <select name="invoicePrice">
        <?php for ($i=0; $i<=25; $i++) { ?>
            <option value="<?= $i;?>" <?php if($i==7){ echo "selected";} ?>><?= chr(65 + $i);?></option>
        <?php } ?>
    </select>
    </div>

    <br/>
    <br />

    PRICE THRESHOLD (PENCE)
    <select name="priceThreshold">
        <?php for ($i=0; $i<=99; $i++) { ?>
            <option value="<?= $i;?>" <?php if($i==2){ echo "selected";} ?>><?= $i;?></option>
        <?php } ?>
    </select>

    <br/>
    <br/>
    <input type="submit" value="Compare" name="submit">
</form>

</body>
</html>
