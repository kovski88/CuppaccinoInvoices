<?php

include('passInfo.php');

if (isset($_COOKIE['PrivatePageLogin'])) {
   if ($_COOKIE['PrivatePageLogin'] == hash('sha512', $password . $passwordAlgo)) {
   } else {
   	setcookie('PrivatePageLogin', '');
	header("Location: /index.php");
   }
}

?>
