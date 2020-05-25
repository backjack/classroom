<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 5/25/2018
 * Time: 4:48 PM
 */

namespace Application\Model;


use Application\Entity\TestGrade;
use Intermatics\BaseTable;

class TestGradeTable extends BaseTable {
    protected $tableName = 'test_grade';
    protected $primary = 'test_grade_id';

    public function getGrade($score){
        if(!is_numeric($score)){
            return '';
        }

        $testGrade = TestGrade::where('min','<=',$score)->where('max','>=',$score)->first();
        if($testGrade){
            return $testGrade->grade;
        }
        else{
            return '';
        }
    }
}