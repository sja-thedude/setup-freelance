<?php

use Illuminate\Database\Seeder;
use App\Models\Country;
use Carbon\Carbon;

/**
 * Class CountriesTableSeeder
 */
class CountriesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('countries')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $now = Carbon::now();
        $countries = array(
            0 =>
                array(
                    'active' => true,
                    'name' => 'Afghanistan',
                    'code' => 'af',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            1 =>
                array(
                    'active' => true,
                    'name' => 'Åland Islands',
                    'code' => 'ax',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            2 =>
                array(
                    'active' => true,
                    'name' => 'Albania',
                    'code' => 'al',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            3 =>
                array(
                    'active' => true,
                    'name' => 'Algeria',
                    'code' => 'dz',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            4 =>
                array(
                    'active' => true,
                    'name' => 'American Samoa',
                    'code' => 'as',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            5 =>
                array(
                    'active' => true,
                    'name' => 'Andorra',
                    'code' => 'ad',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            6 =>
                array(
                    'active' => true,
                    'name' => 'Angola',
                    'code' => 'ao',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            7 =>
                array(
                    'active' => true,
                    'name' => 'Anguilla',
                    'code' => 'ai',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            8 =>
                array(
                    'active' => true,
                    'name' => 'Antarctica',
                    'code' => 'aq',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            9 =>
                array(
                    'active' => true,
                    'name' => 'Antigua and Barbuda',
                    'code' => 'ag',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            10 =>
                array(
                    'active' => true,
                    'name' => 'Argentina',
                    'code' => 'ar',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            11 =>
                array(
                    'active' => true,
                    'name' => 'Armenia',
                    'code' => 'am',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            12 =>
                array(
                    'active' => true,
                    'name' => 'Aruba',
                    'code' => 'aw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            13 =>
                array(
                    'active' => true,
                    'name' => 'Australia',
                    'code' => 'au',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            14 =>
                array(
                    'active' => true,
                    'name' => 'Austria',
                    'code' => 'at',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            15 =>
                array(
                    'active' => true,
                    'name' => 'Azerbaijan',
                    'code' => 'az',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            16 =>
                array(
                    'active' => true,
                    'name' => 'Bahamas',
                    'code' => 'bs',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            17 =>
                array(
                    'active' => true,
                    'name' => 'Bahrain',
                    'code' => 'bh',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            18 =>
                array(
                    'active' => true,
                    'name' => 'Bangladesh',
                    'code' => 'bd',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            19 =>
                array(
                    'active' => true,
                    'name' => 'Barbados',
                    'code' => 'bb',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            20 =>
                array(
                    'active' => true,
                    'name' => 'Belarus',
                    'code' => 'by',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            21 =>
                array(
                    'active' => true,
                    'name' => 'Belgium',
                    'code' => 'be',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            22 =>
                array(
                    'active' => true,
                    'name' => 'Belize',
                    'code' => 'bz',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            23 =>
                array(
                    'active' => true,
                    'name' => 'Benin',
                    'code' => 'bj',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            24 =>
                array(
                    'active' => true,
                    'name' => 'Bermuda',
                    'code' => 'bm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            25 =>
                array(
                    'active' => true,
                    'name' => 'Bhutan',
                    'code' => 'bt',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            26 =>
                array(
                    'active' => true,
                    'name' => 'Bolivia, Plurinational State of',
                    'code' => 'bo',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            27 =>
                array(
                    'active' => true,
                    'name' => 'Bonaire, Sint Eustatius and Saba',
                    'code' => 'bq',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            28 =>
                array(
                    'active' => true,
                    'name' => 'Bosnia and Herzegovina',
                    'code' => 'ba',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            29 =>
                array(
                    'active' => true,
                    'name' => 'Botswana',
                    'code' => 'bw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            30 =>
                array(
                    'active' => true,
                    'name' => 'Bouvet Island',
                    'code' => 'bv',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            31 =>
                array(
                    'active' => true,
                    'name' => 'Brazil',
                    'code' => 'br',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            32 =>
                array(
                    'active' => true,
                    'name' => 'British Indian Ocean Territory',
                    'code' => 'io',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            33 =>
                array(
                    'active' => true,
                    'name' => 'Brunei Darussalam',
                    'code' => 'bn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            34 =>
                array(
                    'active' => true,
                    'name' => 'Bulgaria',
                    'code' => 'bg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            35 =>
                array(
                    'active' => true,
                    'name' => 'Burkina Faso',
                    'code' => 'bf',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            36 =>
                array(
                    'active' => true,
                    'name' => 'Burundi',
                    'code' => 'bi',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            37 =>
                array(
                    'active' => true,
                    'name' => 'Cambodia',
                    'code' => 'kh',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            38 =>
                array(
                    'active' => true,
                    'name' => 'Cameroon',
                    'code' => 'cm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            39 =>
                array(
                    'active' => true,
                    'name' => 'Canada',
                    'code' => 'ca',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            40 =>
                array(
                    'active' => true,
                    'name' => 'Cape Verde',
                    'code' => 'cv',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            41 =>
                array(
                    'active' => true,
                    'name' => 'Cayman Islands',
                    'code' => 'ky',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            42 =>
                array(
                    'active' => true,
                    'name' => 'Central African Republic',
                    'code' => 'cf',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            43 =>
                array(
                    'active' => true,
                    'name' => 'Chad',
                    'code' => 'td',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            44 =>
                array(
                    'active' => true,
                    'name' => 'Chile',
                    'code' => 'cl',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            45 =>
                array(
                    'active' => true,
                    'name' => 'China',
                    'code' => 'cn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            46 =>
                array(
                    'active' => true,
                    'name' => 'Christmas Island',
                    'code' => 'cx',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            47 =>
                array(
                    'active' => true,
                    'name' => 'Cocos (Keeling) Islands',
                    'code' => 'cc',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            48 =>
                array(
                    'active' => true,
                    'name' => 'Colombia',
                    'code' => 'co',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            49 =>
                array(
                    'active' => true,
                    'name' => 'Comoros',
                    'code' => 'km',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            50 =>
                array(
                    'active' => true,
                    'name' => 'Congo',
                    'code' => 'cg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            51 =>
                array(
                    'active' => true,
                    'name' => 'Congo, the Democratic Republic of the',
                    'code' => 'cd',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            52 =>
                array(
                    'active' => true,
                    'name' => 'Cook Islands',
                    'code' => 'ck',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            53 =>
                array(
                    'active' => true,
                    'name' => 'Costa Rica',
                    'code' => 'cr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            54 =>
                array(
                    'active' => true,
                    'name' => 'Côte d\'Ivoire',
                    'code' => 'ci',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            55 =>
                array(
                    'active' => true,
                    'name' => 'Croatia',
                    'code' => 'hr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            56 =>
                array(
                    'active' => true,
                    'name' => 'Cuba',
                    'code' => 'cu',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            57 =>
                array(
                    'active' => true,
                    'name' => 'Curaçao',
                    'code' => 'cw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            58 =>
                array(
                    'active' => true,
                    'name' => 'Cyprus',
                    'code' => 'cy',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            59 =>
                array(
                    'active' => true,
                    'name' => 'Czech Republic',
                    'code' => 'cz',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            60 =>
                array(
                    'active' => true,
                    'name' => 'Denmark',
                    'code' => 'dk',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            61 =>
                array(
                    'active' => true,
                    'name' => 'Djibouti',
                    'code' => 'dj',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            62 =>
                array(
                    'active' => true,
                    'name' => 'Dominica',
                    'code' => 'dm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            63 =>
                array(
                    'active' => true,
                    'name' => 'Dominican Republic',
                    'code' => 'do',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            64 =>
                array(
                    'active' => true,
                    'name' => 'Ecuador',
                    'code' => 'ec',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            65 =>
                array(
                    'active' => true,
                    'name' => 'Egypt',
                    'code' => 'eg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            66 =>
                array(
                    'active' => true,
                    'name' => 'El Salvador',
                    'code' => 'sv',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            67 =>
                array(
                    'active' => true,
                    'name' => 'Equatorial Guinea',
                    'code' => 'gq',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            68 =>
                array(
                    'active' => true,
                    'name' => 'Eritrea',
                    'code' => 'er',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            69 =>
                array(
                    'active' => true,
                    'name' => 'Estonia',
                    'code' => 'ee',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            70 =>
                array(
                    'active' => true,
                    'name' => 'Ethiopia',
                    'code' => 'et',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            71 =>
                array(
                    'active' => true,
                    'name' => 'Falkland Islands (Malvinas)',
                    'code' => 'fk',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            72 =>
                array(
                    'active' => true,
                    'name' => 'Faroe Islands',
                    'code' => 'fo',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            73 =>
                array(
                    'active' => true,
                    'name' => 'Fiji',
                    'code' => 'fj',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            74 =>
                array(
                    'active' => true,
                    'name' => 'Finland',
                    'code' => 'fi',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            75 =>
                array(
                    'active' => true,
                    'name' => 'France',
                    'code' => 'fr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            76 =>
                array(
                    'active' => true,
                    'name' => 'French Guiana',
                    'code' => 'gf',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            77 =>
                array(
                    'active' => true,
                    'name' => 'French Polynesia',
                    'code' => 'pf',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            78 =>
                array(
                    'active' => true,
                    'name' => 'French Southern Territories',
                    'code' => 'tf',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            79 =>
                array(
                    'active' => true,
                    'name' => 'Gabon',
                    'code' => 'ga',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            80 =>
                array(
                    'active' => true,
                    'name' => 'Gambia',
                    'code' => 'gm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            81 =>
                array(
                    'active' => true,
                    'name' => 'Georgia',
                    'code' => 'ge',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            82 =>
                array(
                    'active' => true,
                    'name' => 'Germany',
                    'code' => 'de',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            83 =>
                array(
                    'active' => true,
                    'name' => 'Ghana',
                    'code' => 'gh',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            84 =>
                array(
                    'active' => true,
                    'name' => 'Gibraltar',
                    'code' => 'gi',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            85 =>
                array(
                    'active' => true,
                    'name' => 'Greece',
                    'code' => 'gr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            86 =>
                array(
                    'active' => true,
                    'name' => 'Greenland',
                    'code' => 'gl',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            87 =>
                array(
                    'active' => true,
                    'name' => 'Grenada',
                    'code' => 'gd',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            88 =>
                array(
                    'active' => true,
                    'name' => 'Guadeloupe',
                    'code' => 'gp',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            89 =>
                array(
                    'active' => true,
                    'name' => 'Guam',
                    'code' => 'gu',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            90 =>
                array(
                    'active' => true,
                    'name' => 'Guatemala',
                    'code' => 'gt',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            91 =>
                array(
                    'active' => true,
                    'name' => 'Guernsey',
                    'code' => 'gg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            92 =>
                array(
                    'active' => true,
                    'name' => 'Guinea',
                    'code' => 'gn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            93 =>
                array(
                    'active' => true,
                    'name' => 'Guinea-Bissau',
                    'code' => 'gw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            94 =>
                array(
                    'active' => true,
                    'name' => 'Guyana',
                    'code' => 'gy',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            95 =>
                array(
                    'active' => true,
                    'name' => 'Haiti',
                    'code' => 'ht',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            96 =>
                array(
                    'active' => true,
                    'name' => 'Heard Island and McDonald Islands',
                    'code' => 'hm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            97 =>
                array(
                    'active' => true,
                    'name' => 'Holy See (Vatican City State)',
                    'code' => 'va',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            98 =>
                array(
                    'active' => true,
                    'name' => 'Honduras',
                    'code' => 'hn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            99 =>
                array(
                    'active' => true,
                    'name' => 'Hong Kong',
                    'code' => 'hk',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            100 =>
                array(
                    'active' => true,
                    'name' => 'Hungary',
                    'code' => 'hu',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            101 =>
                array(
                    'active' => true,
                    'name' => 'Iceland',
                    'code' => 'is',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            102 =>
                array(
                    'active' => true,
                    'name' => 'India',
                    'code' => 'in',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            103 =>
                array(
                    'active' => true,
                    'name' => 'Indonesia',
                    'code' => 'id',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            104 =>
                array(
                    'active' => true,
                    'name' => 'Iran, Islamic Republic of',
                    'code' => 'ir',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            105 =>
                array(
                    'active' => true,
                    'name' => 'Iraq',
                    'code' => 'iq',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            106 =>
                array(
                    'active' => true,
                    'name' => 'Ireland',
                    'code' => 'ie',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            107 =>
                array(
                    'active' => true,
                    'name' => 'Isle of Man',
                    'code' => 'im',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            108 =>
                array(
                    'active' => true,
                    'name' => 'Israel',
                    'code' => 'il',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            109 =>
                array(
                    'active' => true,
                    'name' => 'Italy',
                    'code' => 'it',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            110 =>
                array(
                    'active' => true,
                    'name' => 'Jamaica',
                    'code' => 'jm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            111 =>
                array(
                    'active' => true,
                    'name' => 'Japan',
                    'code' => 'jp',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            112 =>
                array(
                    'active' => true,
                    'name' => 'Jersey',
                    'code' => 'je',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            113 =>
                array(
                    'active' => true,
                    'name' => 'Jordan',
                    'code' => 'jo',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            114 =>
                array(
                    'active' => true,
                    'name' => 'Kazakhstan',
                    'code' => 'kz',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            115 =>
                array(
                    'active' => true,
                    'name' => 'Kenya',
                    'code' => 'ke',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            116 =>
                array(
                    'active' => true,
                    'name' => 'Kiribati',
                    'code' => 'ki',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            117 =>
                array(
                    'active' => true,
                    'name' => 'Korea, Democratic People\'s Republic of',
                    'code' => 'kp',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            118 =>
                array(
                    'active' => true,
                    'name' => 'Korea, Republic of',
                    'code' => 'kr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            119 =>
                array(
                    'active' => true,
                    'name' => 'Kuwait',
                    'code' => 'kw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            120 =>
                array(
                    'active' => true,
                    'name' => 'Kyrgyzstan',
                    'code' => 'kg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            121 =>
                array(
                    'active' => true,
                    'name' => 'Lao People\'s Democratic Republic',
                    'code' => 'la',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            122 =>
                array(
                    'active' => true,
                    'name' => 'Latvia',
                    'code' => 'lv',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            123 =>
                array(
                    'active' => true,
                    'name' => 'Lebanon',
                    'code' => 'lb',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            124 =>
                array(
                    'active' => true,
                    'name' => 'Lesotho',
                    'code' => 'ls',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            125 =>
                array(
                    'active' => true,
                    'name' => 'Liberia',
                    'code' => 'lr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            126 =>
                array(
                    'active' => true,
                    'name' => 'Libya',
                    'code' => 'ly',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            127 =>
                array(
                    'active' => true,
                    'name' => 'Liechtenstein',
                    'code' => 'li',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            128 =>
                array(
                    'active' => true,
                    'name' => 'Lithuania',
                    'code' => 'lt',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            129 =>
                array(
                    'active' => true,
                    'name' => 'Luxembourg',
                    'code' => 'lu',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            130 =>
                array(
                    'active' => true,
                    'name' => 'Macao',
                    'code' => 'mo',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            131 =>
                array(
                    'active' => true,
                    'name' => 'Macedonia, the Former Yugoslav Republic of',
                    'code' => 'mk',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            132 =>
                array(
                    'active' => true,
                    'name' => 'Madagascar',
                    'code' => 'mg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            133 =>
                array(
                    'active' => true,
                    'name' => 'Malawi',
                    'code' => 'mw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            134 =>
                array(
                    'active' => true,
                    'name' => 'Malaysia',
                    'code' => 'my',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            135 =>
                array(
                    'active' => true,
                    'name' => 'Maldives',
                    'code' => 'mv',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            136 =>
                array(
                    'active' => true,
                    'name' => 'Mali',
                    'code' => 'ml',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            137 =>
                array(
                    'active' => true,
                    'name' => 'Malta',
                    'code' => 'mt',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            138 =>
                array(
                    'active' => true,
                    'name' => 'Marshall Islands',
                    'code' => 'mh',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            139 =>
                array(
                    'active' => true,
                    'name' => 'Martinique',
                    'code' => 'mq',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            140 =>
                array(
                    'active' => true,
                    'name' => 'Mauritania',
                    'code' => 'mr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            141 =>
                array(
                    'active' => true,
                    'name' => 'Mauritius',
                    'code' => 'mu',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            142 =>
                array(
                    'active' => true,
                    'name' => 'Mayotte',
                    'code' => 'yt',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            143 =>
                array(
                    'active' => true,
                    'name' => 'Mexico',
                    'code' => 'mx',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            144 =>
                array(
                    'active' => true,
                    'name' => 'Micronesia, Federated States of',
                    'code' => 'fm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            145 =>
                array(
                    'active' => true,
                    'name' => 'Moldova, Republic of',
                    'code' => 'md',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            146 =>
                array(
                    'active' => true,
                    'name' => 'Monaco',
                    'code' => 'mc',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            147 =>
                array(
                    'active' => true,
                    'name' => 'Mongolia',
                    'code' => 'mn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            148 =>
                array(
                    'active' => true,
                    'name' => 'Montenegro',
                    'code' => 'me',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            149 =>
                array(
                    'active' => true,
                    'name' => 'Montserrat',
                    'code' => 'ms',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            150 =>
                array(
                    'active' => true,
                    'name' => 'Morocco',
                    'code' => 'ma',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            151 =>
                array(
                    'active' => true,
                    'name' => 'Mozambique',
                    'code' => 'mz',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            152 =>
                array(
                    'active' => true,
                    'name' => 'Myanmar',
                    'code' => 'mm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            153 =>
                array(
                    'active' => true,
                    'name' => 'Namibia',
                    'code' => 'na',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            154 =>
                array(
                    'active' => true,
                    'name' => 'Nauru',
                    'code' => 'nr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            155 =>
                array(
                    'active' => true,
                    'name' => 'Nepal',
                    'code' => 'np',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            156 =>
                array(
                    'active' => true,
                    'name' => 'Netherlands',
                    'code' => 'nl',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            157 =>
                array(
                    'active' => true,
                    'name' => 'New Caledonia',
                    'code' => 'nc',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            158 =>
                array(
                    'active' => true,
                    'name' => 'New Zealand',
                    'code' => 'nz',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            159 =>
                array(
                    'active' => true,
                    'name' => 'Nicaragua',
                    'code' => 'ni',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            160 =>
                array(
                    'active' => true,
                    'name' => 'Niger',
                    'code' => 'ne',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            161 =>
                array(
                    'active' => true,
                    'name' => 'Nigeria',
                    'code' => 'ng',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            162 =>
                array(
                    'active' => true,
                    'name' => 'Niue',
                    'code' => 'nu',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            163 =>
                array(
                    'active' => true,
                    'name' => 'Norfolk Island',
                    'code' => 'nf',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            164 =>
                array(
                    'active' => true,
                    'name' => 'Northern Mariana Islands',
                    'code' => 'mp',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            165 =>
                array(
                    'active' => true,
                    'name' => 'Norway',
                    'code' => 'no',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            166 =>
                array(
                    'active' => true,
                    'name' => 'Oman',
                    'code' => 'om',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            167 =>
                array(
                    'active' => true,
                    'name' => 'Pakistan',
                    'code' => 'pk',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            168 =>
                array(
                    'active' => true,
                    'name' => 'Palau',
                    'code' => 'pw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            169 =>
                array(
                    'active' => true,
                    'name' => 'Palestine, State of',
                    'code' => 'ps',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            170 =>
                array(
                    'active' => true,
                    'name' => 'Panama',
                    'code' => 'pa',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            171 =>
                array(
                    'active' => true,
                    'name' => 'Papua New Guinea',
                    'code' => 'pg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            172 =>
                array(
                    'active' => true,
                    'name' => 'Paraguay',
                    'code' => 'py',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            173 =>
                array(
                    'active' => true,
                    'name' => 'Peru',
                    'code' => 'pe',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            174 =>
                array(
                    'active' => true,
                    'name' => 'Philippines',
                    'code' => 'ph',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            175 =>
                array(
                    'active' => true,
                    'name' => 'Pitcairn',
                    'code' => 'pn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            176 =>
                array(
                    'active' => true,
                    'name' => 'Poland',
                    'code' => 'pl',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            177 =>
                array(
                    'active' => true,
                    'name' => 'Portugal',
                    'code' => 'pt',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            178 =>
                array(
                    'active' => true,
                    'name' => 'Puerto Rico',
                    'code' => 'pr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            179 =>
                array(
                    'active' => true,
                    'name' => 'Qatar',
                    'code' => 'qa',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            180 =>
                array(
                    'active' => true,
                    'name' => 'Réunion',
                    'code' => 're',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            181 =>
                array(
                    'active' => true,
                    'name' => 'Romania',
                    'code' => 'ro',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            182 =>
                array(
                    'active' => true,
                    'name' => 'Russian Federation',
                    'code' => 'ru',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            183 =>
                array(
                    'active' => true,
                    'name' => 'Rwanda',
                    'code' => 'rw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            184 =>
                array(
                    'active' => true,
                    'name' => 'Saint Barthélemy',
                    'code' => 'bl',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            185 =>
                array(
                    'active' => true,
                    'name' => 'Saint Helena, Ascension and Tristan da Cunha',
                    'code' => 'sh',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            186 =>
                array(
                    'active' => true,
                    'name' => 'Saint Kitts and Nevis',
                    'code' => 'kn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            187 =>
                array(
                    'active' => true,
                    'name' => 'Saint Lucia',
                    'code' => 'lc',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            188 =>
                array(
                    'active' => true,
                    'name' => 'Saint Martin (French part)',
                    'code' => 'mf',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            189 =>
                array(
                    'active' => true,
                    'name' => 'Saint Pierre and Miquelon',
                    'code' => 'pm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            190 =>
                array(
                    'active' => true,
                    'name' => 'Saint Vincent and the Grenadines',
                    'code' => 'vc',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            191 =>
                array(
                    'active' => true,
                    'name' => 'Samoa',
                    'code' => 'ws',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            192 =>
                array(
                    'active' => true,
                    'name' => 'San Marino',
                    'code' => 'sm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            193 =>
                array(
                    'active' => true,
                    'name' => 'Sao Tome and Principe',
                    'code' => 'st',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            194 =>
                array(
                    'active' => true,
                    'name' => 'Saudi Arabia',
                    'code' => 'sa',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            195 =>
                array(
                    'active' => true,
                    'name' => 'Senegal',
                    'code' => 'sn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            196 =>
                array(
                    'active' => true,
                    'name' => 'Serbia',
                    'code' => 'rs',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            197 =>
                array(
                    'active' => true,
                    'name' => 'Seychelles',
                    'code' => 'sc',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            198 =>
                array(
                    'active' => true,
                    'name' => 'Sierra Leone',
                    'code' => 'sl',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            199 =>
                array(
                    'active' => true,
                    'name' => 'Singapore',
                    'code' => 'sg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            200 =>
                array(
                    'active' => true,
                    'name' => 'Sint Maarten (Dutch part)',
                    'code' => 'sx',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            201 =>
                array(
                    'active' => true,
                    'name' => 'Slovakia',
                    'code' => 'sk',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            202 =>
                array(
                    'active' => true,
                    'name' => 'Slovenia',
                    'code' => 'si',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            203 =>
                array(
                    'active' => true,
                    'name' => 'Solomon Islands',
                    'code' => 'sb',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            204 =>
                array(
                    'active' => true,
                    'name' => 'Somalia',
                    'code' => 'so',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            205 =>
                array(
                    'active' => true,
                    'name' => 'South Africa',
                    'code' => 'za',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            206 =>
                array(
                    'active' => true,
                    'name' => 'South Georgia and the South Sandwich Islands',
                    'code' => 'gs',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            207 =>
                array(
                    'active' => true,
                    'name' => 'South Sudan',
                    'code' => 'ss',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            208 =>
                array(
                    'active' => true,
                    'name' => 'Spain',
                    'code' => 'es',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            209 =>
                array(
                    'active' => true,
                    'name' => 'Sri Lanka',
                    'code' => 'lk',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            210 =>
                array(
                    'active' => true,
                    'name' => 'Sudan',
                    'code' => 'sd',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            211 =>
                array(
                    'active' => true,
                    'name' => 'Suriname',
                    'code' => 'sr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            212 =>
                array(
                    'active' => true,
                    'name' => 'Svalbard and Jan Mayen',
                    'code' => 'sj',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            213 =>
                array(
                    'active' => true,
                    'name' => 'Swaziland',
                    'code' => 'sz',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            214 =>
                array(
                    'active' => true,
                    'name' => 'Sweden',
                    'code' => 'se',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            215 =>
                array(
                    'active' => true,
                    'name' => 'Switzerland',
                    'code' => 'ch',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            216 =>
                array(
                    'active' => true,
                    'name' => 'Syrian Arab Republic',
                    'code' => 'sy',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            217 =>
                array(
                    'active' => true,
                    'name' => 'Taiwan, Province of China',
                    'code' => 'tw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            218 =>
                array(
                    'active' => true,
                    'name' => 'Tajikistan',
                    'code' => 'tj',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            219 =>
                array(
                    'active' => true,
                    'name' => 'Tanzania, United Republic of',
                    'code' => 'tz',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            220 =>
                array(
                    'active' => true,
                    'name' => 'Thailand',
                    'code' => 'th',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            221 =>
                array(
                    'active' => true,
                    'name' => 'Timor-Leste',
                    'code' => 'tl',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            222 =>
                array(
                    'active' => true,
                    'name' => 'Togo',
                    'code' => 'tg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            223 =>
                array(
                    'active' => true,
                    'name' => 'Tokelau',
                    'code' => 'tk',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            224 =>
                array(
                    'active' => true,
                    'name' => 'Tonga',
                    'code' => 'to',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            225 =>
                array(
                    'active' => true,
                    'name' => 'Trinidad and Tobago',
                    'code' => 'tt',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            226 =>
                array(
                    'active' => true,
                    'name' => 'Tunisia',
                    'code' => 'tn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            227 =>
                array(
                    'active' => true,
                    'name' => 'Turkey',
                    'code' => 'tr',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            228 =>
                array(
                    'active' => true,
                    'name' => 'Turkmenistan',
                    'code' => 'tm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            229 =>
                array(
                    'active' => true,
                    'name' => 'Turks and Caicos Islands',
                    'code' => 'tc',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            230 =>
                array(
                    'active' => true,
                    'name' => 'Tuvalu',
                    'code' => 'tv',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            231 =>
                array(
                    'active' => true,
                    'name' => 'Uganda',
                    'code' => 'ug',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            232 =>
                array(
                    'active' => true,
                    'name' => 'Ukraine',
                    'code' => 'ua',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            233 =>
                array(
                    'active' => true,
                    'name' => 'United Arab Emirates',
                    'code' => 'ae',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            234 =>
                array(
                    'active' => true,
                    'name' => 'United Kingdom',
                    'code' => 'gb',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            235 =>
                array(
                    'active' => true,
                    'name' => 'United States',
                    'code' => 'us',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            236 =>
                array(
                    'active' => true,
                    'name' => 'United States Minor Outlying Islands',
                    'code' => 'um',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            237 =>
                array(
                    'active' => true,
                    'name' => 'Uruguay',
                    'code' => 'uy',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            238 =>
                array(
                    'active' => true,
                    'name' => 'Uzbekistan',
                    'code' => 'uz',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            239 =>
                array(
                    'active' => true,
                    'name' => 'Vanuatu',
                    'code' => 'vu',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            240 =>
                array(
                    'active' => true,
                    'name' => 'Venezuela, Bolivarian Republic of',
                    'code' => 've',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            241 =>
                array(
                    'active' => true,
                    'name' => 'Viet Nam',
                    'code' => 'vn',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            242 =>
                array(
                    'active' => true,
                    'name' => 'Virgin Islands, British',
                    'code' => 'vg',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            243 =>
                array(
                    'active' => true,
                    'name' => 'Virgin Islands, U.S.',
                    'code' => 'vi',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            244 =>
                array(
                    'active' => true,
                    'name' => 'Wallis and Futuna',
                    'code' => 'wf',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            245 =>
                array(
                    'active' => true,
                    'name' => 'Western Sahara',
                    'code' => 'eh',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            246 =>
                array(
                    'active' => true,
                    'name' => 'Yemen',
                    'code' => 'ye',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            247 =>
                array(
                    'active' => true,
                    'name' => 'Zambia',
                    'code' => 'zm',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
            248 =>
                array(
                    'active' => true,
                    'name' => 'Zimbabwe',
                    'code' => 'zw',
                    'image' => 'assets/images/countries/be.png',
                    'created_at' => $now,
                    'updated_at' => $now
                ),
        );

        Country::insert($countries);
    }

}
