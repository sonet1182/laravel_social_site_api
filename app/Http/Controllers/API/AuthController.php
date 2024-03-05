<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'password' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ]);
        } else {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken($user->email . '_Token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'api_token' => $token,
                'data' => $user,
                'message' => 'You have registered Successfully',
            ]);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ]);
        } else {



            $user = User::where([['email', $request->email]])->orWhere([['phone', $request->email]])->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid Credantials',
                ]);
            } else {
                $token = $user->createToken($user->email . '_Token')->plainTextToken;

                return response()->json([
                    'status' => 200,
                    'api_token' => $token,
                    'data' => $user,
                    'message' => 'Logged In Successfully',
                ]);
            }
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Logged Out Successfully',
            ]);
        } catch (\Throwable $e) {
            return $this->getError($e);
        }
    }

    public function reset_password_req(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ]);
        } else {
            $user = User::where([['phone', $request->phone]])->first();

            if (!$user) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid Credantials',
                ]);
            } else {
                $user->otp = rand(1000, 9999);
                $user->update();
                $message = "To recover your password use this otp: " . $user->otp . " - Tutor Sheba";

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'api_key' => '2dYuSnQNkQUUahkgg5f4EkMC414CIm0Kp7H0m1qH',
                        'msg' => $message,
                        'to' => $user->phone
                    ),
                ));
                $response = curl_exec($curl);

                curl_close($curl);


                return response()->json([
                    'status' => 200,
                    'data' => $user,
                    'message' => 'OTP has been send to -' . $user->phone,
                ]);
            }
        }
    }


    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ]);
        } else {
            $user = User::with('teacher', 'verification')->orWhere([['phone', $request->phone]])->first();
            $user->password = Hash::make($request->password);
            $user->update();


            $token = $user->createToken($user->email . '_Token')->plainTextToken;

            return response()->json([
                'status' => 200,
                'api_token' => $token,
                'data' => $user,
                'message' => 'Logged In Successfully',
            ]);
        }
    }

    public function update_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'validation_errors' => $validator->errors(),
            ]);
        } else {
            $user = User::find(auth('sanctum')->user()->id);

            if (Hash::check($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->update();

                return response()->json([
                    'status' => 200,
                    'message' => 'Password Updated Successfully!',
                ]);
            } else {
                return response()->json([
                    'status' => 202,
                    'message' => 'Yor OLd Password is Incorrect!',
                ]);
            }
        }
    }
}
