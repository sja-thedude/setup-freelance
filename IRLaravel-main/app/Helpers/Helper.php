<?php

namespace App\Helpers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Group;
use App\Models\PictureProcessor;
use App\Models\Product;
use App\Models\RedeemHistory;
use App\Models\Reward;
use App\Models\RewardProduct;
use App\Models\SettingTimeslotDetail;
use App\Repositories\NotificationRepository;
use Carbon\Carbon;
use Config;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Str;
use Imagick;
use ImagickPixel;
use URL, Auth;
use App\Modules\ContentManager\Models\Options;
use DateTime;
use DateTimeZone;
use App\Models\Workspace;
use Illuminate\Support\Facades\Log;

class Helper
{
    private $options;

    public function __construct()
    {
        /*// Prevent error when run composer install in the first time
        if (\Schema::hasTable('options')) {
            $this->options = Options::all()->toArray();
        }*/
    }

    public function menu($group = "main-menu")
    {
        $menu = new Menu($group);
        return $menu->generateMenu();
    }

    public function compress($source, $destination)
    {
        $com = new Compress($source, $destination);
        return $com->run();
    }

    public function extract($source, $destination)
    {
        $com = new Compress($source, $destination);
        return $com->extract();
    }

    public function widget($class, $option = [])
    {
        $class = "App\\Widgets\\" . str_replace(".", "\\", $class);
        $widget = new $class;
        return $widget->test();
    }

    public function taxonomyLink($taxonomy, $link = true)
    {
        $res = [];
        if ($link) {
            foreach ($taxonomy as $value) {
                $res[] = '<a href="' . url("/category/" . $value->slug) . '">' . $value->name . '</a>';
            }
        } else {
            foreach ($taxonomy as $value) {
                $res[] = $value->name;
            }
        }
        return implode(",", $res);
    }

    public function bbcode($content)
    {
        $bbcode = new BBCode();
        return $bbcode->toHTML($content);
    }

    public function option($keySearch)
    {
        $result = null;
        foreach ($this->options as $value) {
            if ($value['name'] == $keySearch) {
                $result = $value['value'];
            }
        }
        return $result;
    }

    public function appTitle($title)
    {
        return ($title == "") ? $this->option("site_title") : $title . " - " . $this->option("site_title");
    }

    public function menuList()
    {
        return '';
    }

