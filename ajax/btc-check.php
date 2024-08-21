<?php
/**
 * Golpo - QR Generator
 * ajax/btc-check.php
 *
 * PHP version 5.4+
 *
 * @category  PHP
 * @package   GolpoQR
 * @author    Shah Baizid Arefin <razen@golpocom.com?
 * @copyright 2020-2024 Shah Baizid Arefin
 * 
 * @link      https://golpocom.com
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
    || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')
) {
    exit;
}
$getaddress = filter_input(INPUT_POST, "btc_account", FILTER_SANITIZE_SPECIAL_CHARS);

function validate($address){
    $decoded = decodeBase58($address);

    $d1 = hash("sha256", substr($decoded,0,21), true);
    $d2 = hash("sha256", $d1, true);

    if(substr_compare($decoded, $d2, 21, 4)){
            throw new \Exception("bad digest");
    }
    return true;
}
function decodeBase58($input) {
    $alphabet = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";

    $out = array_fill(0, 25, 0);
    for($i=0;$i<strlen($input);$i++){
        if(($p=strpos($alphabet, $input[$i]))===false){
                throw new \Exception("invalid character found");
        }
        $c = $p;
        for ($j = 25; $j--; ) {
                $c += (int)(58 * $out[$j]);
                $out[$j] = (int)($c % 256);
                $c /= 256;
                $c = (int)$c;
        }
        if($c != 0){
            throw new \Exception("address too long");
        }
    }
    $result = "";
    foreach($out as $val){
            $result .= chr($val);
    }

    return $result;
}
$message = "ok";
try {
    validate($getaddress);
} catch(\Exception $e) {
    $message = $e->getMessage();
}
echo $message;
