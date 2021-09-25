<?php

namespace Guysolamour\Administrable\Services\Shop;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use LaravelDaily\Invoices\Classes\Party;
use Illuminate\Support\Facades\Notification;
use Guysolamour\Administrable\Contracts\Shop\HasPdfInvoice;
use Guysolamour\Administrable\Contracts\Shop\HasPdfInvoiceContract;
use Guysolamour\Administrable\Notifications\Front\Extensions\Shop\InvoicePaid;


class Invoice
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|\Guysolamour\Administrable\Contracts\Shop\HasPdfInvoice
     */
    private $order;

    private $cart;

    private $seller;

    private $buyer;

    private $pdf;

    public function __construct(HasPdfInvoiceContract $order, array $seller = [], array $buyer = [])
    {
        $this->order  = $order;
        $this->cart   = $order->command->formated_products;
        $this->seller = $this->getSeller($seller);
        $this->buyer  = $this->getBuyer($buyer);
    }

    public function pdf(): self
    {
        $this->pdf = \LaravelDaily\Invoices\Facades\Invoice::make('facture')
            ->serialNumberFormat($this->order->command->reference)
            ->seller($this->seller)
            ->buyer($this->buyer)
            ->date($this->order->command->created_at)
            ->dateFormat('d/m/Y H:i:s')
            ->currencyDecimals(0)
            ->currencySymbol('F')
            ->currencyCode('')
            ->currencyFormat('{VALUE} {SYMBOL}')
            ->currencyThousandsSeparator(' ')
            ->currencyDecimalPoint('.')
            ->addItems($this->getItems())
            ->shipping($this->order->deliver_price)
            ->payUntilDays(0)

            ->logo(storage_path('app/public/logo/logo.png'));

        if ($note = shop_settings('invoice_note')) {
            $this->pdf->notes($note);
        }

        if ($discount = $this->cart['all_discount']) {
            $this->pdf->totalDiscount($discount);
        }

        if (shop_settings('tva')) {
            $this->pdf->taxRate(shop_settings('tva_percentage'));
        }

        $this->pdf->render();

        return $this;
    }

    public function send(): void
    {
        $email = Arr::get($this->buyer->custom_fields, 'email');

        if (!$email) {
            return;
        }
        Notification::route('mail', [$email => $this->buyer->name])
            ->notify(new InvoicePaid($this->order));
    }

    private function getSeller(array $seller): Party
    {
        return new Party(array_merge([
            'name'          => config('app.name'),
            'phone'         => configuration('cell'),
            'custom_fields' => [
                'email'           => configuration('email'),
                'site internet'   => config('app.url'),
            ],
        ], $seller));
    }

    private function getBuyer(array $buyer): Party
    {
        return new Party(array_merge([
            'name'          => $this->order->command->client->name,
            'address'       => $this->order->command->client->address,
            'phone'         => $this->order->command->client->phone_number,
            'custom_fields' => [
                'email' => $this->order->command->client->email,
            ],
        ], $buyer));
    }

    private function getItems(): array
    {
        /**
         * @var \Illuminate\Support\Collection
         */
        $items = $this->cart['items'];

        return  $items->map(function ($cartItem) {
            return (new \LaravelDaily\Invoices\Classes\InvoiceItem())
                ->title($cartItem->name)
                ->pricePerUnit($cartItem->price)
                ->quantity($cartItem->qty);
        })->toArray();
    }

    private function getPath(): string
    {
        return "public/invoices/{$this->order->getKey()}/{$this->order->command->reference}.pdf";
    }

    public function save(): self
    {
        $path = $this->getPath();

        Storage::put($path, $this->pdf->pdf->output());

        $this->order->update(['invoice' => $path]);

        return $this;
    }
}
