<?php

namespace App\Http\Controllers\Payment\Checkout;

use App\{
    Models\Cart,
    Models\Order,
    Classes\GeniusMailer,
    Models\PaymentGateway
};
use App\Models\Country;
use App\Models\Reward;
use App\Models\State;
use Mollie\Laravel\Facades\Mollie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use OrderHelper;
use Illuminate\Support\Str;

class MollieController extends CheckoutBaseControlller
{
    public function store(Request $request)
    {

        $input = $request->all();
        $step1 = Session::get('step1');
        $step2 = Session::get('step2');
        $input = array_merge($step1, $step2, $input);
        $data = PaymentGateway::whereKeyword('mollie')->first();
        $total = $request->total;

        $available_currency = OrderHelper::mollie_currencies();

        if (!in_array($this->curr->name, $available_currency)) {
            return redirect()->back()->with('unsuccess', __('Invalid Currency For Molly Payment.'));
        }

        if ($request->pass_check) {
            $auth = OrderHelper::auth_check($input); // For Authentication Checking
            if (!$auth['auth_success']) {
                return redirect()->back()->with('unsuccess', $auth['error_message']);
            }
        }

        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success', __("You don't have any product to checkout."));
        }

        $order['item_name'] = $this->gs->title . " Order";
        $order['item_number'] = Str::random(4) . time();
        $order['item_amount'] = $total;
        $notify_url = route('front.molly.notify');

        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => $this->curr->name,
                'value' => '' . sprintf('%0.2f', $order['item_amount']) . '', // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => $order['item_name'],
            'redirectUrl' => $notify_url,
        ]);
        /** add payment ID to session **/
        Session::put('input_data', $input);
        Session::put('order_data', $order);
        Session::put('order_payment_id', $payment->id);

        $payment = Mollie::api()->payments()->get($payment->id);

        return redirect($payment->getCheckoutUrl(), 303);
    }

    public function notify(Request $request)
    {
        $input = Session::get('input_data');
        $order_data = Session::get('order_data');
        $success_url = route('front.payment.return');
        $cancel_url = route('front.payment.cancle');
        $input_data = $request->all();
        /** Get the payment ID before session clear **/

        $payment = Mollie::api()->payments()->get(Session::get('order_payment_id'));
        if ($payment->status == 'paid') {

            $oldCart = Session::get('cart');
            $cart = new Cart($oldCart);
            OrderHelper::license_check($cart); // For License Checking
            $t_oldCart = Session::get('cart');
            $t_cart = new Cart($t_oldCart);
            $new_cart = [];
            $new_cart['totalQty'] = $t_cart->totalQty;
            $new_cart['totalPrice'] = $t_cart->totalPrice;
            $new_cart['items'] = $t_cart->items;
            $new_cart = json_encode($new_cart);
            $temp_affilate_users = OrderHelper::product_affilate_check($cart); // For Product Based Affilate Checking
            $affilate_users = $temp_affilate_users == null ? null : json_encode($temp_affilate_users);

            $order = new Order;
            $input['cart'] = $new_cart;
            $input['user_id'] = Auth::check() ? Auth::user()->id : NULL;
            $input['affilate_users'] = $affilate_users;
            $input['pay_amount'] = $order_data['item_amount'] / $this->curr->value;
            $input['order_number'] = $order_data['item_number'];
            $input['wallet_price'] = $input['wallet_price'] / $this->curr->value;
            $input['payment_status'] = "Completed";
            $input['txnid'] = $payment->id;

            if ($input['tax_type'] == 'state_tax') {
                $input['tax_location'] = State::findOrFail($input['tax'])->state;
            } else {
                $input['tax_location'] = Country::findOrFail($input['tax'])->country_name;
            }
            $input['tax'] = Session::get('current_tax');


            if ($input['dp'] == 1) {
                $input['status'] = 'completed';
            }
            if (Session::has('affilate')) {
                $val = $request->total / $this->curr->value;
                $val = $val / 100;
                $sub = $val * $this->gs->affilate_charge;
                if ($temp_affilate_users != null) {
                    $t_sub = 0;
                    foreach ($temp_affilate_users as $t_cost) {
                        $t_sub += $t_cost['charge'];
                    }
                    $sub = $sub - $t_sub;
                }
                if ($sub > 0) {
                    $user = OrderHelper::affilate_check(Session::get('affilate'), $sub, $input['dp']); // For Affiliate Checking
                    $input['affilate_user'] = Session::get('affilate');
                    $input['affilate_charge'] = $sub;
                }
            }

            $order->fill($input)->save();
            $order->tracks()->create(['title' => 'Pending', 'text' => 'You have successfully placed your order.']);
            $order->notifications()->create();

            if ($input['coupon_id'] != "") {
                OrderHelper::coupon_check($input['coupon_id']); // For Coupon Checking
            }


            if (Auth::check()) {
                if ($this->gs->is_reward == 1) {
                    $num = $order->pay_amount;
                    $rewards = Reward::get();
                    foreach ($rewards as $i) {
                        $smallest[$i->order_amount] = abs($i->order_amount - $num);
                    }

                    if (isset($smallest)) {
                        asort($smallest);
                        $final_reword = Reward::where('order_amount', key($smallest))->first();
                        Auth::user()->update(['reward' => (Auth::user()->reward + $final_reword->reward)]);
                    }
                }
            }

            OrderHelper::size_qty_check($cart); // For Size Quantiy Checking
            OrderHelper::stock_check($cart); // For Stock Checking
            OrderHelper::vendor_order_check($cart, $order); // For Vendor Order Checking

            Session::put('temporder', $order);
            Session::put('tempcart', $cart);
            Session::forget('cart');
            Session::forget('already');
            Session::forget('coupon');
            Session::forget('coupon_total');
            Session::forget('coupon_total1');
            Session::forget('coupon_percentage');

            if ($order->user_id != 0 && $order->wallet_price != 0) {
                OrderHelper::add_to_transaction($order, $order->wallet_price); // Store To Transactions
            }

            //Sending Email To Buyer
            $data = [
                'to' => $order->customer_email,
                'type' => "new_order",
                'cname' => $order->customer_name,
                'oamount' => "",
                'aname' => "",
                'aemail' => "",
                'wtitle' => "",
                'onumber' => $order->order_number,
            ];

            $mailer = new GeniusMailer();
            $mailer->sendAutoOrderMail($data, $order->id);

            //Sending Email To Admin
            $data = [
                'to' => $this->ps->contact_email,
                'subject' => "New Order Recieved!!",
                'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is " . $order->order_number . ".Please login to your panel to check. <br>Thank you.",
            ];
            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);

            return redirect($success_url);
        }
        return redirect($cancel_url);
    }
}
