<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 12:57 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class Certificate extends Model {

    protected $table = 'certificate';
    protected $primaryKey = 'certificate_id';
    public $timestamps = false;

    public function studentCertificates(){
        return $this->hasMany(StudentCertificate::class,'certificate_id');
    }

    public function session(){
        return $this->belongsTo(Session::class,'session_id');
    }
}