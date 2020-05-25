<?php
/**
 * Created by PhpStorm.
 * User: USER PC
 * Date: 4/10/2018
 * Time: 1:25 PM
 */

namespace Application\Entity;


use Illuminate\Database\Eloquent\Model;

class WidgetValue extends Model {

    protected $table = 'widget_value';
    protected $primaryKey = 'widget_value_id';
    public $timestamps = false;

    public function widget(){
        return $this->belongsTo(Widget::class,'widget_id');
    }

}