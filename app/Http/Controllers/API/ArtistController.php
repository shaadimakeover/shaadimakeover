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
     * path="/api/update-artist-business-profile",
     * operationId="Update Makeup artist",
     * tags={"Update Make up artist"},
     * summary="Update artist",
     * security={{"sanctum":{}}},
     * description="Update artist here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"artist_business_name","artist_business_email","artist_business_phone","artist_location","artist_about","artist_working_since","artist_can_do_makeup_at"},
     *                 @OA\Property(property="artist_business_name", type="string"),
     *               @OA\Property(property="artist_business_email", type="email"),
     *               @OA\Property(property="artist_business_phone", type="number"),
     *               @OA\Property(property="artist_location", type="string"),
     *               @OA\Property(property="artist_about", type="text"),
     *               @OA\Property(property="artist_working_since", type="number"),
     *               @OA\Property(property="artist_can_do_makeup_at",  type="number",description="1=Studio & your Venue both place,0=Only your Venue"),
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Your profile update successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */


    public function updateArtistBusinessProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'artist_business_name' => ['required', 'string'],
            'artist_business_email' => ['required',  'email:rfc,dns'],
            'artist_business_phone' => ['required', 'digits_between:8,15', 'numeric'],
            'artist_location' => ['required', 'string'],
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
                    'artist_about' => $request->artist_about,
                    'artist_working_since' => $request->artist_working_since,
                    'artist_can_do_makeup_at' => $request->artist_can_do_makeup_at
                ]);
            }

            if (!is_null($user)) {
                DB::commit();
                return $this->sendResponse($user, 'Your profile update successfully.', 201);
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
     * path="/api/photo-album",
     * operationId="Photo Album",
     * tags={"Make up artist Photo Album"},
     * summary="Make up artist Photo Album Details Fetch",
     * security={{"sanctum":{}}},
     * description="Get Make up artist Photo Album Details ",
     *      @OA\Response(
     *          response=201,
     *          description="Album retrieved successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */



    public function photoAlbum()
    {
        try {
            $album = PhotoAlbum::select('id', 'name', 'slug')->get();
            if ($album->isNotEmpty()) {
                return $this->sendResponse($album, 'Album retrieved successfully.');
            } else {
                return $this->sendError("Oops! no Album found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/artist-photo-upload",
     * operationId="Make up artist photo upload",
     * tags={"Artist Photo Upload"},
     * summary="Artist Photo Upload",
     * security={{"sanctum":{}}},
     * description="Artist Photo Upload here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"photo_album_id","photo"},
     *                 @OA\Property(property="photo_album_id", type="number"),
     *               @OA\Property(property="photo", type="file"),
     *               
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Your Photo upload successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

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
                $path = 'artist-photos';
                $final_image_url = ImageHelper::customSaveImage($file, $path);
            }

            $user = MakeupArtistPhoto::create([
                'artist_id' => Auth::id(),
                'photo_album_id' => $request->photo_album_id,
                'photo' => $final_image_url,
            ]);
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

    /**
     * @OA\Get(
     * path="/api/artist-photo-delete/{photo_id}",
     * operationId="Artist Photo delete",
     * tags={"Artist Photo Delete"},
     * summary="Artist Photo delete Fetch",
     *  security={{"sanctum":{}}},
     * description="Artist Photo delete ",
     * @OA\Parameter(
     *          name="photo_id",
     *          description="Photo id",
     *          required=true,
     *          example=1,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Photo deleted successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */


    public function artistPhotoDelete($photo_id)
    {
        try {
            $photo = MakeupArtistPhoto::where('id', $photo_id)->where('artist_id', Auth::id())->first();
            if ($photo) {
                if ($photo->photo) {
                    unlink($photo->photo);
                }
                $photo->delete();
                return $this->sendResponse([], 'Photo Deleted successfully.');
            } else {
                return $this->sendError("Oops! no photo found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/pricing-service",
     * operationId="Pricing Service",
     * tags={"Make up artist Pricing Service"},
     * summary="Make up artist Pricing Service Details Fetch",
     * security={{"sanctum":{}}},
     * description="Get Make up artist Pricing Service Details ",
     *      @OA\Response(
     *          response=201,
     *          description="Pricing Service retrieved successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function priceService()
    {
        try {
            $service = PricingService::select('id', 'name')->get();
            if ($service->isNotEmpty()) {
                return $this->sendResponse($service, 'Service retrieved successfully.');
            } else {
                return $this->sendError("Oops! no service found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/add-artist-pricing",
     * operationId="add-artist-pricing",
     * tags={"Artist Pricing Add"},
     * summary="Artist Pricing Add",
     * security={{"sanctum":{}}},
     * description="Artist Pricing Add here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"pricing_service_id","price","description"},
     *                 @OA\Property(property="pricing_service_id", type="number"),
     *               @OA\Property(property="price", type="number"),
     *               @OA\Property(property="description", type="text"),
     *               
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Your pricing update successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function storeArtistPrice(Request $request)
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
            $user = MakeupArtistPricing::create([
                'artist_id' => Auth::id(),
                'pricing_service_id' => $request->pricing_service_id,
                'price' => $request->price,
                'description' => $request->description
            ]);

            if (!is_null($user)) {
                DB::commit();
                return $this->sendResponse($user, 'Your pricing update successfully.', 201);
            } else {
                return $this->sendError('Your pricing update failed!', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }

     /**
     * @OA\Post(
     * path="/api/edit-artist-pricing/{price_id}",
     * operationId="edit-artist-pricing",
     * tags={"Edit Artist Pricing"},
     * summary="Edit Artist Pricing",
     * security={{"sanctum":{}}},
     * description="Edit Artist Pricing here",
     *  @OA\Parameter(
     *          name="price_id",
     *          description="Price id",
     *          required=true,
     *          example=1,
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
     *               required={"pricing_service_id","price","description"},
     *                 @OA\Property(property="pricing_service_id", type="number"),
     *               @OA\Property(property="price", type="number"),
     *               @OA\Property(property="description", type="text"),
     *               
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Your pricing update successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function updateArtistPrice(Request $request, $price_id)
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
            $pricing = MakeupArtistPricing::where('id', $price_id)->where('artist_id', Auth::id())->first();
            if ($pricing) {
                $pricing->update([
                    'pricing_service_id' => $request->pricing_service_id,
                    'price' => $request->price,
                    'description' => $request->description
                ]);
                $result = MakeupArtistPricing::where('id', $price_id)->where('artist_id', Auth::id())->first();
                DB::commit();
                return $this->sendResponse($result, 'Your pricing update successfully.', 201);
            } else {
                return $this->sendError('No pricing found', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }

     /**
     * @OA\Get(
     * path="/api/delete-artist-pricing/{price_id}",
     * operationId="Artist Pricing delete",
     * tags={"Artist Pricing delete"},
     * summary="Artist Pricing delete",
     *  security={{"sanctum":{}}},
     * description="Artist Pricing delete ",
     * @OA\Parameter(
     *          name="price_id",
     *          description="Price Id",
     *          required=true,
     *          example=1,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Price deleted successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function deleteArtistPrice($price_id)
    {
        try {
            $price = MakeupArtistPricing::where('id', $price_id)->where('artist_id', Auth::id())->first();
            if ($price) {
                $price->delete();
                return $this->sendResponse([], 'Pricing deleted successfully.');
            } else {
                return $this->sendError("Oops! no price found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/add-artist-payment-policy",
     * operationId="add-artist-payment-policy",
     * tags={"Artist Payment Policy Add"},
     * summary="Artist Payment Policy Add",
     * security={{"sanctum":{}}},
     * description="Artist Payment Policy Add here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"percentage_of_pay","time_to_pay"},
     *                 @OA\Property(property="percentage_of_pay", type="number"),
     *               @OA\Property(property="time_to_pay", type="string",example="AT THE TIME OF BOOKING",description="AT THE TIME OF BOOKING, ON EVENT DATE, AFTER DELIVERABLES ARE DELIVERED"),
     *               
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Your Payment Policy Added successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function storePaymentPolicy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'percentage_of_pay' => 'required|numeric|max:255',
            'time_to_pay' => 'required|in:AT THE TIME OF BOOKING,ON EVENT DATE,AFTER DELIVERABLES ARE DELIVERED',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $countPaymentPolicy = MakeupArtistPaymentPolicy::where('artist_id', Auth::id())->count();
            if ($countPaymentPolicy <= 3) {
                $user = MakeupArtistPaymentPolicy::create([
                    'artist_id' => Auth::id(),
                    'percentage_of_pay' => $request->percentage_of_pay,
                    'time_to_pay' => $request->time_to_pay
                ]);
                if (!is_null($user)) {
                    DB::commit();
                    return $this->sendResponse($user, 'Your payment policy update successfully.', 200);
                } else {
                    return $this->sendError('Your payment policy update failed!', [], 400);
                }
            } else {
                return $this->sendError('You can not add more than three policy', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }


    /**
     * @OA\Post(
     * path="/api/edit-artist-payment-policy/{payment_policy_id}",
     * operationId="edit-artist-Payment Policy",
     * tags={"Edit Artist Payment Policy"},
     * summary="Edit Artist Payment Policy",
     * security={{"sanctum":{}}},
     * description="Edit Artist Payment Policy here",
     *  @OA\Parameter(
     *          name="payment_policy_id",
     *          description="Payment Policy id",
     *          required=true,
     *          example=1,
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
     *               required={"percentage_of_pay","time_to_pay"},
     *                 @OA\Property(property="percentage_of_pay", type="number"),
     *               @OA\Property(property="time_to_pay", type="string",example="AT THE TIME OF BOOKING",description="AT THE TIME OF BOOKING, ON EVENT DATE, AFTER DELIVERABLES ARE DELIVERED"),
     *               
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Your pricing update successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function updatePaymentPolicy(Request $request, $payment_policy_id)
    {
        $validator = Validator::make($request->all(), [
            'percentage_of_pay' => 'required|numeric|max:255',
            'time_to_pay' => 'required|in:AT THE TIME OF BOOKING,ON EVENT DATE,AFTER DELIVERABLES ARE DELIVERED',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $pricing = MakeupArtistPaymentPolicy::where('id', $payment_policy_id)->where('artist_id', Auth::id())->first();
            if ($pricing) {
                $pricing->update([
                    'percentage_of_pay' => $request->percentage_of_pay,
                    'time_to_pay' => $request->time_to_pay
                ]);
                $result = MakeupArtistPaymentPolicy::where('id', $payment_policy_id)->where('artist_id', Auth::id())->first();
                DB::commit();
                return $this->sendResponse($result, 'Your pricing policy update successfully.', 201);
            } else {
                return $this->sendError('No pricing found', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }


     /**
     * @OA\Get(
     * path="/api/delete-artist-payment-policy/{payment_policy_id}",
     * operationId="Artist Payment Policy delete",
     * tags={"Artist Payment Policy delete"},
     * summary="Artist Payment Policy delete",
     *  security={{"sanctum":{}}},
     * description="Artist Payment Policy delete ",
     * @OA\Parameter(
     *          name="payment_policy_id",
     *          description="Payment Policy Id",
     *          required=true,
     *          example=1,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Payment Policy deleted successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function deletePaymentPolicy($payment_policy_id)
    {
        try {
            $policy = MakeupArtistPaymentPolicy::where('id', $payment_policy_id)->where('artist_id', Auth::id())->first();
            if ($policy) {
                $policy->delete();
                return $this->sendResponse([], 'Payment policy deleted successfully.');
            } else {
                return $this->sendError("Oops! no policy found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/add-artist-cancellation-policy",
     * operationId="add-artist-cancellation-policy",
     * tags={"Artist Cancellation Policy Add"},
     * summary="Artist Cancellation Policy Add",
     * security={{"sanctum":{}}},
     * description="Artist Cancellation Policy Add here",
     *     @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"cancellation_policy"},
     *                 @OA\Property(property="cancellation_policy", type="text",example="No Cancellation Policy"),
     *              
     *               
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Your Cancellation Policy update successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function storeCancellationPolicy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_policy' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $countCancellationPolicy = MakeupArtistCancellationPolicy::where('artist_id', Auth::id())->count();
            if ($countCancellationPolicy <= 3) {
                $user = MakeupArtistCancellationPolicy::create([
                    'artist_id' => Auth::id(),
                    'cancellation_policy' => $request->cancellation_policy
                ]);
                if (!is_null($user)) {
                    DB::commit();
                    return $this->sendResponse($user, 'Your Cancellation policy update successfully.', 200);
                } else {
                    return $this->sendError('Your cancellation policy update failed!', [], 400);
                }
            } else {
                return $this->sendError('You can not add more than three policy', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/edit-artist-cancellation-policy/{payment_cancellation_id}",
     * operationId="edit-artist-Payment Cancellation",
     * tags={"Edit Artist Payment Cancellation Policy"},
     * summary="Edit Artist Payment Cancellation Policy",
     * security={{"sanctum":{}}},
     * description="Edit Artist Payment Cancellation Policy here",
     *  @OA\Parameter(
     *          name="payment_cancellation_id",
     *          description="Payment Cancellation Id",
     *          required=true,
     *          example=1,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),

     *      @OA\RequestBody(
     *         @OA\JsonContent(),
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *               type="object",
     *               required={"cancellation_policy"},
     *                 @OA\Property(property="cancellation_policy", type="text",example="No Cancellation Policy"),
     *              
     *               
     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Your Payment Cancellation Policy update successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function updateCancellationPolicy(Request $request, $payment_cancellation_id)
    {
        $validator = Validator::make($request->all(), [
            'cancellation_policy' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::beginTransaction();
        try {
            $cancellationPolicy = MakeupArtistCancellationPolicy::where('id', $payment_cancellation_id)->where('artist_id', Auth::id())->first();
            if ($cancellationPolicy) {
                $cancellationPolicy->update([
                    'cancellation_policy' => $request->cancellation_policy,
                ]);
                $result = MakeupArtistCancellationPolicy::where('id', $payment_cancellation_id)->where('artist_id', Auth::id())->first();
                DB::commit();
                return $this->sendResponse($result, 'Your cancellation policy update successfully.', 201);
            } else {
                return $this->sendError('No pricing found', [], 400);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error(" :: PROFILE UPDATE EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError($th->getMessage(), [], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/delete-artist-cancellation-policy/{payment_cancellation_id}",
     * operationId="Artist cancellation Policy delete",
     * tags={"Artist Payment cancellation Policy delete"},
     * summary="Artist Payment cancellation Policy delete",
     *  security={{"sanctum":{}}},
     * description="Artist Payment cancellation Policy delete ",
     * @OA\Parameter(
     *          name="payment_cancellation_id",
     *          description="Payment cancellation Policy Id",
     *          required=true,
     *          example=1,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Payment cancellation Policy deleted successfully.",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */

    public function deleteCancellationPolicy($payment_cancellation_id)
    {
        try {
            $cancellationPolicy = MakeupArtistCancellationPolicy::where('id', $payment_cancellation_id)->where('artist_id', Auth::id())->first();
            if ($cancellationPolicy) {
                $cancellationPolicy->delete();
                return $this->sendResponse([], 'Cancellation policy deleted successfully.');
            } else {
                return $this->sendError("Oops! no cancellation policy found", [], 404);
            }
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }
}
