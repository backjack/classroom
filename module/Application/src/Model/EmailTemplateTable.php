<?php
/**
 * Created by PhpStorm.
 * User: ayokunle
 * Date: 6/22/18
 * Time: 1:21 PM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class EmailTemplateTable extends BaseTable
{
    protected $tableName = 'email_template';
    protected $primary = 'email_template_id';

}