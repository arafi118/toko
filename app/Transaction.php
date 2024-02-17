<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Contact;
use App\VariationGroupPrice;
use App\SellingPriceGroup;

class Transaction extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $dates   = ['transaction_date'];
    // protected $jatuh   = ['jatuh_tempo'];
    
    public function purchase_lines()
    {
        return $this->hasMany(\App\PurchaseLine::class);
    }

    public function sell_lines()
    {
        return $this->hasMany(\App\TransactionSellLine::class);
    }

    public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }

    public function payment_lines()
    {
        return $this->hasMany(\App\TransactionPayment::class);
    }

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }

    public function tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax_id');
    }

    public function stock_adjustment_lines()
    {
        return $this->hasMany(\App\StockAdjustmentLine::class);
    }

    public function sales_person()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function return_parent()
    {
        return $this->hasOne(\App\Transaction::class, 'return_parent_id');
    }

    public function check_group($contact_id)
    {
        $price_group_id = Contact::where('id',$contact_id)->first()->selling_price_group_id;
        
        $spg            = SellingPriceGroup::where('id',$price_group_id)->first()->name;

        return $spg;
    }

    public function check_harga($contact_id,$variation_id)
    {
        $price_group_id = Contact::where('id',$contact_id)->first()->selling_price_group_id;

        $variation_price = VariationGroupPrice::where('variation_id',$variation_id)
                                              ->where('price_group_id',$price_group_id)
                                              ->first()->price_inc_tax;

        return $variation_price;
    }
}
