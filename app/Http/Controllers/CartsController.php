<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Processing;
use Stripe;

class CartsController extends Controller
{
    public function index()
    {
        return view('pages.checkout');
    }

 
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!$request->get('product_id')) {
            return [
                'message' => 'Itens retornados',
                'items' => Cart::where('user_id', auth()->user()->id)->sum('quantity') 
            ];
        }

        $product = Product::find($request->get('product_id'));

        $productFoundInCart = Cart::where('product_id', $request->get('product_id'))->pluck('id');
        
        if ($productFoundInCart->isEmpty()) {
            $cartItem = Cart::create([
                'product_id' => $product->id,
                'user_id' => auth()->user()->id,
                'quantity' => 1,
                'price' => $product->sale_price
            ]);
        } else {
            $cartItem = Cart::where('product_id', $request->get('product_id'))->increment('quantity');
        }
        
        $userItems = Cart::where('user_id', auth()->user()->id)->sum('quantity');

        if ($cartItem) {
            return [
                'message' => 'Item adicionado ao carrinho!',
                'items' => $userItems 
            ];
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function getCartItemsForCheckout() {
        $cartItems = Cart::with('product')->where('user_id', auth()->user()->id)->get();
        $finalData = [];
        $amount = 0;

        if (isset($cartItems)) {

            foreach ($cartItems as $cartItem) {
                
                if ($cartItem->product) {
                    
                    foreach ($cartItem->product as $cartProduct) {

                        if ($cartProduct->id == $cartItem->product_id) {
    
                            $finalData[$cartItem->product_id]['id'] = $cartProduct->id;
                            $finalData[$cartItem->product_id]['name'] = $cartProduct->name;
                            $finalData[$cartItem->product_id]['sale_price'] = $cartItem->price;
                            $finalData[$cartItem->product_id]['quantity'] = $cartItem->quantity;
                            $finalData[$cartItem->product_id]['total'] = $cartItem->quantity * $cartItem->price;
                            $amount += $cartItem->quantity * $cartItem->price;
                            $finalData['totalAmount'] = $amount;
                        }
                    }
                }

            }
        }

        return response()->json($finalData);
    }

    public function processPayment(Request $request) {
        $country = $request->get('country');
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $adress = $request->get('adress');
        $number = $request->get('number');
        $city = $request->get('city');
        $email = $request->get('email');
        $phone = $request->get('phone');
        $cardType = $request->get('cardType');
        $cardNumber = $request->get('cardNumber');
        $cvv = $request->get('cvv');
        $expirationMonth = $request->get('expirationMonth');
        $expirationYear = $request->get('expirationYear');
        $amount = $request->get('amount');

        $orders = $request->get('order');
        $ordersArray = [];
        foreach ($orders as $order) {
            if (!empty($order['id'])) {
                $ordersArray[$order['id']]['order_id'] = $order['id'];
                $ordersArray[$order['id']]['quantity'] = $order['quantity'];
            }
        }

        $stripe = Stripe::make(env('STRIPE_KEY'));
        $token = $stripe->tokens()->create([
            'card' => [
                'number' => $cardNumber,
                'cvc' => $cvv,
                'exp_month' => $expirationMonth,
                'exp_year' => $expirationYear
            ]
        ]);

        if (!$token['id']) {
            session()->flush('Erro', 'Token ID não é valido.');
            return;
        }

        $customer = $stripe->customers()->create([
            'name' => $firstName . " " . $lastName,
            'email' => $email,
            'phone' => $phone,
            'address' => [
                'line1' => $adress,
                'line2' => '',
                'city' => $city,
                'country' => $country
            ],
            'shipping' => [
                'name' => $firstName . " " . $lastName,
                'address' => [
                    'line1' => $adress,
                    'line2' => '',
                    'city' => $city,
                    'country' => $country
                ],
            ],
            'source' => $token['id']
        ]);

        $charge = $stripe->charges()->create([
            'customer' => $customer['id'],
            'currency' => 'USD',
            'amount' => $amount,
            'description' => 'Pagamento do pedido'
        ]);
        
        if ($charge['status'] == 'succeeded') {
            $customerIdStripe = $charge['id'];
            $amountReceived = $charge['amount'];
            
            $processingDetails = Processing::create([
                'client_id' => auth()->user()->id,
                'client_name' => $firstName . " " . $lastName,
                'client_address' => json_encode(['line1' => $adress, 'line2' => '', 'city' => $city, 'country' => $country]),
                'order_details' => json_encode($ordersArray),
                'amount' => $amount,
                'currency' => $charge['currency']
            ]);

            if ($processingDetails) {
                Cart::where('user_id', auth()->user()->id)->delete();
                return ['success' => 'Pedido foi concluído com sucesso!'];
            }
        } else {
            return ['error' => 'Não foi possível fazer o pedido!'];
        }

    }
}
