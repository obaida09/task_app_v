<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\GiftCard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DailyCardLimitMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $dailyCardLimit = 3;

        $todayCardsCount = $user->giftCards()->whereDate('used_at', Carbon::today())->count();


        if ($todayCardsCount >= $dailyCardLimit) {
            return response()->json(['message' => 'You have reached the daily gift card limit.'], 403);
        }
        return $next($request);
    }
}
