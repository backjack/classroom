<?php

use Phinx\Migration\AbstractMigration;

class AddPaypalExpress extends AbstractMigration
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

        $data= [

            [
                'key'=>'token',
                'label'=>'Token',
                'type'=>'text',
                'payment_method_id'=>2
            ]
        ];

        $this->insert('payment_method_field',$data);



        $data = [
          [
              'payment_method'=>'Paypal Express',
              'status'=>0,
              'code'=> 'paypalex'
          ]
        ];

        $this->insert('payment_method',$data);

        $data = [
            [
                'payment_method_id'=>5,
                'key'=>'username',
                'label'=>'Api Username',
                'type'=>'text',
            ],
            [
                'payment_method_id'=>5,
                'key'=>'password',
                'label'=>'Api Password',
                'type'=>'text',
            ],
            [
                'payment_method_id'=>5,
                'key'=>'signature',
                'label'=>'Api Signature',
                'type'=>'text',
            ],
            [
                'key'=>'mode',
                'label'=>'Mode',
                'type'=>'radio',
                'options'=>'1=Live,0=Test',
                'payment_method_id'=>5
            ],
            [
                'key'=>'solutiontype',
                'label'=>'Solution Type',
                'type'=>'select',
                'options'=>'Sole=Sole,Mark=Mark',
                'payment_method_id'=>5
            ],
            [
                'key'=>'landingpage',
                'label'=>'Landing Page',
                'type'=>'select',
                'options'=>'Billing=Billing,Login=Login',
                'payment_method_id'=>5
            ],
            [
                'key'=>'headerimageurl',
                'label'=>'Header Image Url',
                'type'=>'text',
                'payment_method_id'=>5
            ],
            [
                'key'=>'logoimageurl',
                'label'=>'Logo Image Url',
                'type'=>'text',
                'payment_method_id'=>5
            ],
            [
                'key'=>'bordercolor',
                'label'=>'Border Color',
                'type'=>'text',
                'payment_method_id'=>5
            ],

        ];

        $this->insert('payment_method_field',$data);

    }
}
