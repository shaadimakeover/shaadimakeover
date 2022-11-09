<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Firebase\Auth\Token\Exception\InvalidToken;

class AuthController extends BaseController
{

    /**
     * @OA\Post(
     * path="/api/social-login",
     * operationId="Social Login",
     * tags={"Auth Management"},
     * summary="User Social Login",
     * description="User Social Login here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name","email","provider", "provider_id", "device_type","device_token"},
     *               @OA\Property(property="name", type="text"),
     *               @OA\Property(property="provider", type="text", enum={"google","facebook","instagram"}),
     *               @OA\Property(property="email", type="text"),
     *               @OA\Property(property="provider_id", type="text"),
     *               @OA\Property(property="device_type", type="text", enum={"ios","android"}),
     *               @OA\Property(property="device_token", type="text")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Login successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Login failed!"),
     * )
     */
    public function socialLogin(Request $request)
    {
        $validator  =   Validator::make($request->all(), [
            "email"  =>  "required",
            "name"  =>  "required",
            "provider"  =>  "required|in:google,facebook,instagram",
            "provider_id"  =>  "required",
            "device_type"  =>  "required|in:ios,android",
            "device_token"  =>  "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $name = explode(" ", $request->name);
            $userCreated = User::firstOrCreate(
                [
                    'email' => $request->email
                ],
                [
                    'email_verified_at' => now(),
                    'first_name' => $name[0],
                    'last_name' => isset($name[1]) ? $name[1] : null,
                ]
            );
            $userCreated->providers()->updateOrCreate(
                [
                    'provider' => $request->provider,
                    'provider_id' => $request->provider_id,
                ],
                [
                    'device' => $request->device,
                    'device_token' => $request->device_token,
                ]
            );
            $userCreated->assignRole('USER');
            $token = $userCreated->createToken('shaadimakeover2022')->plainTextToken;
            $data['token'] = $token;
            $data['user'] = $userCreated;
            if (!is_null($userCreated)) {
                DB::commit();
                return $this->sendResponse($data, 'Login successfully.', 201);
            } else {
                DB::rollBack();
                return $this->sendError('Login failed!', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/register",
     * operationId="Register",
     * tags={"Auth Management"},
     * summary="User Register",
     * description="User Register here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"name","email","phone","location","user_type", "password", "confirm_password"},
     *               @OA\Property(property="name", type="text"),
     *               @OA\Property(property="email",description="optional",type="string"),            
     *               @OA\Property(property="phone", type="integer"),
     *               @OA\Property(property="location", type="text"),
     *               @OA\Property(property="user_type", type="text",  enum={"customer","artist"}),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="confirm_password", type="password"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name"  =>  "required",
            "email"  =>  "nullable|email|unique:users",
            "phone"  =>  "required|numeric|unique:users",
            "location"  =>  "required",
            "user_type" => "required",
            "password" => "required",
            "confirm_password" => "required|same:password"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        DB::beginTransaction();
        try {
            $name = explode(" ", $request->name);
            $data = [
                'first_name' => $name[0],
                'last_name' => isset($name[1]) ? $name[1] : null,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => $request->password,
                'location' => $request->location
            ];

            $user = User::create($data);

            if (!is_null($user)) {
                if ($request->user_type == "customer") {
                    $user->assignRole('USER');
                } else {
                    $user->assignRole('ARTIST');
                }
                DB::commit();
                return $this->sendResponse($user, 'Registration successfully.', 201);
            } else {
                return $this->sendError('Registration failed!');
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/login",
     * operationId="authLogin",
     * tags={"Auth Management"},
     * summary="User Login",
     * description="Login User Here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"email","password"},
     *               @OA\Property(property="email",description="email or phone", type="text"),
     *               @OA\Property(property="password", type="password"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Login Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 200);
        }

        try {

            if (is_numeric($request->get('email'))) {

                $user = User::where('phone', $request->get('email'))->first();

                if (is_null($user)) {
                    return $this->sendError('Failed! mobile number not valid', [], 404);
                }

                if (Auth::attempt(['phone' => $request->email, 'password' => $request->password])) {
                    $user       =       User::find(Auth::id());
                    $token      =       $user->createToken('token')->plainTextToken;

                    $response = [
                        'success' => true,
                        'data'    => $user,
                        'token'    => $token,
                        'message' => 'Login successfully.',
                    ];
                    return response()->json($response, 200);
                } else {

                    return $this->sendError("Whoops! invalid password", [], 400);
                }
            } elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {

                $user = User::where('email', $request->get('email'))->first();

                if (is_null($user)) {
                    return $this->sendError('Failed! mobile number not valid', [], 404);
                }

                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $user = User::find(Auth::id());
                    $token = $user->createToken('token')->plainTextToken;

                    $response = [
                        'success' => true,
                        'data'    => $user,
                        'token'    => $token,
                        'message' => 'Login successfully.',
                    ];
                    return response()->json($response, 200);
                } else {
                    return $this->sendError("Whoops! invalid password", [], 400);
                }
            } else {
                return $this->sendError("Whoops! invalid password", [], 400);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/user",
     * operationId="User Details",
     * tags={"User Details"},
     * summary="User Details Fetch",
     *  security={{"sanctum":{}}},
     * description="Get User Details ",
     *      @OA\Response(
     *          response=201,
     *          description="Register Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function user()
    {
        try {
            $user = Auth::user();
            if (!is_null($user)) {
                return $this->sendResponse($user, 'User retrieved successfully.');
            } else {
                return $this->sendError("Whoops! no user found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }



    /**
     * @OA\Post(
     * path="/api/otp-verify",
     * operationId="Otp Verify",
     * tags={"Auth Management"},
     * summary="User Otp Verify",
     * description="User Otp Verify here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"otp","user_id"},
     *               @OA\Property(property="otp", type="text"),
     *               @OA\Property(property="user_id", type="number")
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Otp verify successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="'otp invalid!"),
     * )
     */
    public function otpVerify(Request $request)
    {
        $validator  =   Validator::make($request->all(), [
            "otp"  =>  "required",
            "user_id" => "required|exist:users,id"
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        try {
            $user  = User::where(['user_id', $request->user_id, 'otp' => $request->otp])->first();
            if (!is_null($user)) {
                $token = $user->createToken('token-name')->plainTextToken;
                $data['token'] = $token;
                $data['user'] = $user;
                return $this->sendResponse($data, 'Otp verify successfully.', 201);
            } else {
                return $this->sendError('otp invalid!', [], 400);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    public function check()
    {
        // Launch Firebase Auth
        $auth = app('firebase.auth');
        //check
        $uid = 'y2WfNc5nubUX5bcre3HtLG1QXPJ3';
        $customToken = $auth->createCustomToken($uid);
        $customTokenString = $customToken->toString();
        //$verifiedIdToken = $auth->verifyIdToken($customTokenString);
        $signInResult = $auth->signInWithCustomToken($customTokenString);
        dd($signInResult);
        // return response()->json([
        //     "success" => true,
        //     "data" => $signInResult,
        //     'message' => "data foun",
        // ], 200);
    }

    public function firebaseLogin(Request $request)
    {

        // Launch Firebase Auth
        $auth = app('firebase.auth');
        // Retrieve the Firebase credential's token
        $idTokenString = $request->input('token');

        try {
            // Try to verify the Firebase credential token with Google
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
        } catch (\InvalidArgumentException $e) {
            // If the token has the wrong format

            return response([
                'success' => false,
                'data' => null,
                'message' => 'Unauthorized - Can\'t parse the token: ' . $e->getMessage(),
            ], 401);
        } catch (InvalidToken $e) { // If the token is invalid (expired ...)

            return response([
                'success' => false,
                'data' => null,
                'message' => 'Unauthorized - Token is invalide: ' . $e->getMessage(),
            ], 401);
        }

        // Retrieve the UID (User ID) from the verified Firebase credential's token
        $uid = $verifiedIdToken->getClaim('sub');

        // Retrieve the user model linked with the Firebase UID
        $user = $auth->getUser($uid);

        // Here you could check if the user model exist and if not create it
        // For simplicity we will ignore this step

        // Once we got a valid user model
        // Create a Personnal Access Token
        // $tokenResult = $user->createToken('Personal Access Token');

        // // Store the created token
        // $token = $tokenResult->token;

        // // Add a expiration date to the token
        // $token->expires_at = Carbon::now()->addWeeks(1);

        // // Save the token to the user
        // $token->save();

        //$user = Customer::where('uid', $uid)->first();

        // Return a JSON object containing the token datas
        // You may format this object to suit your needs
        // return response()->json([
        //     'id' => $user->id,
        //     'access_token' => $tokenResult->accessToken,
        //     'token_type' => 'Bearer',
        //     'expires_at' => Carbon::parse(
        //         $tokenResult->token->expires_at
        //     )->toDateTimeString(),
        // ]);

        $getUser = $this->getOrCreateUser($uid, $user->phoneNumber);
        if ($getUser) {

            return response([
                'success' => true,
                'data' => $getUser,
                'message' => "Login sucessfully!",
            ], 200);
        } else {

            return response([
                'success' => false,
                'data' => null,
                'message' => "Login fail!",
            ], 400);
        }
    }

    public function getOrCreateUser($uid, $phoneNumber)
    {
        try {

            $modifiedPhoneNumber = substr($phoneNumber, 3);
            $user = User::where('phone', $modifiedPhoneNumber)->first();
            /**
             * 1. Find user by phone
             *   - if phone number exist checks uid null or not
             */

            if ($user) {

                if ($user->uid == null) {
                    $user->uid = $uid;
                    $user->save();
                }

                return $user;
            } else {

                $new_user = new User();
                $new_user->uid = $uid;
                $new_user->phone = $modifiedPhoneNumber;
                $new_user->save();
                return $new_user;
            }
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }
}
