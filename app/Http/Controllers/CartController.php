<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use DB;
use App\Cart;
use App\Product;
use Validator;

class CartController extends Controller
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
    public function addToCart(Request $request, $prodId)
    {
        if (!is_numeric($prodId)) {
            return response()->json([
                'status' => $this->clientErrorStatus,
                'error' => 'Product Id must be an integer',
            ]);
        }
        $user = Auth::user();
        if ($user) {
            $user_id = $user->id;
            $prod_id = (int) $prodId;
            $prod = Product::find($prod_id);
            if (!$prod) {
                return response()->json([
                    'status' => $this->notFoundStatus,
                    'error' => 'Product does not exist'
                ]);
            } else {
                $cart = DB::table('shopping_cart')->insert([
                    'user_id' => $user_id,
                    'product_id' => $prod_id
                ]);
                if ($cart) {
                    return response()->json([
                        'status' => $this->successStatus,
                        'message' => 'Product added to cart'
                    ]);
                } else {
                    return response()->json([
                        'status' => $this->serverErrorStatus,
                        'error' => 'An error occured!'
                    ]);
                }
            }
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
     * Get items in user cart
     *
     * @return \Illuminate\Http\Response
     */
    public function getCart()
    {
        $user = Auth::user();
        $userId = $user->id;
        $carts = Cart::select('product_id')->where('user_id', $userId)->get();
    }
}
