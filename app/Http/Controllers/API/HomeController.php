<?php

namespace App\Http\Controllers\API;

use App\Helpers\ImageHelper;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HomeController extends BaseController
{
    /**
     * @OA\Post(
     * path="/api/update-profile/{user_id}",
     * operationId="Update profile",
     * tags={"User Details"},
     * summary="Update profile",
     * security={{"sanctum":{}}},
     * description="Update profile here",
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
     *               required={"first_name","last_name","user_name","zip_code","state_id","subscription_id","profile_photo","password","confirm_password"},
     *               @OA\Property(property="first_name", type="text"),
     *               @OA\Property(property="last_name", type="text"),
     *               @OA\Property(property="user_name", type="text"),
     *               @OA\Property(property="zip_code", type="text"),
     *               @OA\Property(property="state_id", type="integer"),
     *               @OA\Property(property="category_ids", type="integer"),
     *               @OA\Property(property="subscription_id", type="integer"),
     *               @OA\Property(property="profile_photo", type="file"),
     *               @OA\Property(property="password", type="password"),
     *               @OA\Property(property="confirm_password", type="password"),
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

    public function updateProfile(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            "first_name"  =>  "required",
            "last_name"  =>  "required",
            "user_name"  =>  "required",
            "zip_code"  =>  "required",
            "state_id"  =>  "required",
            "category_ids"  =>  "nullable|array",
            "subscription_id"  =>  "required",
            "profile_photo" => "required",
            "password"  =>  "required",
            "confirm_password"  =>  "required|same:password",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {

            $final_image_url = "";
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                $path = 'profile';
                $final_image_url = ImageHelper::customSaveImage($file, $path);
                //dd($image_url);
            }
            $user = User::where('id', $user_id)
                ->update([
                    'country_id' => $request->country_id,
                    'state_id' => $request->state_id,
                    'subscription_id' => $request->subscription_id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'username' => $request->username,
                    'zip_code' => $request->zip_code,
                    'profile_photo_path' => $final_image_url,
                    'is_online' => true,
                    'isProfileCompleted' => true,
                    "password" => $request->password
                ]);

            //$user->assignRole('USER');
            if (!is_null($user)) {
                $user->assignRole('USER');
                DB::commit();
                return $this->sendResponse($user, 'Registration successfully.', 200);
            } else {
                return $this->sendError('Registration failed!', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    public function getUserDetails()
    {

        return Auth::user();
    }

    /**
     * @OA\Get(
     * path="/api/logout",
     * operationId="User logout",
     * tags={"User Details"},
     * summary="Logout profile",
     * security={{"sanctum":{}}},
     * description="Logout profile here",
     *      @OA\Response(
     *          response=201,
     *          description="Get OTP retrieve successfully done",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function logout()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->tokens()->delete();

            return $this->sendResponse(null, 'Logout successfully.');
        }
    }
}
