<?php

use Phinx\Migration\AbstractMigration;

class ChangeToUtf8 extends AbstractMigration
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
        $this->query('ALTER TABLE accounts CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE articles CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE attendance  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE homework  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE newsflash  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE payment_method  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE payment_method_field  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE permission  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE permission_group  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE registration_field  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE role  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE role_permission  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE session  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE session_lesson  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE setting  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE student  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE student_field  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE student_session  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE transaction  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE widget  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
        $this->query('ALTER TABLE widget_value  CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci');
    }
}
