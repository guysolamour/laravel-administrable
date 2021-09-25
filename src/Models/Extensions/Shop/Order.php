<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use App\Models\User;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\ModelTrait;
use Guysolamour\Administrable\Contracts\Shop\HasPdfInvoiceContract;

class Order extends BaseModel implements HasPdfInvoiceContract
{
    use ModelTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_orders';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount'        => 'integer',
        'deliver_price' => 'integer',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['total'];


    public $fillable = ['title','amount','deliver_name', 'deliver_price', 'invoice', 'command_id', 'user_id'];


    public function getTotalAttribute() :int
    {
        return $this->amount + $this->deliver_price;
    }

    public function command()
    {
        return $this->belongsTo(config('administrable.extensions.shop.models.command'));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProductsCountAttribute() :int
    {
        return $this->command->products->count();
    }

    public function scopeGetCurrentMonth($query): int
    {
        return $query->confirmed()->get()->filter(function ($order) {
            return $order->created_at->isCurrentMonth() && $order->created_at->isCurrentYear();
        })->sum('amount');
    }

    public function scopeGetMonth($query, int $month = null, int $year = null): int
    {
        $month = $month ?? now()->month;
        $year  = $year ?? now()->year;

        return $query->confirmed()->get()->filter(function ($order) use ($month, $year) {
            return $order->created_at->month == $month && $order->created_at->year == $year;
        })->sum('amount');
    }

    public function scopeGetYear($query,  int $year = null): int
    {
        $year = $year ?? now()->year;

        return $query->confirmed()->get()->filter(function ($order) use ($year) {
            return  $order->created_at->year == $year;
        })->sum('amount');
    }

    public function generatePdf(bool $send_email = true)
    {
        if (!shop_settings('invoice_management')){
            return;
        }

        $invoice =  (new \Guysolamour\Administrable\Services\Shop\Invoice($this))->pdf()->save();

        if ($send_email){
            $invoice->send();
        }
    }

    public function getInvoicePathAttribute() :string
    {
        return storage_path("/app/{$this->invoice}");
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();
        /**
         * @param $this $model
         */
        static::created(function ($model) {
           $model->generatePdf();
        });
    }
}
