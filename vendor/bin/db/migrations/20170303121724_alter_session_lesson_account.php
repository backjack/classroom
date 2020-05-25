<?php

use Phinx\Migration\AbstractMigration;

class AlterSessionLessonAccount extends AbstractMigration
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
        $this->execute('ALTER TABLE `session_lesson_account` CHANGE COLUMN `session_lesson_id` `lesson_id` INT(10) UNSIGNED NOT NULL
, DROP INDEX `session_lesson_id`,
 DROP FOREIGN KEY `session_lesson_account_ibfk_1`,
 ADD CONSTRAINT `FK_session_lesson_account_2` FOREIGN KEY `FK_session_lesson_account_2` (`lesson_id`)
    REFERENCES `lesson` (`lesson_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;
');
    }
}
