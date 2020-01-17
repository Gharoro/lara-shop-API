<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Response;
use App\User;
use App\Product;
use Validator;

class ProductsController extends Controller
{
    public $successStatus = 200;
    public $clientErrorStatus = 400;
    public $notFoundStatus = 404;
    public $serverErrorStatus = 500;

    /**
     * Add a Product
     *
     * @return \Illuminate\Http\Response
     */
    public function addProduct(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json([
                'status' => 403,
                'error' => 'You are not allowed to access this resource'
            ]);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'description' => 'required',
                'category' => 'required',
                'price' => 'required',
                'qty' => 'required',
                'ratings' => 'nullable',
                'image' => 'required',
                'size' => 'nullable',
                'weight' => 'nullable',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $input = $request->all();

        $file = $request->file('image');
        $fileName = $file->getClientOriginalName();
        $destinationPath = base_path() . '/public/uploads/images/product/' . $fileName;
        $file->move($destinationPath, $fileName);

        $input['image'] = $destinationPath;
        $product = Product::create($input);
        if ($product) {
            return response()->json([
                'status' => $this->successStatus,
                'message' => 'Product successfully added',
                'product' => $product
            ]);
        }
    }

    /**
     * Fetch all products
     *
     *  @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        $results = Product::orderBy('id', 'DESC')->paginate(1);
        if (sizeof($results) < 1) {
            return response()->json([
                'status' => $this->notFoundStatus,
                'message' => 'No product found'
            ]);
        } else {
            return response()->json([
                'status' => $this->successStatus,
                'message' => 'Success',
                'products' => $results
            ]);
        }
    }


    /**
     * Fetch one product
     *
     * @param  int  $prodId
     * @return \Illuminate\Http\Response
     */
    public function getOneProduct($prodId)
    {
        if (!is_numeric($prodId)) {
            return response()->json([
                'status' => $this->clientErrorStatus,
                'error' => 'Product Id must be an integer',
            ]);
        }
        $integerId = (int) $prodId;
        $prod = Product::find($integerId);
        if (!$prod) {
            return response()->json([
                'status' => $this->notFoundStatus,
                'error' => 'No product found, check your product Id'
            ]);
        } else {
            return response()->json([
                'status' => $this->successStatus,
                'message' => 'Success',
                'product' => $prod
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $prodId
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct($prodId)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json([
                'status' => 403,
                'error' => 'You are not allowed to access this resource'
            ]);
        }
        if (!is_numeric($prodId)) {
            return response()->json([
                'status' => $this->clientErrorStatus,
                'message' => 'Product Id must be an integer',
            ]);
        }
        $integerId = (int) $prodId;
        $prod = Product::find($integerId);
        if (!$prod) {
            return response()->json([
                'status' => $this->notFoundStatus,
                'error' => 'Product does not exist'
            ]);
        } else {
            $deleted = $prod->delete();
            if ($deleted) {
                return response()->json([
                    'status' => $this->successStatus,
                    'message' => 'Product successfuly deleted'
                ]);
            }
            return response()->json([
                'status' => $this->serverErrorStatus,
                'error' => 'Unable to delete product'
            ]);
        }
    }
}
