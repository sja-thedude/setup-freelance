const formatCurrency = (amount?: string | number, code?: string) => {

    const commaFormatted = String(amount).replace(
            /(\d)(?=(\d{3})+(?!\d))/g,
            '$1,'
    );
    const periodFormatted = String(amount)
            .replace('.', ',')
            .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');

    const switchOptions: any = {
        // argentine peso (ex: $ 1.234,56)
        ARS: [`ARS ${periodFormatted}`, `${periodFormatted}`, '$'],

        // australian dollar (ex: $ 1,234.56)
        AUD: [`AUD ${commaFormatted}`, `${commaFormatted}`, '$'],

        // bulgarian lev (ex: лв1,234.56)
        BGN: [`BGN ${commaFormatted}`, `${commaFormatted}`, 'лв'],

        // brazilian real (ex: R$ 1.234,56)
        BRL: [`BRL ${periodFormatted}`, `${periodFormatted}`, 'R$'],

        // canadian dollar (ex: $ 1,234.56)
        CAD: [`CAD ${commaFormatted}`, `${commaFormatted}`, '$'],

        // swiss franc (ex: fr. 1.234,56)
        CHF: [`CHF ${periodFormatted}`, `${periodFormatted}`, 'fr.'],

        // chilean peso (ex: $ 1,234.56)
        CLP: [`CLP ${commaFormatted}`, `${commaFormatted}`, '$'],

        // yuan renminbi (ex: ¥ 1,234.56)
        CNY: [`CNY ${commaFormatted}`, `${commaFormatted}`, '¥'],

        // colombian peso (ex: $ 1,234.56)
        COP: [`COP ${commaFormatted}`, `${commaFormatted}`, '$'],

        // czech koruna (ex: 1.234,56 Kč)
        CZK: [`${periodFormatted} CZK`, `${periodFormatted}`, 'Kč'],

        // danish krone (ex: kr. 1.234,56)
        DKK: [`DKK ${periodFormatted}`, `${periodFormatted}`, 'kr.'],

        // european union (ex: €1.234,56)
        EUR: [`EUR ${periodFormatted}`, `${periodFormatted}`, '€'],

        // uk/great britain pound sterling (ex: £1,234.56)
        GBP: [`GBP ${commaFormatted}`, `${commaFormatted}`, '£'],

        // hong kong dollar (ex: HK$ 1,234.56)
        HKD: [`HKD ${commaFormatted}`, `${commaFormatted}`, 'HK$'],

        // croatian kuna (ex: 1,234.56 kn)
        HRK: [`${commaFormatted} HRK`, `${commaFormatted}`, 'kn'],

        // hungarian forint (ex: 1.234,56 Ft)
        HUF: [`${periodFormatted} HUF`, `${periodFormatted}`, 'Ft'],

        // indonesian rupiah (ex: Rp 1,234.56)
        IDR: [`IDR ${commaFormatted}`, `${commaFormatted}`, 'Rp'],

        // new israeli shekel (ex: ₪ 1.234,56)
        ILS: [`ILS ${periodFormatted}`, `${periodFormatted}`, '₪'],

        // indian rupee (ex: ₹ 1,234.56)
        INR: [`INR ${commaFormatted}`, `${commaFormatted}`, '₹'],

        // icelandic krona (ex: kr. 1.234,56)
        ISK: [`ISK ${periodFormatted}`, `${periodFormatted}`, 'kr.'],

        // yen (ex: ¥ 1,234.56)
        JPY: [`JPY ${commaFormatted}`, `${commaFormatted}`, '¥'],

        // won (ex: ₩ 1,234.56)
        KRW: [`KRW ${commaFormatted}`, `${commaFormatted}`, '₩'],

        // moroccan dirham (ex: 1,234.56 .د.م.)
        MAD: [`${commaFormatted} MAD`, `${commaFormatted}`, '.د.م.'],

        // mexican peso (ex: $ 1,234.56)
        MXN: [`MXN ${commaFormatted}`, `${commaFormatted}`, '$'],

        // malaysian ringgit (ex: RM 1,234.56)
        MYR: [`MYR ${commaFormatted}`, `${commaFormatted}`, 'RM'],

        // norwegian krone (ex: kr 1,234.56)
        NOK: [`NOK ${commaFormatted}`, `${commaFormatted}`, 'kr'],

        // new zealand dollar (ex: $ 1,234.56)
        NZD: [`NZD ${commaFormatted}`, `${commaFormatted}`, '$'],

        // philippine peso (ex: ₱ 1,234.56)
        PHP: [`PHP ${commaFormatted}`, `${commaFormatted}`, '₱'],

        // polish zloty (ex: 1.234,56 zł)
        PLN: [`${periodFormatted} PLN`, `${periodFormatted}`, 'zł'],

        // romanian new leu (ex: 1,234.56L)
        RON: [`${commaFormatted} RON`, `${commaFormatted}`, 'L'],

        // russian ruble (ex: 1.234,56 p.)
        RUB: [`${periodFormatted} p.`, `${periodFormatted}`, 'p.'],

        // saudi riyal (ex: 1,234.56 ﷼)
        SAR: [`${commaFormatted} RUB`, `${commaFormatted}`, '﷼'],

        // swedish krona (ex: 1.234,56 kr)
        SEK: [`${periodFormatted} SEK`, `${periodFormatted}`, 'kr'],

        // singapore dollar (ex: $1,234.56)
        SGD: [`SGD ${commaFormatted}`, `${commaFormatted}`, '$'],

        // thai baht (ex: 1,234.56 ฿)
        THB: [`${commaFormatted} THB`, `${commaFormatted}`, '฿'],

        // turkish lira (ex: 1,234.56 ₺)
        TRY: [`${commaFormatted} TRY`, `${commaFormatted}`, '₺'],

        // new taiwan dollar (ex: 元 1,234.56)
        TWD: [`TWD ${commaFormatted}`, `${commaFormatted}`, '元'],

        // us dollar (ex: $1,234.56)
        USD: [`USD ${commaFormatted}`, `${commaFormatted}`, '$'],

        // vietnamese dong (ex: 1.234,56 ₫)
        VND: [`${periodFormatted} VND`, `${periodFormatted}`, '₫'],

        // south african rand (ex: R 1,234.56)
        ZAR: [`ZAR ${commaFormatted}`, `${commaFormatted}`, 'R'],

        // default
        DEFAULT: [periodFormatted, periodFormatted, '€'],
    };

    return switchOptions[code || 'DEFAULT'];
};

export default formatCurrency;