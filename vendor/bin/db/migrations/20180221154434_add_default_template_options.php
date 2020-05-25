<?php

use Phinx\Migration\AbstractMigration;

class AddDefaultTemplateOptions extends AbstractMigration
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
         $this->insert('template',[
             [
           'template_id'=>1,
            'name'=>'Classic',
            'sort_order'=>1,
            'active'=>1
        ]
         ]);

        $this->insert('template_option',[
            [
                'template_id'=>1,
                'label'=>'Navigation Bar',
                'key'=>'color_navbar',
                'type'=>'color',
                'group'=>'color',
                'sort_order'=>'1',
                'value'=>$this->getOptionValue('color_navbar')
            ],
            [
                'template_id'=>1,
                'label'=>'Primary Color',
                'key'=>'color_primary_color',
                'type'=>'color',
                'group'=>'color',
                'sort_order'=>'2',
                'value'=>$this->getOptionValue('color_primary_color')
            ],
            [
                'template_id'=>1,
                'label'=>'Navigation text color',
                'key'=>'color_navtext',
                'type'=>'color',
                'group'=>'color',
                'sort_order'=>'3',
                'value'=>$this->getOptionValue('color_navtext')
            ],
            [
                'template_id'=>1,
                'label'=>'Footer background color',
                'key'=>'color_footer',
                'type'=>'color',
                'group'=>'color',
                'sort_order'=>'4',
                'value'=>$this->getOptionValue('color_footer')
            ],
            [
                'template_id'=>1,
                'label'=>'Footer text color',
                'key'=>'color_footertext',
                'type'=>'color',
                'group'=>'color',
                'sort_order'=>'5',
                'value'=>$this->getOptionValue('color_footertext')
            ],
            [
                'template_id'=>1,
                'label'=>'Page title background color',
                'key'=>'color_page_title',
                'type'=>'color',
                'group'=>'color',
                'sort_order'=>'6',
                'value'=>$this->getOptionValue('color_page_title')
            ],
            [
                'template_id'=>1,
                'label'=>'Page title text color',
                'key'=>'color_page_title_text',
                'type'=>'color',
                'group'=>'color',
                'sort_order'=>'7',
                'value'=>$this->getOptionValue('color_page_title_text')
            ],
            [
                'template_id'=>1,
                'label'=>'Facebook',
                'key'=>'social_facebook',
                'type'=>'text',
                'group'=>'social',
                'sort_order'=>'1',
                'value'=>$this->getOptionValue('social_facebook')
            ],
            [
                'template_id'=>1,
                'label'=>'Twitter',
                'key'=>'social_twitter',
                'type'=>'text',
                'group'=>'social',
                'sort_order'=>'2',
                'value'=>$this->getOptionValue('social_twitter')
            ],
            [
                'template_id'=>1,
                'label'=>'Instagram',
                'key'=>'social_instagram',
                'type'=>'text',
                'group'=>'social',
                'sort_order'=>'3',
                'value'=>$this->getOptionValue('social_instagram')
            ],
            [
                'template_id'=>1,
                'label'=>'Google',
                'key'=>'social_google',
                'type'=>'text',
                'group'=>'social',
                'sort_order'=>'4',
                'value'=>$this->getOptionValue('social_google')
            ],
            [
                'template_id'=>1,
                'label'=>'Youtube',
                'key'=>'social_youtube',
                'type'=>'text',
                'group'=>'social',
                'sort_order'=>'5',
                'value'=>$this->getOptionValue('social_youtube')
            ],

            [
                'template_id'=>1,
                'label'=>'About Us',
                'key'=>'footer_about',
                'type'=>'textarea',
                'group'=>'footer',
                'sort_order'=>'1',
                'class'=>'rte',
                'value'=>$this->getOptionValue('footer_about')
            ],
            [
                'template_id'=>1,
                'label'=>'Address',
                'key'=>'footer_address',
                'type'=>'textarea',
                'group'=>'footer',
                'sort_order'=>'2',
                'class'=>'rte',
                'value'=>$this->getOptionValue('footer_address')
            ],
            [
                'template_id'=>1,
                'label'=>'Email',
                'key'=>'footer_email',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'3',
                'value'=>$this->getOptionValue('footer_email')
            ],
            [
                'template_id'=>1,
                'label'=>'Telephone',
                'key'=>'footer_tel',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'4',
                'value'=>$this->getOptionValue('footer_tel')
            ],
            [
                'template_id'=>1,
                'label'=>'Newsletter Form Code',
                'key'=>'footer_newsletter_code',
                'type'=>'textarea',
                'group'=>'footer',
                'sort_order'=>'5',
                'value'=>$this->getOptionValue('footer_newsletter_code')
            ],
            [
                'template_id'=>1,
                'label'=>'Site Credits',
                'key'=>'footer_credits',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'6',
                'value'=>$this->getOptionValue('footer_credits')
            ],
            [
                'template_id'=>1,
                'label'=>'Show Social Icons',
                'key'=>'footer_show_sicons',
                'type'=>'radio',
                'options'=>'1=Yes,0=No',
                'group'=>'footer',
                'sort_order'=>'7',
                'value'=>$this->getOptionValue('footer_show_sicons')
            ],
            [
                'template_id'=>1,
                'label'=>'Show Newsletter Form',
                'key'=>'footer_show_newsletter',
                'type'=>'radio',
                'options'=>'1=Yes,0=No',
                'group'=>'footer',
                'sort_order'=>'8',
                'value'=>$this->getOptionValue('footer_show_newsletter')
            ],
            [
                'template_id'=>1,
                'label'=>'Show About Us',
                'key'=>'footer_show_about',
                'type'=>'radio',
                'options'=>'1=Yes,0=No',
                'group'=>'footer',
                'sort_order'=>'9',
                'value'=>$this->getOptionValue('footer_show_about')
            ],
            [
                'template_id'=>1,
                'label'=>'Show Contact Us',
                'key'=>'footer_show_contact',
                'type'=>'radio',
                'options'=>'1=Yes,0=No',
                'group'=>'footer',
                'sort_order'=>'10',
                'value'=>$this->getOptionValue('footer_show_contact')
            ],


        ]);

    }

    private function getOptionValue($option){
        $row = $this->fetchRow("select * from setting where setting.key='$option'");

        return $row['value'];

    }
}
