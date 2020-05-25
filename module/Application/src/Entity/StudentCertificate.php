<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 8/3/2018
 * Time: 5:30 PM
 */

namespace Application\Entity;

use Illuminate\Database\Eloquent\Model;

class StudentCertificate extends Model
{
    protected $table = 'student_certificate';
    protected $primaryKey = 'student_certificate_id';
    public $timestamps = false;

    protected $fillable= ['student_id','certificate_id','created_on','tracking_number'];

    public function student(){
        return $this->belongsTo(Student::class,'student_id');
    }

    public function certificate(){
        return $this->belongsTo(Certificate::class,'certificate_id');
    }
}