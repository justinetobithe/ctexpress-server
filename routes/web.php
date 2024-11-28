<?php

use App\Events\PaymongoPaidEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::group([
    'prefix' => 'webhook'
], function () {
    Route::post('/paymongo', function (Request $request) {
        if($request->input('data.attributes.type') == 'payment.paid') {
            // BROADCAST
            broadcast(new PaymongoPaidEvent([
                'payment_intent_id' => $request->input('data.attributes.data.attributes.payment_intent_id', null)
            ]));
            return $request->all();
        }
    });
});
