<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 10/5/2017
 * Time: 11:32 AM
 */

namespace Application\Model;


use Intermatics\BaseTable;

class RelatedCourseTable extends BaseTable {

    protected $tableName = 'related_course';
    protected $primary = 'related_course_id';

}