<?php

namespace App\Http\Controllers\Rider;

use App\{
    Models\Rider,
    Models\Withdraw,
    Models\Currency
};
use App\Http\Controllers\Rider\RiderBaseController;
use Illuminate\Http\Request;

class WithdrawController extends RiderBaseController
{

    public function index()
    {
        $withdraws = Withdraw::where('user_id', '=', $this->rider->id)->where('type', '=', 'rider')->latest('id')->paginate(12);
        $sign = Currency::where('is_default', '=', 1)->first();
        return view('rider.withdraw.index', compact('withdraws', 'sign'));
    }

    public function create()
    {
        $sign = Currency::where('is_default', '=', 1)->first();
        return view('rider.withdraw.withdraw', compact('sign'));
    }


    public function store(Request $request)
    {

        $from = Rider::findOrFail($this->rider->id);

        $withdrawcharge = $this->gs;
        $charge = $withdrawcharge->withdraw_fee;

        if ($request->amount > 0) {

            $amount = $request->amount;

            if ($from->balance >= $amount) {
                $fee = (($withdrawcharge->withdraw_charge / 100) * $amount) + $charge;
                $finalamount = $amount - $fee;
                if($finalamount < 0){
                  return back()->with('error', __('You can not withdraw this amount.'));
                }


                if ($from->balance >= $finalamount) {
                    $finalamount = number_format((float)$finalamount, 2, '.', '');

                    $from->balance = $from->balance - $amount;
                    $from->update();

                    $newwithdraw = new Withdraw();
                    $newwithdraw['user_id'] = $this->rider->id;
                    $newwithdraw['method'] = $request->methods;
                    $newwithdraw['acc_email'] = $request->acc_email;
                    $newwithdraw['iban'] = $request->iban;
                    $newwithdraw['country'] = $request->acc_country;
                    $newwithdraw['acc_name'] = $request->acc_name;
                    $newwithdraw['address'] = $request->address;
                    $newwithdraw['swift'] = $request->swift;
                    $newwithdraw['reference'] = $request->reference;
                    $newwithdraw['amount'] = $finalamount;
                    $newwithdraw['fee'] = $fee;
                    $newwithdraw['type'] = 'rider';
                    $newwithdraw->save();

                    return back()->with('success', __('Withdraw Request Sent Successfully.'));
                } else {
                    return back()->with('error', __('Insufficient Balance.'));
                }
            } else {
                return back()->with('error', __('Insufficient Balance.'));;
            }
        }
        return back()->with('error',  __('Please enter a valid amount.'));
    }
}
