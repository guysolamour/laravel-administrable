<?php

namespace Guysolamour\Administrable\Contracts\Shop;

interface HasPdfInvoiceContract
{
    public function generatePdf(bool $send_email = true);

    public function command();
}
