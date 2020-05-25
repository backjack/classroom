<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/6/2018
 * Time: 4:55 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class InvoiceTable extends BaseTable {

    protected $tableName = 'invoice';
    protected $primary = 'invoice_id';

}