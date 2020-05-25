<?php

use Phinx\Migration\AbstractMigration;

class AddLabelSettings extends AbstractMigration
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
            $data = [
                [
                    'key'=>'label_enroll',
                    'label'=>'Enroll for a Session',
                    'type'=>'text',
                ],
                [
                    'key'=>'label_discussion',
                    'label'=>'Discuss',
                    'type'=>'text',
                ],
                [
                    'key'=>'label_classes_attended',
                    'label'=>'Classes Attended',
                    'type'=>'text'
                ],
                [
                    'key'=>'label_revision_notes',
                    'label'=>'Revision Notes',
                    'type'=>'text',
                ],
                [
                    'key'=>'label_take_test',
                    'label'=>'Take A Test',
                    'type'=>'text'
                ],
                [
                    'key'=>'label_classes',
                    'label'=>'Classes',
                    'type'=>'text'
                ],
                [
                    'key'=>'label_calendar',
                    'label'=>'Calendar',
                    'type'=>'text'
                ],
                [
                    'key'=>'label_blog',
                    'label'=>'Blog',
                    'type'=>'text'
                ],
                [
                    'key'=>'label_contact_us',
                    'label'=>'Contact Us',
                    'type'=>'text'
                ],
                [
                    'key'=>'label_about_us',
                    'label'=>'About Us',
                    'type'=>'text'
                ],
                [
                    'key'=>'label_follow_us',
                    'label'=>'Follow Us',
                    'type'=>'text'
                ]


            ];

        $this->insert('setting',$data);
    }
}
