<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Resources\UserProfileFullResource;
use App\Http\Resources\UserProfileLiteResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Payment\SubscriptionPackage;
use App\Models\Chat\ChatMessage;
use App\Models\Payment\Invoice;
use App\Http\Resources\Payment\InvoiceResource;


class InvoiceController extends Controller
{
    function createInvoice(Request $request){
    	$user = Auth::user();
    	if($user){
    		DB::beginTransaction();
    		$invoice = new Invoice;
            $invoice->title = $request->title;
    		$invoice->from_id = $user->id;
    		$invoice->to_id = $request->to_id;
    		$invoice->invoice_description = $request->invoice_description;
    		$invoice->price = $request->price;
    		$saved = $invoice->save();
    		if($saved){
    			$message = new ChatMessage;
    			$message->user_id = $user->id;
    			$message->chat_id = $request->chat_id;
    			$message->invoice_id = $invoice->id;
    			$messageSaved = $message->save();
    			if($messageSaved){
    				DB::commit();
    			}
    			else{
    				DB::rollBack();
    			}
    			return response()->json(['status' => true, 'message' => 'Invoice created', 'data' => new InvoiceResource($invoice)]);
    		}
    		else{
    			DB::rollBack();
				return response()->json(['status' => false, 'message' => 'Error creating invoice', 'data' => NULL]);
    		}
    	}
    	else{
    		return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => NULL]);
    	}
    }
}
