<?php
/**
 * File contains class Qb_Clients() extends Qb()
 */

/**
 * Include base class for SOAP SERVER
 */
require 'qb.php';

/**
 * Class for import all clients from Qb
 * 
 * @package QB SOAP
 * @version 2013-10-20
 */
class Qb_Clients extends Qb
{
    /**
     * Function send request for Quickbooks
     *
     * @return  string
     * @param   object $param
     * @access  public
     * @version  2013-10-20
     */
    public function sendRequestXML($param = '')
    {
        $id = requestId();

        // <!-- ActiveStatus may have one of the following values: ActiveOnly [DEFAULT], InactiveOnly, All -->
        if($param->ticket == QB_TICKET){
            $request = '<?xml version="1.0" encoding="utf-8"?>
                <?qbxml version="12.0"?>
                <QBXML>
                    <QBXMLMsgsRq onError="stopOnError">
                        <CustomerQueryRq requestID="'.time().'" metaData="NoMetaData" iterator="'.(($id != '')?'Continue':'Start').'" '.(($id != '')?'iteratorID="'.$id.'"':'').'>
                            <MaxReturned>500</MaxReturned>
                            <ActiveStatus>ActiveOnly</ActiveStatus>
                        </CustomerQueryRq>
                    </QBXMLMsgsRq>
                </QBXML>';
            $this->response->sendRequestXMLResult = $request;
        }
        else
            $this->response->sendRequestXMLResult = "E: Invalid ticket.";

        return $this->response;
    }


    /**
     * Function get response from QB
     *
     * @return  string
     * @param   object $param
     * @access  public
     * @version 2013-03-15
     */
    public function receiveResponseXML($param = '')
    {
        $response = simplexml_load_string($param->response);
        $iteratorID = trim($response->QBXMLMsgsRs->CustomerQueryRs->attributes()->iteratorID);

        // set new iteratorID
        requestId($iteratorID);

        if( ($param->ticket == QB_TICKET) && isset($response->QBXMLMsgsRs->CustomerQueryRs->CustomerRet) ){
            $rows = $response->QBXMLMsgsRs->CustomerQueryRs;
            settype($rows, 'array');

            // if list contain only one item row
            if(isset($rows['CustomerRet']->ListID))
                $rows = array($rows['CustomerRet']);
            else
                $rows = $rows['CustomerRet'];

            $data = array();
            foreach ($rows as $i=>$r) {
                settype($r, 'array');

                $data[] = array(
                    'qb_id' => trim($r['ListID']),
                    'qb_es' => trim($r['EditSequence']),
                    'is_active' => trim($r['IsActive']),
                    'phone' => trim($r['Phone']),
                    'notes' => trim($r['Notes']),
                    'fax'   => trim($r['Fax']),
                    'company_name' => trim($r['Name']),

                    'b_email' => trim($r['Email']),
                    'b_email_other' => trim($r['Cc']),
                    'b_phone' => trim($r['AltPhone']),
                    'b_salutation' => trim($r['Salutation']),
                    'b_fname' => trim($r['FirstName']),
                    'b_lname' => trim($r['LastName']),
                    'b_address' => trim($r['BillAddress']->Addr1),
                    'b_address2' => trim($r['BillAddress']->Addr2),
                    'b_address3' => trim($r['BillAddress']->Addr3),
                    'b_city' => trim($r['BillAddress']->City),
                    'b_state' => trim($r['BillAddress']->State),
                    'b_country' => trim($r['BillAddress']->Country),
                    'b_zip' => trim($r['BillAddress']->PostalCode),
                );
            }

            // echo data into log file
            _log(print_r($data,1));

            $this->response->receiveResponseXMLResult = '30';
        }
        else
            $this->response->receiveResponseXMLResult = '100';

        return $this->response;
    }
}