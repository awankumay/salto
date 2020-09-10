<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class TransHistory extends Model
{
    protected $table = 'transhistory';
    protected $fillable = [
        'id_trans', 'users_id', 'invoice', 'id_product', 'qty', 'price', 'date_payment', 'status',
        'note', 'id_visit', 'convicts_id', 'type_product'
    ];
    protected $primaryKey = 'id';
}
