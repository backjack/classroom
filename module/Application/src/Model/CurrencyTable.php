<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/6/2018
 * Time: 4:55 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class CurrencyTable extends BaseTable {

    protected $tableName = 'currency';
    protected $primary = 'currency_id';

}