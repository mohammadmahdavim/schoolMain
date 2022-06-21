<?php

namespace App\Http\Middleware;

use App\Finanace;
use App\Setting;
use Closure;

class FinanceCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $setting = Setting::where('id', 1)->first();
        if ($setting->finance_status == 0 or $setting->finance_deadline == '') {
            return $next($request);
        } else {
            $status = $this->checkDate($setting->finance_deadline);
            if ($status == 'not_expire') {
                return $next($request);
            } else {
                $status = $this->checkFinance();
                if ($status == 'true') {
                    return $next($request);
                } else {
                    alert()->error('ابتدا شهریه خود را پرداخت کنید.', 'ورود ناموفق');
                    return redirect('/student/finance');
                }
            }
        }
    }

    public function checkDate($date)
    {
        $date = explode('/', $date);
        $toGregorian = \Morilog\Jalali\CalendarUtils::toGregorian($date[0], $date[1], $date[2]);
        $gregorian = implode('-', $toGregorian) . ' ' . '23:59:59';
        $dateEx = \Morilog\Jalali\Jalalian::forge("$gregorian")->getTimestamp();
        $nowTimestamp = \Morilog\Jalali\Jalalian::forge("now")->getTimestamp();
        if ($dateEx >= $nowTimestamp) {
            return 'not_expire';
        } else {
            return 'expire ';
        }
    }

    public function checkFinance()
    {
        $user = auth()->user()->id;
        $finance = Finanace::where('user_id', $user)->first();
        if ($finance) {
            if ($finance->remaining == 0 or $finance->status == 1) {
                return 'true';
            } else {
                return 'false';
            }
        } else {
            return 'true';

        }

    }
}
