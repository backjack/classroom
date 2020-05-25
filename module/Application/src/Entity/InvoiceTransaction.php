<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/6/2018
 * Time: 4:54 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class InvoiceTransaction extends Model {

    protected $table = 'invoice_transaction';
    protected $primaryKey = 'invoice_transaction_id';
    public $timestamps = false;
}