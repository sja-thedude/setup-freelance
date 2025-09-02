<?php

namespace Tests\Unit;

use App\Helpers\Helper;
use App\Jobs\SendEmailReminderOrder;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\SettingDeliveryConditions;
use App\Models\SettingPreference;
use App\Models\User;
use App\Models\Vat;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use TestCase;

class SendEmailReminderOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_send_email()
    {
        $user = factory(User::class)->create(['email' => 'tester@example.com']);
        $workspace = Workspace::create([
            'user_id' => $user->id,
            'email' => 'test@example.com',
        ]);
        $pref = SettingPreference::create(['workspace_id' => $workspace->id, 'takeout_min_time' => 10, 'mins_before_notify' => 2]);
        $delivery = SettingDeliveryConditions::create(['workspace_id' => $workspace->id, 'area_start' => 0, 'area_end' => 10, 'price_min' => 0, 'price' => 3.4, 'fee' => 12.34]);
        $order = Order::create([
            'workspace_id' => $workspace->id,
            'total_price' => 100000,
            'user_id' => $user->id,
            'date_time' => now(),
            'date' => now()->addDay()->format('Y-m-d'),
            'time' => now()->addDay()->format('H:i'),
            'status' => Order::PAYMENT_STATUS_PAID,
            'timezone' => 'Asia/Ho_Chi_Minh',
            'code' => '013',
            'extra_code' => 1,
        ]);
        $category = Category::create(['workspace_id' => $workspace->id]);
        $vat = Vat::create(['name' => 'Test', 'in_house' => 12, 'take_out' => 6, 'delivery' => 6]);
        $categoryLang = CategoryTranslation::create(['category_id' => $category->id, 'locale' => 'nl', 'name' => 'Special Milk tea with various types']);
        $product = Product::create(['category_id' => $category->id, 'workspace_id' => $workspace->id, 'vat_id' => $vat->id, 'price' => 20000]);
        ProductTranslation::create(['product_id' => $product->id, 'name' => 'Some product', 'locale' => 'nl']);
        ProductTranslation::create(['product_id' => $product->id, 'name' => 'Some product', 'locale' => 'en']);
        $item = OrderItem::create([
            'order_id' => $order->id, 'workspace_id' => $workspace->id, 'category_id' => $category->id,
            'product_id' => $product->id, 'type' => 0,
            'metas' => json_encode([
                'category' => $product->category,
                'product' => $product,
            ])
        ]);
        SendEmailReminderOrder::dispatch($user, $this->getDataContent($workspace, $order, $user));
        $this->assertTrue(true);
    }

    private function getDataContent($workspace, $order, $user) {
        $dateTimeLocal      = Helper::convertDateTimeToTimezone($order->date . " " . $order->time, $order->timezone);
        $dateTimeLocalParse = Carbon::parse($dateTimeLocal);
        $timeLocal          = $dateTimeLocalParse->format("H:i");
        $codeId = "#" . ($order->group_id ? "G" . $order->parent_code : $order->code) . (!empty($order->group_id) && !empty($order->extra_code) ? '-' . $order->extra_code : '');
        $dataContent = [
            'isSendMail'            => TRUE,
            'isDeleveringAvailable' => TRUE,
            'isDeleveringPriceMin'  => TRUE,
            'cart'                  => $order,
            'listItem'              => $order->orderItems,
            'conditionDelevering'   => $workspace->settingDeliveryConditions->first(),
            'totalPrice'            => 0,
            'content1'              => trans('mail.reminder.content1'),
            'content4'              => trans('mail.reminder.content4'),
            'content5'              => trans('mail.reminder.content5'),
            'content6'              => trans('cart.totaal'),
            'content7'              => trans('cart.success_betaalstatus'),
            'content8'              => trans('cart.success_betaalmethode'),
            'content9'              => trans('cart.success_opmerkingen'),
            'content10'             => trans('cart.groep'),
            'content11'             => trans('cart.levering_op_adres'),
            'content2'              => trans('mail.reminder.content2', [
                'first_name' => $user->first_name,
            ]),
            'content3'              => trans('mail.reminder.content3', [
                'restaurant' => $workspace->name,
                'order_id'   => $codeId,
                'time'       => $timeLocal,
            ]),
            'content12'             => trans('mail.reminder.content12', [
                'restaurant' => $workspace->name,
                'order_id'   => $codeId,
                'time'       => $timeLocal,
            ]),
        ];
        
        //Is test account
        $dataContent['content_note'] = "";
        if($order->is_test_account) {
            $dataContent['content2'] = trans('mail.reminder.content2', [
                'first_name' => strtoupper(trans('strings.admin')),
            ]);
            $dataContent['content_note'] = trans('mail.reminder.content_note');
        }

        return $dataContent;
    }
}
