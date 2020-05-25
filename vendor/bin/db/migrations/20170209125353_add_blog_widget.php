<?php

use Phinx\Migration\AbstractMigration;

class AddBlogWidget extends AbstractMigration
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
              'widget_name'=>'Blog Posts',
              'widget_code'=>'blog',
              'form'=>'<div class="form-group"  >

    <label for="limit" class="control-label">Number of Posts to Display</label>

    <input type="text" name="limit" class="form-control number" value="4" />

</div>',
              'widget_description'=>'Add recent blog posts to site',
              'repeat'=>0
          ]
        ];
        $this->insert('widget',$data);
    }
}
