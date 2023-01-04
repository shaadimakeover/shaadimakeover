<?php

namespace App\Http\Controllers\API;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\MakeupArtistCancellationPolicy;
use App\Models\MakeupArtistPaymentPolicy;
use App\Models\MakeupArtistPhoto;
use App\Models\MakeupArtistPricing;
use App\Models\MakeupArtistProfile;
use App\Models\PhotoAlbum;
use App\Models\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Stripe\Service\PriceService;

class ArtistController extends BaseController
{
    /**
     * @OA\Post(
     * path="/api/artist-profile-update",
     * operationId="Artist Profile Update",
     * tags={"Artist profile Update"},
     * summary="Artist Profile Update",
     * security={{"sanctum":{}}},
     * description="Artist Profile Update here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *            @OA\Schema(
     *               type="object",
     *               required={"artist_business_name","artist_business_email","artist_business_phone","artist_location","is_featured_artist","artist_about","artist_working_since","artist_can_do_makeup_at"},
     *               @OA\Property(property="artist_business_name", type="string"),
     *               @OA\Property(property="artist_business_email", type="email"),
     *               @OA\Property(property="artist_business_phone", type="number"),
     *               @OA\Property(property="artist_location", type="string"),
     *               @OA\Property(property="is_featured_artist",  type="boolean", example="true"),
     *               @OA\Property(property="artist_about", type="text"),
     *               @OA\Property(property="artist_working_since", type="number"),
     *               @OA\Property(property="artist_can_do_makeup_at",  type="boolean", example="true"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=200,
     *          description="Your profile update successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function updateArtist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'artist_business_name' => ['required', 'string'],
            'artist_business_email' => ['required',  'email:rfc,dns'],
            'artist_business_phone' => ['required', 'digits_between:8,15', 'numeric'],
            'artist_location' => ['required', 'string'],
            'is_featured_artist' => ['required', 'in:0,1'],
            'artist_about' => ['required'],
            "artist_working_since"  =>  ["required"],
            "artist_can_do_makeup_at"  =>  ["required", "in:0,1"],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $user = MakeupArtistProfile::where('artist_id', Auth::id())->first();
            if ($user) {
                $user->update([
                    'artist_business_name' => $request->artist_business_name,
                    'artist_business_email' => $request->artist_business_email,
                    'artist_business_phone' => $request->artist_business_phone,
                    'artist_location' => $request->artist_location,
                    'is_featured_artist' => $request->is_featured_artist,
                    'artist_about' => $request->artist_about,
                    'artist_working_since' => $request->artist_working_since,
                    'artist_can_do_makeup_at' => $request->artist_can_do_makeup_at
                ]);
            } else {
                $user = MakeupArtistProfile::create([
                    'artist_id' => Auth::id(),
                    'artist_business_name' => $request->artist_business_name,
                    'artist_business_email' => $request->artist_business_email,
                    'artist_business_phone' => $request->artist_business_phone,
                    'artist_location' => $request->artist_location,
                    'is_featured_artist' => $request->is_featured_artist,
                    'artist_about' => $request->artist_about,
                    'artist_working_since' => $request->artist_working_since,
                    'artist_can_do_makeup_at' => $request->artist_can_do_makeup_at
                ]);
            }

            if (!is_null($user)) {
                DB::commit();
                return $this->sendResponse($user, 'Your profile update successfully.', 200);
            } else {
                return $this->sendError('Your profile update failed!', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }

    public function photoAlbum()
    {
        try {
            $album = PhotoAlbum::select('id', 'name', 'slug')->get();
            if ($album->isNotEmpty()) {
                return $this->sendResponse($album, 'Album retrieved successfully.');
                //return $this->sendResponse($bannerImage, 'Banner retrieved successfully.');
            } else {
                return $this->sendError("Oops! no Album found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    public function artistPhotoUpload(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'photo_album_id' => 'required|exists:photo_albums,id',
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $final_image_url = '';
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $path = 'profile';
                $final_image_url = ImageHelper::customSaveImage($file, $path);
                // dd($final_image_url);
            }

            // $user = MakeupArtistPhoto::where('artist_id', Auth::id())->first();

            // if ($user) {
            //     $user->update([
            //         'photo_album_id' => $request->photo_album_id,
            //         'photo' => $final_image_url,
            //     ]);
            // } else {
            $user = MakeupArtistPhoto::create([
                'artist_id' => Auth::id(),
                'photo_album_id' => $request->photo_album_id,
                'photo' => $final_image_url,
            ]);
            // }

            if (!is_null($user)) {
                DB::commit();
                return $this->sendResponse($user, 'Your profile update successfully.', 200);
            } else {
                return $this->sendError('Your profile update failed!', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }

    public function priceService()
    {
        try {
            $service = PricingService::select('id', 'name')->get();
            if ($service->isNotEmpty()) {
                return $this->sendResponse($service, 'Service retrieved successfully.');
                //return $this->sendResponse($bannerImage, 'Banner retrieved successfully.');
            } else {
                return $this->sendError("Oops! no service found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    public function artistPrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pricing_service_id' => 'required|exists:pricing_services,id',
            'price' => ['required', 'regex:/^\d*(\.\d{2})?$/'],
            'description' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            // $user = MakeupArtistPricing::updateOrCreate(
            //     ['artist_id' => Auth::id()],
            //     ['pricing_service_id' => $request->pricing_service_id, 'price' => $request->price, 'description' => $request->description]
            // );
            // $user = MakeupArtistPricing::where('artist_id', Auth::id())->get();
            // if ($user->isNotEmpty()) {
            //     foreach ($user as $item) {
            //         $item->update([
            //             'pricing_service_id' => $request->pricing_service_id,
            //             'price' => $request->price,
            //             'description' => $request->description,
            //         ]);
            //     }
            // } else {
            $user = MakeupArtistPricing::create([
                'artist_id' => Auth::id(),
                'pricing_service_id' => $request->pricing_service_id,
                'price' => $request->price,
                'description' => $request->description
            ]);
            // }

            if (!is_null($user)) {
                DB::commit();
                return $this->sendResponse($user, 'Your pricing update successfully.', 200);
            } else {
                return $this->sendError('Your pricing update failed!', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }


    public function paymentPolicy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'percentage_of_pay' => 'required|numeric|max:255',
            'time_to_pay' => 'required|numeric|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            // $user = MakeupArtistPricing::updateOrCreate(
            //     ['artist_id' => Auth::id()],
            //     ['pricing_service_id' => $request->pricing_service_id, 'price' => $request->price, 'description' => $request->description]
            // );
            // $user = MakeupArtistPricing::where('artist_id', Auth::id())->get();
            // if ($user->isNotEmpty()) {
            //     foreach ($user as $item) {
            //         $item->update([
            //             'pricing_service_id' => $request->pricing_service_id,
            //             'price' => $request->price,
            //             'description' => $request->description,
            //         ]);
            //     }
            // } else {
            $user = MakeupArtistPaymentPolicy::create([
                'artist_id' => Auth::id(),
                'percentage_of_pay' => $request->percentage_of_pay,
                'time_to_pay' => $request->time_to_pay
            ]);
            // }

            if (!is_null($user)) {
                DB::commit();
                return $this->sendResponse($user, 'Your payment policy update successfully.', 200);
            } else {
                return $this->sendError('Your payment policy update failed!', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }
    
    public function cancellationPolicy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_policy' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            // $user = MakeupArtistPricing::updateOrCreate(
            //     ['artist_id' => Auth::id()],
            //     ['pricing_service_id' => $request->pricing_service_id, 'price' => $request->price, 'description' => $request->description]
            // );
            // $user = MakeupArtistPricing::where('artist_id', Auth::id())->get();
            // if ($user->isNotEmpty()) {
            //     foreach ($user as $item) {
            //         $item->update([
            //             'pricing_service_id' => $request->pricing_service_id,
            //             'price' => $request->price,
            //             'description' => $request->description,
            //         ]);
            //     }
            // } else {
            $user = MakeupArtistCancellationPolicy::create([
                'artist_id' => Auth::id(),
                'cancellation_policy' => $request->cancellation_policy
            ]);
            // }

            if (!is_null($user)) {
                DB::commit();
                return $this->sendResponse($user, 'Your Cancellation policy update successfully.', 200);
            } else {
                return $this->sendError('Your cancellation policy update failed!', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }
}
