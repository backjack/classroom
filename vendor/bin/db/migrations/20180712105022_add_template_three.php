<?php

use Phinx\Migration\AbstractMigration;

class AddTemplateThree extends AbstractMigration
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
                'template_id'=>3,
                'name'=>'Learning',
                'sort_order'=>3,
                'active'=>0
            ]
        ]);

        $this->insert('template_option',[

            [
                'template_id'=>3,
                'label'=>'Show homepage cover photo',
                'key'=>'show_cover_photo',
                'type'=>'radio',
                'group'=>'home_page',
                'sort_order'=>'1',
                'options'=>'1=Yes,0=No',
                'value'=>'1'
            ],
            [
                'template_id'=>3,
                'label'=>'Homepage cover photo',
                'key'=>'homepage_photo',
                'type'=>'image',
                'group'=>'home_page',
                'sort_order'=>'2',
                'placeholder'=>'1732px x 1155px'
            ],
            [
                'template_id'=>3,
                'label'=>'Cover photo title',
                'key'=>'cover_photo_title',
                'type'=>'text',
                'group'=>'home_page',
                'sort_order'=>'3',
                'value'=>'Courses for Web & Mobile'
            ],
            [
                'template_id'=>3,
                'label'=>'Cover photo text',
                'key'=>'cover_photo_text',
                'type'=>'textarea',
                'group'=>'home_page',
                'sort_order'=>'4',
                'value'=>'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aspernatur consectetur consequatur distinctio earum ipsam.'
            ],

            //paralax photo
            [
                'template_id'=>3,
                'label'=>'Show homepage paralax photo',
                'key'=>'show_paralax_photo',
                'type'=>'radio',
                'group'=>'home_page',
                'sort_order'=>'1',
                'options'=>'1=Yes,0=No',
                'value'=>'5'
            ],
            [
                'template_id'=>3,
                'label'=>'Homepage paralax photo',
                'key'=>'homepage_photo',
                'type'=>'image',
                'group'=>'home_page',
                'sort_order'=>'6',
                'placeholder'=>'1664px x 1202px'
            ],
            [
                'template_id'=>3,
                'label'=>'Paralax photo title',
                'key'=>'paralax_photo_title',
                'type'=>'text',
                'group'=>'home_page',
                'sort_order'=>'7',
                'value'=>'Upgrade your skills'
            ],
            [
                'template_id'=>3,
                'label'=>'paralax photo text',
                'key'=>'Paralax_photo_text',
                'type'=>'textarea',
                'group'=>'home_page',
                'sort_order'=>'8',
                'value'=>'Millions use E-learning to improve their skills'
            ],
            //end paralax
            [
                'template_id'=>3,
                'label'=>'Show reviews',
                'key'=>'show_reviews',
                'type'=>'radio',
                'group'=>'home_page',
                'sort_order'=>'1',
                'options'=>'1=Yes,0=No',
                'value'=>'9'
            ],
            [
                'template_id'=>3,
                'label'=>'Review 1 text',
                'key'=>'review_1_text',
                'type'=>'textarea',
                'group'=>'home_page',
                'sort_order'=>'2',
            ],
            [
                'template_id'=>3,
                'label'=>'Review 1 name',
                'key'=>'review_1_name',
                'type'=>'text',
                'group'=>'home_page',
                'sort_order'=>'3',
            ],
            [
                'template_id'=>3,
                'label'=>'Review 1 company',
                'key'=>'review_1_company',
                'type'=>'text',
                'group'=>'home_page',
                'sort_order'=>'4',
            ],
            [
                'template_id'=>3,
                'label'=>'Review 1 stars',
                'key'=>'review_1_stars',
                'type'=>'select',
                'group'=>'home_page',
                'sort_order'=>'5',
                'options'=>'1=1,2=2,3=3,4=4,5=5'
            ],
            [
                'template_id'=>3,
                'label'=>'Review 1 photo',
                'key'=>'review_1_photo',
                'type'=>'image',
                'group'=>'home_page',
                'sort_order'=>'6',
                'placeholder'=>'50px x 50px'
            ],


            [
                'template_id'=>3,
                'label'=>'Review 2 text',
                'key'=>'review_2_text',
                'type'=>'textarea',
                'group'=>'home_page',
                'sort_order'=>'7',
            ],
            [
                'template_id'=>3,
                'label'=>'Review 2 name',
                'key'=>'review_2_name',
                'type'=>'text',
                'group'=>'home_page',
                'sort_order'=>'8',
            ],
            [
                'template_id'=>3,
                'label'=>'Review 2 company',
                'key'=>'review_2_company',
                'type'=>'text',
                'group'=>'home_page',
                'sort_order'=>'9',
            ],
            [
                'template_id'=>3,
                'label'=>'Review 2 stars',
                'key'=>'review_2_stars',
                'type'=>'select',
                'group'=>'home_page',
                'sort_order'=>'10',
                'options'=>'1=1,2=2,3=3,4=4,5=5'
            ],
            [
                'template_id'=>3,
                'label'=>'Review 2 photo',
                'key'=>'review_2_photo',
                'type'=>'image',
                'group'=>'home_page',
                'sort_order'=>'11',
                'placeholder'=>'50px x 50px'
            ],

            [
                'template_id'=>3,
                'label'=>'Review 3 text',
                'key'=>'review_3_text',
                'type'=>'textarea',
                'group'=>'home_page',
                'sort_order'=>'12',
            ],
            [
                'template_id'=>3,
                'label'=>'Review 3 name',
                'key'=>'review_3_name',
                'type'=>'text',
                'group'=>'home_page',
                'sort_order'=>'13',
            ],
            [
                'template_id'=>3,
                'label'=>'Review 3 company',
                'key'=>'review_3_company',
                'type'=>'text',
                'group'=>'home_page',
                'sort_order'=>'14',
            ],
            [
                'template_id'=>3,
                'label'=>'Review 3 stars',
                'key'=>'review_3_stars',
                'type'=>'select',
                'group'=>'home_page',
                'sort_order'=>'15',
                'options'=>'1=1,2=2,3=3,4=4,5=5'
            ],
            [
                'template_id'=>3,
                'label'=>'Review 3 photo',
                'key'=>'review_3_photo',
                'type'=>'image',
                'group'=>'home_page',
                'sort_order'=>'16',
                'placeholder'=>'50px x 50px'
            ],




            [
                'template_id'=>3,
                'label'=>'Navigation Bar Background Color',
                'key'=> 'navbar_bgcolor',
                'type'=>'color',
                'group'=>'navigation_bar',
                'sort_order'=>'1',
            ],
            [
                'template_id'=>3,
                'label'=>'Navigation Bar Font Color',
                'key'=> 'navbar_fontcolor',
                'type'=>'color',
                'group'=>'navigation_bar',
                'sort_order'=>'2',
            ],
            [
                'template_id'=>3,
                'label'=>'Primary Color',
                'key'=>'color_primary_color',
                'type'=>'color',
                'group'=>'colors',
                'sort_order'=>'1',
            ],
            [
                'template_id'=>3,
                'label'=>'Primary Button Color',
                'key'=>'color_primary_btn_color',
                'type'=>'color',
                'group'=>'colors',
                'sort_order'=>'2',
            ],
            [
                'template_id'=>3,
                'label'=>'Page title background color',
                'key'=>'color_page_title',
                'type'=>'color',
                'group'=>'colors',
                'sort_order'=>'3',
            ],
            [
                'template_id'=>3,
                'label'=>'Page title text color',
                'key'=>'color_page_title_text',
                'type'=>'color',
                'group'=>'colors',
                'sort_order'=>'5',
            ],
            [
                'template_id'=>3,
                'label'=>'Footer background color',
                'key'=>'footer_bgcolor',
                'type'=>'color',
                'group'=>'footer',
                'sort_order'=>'1',
            ],
            [
                'template_id'=>3,
                'label'=>'Footer text color',
                'key'=>'footer_textcolor',
                'type'=>'color',
                'group'=>'footer',
                'sort_order'=>'2',
            ], 
            [
                'template_id'=>3,
                'label'=>'Newsletter Form Code',
                'key'=>'footer_newsletter_code',
                'type'=>'textarea',
                'group'=>'footer',
                'sort_order'=>'3',
            ],
            [
                'template_id'=>3,
                'label'=>'Site Credits',
                'key'=>'footer_credits',
                'type'=>'text',
                'group'=>'footer',
                'sort_order'=>'4',
            ],
            [
                'template_id'=>3,
                'label'=>'Facebook Url',
                'key'=> 'footer_facebook',
                'type'=>'text',
                'group'=>'social',
                'placeholder'=>'Full facebook url',
                'sort_order'=>'5',
                'value'=>'#',
            ],
            [
                'template_id'=>3,
                'label'=>'Twitter Url',
                'key'=> 'footer_twitter',
                'type'=>'text',
                'group'=>'social',
                'placeholder'=>'Full twitter url',
                'sort_order'=>'6',
                'value'=>'#',
            ],
            [
                'template_id'=>3,
                'label'=>'Google+ Url',
                'key'=> 'footer_google',
                'type'=>'text',
                'group'=>'social',
                'placeholder'=>'Full Google+ url',
                'sort_order'=>'7',
                'value'=>'#',
            ],
            [
                'template_id'=>3,
                'label'=>'Linkedin Url',
                'key'=> 'footer_linkedin',
                'type'=>'text',
                'group'=>'social',
                'placeholder'=>'Full linkedin url',
                'sort_order'=>'8',
                'value'=>'#',
            ],
            [
                'template_id'=>3,
                'label'=>'Instagram Url',
                'key'=> 'footer_instagram',
                'type'=>'text',
                'group'=>'social',
                'placeholder'=>'Full instagram url',
                'sort_order'=>'9',
                'value'=>'#',
            ],

        ]);


    }
}
