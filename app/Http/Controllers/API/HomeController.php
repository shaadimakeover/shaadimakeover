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
     *               required={"first_name","last_name","location","user_type","is_accept_terms_conditions","password","confirm_password"},
     *               @OA\Property(property="first_name", type="text"),
     *               @OA\Property(property="last_name", type="text"),
     *               @OA\Property(property="email", type="email"),
     *               @OA\Property(property="location", type="text"),
     *               @OA\Property(property="user_type", type="string", enum={"user","artist"}),
     *               @OA\Property(property="is_accept_terms_conditions", type="string", enum={"true","false"}),
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
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['nullable', 'email:rfc,dns', 'unique:users,email'],
            'location' => ['required', 'string'],
            'user_type' => ['required', 'in:artist,user'],
            'is_accept_terms_conditions' => ['required', 'in:true,false'],
            "password"  =>  "required",
            "confirm_password"  =>  "required|same:password",
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {

            // $final_image_url = "";
            // if ($request->hasFile('profile_photo')) {
            //     $file = $request->file('profile_photo');
            //     $path = 'profile';
            //     $final_image_url = ImageHelper::customSaveImage($file, $path);
            //     //dd($image_url);
            // }
            $user = User::where('id', $user_id)
                ->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'location' => $request->location,
                    'is_terms_conditions' => $request->is_accept_terms_conditions == "true" ? true : false,
                    'isProfileCompleted' => true,
                    "password" => bcrypt($request->password)
                ]);

            if (!is_null($user)) {
                $getUser = User::find($user_id);
                // if ($request->user_type == 'artist') {
                //     $getUser->assignRole('ARTIST');
                // } else {
                //     $getUser->assignRole('USER');
                // }
                DB::commit();
                return $this->sendResponse($getUser, 'Your profile update successfully.', 200);
            } else {
                return $this->sendError('Your profile update failed!', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
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


    public function getUserDetails()
    {
        return Auth::user();
    }
}
