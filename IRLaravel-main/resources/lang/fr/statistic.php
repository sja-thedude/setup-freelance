<?php

$originalTranslations = [
    'title' => 'Statistiques',
    'per_product' => 'Par produit',
    'search_product' => 'Rechercher par produit',
    'tbl_title' => 'Titre',
    'tbl_amount_sell' => 'Quantité vendue',
    'tbl_total_revenue' => 'Revenu total',
    'discount_this_time' => 'Réductions pendant cette période',
    'total_incl_discount' => 'Total incl. réduction',
    'total' => 'Total',
    'discount' => 'Réductions',
    'discount_at_vat' => 'Réduction à la TVA% :',
    'number_of_orders' => 'Nombre de commandes',
    'physical_product' => 'Produits physiques',
    'per_payment_method' => 'Par méthode de paiement',
    'diverse' => 'Divers',
    'leverkost' => 'Frais de livraison',
    'cash' => 'Espèces',
    'paid_online' => 'Payé en ligne',
    'for_invoice' => 'Pour facture',
    'turnover_at_vat' => 'Chiffre d\'affaires à la TVA% :',
    'print_pdf' => 'Imprimer PDF',
    'print_op_bonprinter' => 'Imprimer sur imprimante de tickets',
    'tbl_number_and_product' => 'Quantité x produit',
    'bon_printer_orders' => '#Commandes',
    'tot' => 'Total',
];

return \App\Helpers\Helper::getFileJsonLang($originalTranslations, str_replace('.php', '.json', basename(__FILE__)), basename(__DIR__), !empty($loadFromJs) ? $loadFromJs : false);