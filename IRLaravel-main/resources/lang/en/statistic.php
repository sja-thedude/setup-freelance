<?php

$originalTranslations = [
    'title' => 'Statistics',
    'per_product' => 'Per product',
    'search_product' => 'Search by product',
    'tbl_title' => 'Title',
    'tbl_amount_sell' => 'Amount sold',
    'tbl_total_revenue' => 'Total revenue',
    'discount_this_time' => 'Discounts in this period',
    'total_incl_discount' => 'Total incl. discount',
    'total' => 'Total',
    'discount' => 'Discounts',
    'discount_at_vat' => 'Discount at VAT%:',
    'number_of_orders' => 'Number of orders',
    'physical_product' => 'Physical products',
    'per_payment_method' => 'Per payment method',
    'diverse' => 'Miscellaneous',
    'leverkost' => 'Delivery cost',
    'cash' => 'Cash',
    'paid_online' => 'Paid online',
    'for_invoice' => 'For invoice',
    'turnover_at_vat' => 'Turnover at VAT%:',
    'print_pdf' => 'Print PDF',
    'print_op_bonprinter' => 'Print on receipt printer',
    'tbl_number_and_product' => 'Number x product',
    'bon_printer_orders' => '#Orders',
    'tot' => 'Total',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);