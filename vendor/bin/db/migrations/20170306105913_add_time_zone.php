<?php

use Phinx\Migration\AbstractMigration;

class AddTimeZone extends AbstractMigration
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
        $timezones = '';
        foreach(timezone_abbreviations_list() as $abbr => $timezone){
            foreach($timezone as $val){
                if(isset($val['timezone_id'])){
                    $timezones .= $val['timezone_id'].',';
                }
            }
        }
        $len = strlen($timezones);
        $timezones = substr($timezones,0,$len-1);
        $data = [
            [
                'key'=>'general_timezone',
                'label'=>'Site Timezone',
                'value'=>'UTC',
                'type'=>'select',
                'options'=>$timezones
            ]
        ];
        $this->insert('setting',$data);
    }
}
