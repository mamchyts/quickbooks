<?php
/**
 * File for integration QB
 * QB Webconnector send soap request to this file
 * 
 * @package QB SOAP
 */

/**
 * Log function
 *
 * @param string $mess
 */
function _log($mess = ''){
    $file_name = './log/'.date("Y_m_d").'/clients.log';
    if(!file_exists(dirname($file_name)))
        mkdir(dirname($file_name), 0777);

    $f = fopen($file_name, "ab");
    fwrite($f, "===============================================================================================================\n");
    fwrite($f, "[" . date("m/d/Y H:i:s") . "] ".$mess."\n");
    fclose($f);
}

/**
 * Log function
 *
 * @param string $mess
 */
function requestId($id = '')
{
    $file_name = './log/'.date("Y_m_d").'/clients_id.log';

    // save id into file
    if(trim($id) !== ''){
        $f = fopen($file_name, "c+b");
        fwrite($f, $id);
        fclose($f);
    }

    $id = trim(file_get_contents($file_name));
    return $id;
}

/**
 * System variables
 */
define('QB_LOGIN',    'admin');
define('QB_PASSWORD', '');
define('QB_TICKET',   '93f91a390fa604207f40e8a94d0d8fd11005de108ec1664234305e17e');

/**
 * Main class for SOAP SERVER
 */
require 'qb_clients.php';

/**
 * Create SOAP server
 */
$server = new SoapServer("qbwebconnectorsvc.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE));
$server->setClass("Qb_Clients");
$server->handle();