    public function recursive_array_search($needle, $haystack)
    {

        foreach ($haystack as $key => $value) {
            $current_key = $key;
            if ($needle === $value OR (is_array($value) && $this->recursive_array_search($needle, $value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }

    /**
     * Check if user has Route or not
     *
     * @param String $routeName
     * @param string $guard
     * @return boolean
     */
    public static function checkUserPermission($routeName, $guard = 'admin')
    {
        /** @var \App\Models\User $user */
        $user = \Auth::guard($guard)->user();

        // If is super admin
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($guard != 'admin') {
            return false;
        }

        $guardDetail = ($user->isSuperAdmin() ? 'super_admin' : 'account_manager');

        return $user->hasRouteUseConfigPermissions($routeName, $guardDetail);
    }

    /**
     * Check current user is admin or not
     *
     * @param string $guard
     * @return bool
     */
    public static function isAdmin($guard = 'admin')
    {
        /** @var \App\Models\User $user */
        $user = \Auth::guard($guard)->user();

        return (!empty($user) && $user->isAdmin());
    }

    /**
     * Check current user is super admin or not
     *
     * @param string $guard
     * @return bool
     */
    public static function isSuperAdmin($guard = 'admin')
    {
        /** @var \App\Models\User $user */
        $user = \Auth::guard($guard)->user();

        return (!empty($user) && $user->isSuperAdmin());
    }

    /**
     * Get full resource URL
     *
     * @param string $link
     * @param string $default
     * @param string $path
     * @return string
     */
    public static function getLinkFromDataSource($link, $default = null, $path = null)
    {
        $baseUrl = URL::to('/') . '/';

        if ($link == null) {
            // Get default avatar if null
            $link = ($default == null) ? '' : $baseUrl . $default;
            return $link;
        }

        $regex = "/^(http|https):\/\//";
        $match = preg_match($regex, $link);

        if (!$match) {
            $link = ($path) ? (trim($path, '/') . '/' . $link) : $link;
            $link = $baseUrl . $link;
        }

        return $link;
    }

    /**
     * @param string $value
     * @return string
     */
    public static function getRelativeResource($value)
    {
        // Validate value
        if (empty($value) || !is_string($value)) {
            return $value;
        }

        $baseUrl = url('/') . '/';
        /*$baseUrlLength = strlen($baseUrl);

        return substr($value, $baseUrlLength);*/
        return str_replace($baseUrl, '', $value);
    }

    /**
     * Config date format in here
     */
    public static function getDateFormat()
    {
        return 'd/m/Y';
    }

    /**
     * Config time format in here
     */
    public static function getTimeFormat()
    {
        return 'H:i';
    }

    /**
     * Config js date format in here
     */
    public static function getJsDateFormat()
    {
        return 'dd/mm/yyyy';
    }

    /**
     * Config js date format in here
     */
    public static function getJsTimeFormat()
    {
        return 'HH:MM';
    }

    /**
     * Config js date format in here
     */
    public static function getJsDateTimeFormat()
    {
        return static::getJsDateFormat() . ' ' . static::getJsTimeFormat();
    }

    /**
     * Format datetime by config
     *
     * @param \Carbon\Carbon|string $datetime
     * @param string|null $format
     * @param string|null $guard
     * @param string|null $timezone
     * @return string
     */
    public static function getDatetimeFromFormat($datetime, $format = null, $guard = 'admin', $timezone = null)
    {
        // When empty datetime
        if (empty($datetime)) {
            return null;
        }

        // Convert date string to Carbon
        if (!($datetime instanceof Carbon)) {
            $datetime = new Carbon($datetime);
        }

        // Default format
        if (empty($format)) {
            $format = static::getDateFormat() . ' ' . static::getTimeFormat();
        }

        // When invalid datetime
        if ($datetime->year < 1) {
            return null;
        }

        // Only config timezone if param is null
        if (empty($timezone)) {
            // Get timezone from user logged in
            if (!empty(Auth::guard($guard))) {
                /** @var \App\Models\User $me */
                $me = Auth::guard($guard)->user();

                if (!empty($me) && !empty($me->timezone)) {
                    $timezone = $me->timezone;
                }
            }

            // If still null, get timezone from config
            if (empty($timezone)) {
                $timezone = Config::get('app.timezone');
            }
        }

        // Format datetime
        return $datetime->setTimezone($timezone)->format($format);
    }

    /**
     * Format date by config
     *
     * @param \Carbon\Carbon|string $datetime
     * @param string $format
     * @return string
     */
    public static function getDateFromFormat($datetime, $format = null, $guard = 'admin')
    {
        // When empty datetime
        if (empty($datetime)) {
            return null;
        }

        // Convert date string to Carbon
        if (!($datetime instanceof Carbon)) {
            $datetime = new Carbon($datetime);
        }

        // Default format
        if (empty($format)) {
            $format = static::getDateFormat();
        }

        // When invalid datetime
        if ($datetime->year < 1) {
            return null;
        }

        // Format date
        /** @var \App\Models\User $me */
        $me = Auth::guard($guard)->user();
        $timezone = (!empty($me) && !empty($me->timezone)) ? $me->timezone : Config::get('app.timezone');
        $strDate = $datetime->setTimezone($timezone)->format($format);

        return $strDate;
    }

    /**
     * Format time by config
     *
     * @param \Carbon\Carbon|string $datetime
     * @param string $format
     * @return string
     */
    public static function getTimeFromFormat($datetime, $format = null, $guard = 'admin')
    {
        // When empty datetime
        if (empty($datetime)) {
            return null;
        }

        // Convert date string to Carbon
        if (!($datetime instanceof Carbon)) {
            $datetime = new Carbon($datetime);
        }

        // Default format
        if (empty($format)) {
            $format = static::getTimeFormat();
        }

        // When invalid datetime
        if ($datetime->year < 1) {
            return null;
        }

        // Format time
        /** @var \App\Models\User $me */
        $me = Auth::guard($guard)->user();
        $timezone = (!empty($me) && !empty($me->timezone)) ? $me->timezone : Config::get('app.timezone');
        $strDate = $datetime->setTimezone($timezone)->format($format);

        return $strDate;
    }

    /**
     * Get birthday before date validation
     *
     * @param string|null $timezone
     * @return string Date format: Y-m-d
     */
    public function getBirthdayBeforeDate($timezone = null)
    {
        $now = \Carbon\Carbon::now();

        if (!empty($timezone)) {
            $now->tz($timezone);
        }

        // Subtract from config

        // Subtract years
        if (!empty(config('validation.birthday_before.year'))) {
            $now->subYears((int)config('validation.birthday_before.year'));
        }

        // Subtract months
        if (!empty(config('validation.birthday_before.month'))) {
            $now->subMonths((int)config('validation.birthday_before.month'));
        }

        // Subtract days
        if (!empty(config('validation.birthday_before.day'))) {
            $now->subDays((int)config('validation.birthday_before.day'));
        }

        return $now->toDateString();
    }

    /**
     * Get Active locale language
     *
     * @return array
     */
    public static function getActiveLanguages()
    {
        return Config::get('languages');
    }

    /**
     * Get Active locale language
     *
     * @return array
     */
    public static function getActiveWorkspaces()
    {
        // Read from cache
        if (Config::has('workspace.active_workspaces')) {
            return Config::get('workspace.active_workspaces');
        }

        // Cache from db and return
        /** @var \App\Models\User $user */
        $user = \Auth::guard('admin')->user();

        // If invalid user
        if (empty($user)) {
            return [];
        }

        $workspaceInstance = Workspace::getInstance();
        /** @var \App\Models\Workspace $workspaces */
        $workspaces = Workspace::where('workspaces.active', Workspace::IS_YES);

        if (!$user->isSuperAdmin()) {
            // Filter by workspace permission
            $workspaces->withUser($user->id);
        }

        // list
        $workspaces = $workspaces->pluck('workspaces.name', 'workspaces.' . $workspaceInstance->getKeyName());
        $workspaces = $workspaces->toArray();

        // Cache active workspaces
        Config::set('workspace.active_workspaces', $workspaces);

        return $workspaces;
    }

    /**
     * Get default workspace
     *
     * @return \App\Models\Workspace|null
     */
    public static function getDefaultWorkspace()
    {
        // list from cache
        $workspaces = static::getActiveWorkspaces();
        $workspace = null;
        $workspaceInstance = Workspace::getInstance();

        if (!empty($workspaces)) {
            foreach ($workspaces as $id => $name) {
                // Get first item and break
                $workspace = Workspace::active()
                    ->where($workspaceInstance->getKeyName(), $id)
                    ->first();
                break;
            }
        }

        return $workspace;
    }

    /**
     * Translate by \Dimsav\Translatable\Translatable
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $locale
     * @param string $field
     * @return string|null
     */
    public static function translate($model, $locale, $field)
    {
        // Empty model
        if (empty($model)) {
            return null;
        }

        $translation = $model->translate($locale);

        // Not found locale lang
        if (empty($translation)) {
            return null;
        }

        // Get value by field name
        return $translation->getAttribute($field);
    }

    /**
     * Overwrite trans function in Laravel helpers core
     * Parameter $domain is passed to Translator but never used
     * @link https://github.com/laravel/framework/issues/2249
     *
     * @param string $id
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     * @return string
     */
    public static function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        $tmpDomain = $domain . '.';
        $string = $tmpDomain . $id;
        // Call from laravel helpers core
        $result = trans($string, $parameters, $locale);

        // Exception with match string when not found in translate file
        if ($result == $string) {
            // Find match in begin of string
            $find = strpos($result, $tmpDomain);

            if ($find !== false) {
                // Remove domain and group of result
                $result = substr_replace($result, '', $find, strlen($tmpDomain));
            }
        }

        return $result;
    }

    /**
     * Set KCFinder upload dir
     *
     * @param string $dir
     * @return bool
     */
    public static function setKCFinderUploadDir($dir)
    {
        if (!isset($_SESSION['KCFINDER'])) {
            $_SESSION['KCFINDER'] = array();
        }

        $_SESSION['KCFINDER']['uploadURL'] = url($dir);
        $_SESSION['KCFINDER']['uploadDir'] = public_path($dir);

        return true;
    }

    /**
     * Get timezone list
     *
     * @link https://stackoverflow.com/questions/1727077/generating-a-drop-down-list-of-timezones-with-php#answer-40636798
     * @return array
     */
    public function getTimezones()
    {
        static $timezones = null;

        if ($timezones === null) {
            $timezones = [];
            $offsets = [];
            $now = new DateTime('now', new DateTimeZone('UTC'));

            foreach (DateTimeZone::listIdentifiers() as $timezone) {
                $now->setTimezone(new DateTimeZone($timezone));
                $offsets[] = $offset = $now->getOffset();
                $timezones[$timezone] = '(' . $this->formatGmtOffset($offset) . ') ' . $this->formatTimezoneName($timezone);
            }

            array_multisort($offsets, $timezones);
        }

        return $timezones;
    }

    /**
     * Format GMT offset
     *
     * @param $offset
     * @return string
     */
    public function formatGmtOffset($offset)
    {
        $hours = intval($offset / 3600);
        $minutes = abs(intval($offset % 3600 / 60));
        return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
    }

    /**
     * Format timezone name
     *
     * @param $name
     * @return mixed
     */
    public function formatTimezoneName($name)
    {
        $name = str_replace('/', ', ', $name);
        $name = str_replace('_', ' ', $name);
        $name = str_replace('St ', 'St. ', $name);
        return $name;
    }

    /**
     * @param $orginalTranslations
     * @param $fileName
     * @return array
     */
    public static function getFileJsonLang($orginalTranslations, $fileName, $locale = null, $loadFromJs = false)
    {
        if(empty($locale)) {
            $locale = app()->getLocale();
        }

        $pathFile = 'lang/' . $locale . '/' . $fileName;
        $jsonTranslationsString = '';

        if (file_exists(storage_path($pathFile))) {
            $jsonTranslationsString = file_get_contents(storage_path($pathFile));
        }

        if (!in_array(substr($jsonTranslationsString, 0, 1), ['{', '['])) {
            return $orginalTranslations;
        }

        if(!empty($loadFromJs)) {
            $orginalTranslationConverts = array();
            self::getChildNode("", is_array($orginalTranslations) ? $orginalTranslations : [], $orginalTranslationConverts);

            return array_replace_recursive($orginalTranslationConverts, json_decode($jsonTranslationsString, TRUE));
        }

        return array_replace_recursive($orginalTranslations, json_decode($jsonTranslationsString, TRUE));
    }

    /**
     * @param       $stringKey
     * @param array $array
     * @param       $result
     */
    public static function getChildNode($stringKey, array $array, &$result)
    {
        foreach ($array as $key => $value) {
            if ($stringKey !== "") {
                $currentKey = $stringKey . "." . $key;
            } else {
                $currentKey = $key;
            }
            if (is_array($value)) {
                Helper::getChildNode($currentKey, $value, $result);
            } else {
                $result[$currentKey] = $value;
            }
        }
    }

    /**
     * @param array $errors
     * @return string|null
     */
    public function getFirstError($errors = null)
    {
        if ($errors == null || empty($errors) || !is_array($errors) || count($errors) == 0)
            return null;

        /**
         * @var string $field
         * @var array $errors - Array<String>
         */
        foreach ($errors as $field => $messages) {
            return $errors[$field];
        }

        return null;
    }

    /**
     * @param mixed|null $errors
     * @return string|null
     */
    public function getFirstErrorMessage($errors = null)
    {
        if ($errors !== null) {
            /**
             * @var string $field
             * @var array $errors - Array<String>
             */
            foreach ($errors as $field => $messages) {
                if ($messages !== null && count($messages) > 0)
                    return $messages[0];
            }
        }

        return null;
    }

    /**
     * Checking which `guard` is loggedin
     *
     * @link https://stackoverflow.com/a/58694700/2809971
     *
     * @return mixed|null
     */
    public function activeGuard()
    {
        foreach (array_keys(config('auth.guards')) as $guard) {
            if (auth()->guard($guard)->check()) return $guard;
        }

        return null;
    }

    /**
     * @param $sortBy
     * @return string
     */
    public static function getFullSortUrl($sortBy)
    {
        if ($sortBy == request()->sort_by) {
            $counter = 1;
            if (request()->counter == 1) {
                $counter = $counter + 1;
            }

            $sortArray = [
                'sort_by' => $sortBy,
                'order_by' => request()->order_by == 'asc' ? 'desc' : 'asc',
                'counter' => $counter
            ];

            $fullUrl = '';
            if (request()->counter == 2) {
                if (request()->page) {
                    $fullUrl = static::remove_query_params(['sort_by', 'order_by', 'counter']);
                } else {
                    $fullUrl = request()->url();
                }
            }

            $fullUrl = !empty($fullUrl) ? $fullUrl : request()->fullUrlWithQuery($sortArray);
        } else {
            $fullUrl = request()->fullUrlWithQuery([
                'sort_by' => $sortBy,
                'order_by' => 'asc',
                'counter' => 1
            ]);
        }

        return $fullUrl;
    }

    /**
     * @param $sortBy
     * generate icon up | down
     */
    public static function getIconSort($sortBy)
    {
        $html = '<div class="column-sorter">';
        if (request()->sort_by == $sortBy && request()->order_by == 'asc') {
            $html .= '<i class="fa fa-angle-up on"></i>';
            $html .= '<i class="fa fa-angle-down"></i>';
        } elseif (request()->sort_by == $sortBy && request()->order_by == 'desc') {
            $html .= '<i class="fa fa-angle-up"></i>';
            $html .= '<i class="fa fa-angle-down on"></i>';
        } else {
            $html .= '<i class="fa fa-angle-up"></i>';
            $html .= '<i class="fa fa-angle-down"></i>';
        }
        $html .= '</div>';

        echo $html;
    }

    /**
     * @param array $params
     * @return string
     *
     * URL before:
     * https://example.com/orders/123?order=ABC009&status=shipped
     *
     * 1. remove_query_params(['status'])
     * 2. remove_query_params(['status', 'order'])
     *
     * URL after:
     * 1. https://example.com/orders/123?order=ABC009
     * 2. https://example.com/orders/123
     */
    public static function remove_query_params(array $params = [])
    {
        $url = url()->current(); // get the base URL - everything to the left of the "?"
        $query = request()->query(); // get the query parameters (what follows the "?")

        foreach ($params as $param) {
            unset($query[$param]); // loop through the array of parameters we wish to remove and unset the parameter from the query array
        }

        return $query ? $url . '?' . http_build_query($query) : $url; // rebuild the URL with the remaining parameters, don't append the "?" if there aren't any query parameters left
    }

    public static function convertDateTimeToUTC($dateTimeOrigin, $timezone = 'UTC') {
        $dateTimeTmp = date(config('datetime.dateTimeDb'), strtotime($dateTimeOrigin));
        $dateTime = \Carbon\Carbon::createFromFormat(config('datetime.dateTimeDb'), $dateTimeTmp, $timezone);
        $dateTime->setTimezone('UTC');

        return $dateTime;
    }

    public static function convertDateTimeToTimezone($dateTimeOrigin, $timezone = 'UTC') {
        $dateTimeTmp = date(config('datetime.dateTimeDb'), strtotime($dateTimeOrigin));
        $dateTime = \Carbon\Carbon::createFromFormat(config('datetime.dateTimeDb'), $dateTimeTmp, 'UTC');
        $dateTime->setTimezone($timezone);

        return $dateTime;
    }

    /**
     * @param $array
     * @return bool
     */
    public static function checkOverlap($array) {
        $isOverlap = false;
        $numElements = count($array);
        for ($i=0; $i<$numElements; $i++) {
            if (!empty($array[$i])) {
                $datas[$i]['area_start'] = $array[$i]['area_start'];
                $datas[$i]['area_end'] = $array[$i]['area_end'];

                if ($i > 0) {
                    $j = $i - 1;
                    //for ($j = 0; $j < $i; $j++) {
                        if (
                            ($datas[$i]['area_start'] <= $datas[$j]['area_start'] && $datas[$i]['area_end'] >= $datas[$j]['area_start'])
                            || ($datas[$i]['area_start'] >= $datas[$j]['area_start'] && $datas[$i]['area_end'] <= $datas[$j]['area_end'])
                            || ($datas[$i]['area_start'] >= $datas[$j]['area_start'] && $datas[$i]['area_end'] >= $datas[$j]['area_end']
                                // Use < for case 0-2,2-5,5-8 And use <= for case 0-2,3-5,6-8.
                                && $datas[$i]['area_start'] < $datas[$j]['area_end'])
                        ) {
                            $isOverlap = true;
                            break;
                        }
                    //}
                }

                if ($isOverlap) {
                    break;
                }
            }
        }

        return $isOverlap;
    }

    public static function splitTime($timeLimit, $timeArrs) {
        $result = [];

        if(!empty($timeArrs)) {
            foreach($timeArrs as $timeArr) {
                $start = date('Y-m-d') .' '. $timeArr['start_time'];
                $end = date('Y-m-d') .' '. $timeArr['end_time'];
                $result = static::eachSplitTime($timeLimit, $start, $end, $result);
            }
        }

        return $result;
    }

    public static function eachSplitTime($timeLimit, $start, $end, $result) {
        $endNextTime = date('Y-m-d H:i:s', strtotime($timeLimit . ' minutes', strtotime($start)));

        if(strtotime($endNextTime) <= strtotime($end)) {
            $result[] = $start;
            $result = static::eachSplitTime($timeLimit, $endNextTime, $end, $result);
        } else {
            if(strtotime($start) <= strtotime($end)) {
                $result[] = $start;
            }
        }

        return $result;
    }

    /**
     * Correct day of week
     *
     * @param $day
     * @return int
     */
    public static function correctDayOfWeek($day = null)
    {
        if ($day === null) {
            return null;
        }

        // Sunday: both 0, 7 is correct
        // We will convert to 0
        if ($day == 7) {
            $day = 0;
        }

        return $day;
    }

    /**
     * Format currency number
     *
     * @param float $number
     * @param int $decimals
     * @param string $des_point
     * @param string $thousands_sep
     * @return string
     */
    public function formatCurrencyNumber($number, $decimals = 2, $des_point = '.', $thousands_sep = '')
    {
        return number_format($number, $decimals, $des_point, $thousands_sep);
    }

    /**
     * Format distance number
     *
     * @param float $number
     * @param int $decimals
     * @param string $des_point
     * @param string $thousands_sep
     * @return string
     */
    public function formatDistanceNumber($number, $decimals = 2, $des_point = '.', $thousands_sep = '')
    {
        return number_format($number, $decimals, $des_point, $thousands_sep);
    }

    /**
     * Calculate distance between 2 places with lat lng values
     *
     * @param array $currentLocation
     * @param $workspace
     * @return false|string
     */
    public static function calculateDistance($workspace, $currentLocation = [])
    {
        if (empty($workspace)) {
            return false;
        }

        if (empty($workspace->address_long) || empty($workspace->address_lat)) {
            return false;
        }

        if (!isset($currentLocation['lat']) || !isset($currentLocation['lng'])) {
            return false;
        }

        $distance = ( 6371000 * acos( cos( deg2rad ($currentLocation['lat']) ) * cos( deg2rad($workspace->address_lat) )
               * cos( deg2rad($workspace->address_long) - deg2rad($currentLocation['lng']) ) + sin( deg2rad($currentLocation['lat']) )
               * sin( deg2rad($workspace->address_lat))));

        return (new Helper)->formatDistanceNumber($distance);
    }
    /**
     * @param $pattern
     * @param $object
     * @return array
     */
    public static function explodeStr($pattern, $object)
    {
        $result = $object ? preg_split($pattern, $object) : array();

        return array_map('trim', array_filter($result));
    }

    /**
     * Create a new token for the user.
     *
     * @param int $length
     * @return string
     */
    public function createNewToken(int $length = 40)
    {
        return hash_hmac('sha256', Str::random($length), config('app.key'));
    }

    /**
     * @param $dataCart
     * @param $paymentMethod
     * @param $conditionDelevering
     * @return mixed
     */
    public static function createDataOrder($dataCart, $paymentMethod, $conditionDelevering)
    {
        $dateTimeLocal          = Helper::convertDateTimeToTimezone($dataCart->date_time, $dataCart->timezone);
        $data                   = $dataCart->toArray();
        $data['items']          = $data['cart_items'] ?? array();
        $data['coupon_code']    = $data['coupon']['code'] ?? NULL;
        $data['payment_method'] = $paymentMethod;
        $data['lng']            = $data['long'];
        $data['ship_price']     = $dataCart->type === Cart::TYPE_LEVERING ? 0 : NULL;
        $data['date_time']      = Carbon::parse($dateTimeLocal)->format('Y-m-d H:i');
        $data['date']           = Carbon::parse($dateTimeLocal)->format('Y-m-d');
        $data['time']           = Carbon::parse($dateTimeLocal)->format('H:i');

        if ($conditionDelevering
            && $conditionDelevering->free > $dataCart->sub_total_price
            && $dataCart->sub_total_price >= $conditionDelevering->price_min
            && !$dataCart->group_id
        ) {
            $data['ship_price']                    = $conditionDelevering->price;
            $data['setting_delivery_condition_id'] = $conditionDelevering->id;
        }

        if ($dataCart->type === Cart::TYPE_TAKEOUT) {
            $data['ship_price']                    = NULL;
            $data['setting_delivery_condition_id'] = NULL;
        }

        if (isset($data['coupon_discount'])) {
            unset($data['coupon_discount']);
        }

        unset($data['cart_items']);
        unset($data['long']);

        if (isset($data['group_id']) && !empty($data['group_id']) && isset($data['setting_timeslot_detail_id'])) {
            unset($data['setting_timeslot_detail_id']);
        }

        if (isset($data['coupon_id'])) {
            $couponDiscount = self::calculateCouponDiscount($dataCart, $data['coupon_id']);
        }

        if (!empty($data['redeem_history_id'])) {
            $redeemDiscount = self::calculateRedeemDiscount($dataCart, $data['redeem_history_id']);
        }

        if (GroupHelper::isApplyGroupDiscount($dataCart)) {
            $groupDiscount = GroupHelper::calculateTotalGroupDiscount($dataCart);
        }

        foreach($data['items'] as $key => $t) {
            $cartOptionItems = collect($t['cart_option_items']);
            $optionItemIds = $cartOptionItems->pluck('optie_item_id')->all();
            $resultKey = self::convertKeyFromProductIdAndOptionItemIds($t['product_id'], $optionItemIds);
            $t['quantity'] = $t['total_number'];

            if (!empty($couponDiscount) && isset($couponDiscount['discountProducts'])) {
                $discountProducts = $couponDiscount['discountProducts'];
                if (isset($discountProducts[$resultKey])) {
                    $t['coupon_id'] = $data['coupon_id'];
                    $t['discount'] = $discountProducts[$resultKey];
                }
            }

            if (!empty($redeemDiscount) && isset($redeemDiscount['discountProducts'])) {
                $discountProducts = $redeemDiscount['discountProducts'];
                if (isset($discountProducts[$resultKey])) {
                    $t['discount'] = $discountProducts[$resultKey];
                    $t['redeem_history_id'] = $data['redeem_history_id'];
                }
            }

            if (GroupHelper::isApplyGroupDiscount($dataCart)) {
                $discountProducts = $groupDiscount['discountProducts'];
                if (isset($discountProducts[$resultKey])) {
                    $t['discount'] = $discountProducts[$resultKey];
                    $t['group_id'] = $data['group_id'];
                }
            }

            $option = array();
            foreach($t['cart_option_items'] as $op) {
                $option['option_id'] = $op['optie_id'];
                if (isset($op['option_item']['id'])) {
                    $option['option_items'][]['option_item_id'] = $op['option_item']['id'];
                }
            }

            $t['options'][] = $option;
            $data['items'][$key] = $t;
        }

        return $data;
    }

    /**
     * @param $cart
     * @return array
     */
    public static function handleCartWithoutLogin(&$cart)
    {
        $newAddCartItem = array();

        foreach ($cart->cartItems as $key => $cartItem) {
            $newAddCartOptItem    = array();
            $newAddCartItem[$key] = $cartItem->toArray();

            foreach ($cartItem->cartOptionItems as $k => $optItem) {
                $newAddCartOptItem[$k] = $optItem->toArray();
                unset($newAddCartOptItem[$k]['option']);
                unset($newAddCartOptItem[$k]['option_item']);
            }

            unset($newAddCartItem[$key]['category']);
            unset($newAddCartItem[$key]['product']);
            unset($newAddCartItem[$key]['cart_option_items']); // Remove attribute relation

            $newAddCartItem[$key]['cart_option_items'] = $newAddCartOptItem;
        }

        $cart = $cart->toArray();
        unset($cart['cart_items']);
        unset($cart['workspace']);
        unset($cart['coupon']);

        return $newAddCartItem;
    }

    /**
     * @param $sourceFile source file path
     * @param $destFilePath dest file path
     * @param $destFileName dest file name, default img%02d.png
     * @param $limitSize default 10000
     * @param $col default 0
     * @return arrray
     */
    public function splitImage($sourceFile, $destFilePath, $destFileName = 'img%02d.png', $limitSize = 30000, $col = 0, $maxRetries = 1, $retryCount = 0) {
        $result = [];

        $fileSize = filesize($sourceFile);

        if($fileSize > $limitSize) {
            $fileParts =  ceil($fileSize / $limitSize);
            $source = @imagecreatefrompng($sourceFile);
            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            $heightItem = ceil($sourceHeight / $fileParts);

            if(!\File::exists($destFilePath)) {
                \File::makeDirectory($destFilePath, 0777, true, true);
            }

            for($row = 0; $row < $fileParts; $row++) {
                try {
                    $fn = sprintf($destFileName, $row);
                    $im = @imagecreatetruecolor($sourceWidth, $heightItem);
                    $filePath = implode('/', [$destFilePath, $fn]);

                    imagecopyresized($im, $source, 0, 0, $col * $sourceWidth, $row * $heightItem, $sourceWidth, $heightItem, $sourceWidth, $heightItem);
                    imagepng($im, $filePath);
                    imagedestroy($im);

                    $result[] = $fn;
                } catch (\Exception $e) {
                    Log::info('Helper splitImage row Failed', [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'func_args' => func_get_args(),
                        'extra_args' => [
                            'row' => $row,
                            'fn' => $fn,
                            'sourceWidth' => $sourceWidth,
                            'heightItem' => $heightItem,
                            'filePath' => $filePath,
                            'src_x' => $col * $sourceWidth,
                            'src_y' => $row * $heightItem
                        ]
                    ]);

                    // Script failed let's retry
                    if($retryCount < $maxRetries) {
                        Log::info('Helper splitImage row Failed: Trying to recover sleep 1 second (Retry ' . ($retryCount + 1) . ')');

                        // Sleep for 1 second to make sure we do not spam the IO system
                        sleep(1);

                        // Increase retry counter
                        $retryCount++;

                        // Run this again as a recovery
                        return $this->splitImage($sourceFile, $destFilePath, $destFileName, $limitSize, $col, $maxRetries, $retryCount);
                    }

                    // Simulate same behavior then before..
                    throw new \Exception('Helper splitImage row Failed: FATAL - ' . $e->getMessage(), $e->getCode());
                }
            }
        }

        return $result;
    }

    /**
     * Will automatically use the correct imagecreate function.
     * Not 100% trustable a image could have the wrong extension. So only use were you know the extension is correct.
     * @param $filename
     * @return false|\GdImage|resource
     */
    public function imagecreatefromfile($filename) {
        if (!file_exists($filename)) {
            throw new InvalidArgumentException('File "'.$filename.'" not found.');
        }

        switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
            case 'jpeg':
            case 'jpg':
                return imagecreatefromjpeg($filename);
                break;

            case 'png':
                return imagecreatefrompng($filename);
                break;

            case 'gif':
                return imagecreatefromgif($filename);
                break;

            default:
                throw new InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
                break;
        }
    }

    /**
     * Returns a RGBA raster array of the image
     * @param $source
     * @param bool $flat
     * @return array
     */
    public function getArrayOfPixelsFromFile($source, $flat = false) {
        // This needs more memory for bigger images (we can't lower this memory usage)
        ini_set('memory_limit','4096M');

        $image = $this->imagecreatefromfile($source);

        $width = imagesx($image);
        $height = imagesy($image);
        $colors = array();

        $xCount = 0;
        for ($y = 0; $y < $height; $y++) {
            $y_array = array();
            for ($x = 0; $x < $width; $x++) {
                $rgba = imagecolorat($image, $x, $y);

                $red = ($rgba >> 16) & 0xFF;
                $green = ($rgba >> 8) & 0xFF;
                $blue = $rgba & 0xFF;
                $alpha = ($rgba & 0x7F000000) >> 24;
                $alpha = 255; // Not sure why but printer expects 255 not normal alpha value

                if($flat) {
                    $colors[$xCount++] = $red;
                    $colors[$xCount++] = $green;
                    $colors[$xCount++] = $blue;
                    $colors[$xCount++] = $alpha;
                }
                else {
                    $y_array[$xCount++] = $red;
                    $y_array[$xCount++] = $green;
                    $y_array[$xCount++] = $blue;
                    $y_array[$xCount++] = $alpha;
                }
            }

            if(!$flat) {
                $colors[$y] = $y_array;
            }
        }

        return [
            'data' => $colors,
            'width' => $width,
            'height' => $height
        ];
    }

    /**
     * Converts a RGBA raster array image to a mono image
     * @param $imgdata
     * @param float $halftone (1.0)
     * @param int $brightness (0 = HALFTONE_DITHER; 1 = HALFTONE_ERROR_DIFFUSION; 2 = HALFTONE_THRESHOLD)
     */
    public function toMonoImage($imgdata, $halftone, $brightness) {
        $m8 = [
            [2, 130, 34, 162, 10, 138, 42, 170],
            [194, 66, 226, 98, 202, 74, 234, 106],
            [50, 178, 18, 146, 58, 186, 26, 154],
            [242, 114, 210, 82, 250, 122, 218, 90],
            [14, 142, 46, 174, 6, 134, 38, 166],
            [206, 78, 238, 110, 198, 70, 230, 102],
            [62, 190, 30, 158, 54, 182, 22, 150],
            [254, 126, 222, 94, 246, 118, 214, 86]
        ];

        $d = $imgdata['data'];
        $w = $imgdata['width'];
        $h = $imgdata['height'];
        $r = [];
        $n = 0;
        $p = 0;
        $q = 0;
        $t = 128;
        $e = [];

        if($halftone == 1) {
            $i = $w;
            while($i--) {
                $e[] = 0;
            }
        }

        for($j = 0; $j < $h; $j++) {
            $e1 = 0;
            $e2 = 0;
            $i = 0;

            while ($i < $w) {
                $b = $i & 7;
                if($halftone == 0) {
                    $t = $m8[$j & 7][$b];
                }

                $v = pow(
                        (
                            ($d[$p++] * 0.29891 + $d[$p++] * 0.58661 + $d[$p++] * 0.11448)
                            * $d[$p] / 255 + 255 - $d[$p++]
                        ) / 255, 1 / $brightness) * 255 | 0;

                if($halftone == 1) {
                    $v += $e[$i] + $e1 >> 4;
                    $f = $v - ($v < $t ? 0 : 255);
                    if ($i > 0) {
                        $e[$i - 1] += $f;
                    }
                    $e[$i] = $f * 7 + $e2;
                    $e1 = $f * 5;
                    $e2 = $f * 3;
                }
                if ($v < $t) {
                    $n |= 128 >> $b;
                }
                $i++;
                if ($b == 7 || $i == $w) {
                    $r[$q++] = chr($n == 16 ? 32 : $n);
                    $n = 0;
                }
            }

        }

        return implode('', $r);
    }
    
    public function processImage($imagePath, $halftone = 0, $brightness = 1.0) {
        // This needs more memory for bigger images
        ini_set('memory_limit', '2048M');
        $m8 = [
            [2, 130, 34, 162, 10, 138, 42, 170],
            [194, 66, 226, 98, 202, 74, 234, 106],
            [50, 178, 18, 146, 58, 186, 26, 154],
            [242, 114, 210, 82, 250, 122, 218, 90],
            [14, 142, 46, 174, 6, 134, 38, 166],
            [206, 78, 238, 110, 198, 70, 230, 102],
            [62, 190, 30, 158, 54, 182, 22, 150],
            [254, 126, 222, 94, 246, 118, 214, 86]
        ];
        
        // Create an Imagick object
        $imagick = new \Imagick($imagePath);
        $imagick->setImageColorspace(\Imagick::COLORSPACE_RGB);
        
        $width = $imagick->getImageWidth();
        $height = $imagick->getImageHeight();
        
        $r = []; // Resulting pixel array
        $n = 0;
        $q = 0;
        $t = 128;
        $e = [];
        
        // Use a pixel iterator to process the image
        $pixelIterator = $imagick->getPixelIterator();
        
        if ($halftone == 1) {
            $e = array_fill(0, $width, 0); // Error diffusion array
        }
        
        foreach ($pixelIterator as $j => $row) {
            $e1 = 0;
            $e2 = 0;
            
            foreach ($row as $i => $pixel) {
                $b = $i & 7;
                
                // Halftone threshold from the $m8 matrix
                if ($halftone == 0) {
                    $t = $m8[$j & 7][$b];
                }
                
                // Get RGB values
                $colors = $pixel->getColor();
                $red = $colors['r'];
                $green = $colors['g'];
                $blue = $colors['b'];
                
                // Convert RGB to grayscale
                $v = pow(($red * 0.29891 + $green * 0.58661 + $blue * 0.11448) / 255, 1 / $brightness) * 255 | 0;
                
                // Apply halftone (dithering)
                if ($halftone == 1) {
                    $v += ($e[$i] + $e1) >> 4;
                    $f = $v - ($v < $t ? 0 : 255);
                    if ($i > 0) {
                        $e[$i - 1] += $f;
                    }
                    $e[$i] = $f * 7 + $e2;
                    $e1 = $f * 5;
                    $e2 = $f * 3;
                }
                
                // Determine black or white pixel
                if ($v < $t) {
                    $n |= 128 >> $b;
                }
                
                if ($b == 7 || $i == $width - 1) {
                    $r[$q++] = chr($n == 16 ? 32 : $n);
                    $n = 0;
                }
            }
        }
        
        // Finalize halftone or grayscale image
        return [
            'data'=> implode('', $r),
            'width'=> $width,
            'height'=>$height];
    }

    /**
     * @param $productsGroupByCategory
     * @return false|int|string
     */
    public static function handleDataSelect2($productsGroupByCategory)
    {
        $result = [];

        foreach ($productsGroupByCategory as $key => $category) {
            $listProduct = $category->products;
            $categoryTrans = $category->translate(app()->getLocale());

            $result[$key]['id']   = $category->id;
            $result[$key]['text'] = $categoryTrans ? $categoryTrans->name : NULL;

            $products = array();
            foreach ($listProduct as $k => $product) {
                $productTrans = $product->translate(app()->getLocale());
                $products[$k]['id']   = (string) $product->id;
                $products[$k]['text'] = $productTrans ? $productTrans->name : NULL;
            }

            $result[$key]['children'] = $products;
        }

        return json_encode($result);
    }

    /**
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public static function getNotificationCountByUser() {
        $notificationRepository = new NotificationRepository(app());
        return $notificationRepository->getNotificationByUser(request(), auth()->user()->id, true)->count();
    }

    /**
     * Count notification of user
     *
     * @return string
     */
    public static function displayNotificationNumberByUser()
    {
        $userId = auth()->user()->id;
        $request = request();
        /** @var \App\Models\Workspace|null $workspace */
        $workspace = session('workspace');
        $request = $request->merge([
            'workspace_id' => !empty($workspace) ? $workspace->id : null,
        ]);

        $notificationRepository = new NotificationRepository(app());
        $total = $notificationRepository->countNotificationByUser($request, $userId, true);

        return ($total > 9) ? (9 . '+') : ($total . '');
    }

    /**
     * Get URL with default restaurant
     *
     * @param string $url
     * @param string|null $domain
     * @param string|null $baseUrl
     * @return string
     */
    public function getUrlWithDefaultRestaurant(string $url, string $domain = null, string $baseUrl = null)
    {
        if (empty($baseUrl)) {
            // Get base URL from config
            $baseUrl = config('app.url');
        }

        if (empty($domain)) {
            // Get default restaurant URL from config
            $domain = config('app.default_restaurant_url');
        }

        // When not config
        if (empty($baseUrl) || empty($domain) || strpos($url, $domain) !== false) {
            return $url;
        }

        return (string)str_replace($baseUrl, $domain, $url);
    }

    /**
     * Get referer redirect URL
     *
     * @param string $link
     * @param string $domain
     * @param string|null $domainReferer
     * @return string|null
     */
    public function getRefererRedirectUrl(string $link, string $domain, string $domainReferer = null)
    {
        $full_link = null;

        if (!empty($domainReferer) && $domainReferer != $domain) {
            $full_link = $this->getUrlWithDefaultRestaurant($link, rtrim($domainReferer, '/'), $domain);
        }

        return $full_link;
    }

    /**
     * Get sub-domain of a workspace
     *
     * @param int|null $workspaceId
     * @return string
     */
    public function getSubDomainOfWorkspace(int $workspaceId = null)
    {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0 ? 'https://' : 'http://';
        $domain = parse_url(config('app.url'), PHP_URL_HOST);
        $slug = '';

        if (!empty($workspaceId)) {
            /** @var \App\Models\Workspace|null $workspace */
            $workspace = \App\Models\Workspace::whereId($workspaceId)
                ->active()
                ->first();

            if (!empty($workspace)) {
                $slug = $workspace->slug;
            }
        }

        if (!empty($slug)) {
            $domain = $slug . '.' . $domain;
        }

        return $protocol . $domain;
    }

    public static function formatPrice($price) {
        return number_format((float)(!empty($price) ? $price : 0), 2, '.', '');
    }

    /**
     * Get unique deeplink from base
     * We have 2 types:
     * 1) base app: appit://appoint.be/
     * 2) template: appit{token.lowerCase()}://appoint.be/
     *
     * @param string $deeplink
     * @param string $unique
     * @return string
     */
    public function getUniqueDeeplink($deeplink, $unique)
    {
        $arrDeeplink = explode(':', $deeplink);
        $result = $arrDeeplink[0];

        // Add unique string to deeplink
        $result .= $unique . ':';

        // To lower case scheme
        $result = strtolower($result);

        for ($i = 1; $i < count($arrDeeplink); $i++) {
            $result .= $arrDeeplink[$i];
        }

        return $result;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function getAppTimezone(\Illuminate\Http\Request $request)
    {
        $appToken = $request->get('Timezone');

        if (!$request->has('Timezone') && $request->hasHeader('Timezone')) {
            $appToken = $request->header('Timezone');
            $request->merge([
                'Timezone' => $appToken,
            ]);
        }

        return $appToken;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function getAppToken(\Illuminate\Http\Request $request)
    {
        $appToken = $request->get('App-Token');

        if (!$request->has('App-Token') && $request->hasHeader('App-Token')) {
            $appToken = $request->header('App-Token');
            $request->merge([
                'App-Token' => $appToken,
            ]);
        }

        return $appToken;
    }

    public function getGroupToken(\Illuminate\Http\Request $request)
    {
        $groupToken = $request->get('Group-Token');

        if (!$request->has('Group-Token') && $request->hasHeader('Group-Token')) {
            $groupToken = $request->header('Group-Token');
            $request->merge([
                'Group-Token' => $groupToken,
            ]);
        }

        return $groupToken;
    }

    /**
     * Get Workspace from App-Token
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Models\Workspace|null
     * @throws \Exception
     */
    public function getWorkspaceFromAppToken(\Illuminate\Http\Request $request)
    {
        $appToken = $this->getAppToken($request);

        /** @var \App\Models\Workspace $workspace */
        $workspace = \App\Models\Workspace::whereToken($appToken)->first();

        return $workspace;
    }

    public function getGroupRestaurantFromGroupToken(\Illuminate\Http\Request $request)
    {
        $groupToken = $this->getGroupToken($request);
        $groupRestaurant = \App\Models\GroupRestaurant::whereToken($groupToken)->first();
        return $groupRestaurant;
    }

    /**
     * Get App-Token from Workspace
     *
     * @param \App\Models\Workspace $workspace
     * @return string
     */
    public function getAppTokenFromWorkspace(Workspace $workspace)
    {
        $appToken = '';

        if (
            // When is default restaurant
            strpos(config('app.default_restaurant_url'), '//' . $workspace->slug . '.') === false
            // And enable manage app
            && (!empty($workspace->workspaceApp) && $workspace->workspaceApp->active)
        ) {
            $appToken = $workspace->token;
        }

        return $appToken;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array[]
     */
    public function getMobileConfig(\Illuminate\Http\Request $request)
    {
        $config = [
            'ios' => [
                'deeplink' => config('mobile.ios.deeplink'),
                'download' => config('mobile.ios.download'),
            ],
            'android' => [
                'deeplink' => config('mobile.android.deeplink'),
                'download' => config('mobile.android.download'),
            ],
        ];

        // Get App-Token from request
        $appToken = $this->getAppToken($request);

        if (!empty($appToken)) {
            // Deeplink
            $config['ios']['deeplink'] = $this->getUniqueDeeplink(config('mobile.ios.deeplink'), $appToken);
            $config['android']['deeplink'] = $this->getUniqueDeeplink(config('mobile.android.deeplink'), $appToken);
        }

        // Get Group-Token from request
        $groupToken = $this->getGroupToken($request);
        if (!empty($groupToken)) {
                //Deeplink
                $config['ios']['deeplink'] = $this->getUniqueDeeplink(config('mobile.ios.deeplink'), $groupToken);
                $config['android']['deeplink'] = $this->getUniqueDeeplink(config('mobile.android.deeplink'), $groupToken);
        }

        return $config;
    }

    /**
     * Build class name if has category icon
     *
     * @param $currentCategory
     * @return string
     */
    public static function buildClassIfHasIcon($currentCategory)
    {

        if (empty($currentCategory)) {
            return '';
        }

        if (!empty($currentCategory['favoriet_friet']) && !empty($currentCategory['kokette_kroket'])) {
            return 'has-both-icons';
        }

        if (!empty($currentCategory['favoriet_friet']) || !empty($currentCategory['kokette_kroket'])) {
            return 'has-icon';
        }

        return '';
    }

    public static function getIdenticalCartItemWithoutLogin($cart, $newCartItem, $randomId)
    {
        $identicalCartItems = [];
        $newCartOptionItems = $newCartItem[$randomId]['cart_option_items'];
        $newProductId = $newCartItem[$randomId]['product_id'];

        foreach ($cart->cartItems as $cartItem) {
            $oldCartOptionItems = $cartItem->cartOptionItems;

            if (count($newCartOptionItems) == 0) {
                //Product with no options
                if ($cartItem->product_id == $newCartItem[$randomId]['product_id'] && $oldCartOptionItems->count() == 0) {
                    return [$cartItem->id];
                }
            }

            if ($oldCartOptionItems->count() != count($newCartOptionItems)) {
                continue;
            }

            $i = 0;
            foreach ($newCartOptionItems as $newCartOptionItem) {
                if(gettype($newCartOptionItem) == 'array') {
                    $newOptieId = $newCartOptionItem['optie_id'];
                    $newOptieItemId = $newCartOptionItem['optie_item_id'];
                } else {
                    $newOptieId = $newCartOptionItem->optie_id;
                    $newOptieItemId = $newCartOptionItem->optie_item_id;
                }

                $cartItemIds = $oldCartOptionItems->where('optie_id', $newOptieId)
                    ->where('optie_item_id', $newOptieItemId)->where('product_id', $newProductId)->pluck('cart_item_id')->toArray();

                if ($i == 0) {
                    $identicalCartItems = $cartItemIds;
                } else {
                    $identicalCartItems = array_intersect($identicalCartItems, $cartItemIds);
                }

                $i++;
            }

            if (!empty($identicalCartItems)) {
                return $identicalCartItems;
            }
        }

        return false;
    }

    public static function getOptionsForFirebaseProjects()
    {
        $fcmProjects = config('fcm.projects');
        $options = [
            NULL => trans('strings.default')
        ];
        foreach ($fcmProjects as $key => $fcmProject) {
            $options[$key] = $fcmProject['name'];
        }

        return $options;
    }

    /**
     * Calculate coupon discount
     *
     * @param $cart
     * @param $couponId
     * @return array
     */
    public static function calculateCouponDiscount($cart, $couponId)
    {
        $coupon = Coupon::find($couponId);
        if ($coupon) {
            $couponProducts = $coupon->products->pluck('id')->toArray();

            // Apply for categories
            $couponProducts = Helper::getCategoryIds($coupon, $couponProducts);

            if (count($couponProducts) > 0) {
                $productPrices = self::calculatePriceFromCart($cart, $couponProducts);
                $discountValue = self::calculateCouponDiscountValue($coupon, $productPrices);

                return Helper::getProductDiscountValues($productPrices['vatProducts'], $productPrices['unitPricesProduct'], $discountValue);
            }
        }

        return [];
    }

    /**
     * Calculate product prices from cart
     *
     * @param $cart
     * @param array $limitProducts
     * @return array[]
     */
    public static function calculatePriceFromCart($cart, $limitProducts = [])
    {
        if (empty($cart)) {
            $workspaceSlug = Helper::getSubDomainOfRequest();
            $cart = session()->get('cart_without_login_'.$workspaceSlug);
        }

        $vatsProduct = [];
        $unitPricesProduct = [];
        if ($cart) {
            $totalPrice = 0;
            foreach ($cart->cartItems as $cartItems) {
                $totalOptions         = 0;
                $product              = $cartItems->product;
                if (!in_array($product->id, $limitProducts)) {
                    continue;
                }
                $number               = $cartItems->total_number;
                $productOptions       = $cartItems->cartOptionItems;
                $groupCartOptionItems = $productOptions->groupBy('optie_id');

                foreach ($groupCartOptionItems as $cartOptionItems) {
                    $options = collect();
                    foreach ($cartOptionItems as $cartOptionItem) {
                        $optionItem = $cartOptionItem->optionItem;
                        $options->push($optionItem);
                    }

                    $isMaster = $options
                        ->where('master', true)
                        ->first();

                    if ($isMaster) {
                        $totalOptions += $number * $isMaster->price;
                    } else {
                        $totalOptions += $number * $options->sum('price');
                    }
                }
                $totalPrice += $totalOptions + $product->price * $number;

                // Get vat of product
                $field = "take_out";
                if ($cart->type == Cart::TYPE_LEVERING) {
                    $field = "delivery";
                }

                $optionItemIds = $productOptions->pluck('optie_item_id')->all();
                $resultKey = self::convertKeyFromProductIdAndOptionItemIds($product->id, $optionItemIds);
                $vatsProduct[$resultKey] = $product->vat->{$field};

                if (!isset($unitPricesProduct[$resultKey])) {
                    $unitPricesProduct[$resultKey] = $totalOptions + $product->price * $number;
                }
            }
        }

        return [
            'vatProducts' => $vatsProduct,
            'unitPricesProduct' => $unitPricesProduct
        ];
    }

    /**
     * Calculate discount value for each product
     *
     * @param $vatProducts
     * @param $unitPricesProduct
     * @param $discountValue
     * @return array
     */
    public static function getProductDiscountValues($vatProducts, $unitPricesProduct, $discountValue)
    {
        $result = [];
        foreach ($vatProducts as $key => $vatProduct) {
            $result[$key] = (float)0;
        }

        //Sort the products by price decreasingly
        uasort($unitPricesProduct, function ($a, $b) {
            if ($a == $b) {
                return 0;
            }

            return ($a > $b)? -1: 1;
        });

        $unitPricesProduct = self::sortProductByVATAndPrice($vatProducts, $unitPricesProduct);

        //Calculate the discount for each application product
        foreach ($unitPricesProduct as $key => $priceProduct) {
            if ($discountValue == 0) {
                break;
            }

            $result[$key] = (float)$priceProduct;
            if ($priceProduct > $discountValue) {
                $result[$key] = (float)$discountValue;
            }

            $discountValue -= $result[$key];
        }

        return [
            'discountProducts' => $result,
            'totalDiscount' => array_sum($result)
        ];
    }

    /**
     * Show coupon discount in manager
     *
     * @param $coupon
     * @return string|null
     */
    public static function showCouponDiscount($coupon)
    {
        if ($coupon) {
            $discountType = $coupon->discount_type;
            switch ($discountType) {
                case Coupon::DISCOUNT_FIXED_AMOUNT:
                    return '<i class="fa fa-euro"></i>' . $coupon->discount;
                case Coupon::DISCOUNT_PERCENTAGE:
                    return $coupon->percentage . ' <i class="fa fa-percent"></i>';
            }
        }

        return NULL;
    }

    /**
     * Show redeem discount in manager
     *
     * @param $reward
     * @return string|null
     */
    public static function showRedeemDiscount($reward)
    {
        if ($reward) {
            $discountType = $reward->discount_type;
            switch ($discountType) {
                case Coupon::DISCOUNT_FIXED_AMOUNT:
                    return '<i class="fa fa-euro"></i> ' . $reward->reward;
                case Coupon::DISCOUNT_PERCENTAGE:
                    return $reward->percentage . ' <i class="fa fa-percent"></i>';
            }
        }

        return NULL;
    }

    /**
     * Calculate the coupon discount value when apply the coupon
     *
     * @param $coupon
     * @param $productPrices
     * @return float|int|mixed
     */
    public static function calculateCouponDiscountValue($coupon, $productPrices)
    {
        if ($coupon) {
            if ($coupon->discount_type == Coupon::DISCOUNT_FIXED_AMOUNT) {
                return $coupon->discount;
            } elseif ($coupon->discount_type == Coupon::DISCOUNT_PERCENTAGE) {
                $applicablePrice = self::calculateApplicablePrice($productPrices);
                return ($coupon->percentage * $applicablePrice)/100;
            }
        }

        return 0;
    }

    /**
     * Calculate redeem discount value when applying loyalty
     *
     * @param $reward
     * @param $productPrices
     * @return float|int|mixed
     */
    public static function calculateRedeemDiscountValue($reward, $productPrices)
    {
        if ($reward) {
            if ($reward->discount_type == Coupon::DISCOUNT_FIXED_AMOUNT) {
                return $reward->reward;
            } elseif ($reward->discount_type == Coupon::DISCOUNT_PERCENTAGE) {
                $applicablePrice = self::calculateApplicablePrice($productPrices);
                return ($reward->percentage * $applicablePrice)/100;
            }
        }

        return 0;
    }

    /**
     * Calculate the price of applicable product based on the productPrices
     *
     * @param $productPrices
     * @return float|int
     */
    public static function calculateApplicablePrice($productPrices)
    {
        $applicablePrice = 0;
        if (isset($productPrices['unitPricesProduct'])) {
            $applicablePrice = array_sum($productPrices['unitPricesProduct']);
        }

        return $applicablePrice;
    }

    /**
     * Calculate discount value from order
     *
     * @param $order
     * @param $discount
     * @param int $discountType
     * @return float|int|mixed
     */
    public static function calculateOriginalDiscountFromOrder($order, $discount, $discountType = Cart::COUPON)
    {
        if (empty($order)) {
            return 0;
        }

        //Limit products
        $limitProducts = [];
        switch ($discountType) {
            case Cart::COUPON:
                $limitProducts = isset($discount->products)?$discount->products->pluck('id')->toArray():[];
                break;

            case Cart::REDEEM:
                $limitProducts = isset($discount->reward->products)?$discount->reward->products->pluck('id')->toArray():[];
                break;
        }

        if (count($limitProducts) == 0) {
            return 0;
        }

        $productPrices = self::calculateProductPriceFromOrder($order, $limitProducts);
        switch ($discountType) {
            case Cart::COUPON:
                return self::calculateCouponDiscountValue($discount, $productPrices);

            case Cart::REDEEM:
                return self::calculateRedeemDiscountValue($discount->reward, $productPrices);
        }

        return 0;
    }

    /**
     * Calculate productPrices from order
     *
     * @param $order
     * @param array $limitProducts
     * @return array[]
     */
    public static function calculateProductPriceFromOrder($order, $limitProducts = [])
    {
        //Calculate product prices from order
        $vatsProduct = [];
        $unitPricesProduct = [];
        if ($order) {
            $totalPrice = 0;
            foreach ($order->orderItems as $orderItem) {
                $totalOptions         = 0;
                $product              = $orderItem->product;
                if (!in_array($product->id, $limitProducts)) {
                    continue;
                }
                $number               = $orderItem->total_number;
                $productOptions       = $orderItem->optionItems;
                $groupCartOptionItems = $productOptions->groupBy('optie_id');

                foreach ($groupCartOptionItems as $cartOptionItems) {
                    $options = collect();
                    foreach ($cartOptionItems as $cartOptionItem) {
                        $optionItem = $cartOptionItem->optionItem;
                        $options->push($optionItem);
                    }

                    $isMaster = $options
                        ->where('master', true)
                        ->first();

                    if ($isMaster) {
                        $totalOptions += $number * $isMaster->price;
                    } else {
                        $totalOptions += $number * $options->sum('price');
                    }
                }
                $totalPrice += $totalOptions + $product->price * $number;

                // Get vat of product
                $field = "take_out";
                if ($order->type == \App\Models\Order::TYPE_DELIVERY) {
                    $field = "delivery";
                }

                $optionItemIds = $productOptions->pluck('optie_item_id')->all();
                $resultKey = self::convertKeyFromProductIdAndOptionItemIds($product->id, $optionItemIds);
                $vatsProduct[$resultKey] = $product->vat->{$field};

                if (!isset($unitPricesProduct[$resultKey])) {
                    $unitPricesProduct[$resultKey] = $totalOptions + $product->price * $number;
                }
            }
        }

        return [
            'vatProducts' => $vatsProduct,
            'unitPricesProduct' => $unitPricesProduct
        ];
    }

    /**
     * Get the percentage when apply discount with percentage setting
     *
     * @param $discountId
     * @param int $discountType
     * @return \Illuminate\Support\HigherOrderCollectionProxy|int|mixed
     */
    public static function getPercentage($discountId, $discountType = Cart::COUPON)
    {
        switch ($discountType) {
            case Cart::COUPON:
                $coupon = Coupon::find($discountId);
                if (isset($coupon->discount_type) && $coupon->discount_type == Coupon::DISCOUNT_PERCENTAGE) {
                    return $coupon->percentage??0;
                }
                break;

            case Cart::REDEEM:
                $redeemHistory = RedeemHistory::find($discountId);
                if (isset($redeemHistory->reward->discount_type) && $redeemHistory->reward->discount_type == Coupon::DISCOUNT_PERCENTAGE) {
                    return $redeemHistory->reward->percentage??0;
                }
                break;

            case Cart::GROUP:
                $group = Group::find($discountId);
                if (isset($group->discount_type) && $group->discount_type == Group::PERCENTAGE) {
                    return $group->percentage??0;
                }
                break;
        }

        return 0;
    }

    /**
     * Show the title in Klantenkaart page
     *
     * @param $reward
     * @return mixed|string|null
     */
    public static function showTitleLoyalties($reward)
    {
        if (!$reward) {
            return NULL;
        }

        $title = $reward->title;
//        if ($reward->type == Reward::KORTING) {
//            switch ($reward->discount_type) {
//                case Coupon::DISCOUNT_FIXED_AMOUNT:
//                    $title =  '<i class="fa fa-euro icon"></i>' . $reward->reward;
//                    break;
//
//                case Coupon::DISCOUNT_PERCENTAGE:
//                    $title =  $reward->percentage . '<i class="fa fa-percent"></i>';
//                    break;
//            }
//
//            return trans('reward.reward_title', ['discount' => $title]);
//        }

        return $title;
    }

    /**
     * Check if there is any failed product in carts. Products which are failed should be:
     * - Not available (when product or category which contain this product is inactive)
     * - Product has options which are not existed or not satisfied min-max conditions
     *
     */
    public static function getFailedOpties($cart)
    {
        $memoryCacheService = \App\Services\MemoryCacheService::getInstance();
        $failedOpties = [];
        foreach($cart->cartItems as $item) {
            $product = $item->product;
            if (!$product) {
                continue;
            }

            $failedOpties[$product->id] = [];
            $newOtp = $optionCheck = [];

            if($memoryCacheService->get('getFailedOpties', $product->id, null, true) === null) {
                $productOptions = $product->productOptions()->with('option')->where('is_checked', 1)->get();
                $memoryCacheService->set('getFailedOpties', $product->id, $productOptions, true);
            } else {
                $productOptions = $memoryCacheService->get('getFailedOpties', $product->id, null);
            }

            $productOptionIds = $productOptions->pluck('opties_id')->toArray();
            $cartOptionItems = $item->cartOptionItems;
            if (!empty($productOptions)) {
                foreach ($productOptions as $_item) {
                    $option = $_item->option;
                    if (!empty($option)) {
                        $optionCheck[$_item->opties_id] =  [
                            'min' => $option->min,
                            'max' => $option->max,
                            'count' => 0
                        ];
                    }
                }
            }

            foreach ($cartOptionItems as $cartOptionItem) {
                $option = $cartOptionItem->option;
                //Option which is not existed in product
                if (!in_array($option->id, $productOptionIds)) {
                    array_push($failedOpties[$product->id], $option->id);
                }

                //Check if the product which has the option not satisfied the min-max condition
                if(!empty($newOtp[$cartOptionItem->optie_id])) {
                    $newOtp[$cartOptionItem->optie_id] = $newOtp[$cartOptionItem->optie_id] + 1;
                } else {
                    $newOtp[$cartOptionItem->optie_id] = 1;
                }

                $optionCheck[$cartOptionItem->optie_id]['count'] = $newOtp[$cartOptionItem->optie_id];
            }

            if (!empty($optionCheck)) {
                foreach ($optionCheck as $optieId => $_value) {
                    if (isset($_value['min']) && isset($_value['max'])) {
                        if (($_value['count'] < $_value['min']) || ($_value['count'] > $_value['max'])) {
                            array_push($failedOpties[$product->id], $optieId);
                        }
                    } else {
                        array_push($failedOpties[$product->id], $optieId);
                    }
                }
            }

            $failedOpties[$product->id] = array_unique($failedOpties[$product->id]);
        }

        foreach ($failedOpties as $productId => $failedOptie) {
            if (count($failedOptie) == 0) {
                unset($failedOpties[$productId]);
            }
        }

        return $failedOpties;
    }

    /**
     * Calculate redeem discount
     *
     * @param $cart
     * @param $redeemHistoryId
     * @return array
     */
    public static function calculateRedeemDiscount($cart, $redeemHistoryId)
    {
        $redeemHistory = RedeemHistory::find($redeemHistoryId);
        if ($redeemHistory) {
            $products = $redeemHistory->reward->products;
            $rewardProducts = RewardProduct::where(['reward_id' => $redeemHistory->reward_level_id])->get();
            $discountProducts = $rewardProducts->pluck('product_id')->toArray();

            // Apply for categories
            $discountProducts = Helper::getCategoryIds($redeemHistory->reward, $discountProducts);

            if (count($discountProducts) > 0) {
                $productPrices = self::calculatePriceFromCart($cart, $discountProducts);
                $discountValue = self::calculateRedeemDiscountValue($redeemHistory->reward, $productPrices);

                return Helper::getProductDiscountValues($productPrices['vatProducts'], $productPrices['unitPricesProduct'], $discountValue);
            }
        }

        return [];
    }

    public static function getApplicableProducts($attributes)
    {
        if (!empty($attributes['coupon_id'])) {
            $coupon = Coupon::find($attributes['coupon_id']);
            if ($coupon) {
                return $coupon->products->pluck('id')->toArray();
            }
        }

        if (!empty($attributes['redeem_history_id'])) {
            $redeemHistory = RedeemHistory::find($attributes['redeem_history_id']);
            if (isset($redeemHistory->reward)) {
                return $redeemHistory->reward->products->pluck('id')->toArray();
            }
        }

        if (empty($attributes['coupon_id'])
            && empty($attributes['redeem_history_id'])
            && !empty($attributes['group_id'])
        ) {
            $group = Group::find($attributes['group_id']);
            if ($group) {
                return GroupHelper::getLimitProducts($group);
            }
        }

        return [];
    }

    public static function isInGroupProducts($groupId, $product)
    {
        if (empty($groupId)) {
            return true;
        }

        $group = Group::find($groupId);
        if (empty($group)) {
            return true;
        }

        $groupProducts = $group->getProducts();
        $groupProductIds = array_column($groupProducts, 'id');
        if (in_array($product->id, $groupProductIds)) {
            return true;
        }

        return false;
    }

    /**
     * Convert to the timezone of this order and separate to date and time
     *
     * @param $datetime
     * @param $timezone
     * @return array
     */
    public static function separateDateTime($datetime, $timezone)
    {
        $systemTimezone = config('app.timezone');
        $fixedTimezone = config('app.timezone_fixed');
        if (!empty($fixedTimezone)) {
            $timezone = $fixedTimezone;
        }

        $datetime = Carbon::parse($datetime, $systemTimezone)
            ->tz($timezone)
            ->toDateTimeString();

        $arrDateTime = explode(' ', $datetime);

        return [
            'date' => $arrDateTime[0],
            'time' => $arrDateTime[1]
        ];
    }

    /**
     * Get the number of order for a timeslot
     *
     * @param SettingTimeslotDetail $time
     */
    public static function calculateTimeslotOrderNo(SettingTimeslotDetail $time)
    {
        $numberOfOrder = 0;

        if (!empty($time)) {
            $numberOfOrder += $time->orders->filter(function($value, $key) {
                return ($value->status == \App\Models\Order::PAYMENT_STATUS_PAID
                    || in_array($value->payment_method, [\App\Models\SettingPayment::TYPE_CASH, \App\Models\SettingPayment::TYPE_FACTUUR]));
            })->count();
        }

        return $numberOfOrder;
    }

    /**
     * Convert day_number to Carbon date in current week and next week
     *
     * @param $dayNumber
     * @return array
     */
    public static function convertWeekdayToDate($dayNumber)
    {
        $dayNumbers = config('common.week_days');
        if (new Carbon('this ' . $dayNumbers[$dayNumber]) == new Carbon('next ' . $dayNumbers[$dayNumber])) {
            //This date in current week is in past
            return [
                new Carbon('this ' . $dayNumbers[$dayNumber]),
                new Carbon('second ' . $dayNumbers[$dayNumber]),
                new Carbon('third ' . $dayNumbers[$dayNumber]),
                (new Carbon('this ' . $dayNumbers[$dayNumber]))->subDay(1),
                (new Carbon('second ' . $dayNumbers[$dayNumber]))->subDay(1),
                (new Carbon('third ' . $dayNumbers[$dayNumber]))->subDay(1),
            ];
        } else {
            //This date in current week is today or in future
            return [
                new Carbon('this ' . $dayNumbers[$dayNumber]),
                new Carbon('next ' . $dayNumbers[$dayNumber]),
                new Carbon('second ' . $dayNumbers[$dayNumber]),
                (new Carbon('this ' . $dayNumbers[$dayNumber]))->subDay(1),
                (new Carbon('next ' . $dayNumbers[$dayNumber]))->subDay(1),
                (new Carbon('second ' . $dayNumbers[$dayNumber]))->subDay(1),
            ];
        }
    }

    /**
     * Sort list products by VAT ASC and price DESC
     *
     * @param $vatProducts
     * @param $priceProducts
     */
    public static function sortProductByVATAndPrice($vatProducts, $priceProducts)
    {
        $products = $result = [];

        foreach ($vatProducts as $productKey => $vatProduct) {
            $products[$productKey]['vat'] = $vatProduct;
            $products[$productKey]['price'] = $priceProducts[$productKey];
        }

        uasort($products, function ($a, $b) {
            if ($a['vat'] == $b['vat']) {
                if ($a['price'] < $b['price']) {
                    return 1;
                }
            }

            // sort the higher score first:
            return $a['vat'] > $b['vat'] ? 1 : -1;
        });

        foreach ($products as $productKey => $product) {
            $result[$productKey] = $product['price'];
        }

        return $result;
    }

    /**
     * Strip HTML/CSS
     *
     * @param string $input
     * @return string
     * @throws \Exception
     */
    public static function stripHTMLCSS($input) {
        // Fallback: to make sure the code will never fail
        if(!class_exists('HTMLPurifier')) {
            return strip_tags($input);
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.DefinitionImpl', null);
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('HTML.Doctype', 'HTML 4.01 Transitional');
        $config->set('HTML.Allowed', '');

        $filter = new HTMLPurifier($config);

        // Do actual clean up
        return trim(
            preg_replace('/[\s]{2,}/', "\r\n",
                trim(
                    preg_replace(array('/[\h]+/', '/[\n\r]+/'), ' ',
                        $filter->purify($input)
                    )
                )
            )
        );
    }

    /**
     * @param $string
     * @param $character
     * @return bool
     */
    public static function checkSpecialCharacters($string, $character) {
        $pattern = '/'.$character.'/';

        if (preg_match($pattern, $string)){
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param null $host
     * @return false|mixed|string
     */
    public static function getSubDomainOfRequest($host = null) {
        if (empty($host)) {
            $host = request()->getHost();
        }

        $primaryDomain = parse_url(config('app.url'), PHP_URL_HOST);
        $workspaceSlug = str_replace($primaryDomain, '', $host);
        $workspaceSlug = substr($workspaceSlug, 0, -1);

        return $workspaceSlug;
    }

    public static function printerGroupAvaiWorkspaces($restaurants, $exists) {
        if(!empty($exists)) {
            foreach ($exists as $id => $name) {
                $restaurants[$id] = $name;
            }
        }

        return $restaurants;
    }

    /**
     * @param $model
     * @param array $currentIds
     * @return array
     */
    public static function getCategoryIds($model, $currentIds = []) {
        $categoryIds = $model->categoriesRelation->pluck('category_id')->toArray();
        $productIds = Product::whereIn('category_id', $categoryIds)
            ->where('active', 1)
            ->pluck('id')->all();

        $limitProducts = array_unique(array_merge($currentIds, $productIds));

        return $limitProducts;
    }

    /**
     * Get base storage URL
     *
     * @param null $appUrl
     * @return string
     */
    public function getBaseStorageUrl($appUrl = null)
    {
        if ($appUrl === null) {
            $appUrl = trim(config('app.url'), '/');
        }

        return $appUrl . '/storage/';
    }

    /**
     * Get Storage path from URL
     *
     * @param string $storageUrlFile
     * @return string
     */
    public function getStoragePathFromUrl(string $storageUrlFile)
    {
        $storageUrl = $this->getBaseStorageUrl();

        return (string)str_replace($storageUrl, '', $storageUrlFile);
    }

    public static function convertKeyFromProductIdAndOptionItemIds($productId, $optionItemIds) {
        $optionItemKey = implode('_', $optionItemIds);
        return 'productId_' . $productId . '-optionItemIds_' . $optionItemKey;
    }

    public static function getProductIdsFromKey($keys) {
        $ids = [];

        if(!empty($keys)) {
            foreach ($keys as $key) {
                if(!str_contains($key, 'optionItemIds')) {
                    $ids[] = (int)$key;
                } else {
                    $split = explode('-', $key);
                    $productSplit = explode('_', $split[0]);
                    $ids[] = (int)$productSplit[1];
                }
            }
        }

        return array_unique($ids);
    }

    /**
     * Get milliseconds string
     *
     * @param mixed $microtime
     * @return string
     */
    public function getMillisecondsString($microtime = null)
    {
        if ($microtime === null) {
            $microtime = microtime();
        }

        $chunks = explode(' ', $microtime);
        return sprintf('%d%d', $chunks[1], $chunks[0] * 1000);
    }

    /**
     * Generate a fake email
     *
     * @param string $email
     * @return string
     */
    public function generateFakeEmail(string $email)
    {
        $email = explode('@', $email);
        $email[0] = $email[0] . '_' . $this->getMillisecondsString();
        $email = implode('@', $email);

        return $email;
    }
}
