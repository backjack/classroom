<?php
/**
 * Created by PhpStorm.
 * User: ayokunle
 * Date: 6/22/18
 * Time: 11:09 AM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_template';
    protected $primaryKey = 'email_template_id';
    public $timestamps = false;
}