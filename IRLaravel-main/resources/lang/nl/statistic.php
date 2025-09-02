<?php

$originalTranslations = [
    'title' => 'Statistieken',
    'per_product' => 'Per product',
    'search_product' => 'Zoeken op product',
    'tbl_title' => 'Titel',
    'tbl_amount_sell' => 'Aantal verkocht',
    'tbl_total_revenue' => 'Totale omzet',
    'discount_this_time' => 'Kortingen in deze periode',
    'total_incl_discount' => 'Totaal incl. korting',
    'total' => 'Totaal',
    'discount' => 'Kortingen',
    'discount_at_vat' => 'Korting aan BTW%',
    'number_of_orders' => 'Aantal bestellingen',
    'physical_product' => 'Fysieke producten',
    'per_payment_method' => 'Per betaalmethode',
    'diverse' => 'Diverse',
    'leverkost' => 'Leverkost',
    'cash' => 'Cash',
    'paid_online' => 'Online betaald',
    'for_invoice' => 'Voor factuur',
    'turnover_at_vat' => 'Omzet aan BTW%',
    'print_pdf' => 'Print PDF',
    'print_op_bonprinter' => 'Print op bonprinter',
    'tbl_number_and_product' => 'Aantal x product',
    'bon_printer_orders' => '#Bestellingen',
    'tot' => 'Totaal',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);