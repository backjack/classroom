<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:01 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class DownloadSession extends Model {

    protected $table = 'download_session';
    protected $primaryKey = 'download_session_id';
    public $timestamps = false;

}