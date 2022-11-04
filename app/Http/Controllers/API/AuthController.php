<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
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
     *               required={"first_name","last_name","email","phone", "password", "confirm_password"},
     *               @OA\Property(property="first_name", type="text"),
     *               @OA\Property(property="last_name", type="text"),
     *               @OA\Property(property="email", type="text"),
     *               @OA\Property(property="phone", type="integer"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="confirm_password", type="password")
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
        $validator  =   Validator::make($request->all(), [
            "first_name"  =>  "required",
            "last_name"  =>  "required",
            "email"  =>  "required|email|unique:users",
            "phone"  =>  "required|unique:users",
            "password"  =>  "required",
            "confirm_password"  =>  "required|same:password",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        //DB::beginTransaction();
        try {
            $inputs = $request->all();
            $user   =   User::create($inputs);
            $user->assignRole('USER');
            if (!is_null($user)) {
                //DB::commit();
                return $this->sendResponse($user, 'User registration successfully.', 201);
            } else {
                return $this->sendError('Registration failed!', [], 400);
            }
        } catch (\Throwable $th) {
            //DB::rollBack();
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
     *               required={"email", "password"},
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="password", type="password")
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
            "email" =>  "required|email",
            "password" =>  "required",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {

            $user = User::where("email", $request->email)->first();

            if (is_null($user)) {
                return $this->sendError('Failed! email not found', [], 404);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user       =       User::find(Auth::id());
                $token      =       $user->createToken('token')->plainTextToken;
                $userData = [
                    'user_id' => $user->id,
                    'user_name' => $user->full_name,
                    'token' => $token
                ];

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
}
