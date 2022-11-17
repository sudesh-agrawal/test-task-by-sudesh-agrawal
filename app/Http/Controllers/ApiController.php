<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Validator;
use App\Models\User;

class ApiController extends Controller
{

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->messages()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $login_token = base64_encode($request->email.':'.$request->password);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'wallets' => 0.00,
                'login_token' => $login_token
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'User created successfully'
            ], Response::HTTP_OK);
        }catch(\Exception $e) {
            Log::error($e);

            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->messages()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $login_token = base64_encode($request->email.':'.$request->password);
            $user = User::where('login_token', $login_token)->first();

            if($user){
                return response()->json([
                    'status' => 200,
                    'message' => 'User authentication complete',
                    'data' => $user->only(['id', 'name', 'email', 'login_token'])
                ], Response::HTTP_OK);
            }else{
                return response()->json([
                    'status' => 404,
                    'message' => 'Invalid login credentials'
                ], Response::HTTP_NOT_FOUND);
            }

        }catch(\Exception $e) {
            Log::error($e);

            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function addMoney(Request $request){
        $message = [
            'regex' => 'Invalid :attribute format. Please anter :attribute in this format (0, 0.00).'
        ];
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/'
        ], $message);

        

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->messages()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            if($request->amount < 3 || $request->amount > 100){
                return response()->json([
                    'status' => 400,
                    'error' => array(
                        'amount' => "You can add a minimum of $3 and a maximum of $100 to their wallet in a single operation"
                    )
                ], Response::HTTP_BAD_REQUEST);
            }
        
            $user_id = $request['user']['id'];
            $wallet_amount = $request['user']['wallets'];
            $add_amount = $request->amount;
            $total_amount = $wallet_amount + $add_amount;

            User::find($user_id)->update([
                'wallets' => $total_amount
            ]);

            return response()->json([
                'status' => 200,
                'total_amount' => number_format($total_amount, 2)
            ], Response::HTTP_OK);
        }catch(\Exception $e) {
            Log::error($e);

            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function buyCookie(Request $request){
        $validator = Validator::make($request->all(), [
            "quantity" => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->messages()
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user_id = $request['user']['id'];
            $paid_amount = $request->quantity * 1;
            $wallet_amount = $request['user']['wallets'];

            if($paid_amount > $wallet_amount){
                return response()->json([
                    'status' => 400,
                    'message' => 'Insufficient amount in your wallet. Please add money in your wallet.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $remaining_amount = $wallet_amount - $paid_amount;
            User::find($user_id)->update([
                'wallets' => $remaining_amount
            ]);

            return response()->json([
                'status' => 200,
                'message' => "Thanks for the purchase.",
                'paid_amount' => number_format($paid_amount, 2),
                'remaining_amount' => number_format($remaining_amount, 2)
            ], Response::HTTP_OK);
        }catch(\Exception $e) {
            Log::error($e);

            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
