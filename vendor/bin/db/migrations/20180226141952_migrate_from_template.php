<?php

use Phinx\Migration\AbstractMigration;

class MigrateFromTemplate extends AbstractMigration
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
        $map = [
          'navbar_bgcolor'=>'color_navbar',
            'navbar_fontcolor'=>'color_navtext',
            'color_primary_color'=>'color_primary_color',
            'footer_bgcolor'=>'color_footer',
            'footer_credits_bgcolor'=>'color_footer',
            'footer_textcolor'=>'color_footertext',
            'footer_credits_bgcolor'=>'color_footer',
            'footer_credits_textcolor'=>'color_footertext',
            'footer_newsletter_code'=>'',
            'footer_tel'=>'footer_tel',
            'footer_email'=>'footer_email',
            'footer_address'=>'footer_address',
            'footer_website'=>'footer_website',
            'footer_about'=>'footer_about',
            'color_page_title'=>'color_page_title',
            'color_page_title_text'=>'color_page_title_text',
            'topbar_instagram'=>'social_instagram',
            'topbar_google'=>'social_google',
            'topbar_twitter'=>'social_twitter',
            'topbar_facebook'=>'social_facebook',
            'topbar_bgcolor'=>'color_primary_color',

        ];

        foreach($map as $key=>$value){
            $this->setOptionValue($key,$this->getOptionValue($value,1));
        }
        $this->execute('update template set active=0');
        $this->execute('update template set active=1 where template_id=2');

    }


    private function getOptionValue($option,$templateId){
        $row = $this->fetchRow("select * from template_option where template_option.key='$option' and template_option.template_id='$templateId'");

        return $row['value'];
    }

    private function setOptionValue($option,$value){
        $result = $this->execute("update template_option set value='$value' where template_option.key='$option' and template_id=2");

    }


}
