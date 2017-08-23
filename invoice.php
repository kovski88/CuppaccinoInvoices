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

?>

<h2>Step 1</h2>
<h3>Upload a Master File</h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?p=masterUpload" method="post" enctype="multipart/form-data">
    Select Master Price List to upload:
    <input type="file" name="masterFileToUpload" id="masterFileToUpload">
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
    <input type="submit" value="Upload Master File" name="submit">
</form>


<h2>Step 2</h2>
<h3>Upload an Invoice</h3>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?p=invoiceUpload" method="post" enctype="multipart/form-data">
    Select Invoice Price List to upload:
    <input type="file" name="invoiceFileToUpload" id="invoiceFileToUpload">
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
    <input type="submit" value="Upload Invoice" name="submit">
</form>


