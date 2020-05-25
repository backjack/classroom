<?php

use Phinx\Migration\AbstractMigration;

class AddPayGateway extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->insert('payment_method',[
           'payment_method_id'=>6,
            'payment_method'=>'PayU: Online Payment',
            'status'=>0,
            'code'=>'payu',
            'sort_order'=>0,
            'currency'=>"ARS BRL CLP COP CZK HUF INR MXN NGN PAB PEN PLN RON RUB ZAR TRY"
        ]);

        $this->insert('payment_method_field',[
           [
               'payment_method_id'=>6,
               'key'=>'payu_easyplus_safe_key',
               'label'=>'Safe key',
               'placeholder'=>'',
               'type'=>'text'
           ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_api_username',
                'label'=>'API Username',
                'placeholder'=>'',
                'type'=>'text'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_api_password',
                'label'=>'API Password',
                'placeholder'=>'',
                'type'=>'text'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_transaction_mode',
                'label'=>'Transaction mode',
                'placeholder'=>'',
                'type'=>'select',
                'options'=>'staging=Staging,production=Production'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_transaction_type',
                'label'=>'Transaction type',
                'placeholder'=>'',
                'type'=>'select',
                'options'=>'PAYMENT=PAYMENT,RESERVE=RESERVE'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_payment_currency',
                'label'=>'Billing currency',
                'placeholder'=>'',
                'type'=>'select',
                'options'=>'ARS=Argentina Peso (ARS),BRL=Brazil Real (BRL),CLP=Chile Peso (CLP),COP=Colombia Peso (COP),CZK=Czech Republic Koruna (CZK),HUF=Hungary Forint (HUF),INR=India Rupee (INR),MXN=Mexico Peso (MXN),NGN=Nigeria Naira (NGN),PAB=Panama Balboa (PAB),PEN=Peru Sol (PEN),PLN=Poland Zloty (PLN),RON=Romania Leu (RON),RUB=Russia Ruble (RUB),ZAR=South Africa Rand (ZAR),TRY=Turkey Lira (TRY)'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_method_credit_card',
                'label'=>'Payment Method: Credit Card',
                'type'=>'checkbox'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_method_discovery_miles',
                'label'=>'Payment Method: Discovery Miles',
                'type'=>'checkbox'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_method_ebucks',
                'label'=>'Payment Method: Ebucks',
                'type'=>'checkbox'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_method_eft',
                'label'=>'Payment Method: EFT',
                'type'=>'checkbox'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_method_masterpass',
                'label'=>'Payment Method: Masterpass',
                'type'=>'checkbox'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_method_rcs',
                'label'=>'Payment Method: RCS',
                'type'=>'checkbox'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_method_eft_pro',
                'label'=>'Payment Method: EFT Pro',
                'type'=>'checkbox'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_method_creditcard_vco',
                'label'=>'Payment Method: Credit Card VCO',
                'type'=>'checkbox'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_method_mobicred',
                'label'=>'Payment Method: Mobicred',
                'type'=>'checkbox'
            ],
            [
                'payment_method_id'=>6,
                'key'=>'payu_easyplus_debug',
                'label'=>'Debug',
                'type'=>'checkbox'
            ],

        ]);

    }
}
