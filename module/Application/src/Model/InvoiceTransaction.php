<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/6/2018
 * Time: 4:56 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class InvoiceTransaction extends BaseTable {

    protected $tableName = 'invoice_transaction';
    protected $primary = 'invoice_transaction_id';


}