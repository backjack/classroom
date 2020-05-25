<?php

use Phinx\Migration\AbstractMigration;

class AddTemplateTwo extends AbstractMigration
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
                'template_id'=>2,
                'name'=>'Learn Plus',
                'sort_order'=>2,
                'active'=>0
            ]
        ]);

        $this->insert('template_option',[
            [
                'template_id'=>2,
                'label'=>'Top Bar Background Color',
                'key'=>'topbar_bgcolor',
                'type'=>'color',
                'group'=>'top_bar',
                'sort_order'=>'1',
            ],
            [
                'template_id'=>2,
                'label'=>'Top Bar Font Color',
                'key'=>'topbar_fontcolor',
                'type'=>'color',
                'group'=>'top_bar',
                'sort_order'=>'2',
            ],
            [
                'template_id'=>2,
                'label'=>'Top Bar Slogan',
                'key'=> 'topbar_slogan',
                'type'=>'text',
                'group'=>'top_bar',
                'placeholder'=>'Slogan text on top bar',
                'sort_order'=>'3',
                'value'=>'Enroll for your online course or training session today',

            ],
            [
            'template_id'=>2,
            'label'=>'Top Bar Icon',
            'key'=> 'topbar_icon',
            'type'=>'text',
            'group'=>'top_bar',
            'placeholder'=>'Font awesome icon',
            'sort_order'=>'4',
            'value'=>'graduation-cap',
        ],
            [
                'template_id'=>2,
                'label'=>'Facebook Url',
                'key'=> 'topbar_facebook',
                'type'=>'text',
                'group'=>'top_bar',
                'placeholder'=>'Full facebook url',
                'sort_order'=>'4',
                'value'=>'#',
            ],
            [
                'template_id'=>2,
                'label'=>'Twitter Url',
                'key'=> 'topbar_twitter',
                'type'=>'text',
                'group'=>'top_bar',
                'placeholder'=>'Full twitter url',
                'sort_order'=>'5',
                'value'=>'#',
            ],
            [
                'template_id'=>2,
                'label'=>'Google+ Url',
                'key'=> 'topbar_google',
                'type'=>'text',
                'group'=>'top_bar',
                'placeholder'=>'Full Google+ url',
                'sort_order'=>'6',
                'value'=>'#',
            ],
            [
                'template_id'=>2,
                'label'=>'Linkedin Url',
                'key'=> 'topbar_linkedin',
                'type'=>'text',
                'group'=>'top_bar',
                'placeholder'=>'Full linkedin url',
                'sort_order'=>'7',
                'value'=>'#',
            ],
            [
                'template_id'=>2,
                'label'=>'Instagram Url',
                'key'=> 'topbar_instagram',
                'type'=>'text',
                'group'=>'top_bar',
                'placeholder'=>'Full instagram url',
                'sort_order'=>'8',
                'value'=>'#',
            ],
            [
                'template_id'=>2,
                'label'=>'Navigation Bar Background Color',
                'key'=> 'navbar_bgcolor',
                'type'=>'color',
                'group'=>'navigation_bar',
                'sort_order'=>'1',
            ],
            [
                'template_id'=>2,
                'label'=>'Navigation Bar Font Color',
                'key'=> 'navbar_fontcolor',
                'type'=>'color',
                'group'=>'navigation_bar',
                'sort_order'=>'2',
            ],
            [
                'template_id'=>2,
                'label'=>'Primary Color',
                'key'=>'color_primary_color',
                'type'=>'color',
                'group'=>'colors',
                'sort_order'=>'1',
            ],
            [
                'template_id'=>2,
                'label'=>'Primary Button Color',
                'key'=>'color_primary_btn_color',
                'type'=>'color',
                'group'=>'colors',
                'sort_order'=>'2',
            ],
            [
                'template_id'=>2,
                'label'=>'Secondary Button Color',
                'key'=>'color_secondary_btn_color',
                'type'=>'color',
                'group'=>'colors',
                'sort_order'=>'3',
            ],
            [
                'template_id'=>2,
                'label'=>'Page title background color',
                'key'=>'color_page_title',
                'type'=>'color',
                'group'=>'colors',
                'sort_order'=>'4',
            ],
            [
                'template_id'=>2,
                'label'=>'Page title text color',
                'key'=>'color_page_title_text',
                'type'=>'color',
                'group'=>'colors',
                'sort_order'=>'5',
            ],
            [
                'template_id'=>2,
                'label'=>'Footer background color',
                'key'=>'footer_bgcolor',
                'type'=>'color',
                'group'=>'footer',
                'sort_order'=>'1',
            ],
            [
                'template_id'=>2,
                'label'=>'Footer text color',
                'key'=>'footer_textcolor',
                'type'=>'color',
                'group'=>'footer',
                'sort_order'=>'2',
            ],
            [
                'template_id'=>2,
                'label'=>'About Us',
                'key'=>'footer_about',
                'type'=>'textarea',
                'group'=>'footer',
                'sort_order'=>'3',
                'class'=>'rte', 
            ],
            [
                'template_id'=>2,
                'label'=>'Website',
                'key'=>'footer_website',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'4',
            ],
            [
                'template_id'=>2,
                'label'=>'Address',
                'key'=>'footer_address',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'5',
            ],
            [
                'template_id'=>2,
                'label'=>'Email',
                'key'=>'footer_email',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'6',
            ],
            [
                'template_id'=>2,
                'label'=>'Telephone',
                'key'=>'footer_tel',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'7',
            ],
            [
                'template_id'=>2,
                'label'=>'Newsletter Form Code',
                'key'=>'footer_newsletter_code',
                'type'=>'textarea',
                'group'=>'footer',
                'sort_order'=>'8',
            ],
            [
                'template_id'=>2,
                'label'=>'Site Credits',
                'key'=>'footer_credits',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'9',
            ],


        ]);


    }
}
