<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @group  Product management
 *
 * APIs for managing products
 */
class ProductController extends BaseController
{
    /**
     * @authenticated
     * @response  {
     *       "status": true,
     *       "data": [
     *           {
     *               "id": 1,
     *               "title": "new task",
     *               "description": "demo description",
     *               "user_id": 56,
     *               "created_at": "2021-02-17T15:24:36.000000Z",
     *               "updated_at": "2021-02-17T15:24:36.000000Z",
     *               "user": {
     *                   "id": 56,
     *                   "first_name": "john",
     *                   "last_name": "doe",
     *                   "email": "john@gmail.com",
     *                   "phone": "1122334455",
     *                   "email_verified_at": null,
     *                   "current_team_id": null,
     *                   "profile_photo_path": null,
     *                   "active": 0,
     *                   "created_at": "2021-02-18T12:14:01.000000Z",
     *                   "updated_at": "2021-02-18T12:14:01.000000Z",
     *                   "full_name": "john doe",
     *                   "role_name": "USER"
     *               }
     *           }
     *       ]
     *   }
     * @response  401 {
     *   "message": "Unauthenticated."
     *}
     */
    public function index()
    {
        try {
            $products = Product::all();
            return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            $product = Product::create($input);
            return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $product = Product::find($id);

            if (is_null($product)) {
                return $this->sendError('Product not found.', [], 404);
            }

            return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        try {
            $product->name = $input['name'];
            $product->detail = $input['detail'];
            $product->save();

            return $this->sendResponse(new ProductResource($product), 'Product updated successfully.');
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();
            return $this->sendResponse([], 'Product deleted successfully.');
        } catch (\Throwable $th) {
            Log::error(" :: EXCEPTION :: " . $th->getMessage() . "\n" . $th->getTraceAsString());
            return $this->sendError('Server Error!', [], 500);
        }
    }
}
