<?php

namespace Guysolamour\Administrable\Models\Extensions\Shop;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Guysolamour\Administrable\Facades\Cart;
use Guysolamour\Administrable\Traits\HasNote;
use Guysolamour\Administrable\Models\BaseModel;
use Guysolamour\Administrable\Traits\DraftableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Guysolamour\Administrable\Events\Shop\ConfirmCommandPayment;

class Command extends BaseModel
{
    use HasFactory;
    use HasNote;
    use DraftableTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shop_commands';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference', 'name', 'phone_number', 'email', 'state', 'ip', 'paid', 'online',
        'amount', 'address', 'products', 'globals','user_id', 'deliver', 'city', 'country', 'created_at'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['formated_products', 'state_label'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id'  => 'integer',
        'globals'  => 'array',
        'products' => 'collection',
        'paid'     => 'boolean',
        'online'   => 'boolean',
    ];

    public const STATE = [
        'on_hold'    => ['name' => 'on_hold',      'label' => 'En attente'],
        'processing' => ['name' => 'processing',   'label' => 'En cours'],
        'pending'    => ['name' => 'pending',      'label' => 'Attente de paiement'],
        'completed'  => ['name' => 'completed',    'label' => 'Terminée'],
        'cancelled'  => ['name' => 'cancelled',    'label' => 'Annulée'],
        'refunded'   => ['name' => 'refunded',     'label' => 'Remboursée'],
        'failed'     => ['name' => 'failed',       'label' => 'Echouée'],
    ];


    public static function getCreatedCommandReference() :string
    {
        $shop_settings = shop_settings();

        $num = self::max('id') + 1;

        $id = str_pad(($num), 8, 0, STR_PAD_LEFT); // 0000001

        return $shop_settings->command_prefix . $id . $shop_settings->command_suffix;
    }

    public function isCompleted() :bool
    {
        return $this->state === self::STATE['completed']['name'];
    }

    public function isNotCompleted() :bool
    {
        return !$this->isCompleted();
    }

    public function getAmountAttribute(): int
    {
       return $this->formated_products['total'] + $this->deliver['area']->pivot->price;
    }

    public function getTotalAttribute() :int
    {
        return Arr::get($this->formated_products, 'total', 0);
    }

    public function getDeliverNameAttribute() :string
    {
        return $this->deliver->get('deliver')->name;
    }

    public function getDeliverPriceAttribute() :int
    {
        return $this->deliver->get('area')->pivot->price;
    }

    public function getStateLabelAttribute() :?string
    {
        foreach(self::STATE as $state){
            if ($state['name'] === $this->state){
                return $state['label'];
            }
        }

        return $this->state;
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault(function ($user, $command) {
            $user->name         = $command->name;
            $user->phone_number = $command->phone_number;
            $user->address      = $command->address;
            $user->city         = $command->city;
            $user->country      = $command->country;
            $user->email        = $command->email;
        });
    }

    public function user()
    {
        return $this->client();
    }

    public function isPaid() :bool
    {
        return $this->paid;
    }

    public function isNotPaid() :bool
    {
        return !$this->isPaid();
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }

    public function confirmPayment(bool $paid = true)
    {
        $this->update([
            'paid'  => $paid,
            'state' => self::STATE['completed']['name']
        ]);

        event(new ConfirmCommandPayment($this));
    }

    public function setProductsAttribute($value) :void
    {
        $value = is_string($value) ? $value : json_encode($value);

        $this->attributes['products'] = $value;
    }

    public function setDeliverAttribute($value) :void
    {
        $value = is_string($value) ? $value : json_encode($value);

        $this->attributes['deliver'] = $value;
    }

    /**
     * @param string $value
     * @return \Illuminate\Support\Collection
     */
    public function getDeliverAttribute($value)
    {
        $value =  json_decode($value, true);

        if (!$value){
            return collect();
        }

        $deliver  = config('administrable.extensions.shop.models.deliver')::where('id', $value['deliver_id'])->with([
            'areas' => fn ($query) => $query->where('id', $value['area_id']),
        ])->first();

        return collect([
            'deliver' => $deliver,
            'area'    => $deliver->areas->first()
        ]);
    }

    public function updateProducts($products)
    {
        $this->update(compact('products'));
    }

    /**
     * @param \Guysolamour\Administrable\Contracts\Shop\ShopContract|\Illuminate\Database\Eloquent\Model $model
     * @return Collection
     */
    public function addProductsItem($model)
    {
        if (!$this->exists){
            return;
        }

        $products =  Cart::merge($this->products, [
            'id'    => $model->getKey(),
            'model' => $model,
            'price' => $model->getPrice(),
        ]);

        $this->updateProducts($products);

        return $products;
    }

    /**
     * @param integer $rowId
     * @param string $key
     * @param mixed $value
     * @return \Illuminate\Support\Collection
     */
    public function updateProductsItem(int $rowId, string $key, $value)
    {
        $products = $this->products->transform(function ($item) use ($rowId, $key, $value) {
            if ($item['rowId'] == $rowId) {
                $item[$key] =  $value;
            }
            return $item;
        });

        $this->updateProducts($products->values()->toJson());

        return $products;
    }

    public function applyDiscount(int $discount)
    {
        $this->update([
            'globals' => compact('discount'),
        ]);

        return  Cart::hydrate($this->products, $this->globals);
    }

    /**
     * @param integer $rowId
     * @return Collection
     */
    public function removeProductsItem(int $rowId)
    {
        $products =  $this->products->filter(fn ($item) => $item['rowId'] != $rowId);

        $this->updateProducts($products->values()->toJson());

        return $products;
    }

    public function getFormatedProductsAttribute()
    {
        return  Cart::hydrate($this->products, $this->globals);
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
         * @param self
         */
        static::created(function ($command) {
            $command->update([
                'reference' => $command::getCreatedCommandReference(),
                'ip'        => request()->ip(),
            ]);
        });
    }
}


