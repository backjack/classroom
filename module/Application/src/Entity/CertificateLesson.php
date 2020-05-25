<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:58 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class CertificateLesson extends  Model{

    protected $table = 'certificate_lesson';
    protected $primaryKey = 'certificate_lesson_id';
    public $timestamps = false;

}