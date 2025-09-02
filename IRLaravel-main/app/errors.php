<?php

// Error codes
// Increment for all keys

// Invalid coupon by code
define('ERROR_COUPON_CODE_INVALID', 1);
// Coupon is expired
define('ERROR_COUPON_EXPIRED', 2);
// Max time used coupon for all users
define('ERROR_COUPON_MAX_TIME_ALL', 3);
// Max time used coupon for single user
define('ERROR_COUPON_MAX_TIME_SINGLE', 4);
// Reward is expired
define('ERROR_REWARD_REWARD_EXPIRED', 5);
// Loyalty already redeem
define('ERROR_LOYALTY_ALREADY_REDEEM', 6);
// Order invalid timeslot
define('ERROR_ORDER_INVALID_TIMESLOT', 7);
// Order overdue cutoff time
define('ERROR_ORDER_OVERDUE_CUTOFF_TIME', 8);
//Order with date = null
define('ERROR_ORDER_ZERO_TIMESLOT', 9);
