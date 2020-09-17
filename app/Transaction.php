<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Transaction extends Model
{
    protected $table = 'transhistory';
    protected $primaryKey = 'id';
    protected $fillable = [
        'status',
        'date_payment',
        'note',
        'invoice'
    ];
    public function GetCount()
    {
        return Transaction::count();
    }

    public function getDetails($id){
        return DB::table('transhistory')->select(DB::raw("transhistory.id, transhistory.id_trans,
                users.name as userapp,
                transhistory.invoice,
                transhistory.status,
                transhistory.note,
                transhistory.id_visit,
                transhistory.convicts_id,
                transhistory.id_product,
                products.name as product_name,
                transhistory.qty as qty_item,
                transhistory.price as price,
                transhistory.created_at as date_transaction"))
        ->where('transhistory.id_trans', $id)
        ->leftJoin('users', 'transhistory.users_id', '=', 'users.id')
        ->leftJoin('convicts', 'transhistory.convicts_id', '=', 'convicts.id')
        ->leftJoin('appointments', 'transhistory.id_visit', '=', 'appointments.id')
        ->leftJoin('products', 'transhistory.id_product', '=', 'products.id')
        ->get();
    }
}
