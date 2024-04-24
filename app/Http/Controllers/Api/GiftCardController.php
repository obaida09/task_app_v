<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GiftCard;
use App\Models\UserWallet;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;
use App\Http\Requests\GiftCardRequest;
use App\Http\Resources\Api\GiftCardResource;

class GiftCardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
       $this->middleware('CheckRole:admin')->only('store', 'update', 'destroy');
    }

    public function index(Request $request)
    {
        // Setting permissions for ( user, admin )
        if (auth()->user()->role == 'admin' ) {
            $giftCard = GiftCard::with('user');
        }else {
            $giftCard = GiftCard::whereUserId(auth()->user()->id);
        }
        // Filter for if used Card or not
        if(isset($request->isUsed) && auth()->user()->role == 'admin') {
            $request->isUsed ? $giftCard->whereStatus(false) : $giftCard->whereStatus(true);
        }
        
        return GiftCardResource::collection($giftCard->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GiftCardRequest $request)
    {
        $data = $request->validated();
        GiftCard::create($data);
        return response()->json([
            'message' => 'Gift Card Successfully Created',
            'giftCard' => $data,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $giftCard = GiftCard::whereId($id)->with('user')->first();
        // Preventing the user from accessing a card that je does not own
        if (auth()->user()->role != 'admin' && auth()->user()->id != $giftCard->user_id ) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return response()->json([
            'message' => 'Gift Card Successfully get',
            'giftCard' => GiftCardResource::make($giftCard),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(GiftCardRequest $request, GiftCard $giftCard)
    {
        $data = $request->validated();
        $giftCard->update($data);
        return response()->json([
            'message' => 'Gift Card Successfully Updated',
            'giftCard' => $data,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GiftCard $giftCard)
    {
        $giftCard->delete();
        return response()->json([
            'message' => 'Gift Card Successfully Deleted'
        ], 400);
    }

    public function useGiftCard(Request $request)
    {
        $giftCard = GiftCard::where('card_number', $request->giftCardNumber)->first();
        if($giftCard == null) {
            return response()->json(['message' => 'Gift Card Not Found'], 404);
        }
        if(!$giftCard->status) {
            return response()->json(['message' => 'Gift Card Expire'], 400);
        }
        $wallet = UserWallet::whereUserId(auth()->user()->id)->first();
        $wallet->updateBalance($giftCard->value);
        $giftCard->update([
            'user_id' => auth()->user()->id,
            'status' => 0,
            'used_at' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Gift Card Successfully',
        ], 400);
    }
}
