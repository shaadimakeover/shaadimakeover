<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\TopArtistResource;
use App\Models\Banner;
use App\Models\Category;
use App\Models\MakeupArtistCancellationPolicy;
use App\Models\MakeupArtistPaymentPolicy;
use App\Models\MakeupArtistPhoto;
use App\Models\MakeupArtistPost;
use App\Models\MakeupArtistPricing;
use App\Models\MakeupArtistProfile;
use App\Models\PhotoAlbum;
use App\Models\Rating;
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
                if ($request->user_type == 'artist') {
                    $getUser->assignRole('ARTIST');
                } else {
                    $getUser->assignRole('USER');
                }
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

    /**
     * @OA\Get(
     * path="/api/banner-image",
     * operationId="Banner",
     * tags={"Banner Details"},
     * summary="Banner Image Fetch",
     * description="Get Banner Image ",
     *      @OA\Response(
     *          response=200,
     *          description="Banner retrieved successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function bannerImage()
    {
        try {
            $banner = Banner::with('artist')->where('status', 1)->get();
            if ($banner) {
                return $this->sendResponse(BannerResource::collection($banner), 'Banner retrieved successfully.');
                //return $this->sendResponse($bannerImage, 'Banner retrieved successfully.');
            } else {
                return $this->sendError("Oops! no banner found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/category",
     * operationId="Category",
     * tags={"Category Details"},
     * summary="Category Image Fetch",
     * description="Get category details ",
     *      @OA\Response(
     *          response=200,
     *          description="category details retrieved successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function category()
    {
        try {
            $category = Category::where('active', 1)->get();
            if ($category) {
                return $this->sendResponse(CategoryResource::collection($category), 'Category retrieved successfully.');
            } else {
                return $this->sendError("Whoops! no category found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/top-artist",
     * operationId="Top Artist",
     * tags={"Top Artist Details"},
     * summary="Top Artist Fetch",
     * description="Get Top Artist details ",
     *      @OA\Response(
     *          response=200,
     *          description="Top Artist details retrieved successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function topArtist()
    {
        try {
            $topArtist = User::role('ARTIST')->where('isTopExpert', 1)->where('active', 1)->get();
            if ($topArtist) {
                return $this->sendResponse(TopArtistResource::collection($topArtist), 'Top artist retrieved successfully.');
            } else {
                return $this->sendError("Whoops! no top artist found", [], 404);
            }
        } catch (\Throwable $th) {
            dd($th);
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/post",
     * operationId="Posts",
     * tags={"Posts"},
     * summary="Posts Fetch",
     * description="Get Posts details ",
     *      @OA\Response(
     *          response=201,
     *          description="Posts details retrieved successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function post()
    {
        try {
            $post = MakeupArtistPost::where('status', 1)->get();
            if ($post->isNotEmpty()) {
                return $this->sendResponse(PostResource::collection($post), 'Post retrieved successfully.');
            } else {
                return $this->sendError("Whoops! no post found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/artist-details/{artist_id}",
     * operationId="Artist",
     * tags={"Artist Details"},
     * summary="Artist Full Details Fetch",
     * description="Get Artist Details",
     * @OA\Parameter(
     *          name="artist_id",
     *          description="Artist ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Artist Details retrieved successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */


    public function artistDetails($artist_id)
    {
        $getArtist = User::where('id', $artist_id)->first();
        if (!$getArtist) {
            return $this->sendResponse([], 'No Artist Found');
        }
        $getArtistProfile = MakeupArtistProfile::where('artist_id', $artist_id)->first();
        $countArtistPhotos = MakeupArtistPhoto::where('artist_id', $artist_id)->get()->count();
        $getArtistTopPhoto = $this->profilePhoto("top-photos", $artist_id);
        $getArtistBridalPhoto = $this->profilePhoto('bridal-makeup', $artist_id);
        $getArtistEngagementPhoto = $this->profilePhoto('engagement-makeup', $artist_id);
        $getArtistPartyPhoto = $this->profilePhoto('party-makeup', $artist_id);
        $getArtistStudioPhoto = $this->profilePhoto('studio-photo', $artist_id);
        $getArtistProfilePhoto = $this->profilePhoto('profile-photo', $artist_id);
        $getArtistAchievementPhoto = $this->profilePhoto('achievement-photo', $artist_id);
        $getArtistHairStylePhoto = $this->profilePhoto('hair-style-photo', $artist_id);
        $getArtistMehendiPhoto = $this->profilePhoto('mehendi-photo', $artist_id);
        $getArtistPricing = MakeupArtistPricing::with('pricingService')->where('artist_id', $artist_id)->get()->toArray();
        $getArtistPaymentPolicies = MakeupArtistPaymentPolicy::where('artist_id', $artist_id)->get()->toArray();
        $getArtistPaymentCancellationPolicies = MakeupArtistCancellationPolicy::where('artist_id', $artist_id)->get()->toArray();
        $artistRating = Rating::where('artist_id', $artist_id)->count();
        try {
            $data = [
                "artist_id" => $getArtist->id ?? '',
                "artist_name" => $getArtist->full_name ?? '',
                "artist_email" => $getArtist->email ?? '',
                "artist_phone" => $getArtist->phone ?? '',
                "artist_business_name" => $getArtistProfile->artist_business_name ?? '',
                "artist_business_email" => $getArtistProfile->artist_business_email ?? '',
                "artist_business_phone" => $getArtistProfile->artist_business_phone ?? '',
                "artist_location" => $getArtistProfile->artist_location ?? '',
                "is_featured_artist" => $getArtistProfile->is_featured_artist ?? '',
                "artist_about" => $getArtistProfile->artist_about ?? '',
                "artist_working_since" => $getArtistProfile->artist_working_since ?? '',
                "artist_can_do_makeup_at" => $getArtistProfile->artist_can_do_makeup_at ?? '',
                "artist_thumbnail" => config('app.url') . '/' . $getArtist->profile_photo_path,
                "artist_photos" => [
                    "top_photos" => $getArtistTopPhoto,
                    "bridal_makeup" => $getArtistBridalPhoto,
                    "engagement_makeup" => $getArtistEngagementPhoto,
                    "party_makeup" => $getArtistPartyPhoto,
                    "studio_photo" => $getArtistStudioPhoto,
                    "profile_photo" => $getArtistProfilePhoto,
                    "achievement_photo" => $getArtistAchievementPhoto,
                    "hair_style_photo" => $getArtistHairStylePhoto,
                    "mehandi_photo" => $getArtistMehendiPhoto
                ],
                "artist_total_photos" => $countArtistPhotos,
                "pricing" => $getArtistPricing,
                "payment_policy" => $getArtistPaymentPolicies,
                "cancellation_policy" => $getArtistPaymentCancellationPolicies,
                "total_ratings" => $artistRating,
                "total_reviews" => 0,
                "reviews" => [
                    // [
                    //     "user_id" => 15,
                    //     "user_name" => "Demo",
                    //     "user_avatar" => "",
                    //     "ratings" => 4.0,
                    //     "comment" => "",
                    //     "date" => "12/12/2022"
                    // ],
                    // [
                    //     "user_id" => 15,
                    //     "user_name" => "Demo",
                    //     "user_avatar" => "",
                    //     "ratings" => 4.0,
                    //     "comment" => "",
                    //     "date" => "12/12/2022"
                    // ],
                    // [
                    //     "user_id" => 15,
                    //     "user_name" => "Demo",
                    //     "user_avatar" => "",
                    //     "ratings" => 4.0,
                    //     "comment" => "",
                    //     "date" => "12/12/2022"
                    // ],

                ]
            ];

            return $this->sendResponse($data, 'Artist Details retrieved successfully.');
        } catch (\Throwable $th) {
            dd($th);
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    public function profilePhoto($slug, $artist_id)
    {
        if ($slug) {
            $getAlbum = PhotoAlbum::where('slug', $slug)->first();
            if ($getAlbum) {
                $getPhoto = MakeupArtistPhoto::where('artist_id', $artist_id)->where('photo_album_id', $getAlbum->id)->get()->toArray();
                return $getPhoto;
            }
            return [];
        }
        return [];
    }
}
