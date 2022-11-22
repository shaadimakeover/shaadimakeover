<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class AuthController extends BaseController
{

    /**
     * @OA\Post(
     * path="/api/social-login",
     * operationId="Social Login",
     * tags={"Auth Management"},
     * summary="Social login",
     * description="Social login here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name","email","provider","provider_id"},
     *               @OA\Property(property="name", type="text"),
     *               @OA\Property(property="email", type="text"),
     *               @OA\Property(property="phone", type="integer"),
     *               @OA\Property(property="avatar", type="file"),
     *               @OA\Property(property="provider", type="text"),
     *               @OA\Property(property="provider_id", type="text")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Social login successfully done",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function socialLogin(Request $request)
    {
        $validator  =   Validator::make($request->all(), [
            "name"  =>  "required",
            "email"  =>  "required|email|unique:users",
            "phone"  =>  "nullable|unique:users",
            "avatar" => "nullable",
            "provider"  =>  "required",
            "provider_id"  =>  "required"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $name = $request->name;
            $splitName = explode(' ', $name, 2); // Restricts it to only 2 values, for names like Billy Bob Jones
            $first_name = $splitName[0];
            $last_name = !empty($splitName[1]) ? $splitName[1] : ''; // If last name doesn't exist, make it empty

            $userCreated = User::firstOrCreate(
                [
                    'email' => $request->email
                ],
                [
                    'email_verified_at' => now(),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'active' => true,
                ]
            );

            $userCreated->providers()->updateOrCreate(
                [
                    'provider' => $request->provider,
                    'provider_id' => $request->provider,
                ],
                [
                    'avatar' => $request->avatar
                ]
            );

            $userCreated->assignRole('USER');

            DB::commit();

            $token = $userCreated->createToken('my-assam-token')->plainTextToken;

            $response = [
                'success' => true,
                'data'    => $userCreated,
                'token'    => $token,
                'message' => 'Login successfully.',
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: SOCIAL LOGIN EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/login",
     * operationId="Login",
     * tags={"Auth Management"},
     * summary="login",
     * description="login here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"phone_number","password"},
     *               @OA\Property(property="phone_number", type="string"),
     *               @OA\Property(property="password", type="string")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Social login successfully done",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required_without:phone_number', 'email:rfc,dns', 'exists:users,email'],
            'phone_number' => ['required_without:email', 'string', 'exists:users,phone'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        try {
            if ($request->email) {
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $getUser = User::where('email', $request->email)->first();
                    Auth::login($getUser, true);
                    $token = $getUser->createToken('MyApp')->plainTextToken;

                    return $this->sendResponse($token, 'login successfully');
                } else {
                    return $this->sendError('Invalid Credential.');
                }
            } else {

                if (Auth::attempt(['phone' => $request->phone_number, 'password' => $request->password])) {
                    $getUser = User::where('phone', $request->phone_number)->first();
                    Auth::login($getUser, true);
                    $token = $getUser->createToken('MyApp')->plainTextToken;

                    $token = $getUser->createToken('MyApp')->plainTextToken;

                    $response = [
                        'success' => true,
                        'data'    => $getUser,
                        'token'    => $token,
                        'message' => 'OTP verified successfully',
                    ];
                    return response()->json($response, 200);
                    //return $this->sendResponse($token, 'login successfully');
                } else {
                    return $this->sendError('Invalid Credential.');
                }
            }
        } catch (\Throwable $th) {
            Log::error(" :: GET OTP EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/register",
     * operationId="Register",
     * tags={"Auth Management"},
     * summary="Register with mobile or email otp verify",
     * description="This api for send otp in mobile or email. If you send email in body that time no need to send phone number, and if send otp in mobile sms that time no need to send email in body param.",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"first_name","last_name","phone_number","location","user_type","password","confirm_password"},
     *               @OA\Property(property="first_name", type="string" ,description="First name"),
     *               @OA\Property(property="last_name", type="string" ,description="Last name"),
     *               @OA\Property(property="phone_number", type="string" ,description="Phone number"),
     *               @OA\Property(property="location", type="string" ,description="Location"),
     *               @OA\Property(property="password", type="string" ,description="Password"),
     *               @OA\Property(property="confirm_password", type="string" ,description="Confirm Password"),
     *               @OA\Property(property="user_type",type="text", enum={"user","artist"})
     *
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Get OTP retrieve successfully done",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function register(Request $request)
    {
        /* Get credentials from .env */
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required_without:phone_number', 'email:rfc,dns','unique:users,email'],
            'phone_number' => ['required_without:email', 'string','unique:users,phone'],
            'location' => ['required', 'string'],
            'user_type' => ['required', 'in:artist,user'],
            'password' => ['required', 'string'],
            'confirm_password' => ['required', 'same:password']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        try {
            if ($request->email) {

                $response = $this->twilio->verify->v2->services($this->twilio_verify_sid)
                    ->verifications
                    ->create($request->email, "email");

                if ($response) {
                    $input = $request->all();
                    $userCreated = User::create($input);
                    if ($userCreated) {
                        if ($request->user_type == 'artist') {
                            $userCreated->assignRole('ARTIST');
                        } else {
                            $userCreated->assignRole('USER');
                        }
                        return $this->sendResponse($userCreated, 'OTP send successfully');
                    } else {
                        return $this->sendError('User not register, Please try again!');
                    }
                } else {
                    return $this->sendError('Invalided phone number.');
                }
            } else {

                $response = $twilio->verify->v2->services($twilio_verify_sid)
                    ->verifications
                    ->create($request->phone_number, "sms");

                if ($response) {
                    $input = $request->all();
                    $input['phone'] = $request->phone_number;
                    unset($input['phone_number']);
                    $userCreated = User::create($input);
                    if ($userCreated) {
                        if ($request->user_type == 'artist') {
                            $userCreated->assignRole('ARTIST');
                        } else {
                            $userCreated->assignRole('USER');
                        }
                        return $this->sendResponse($userCreated, 'OTP send successfully');
                    } else {
                        return $this->sendError('User not register, Please try again!');
                    }
                } else {
                    return $this->sendError('Invalided phone number.');
                }
            }
        } catch (\Throwable $th) {
            dd($th);
            Log::error(" :: GET OTP EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/verify",
     * operationId="Verify OTP",
     * tags={"Auth Management"},
     * summary="Verify OTP",
     * description="Verify otp here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"code"},
     *               @OA\Property(property="code", type="string"),
     *               @OA\Property(property="phone_number", type="string"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Get OTP retrieve successfully done",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */



    public function verify(Request $request)
    {
        /* Get credentials from .env */
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);

        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string'],
            'email' => ['required_without:phone_number', 'email:rfc,dns'],
            'phone_number' => ['required_without:email', 'string']
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        try {

            if ($request->email) {
                $verification = $twilio->verify->v2->services($twilio_verify_sid)
                    ->verificationChecks
                    ->create([
                        'to' => $request->email,
                        "code" => $request->code
                    ]);
            } else {
                $verification = $twilio->verify->v2->services($twilio_verify_sid)
                    ->verificationChecks
                    ->create([
                        'to' => $request->phone_number,
                        "code" => $request->code
                    ]);
            }

            if ($verification->valid) {
                if ($verification->channel == "email") {
                    tap(User::where('email', $request->email))->update(['isVerified' => true]);
                    $getUser = User::where('email', '=', $request->email)->first();
                } else {
                    tap(User::where('phone', $request->phone_number))->update(['isVerified' => true]);
                    $getUser = User::where('phone', '=', $request->phone_number)->first();
                }

                Auth::login($getUser, true);
                $token = $getUser->createToken('MyApp')->plainTextToken;

                $response = [
                    'success' => true,
                    'data'    => $getUser,
                    'token'    => $token,
                    'message' => 'OTP verified successfully',
                ];
                return response()->json($response, 200);
            } else {
                return $this->sendError('Invalid otp code entered!');
            }
        } catch (\Throwable $th) {
            Log::error(" :: VERIFY OTP EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }


    /**
     * @OA\Post(
     * path="/api/reset-password/{user_id}",
     * operationId="Reset Password",
     * tags={"Auth Management"},
     * summary="Reset Password",
     * description="Reset Password here",
     * @OA\Parameter(
     *          name="user_id",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"password","confirm_password"},
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="confirm_password", type="confirm_password")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Get OTP retrieve successfully done",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function resetPassword(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            "password"  =>  "required",
            "confirm_password"  =>  "required|same:password",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        try {
            $getUser = User::find($user_id);
            if ($getUser) {
                $getUser->password = $request->password;
                $getUser->save();
            } else {
                return $this->sendError('User not found.');
            }
        } catch (\Throwable $th) {
            Log::error(" :: RESET PASSWORD EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }
}
