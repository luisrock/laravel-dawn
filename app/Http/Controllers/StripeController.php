<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\UserPaymentIntent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\Product;
use Stripe\Webhook;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Exception\SignatureVerificationException;
use App\Mail\StripeTransactions;


class StripeController extends Controller
{

    public function sendEmail($user, $emailMessage, $subject = '')
    {
        $mailable = new StripeTransactions($emailMessage, $subject);
        Mail::to($user)->send($mailable);
    }

    private function storePaymentIntent($session, $paymentMethod, $paymentIntent)
    {
        Log::info('STORE', [$paymentIntent]);
        $method = $paymentMethod->type;
        $user_id = $session->metadata->user_id;
        //if not user)id, log ans return
        if (!$user_id) {
            Log::warning(__('STORE - NO USER ID'), ['session' => $session]);
            return;
        }

        
        $paymentIntentData = [
            'user_id' => $user_id,
            'stripe_payment_intent_id' => $session->payment_intent,
            'stripe_customer_id' => $session->customer,
            'payment_method' => $method,
            'status' => $session->payment_status,
            'currency' => $session->currency,
            'amount' => $session->amount_total,
            'description' => $session->metadata->description ?? null,
        ];
        
        if ($method === 'card') {
            $paymentIntentData['last_digits'] = $paymentMethod->card->last4 ?? null;
            $paymentIntentData['brand'] = $paymentMethod->card->brand ?? null;
            $paymentIntentData['card_id'] = $paymentMethod->id ?? null;
            $paymentIntentData['card_type'] = $paymentMethod->card->funding ?? null; 
        } elseif ($method === 'boleto') {
            $boleto_details = $paymentIntent->next_action->boleto_display_details ?? null;
            $paymentIntentData['barcode'] = ($boleto_details) ? $boleto_details->number ?? null : null;
            $paymentIntentData['expiration_date'] = ($boleto_details && $boleto_details->expires_at) ? date('Y-m-d H:i:s', $boleto_details->expires_at) ?? null : null;
            $paymentIntentData['voucher_url'] = ($boleto_details) ? $boleto_details->hosted_voucher_url ?? null : null;
        }
        //use paymentIntent ID to request the charge to Stripe API in order to get the receipt_url
        $latest_charge = $paymentIntent->latest_charge ?? null;
        if($latest_charge) {
            $charge = Charge::retrieve($latest_charge);
            if($charge) {
                $paymentIntentData['receipt_url'] = $charge->receipt_url ?? null;
            }
        }
        return UserPaymentIntent::create($paymentIntentData);
    }

    private function updatePaymentIntent($dbPaymentIntent, $paymentMethod, $status, $receipt_url = null)
    {
        Log::info('UPDATE', [$dbPaymentIntent]);

        $method = $paymentMethod->type;
        if(!$status) {
            //log and return
            Log::warning(__('UPDATE - NO STATUS'), ['paymentIntent' => $dbPaymentIntent]);
            return;
        }
        if(!$method) {
            //log and return
            Log::warning(__('UPDATE - NO METHOD'), ['paymentMethod' => $paymentMethod]);
            return;
        }
        if ($dbPaymentIntent) {
            $dbPaymentIntentData = [
                'status' => $status,
                'payment_method' => $method,
            ];
            if($receipt_url) {
                $dbPaymentIntentData['receipt_url'] = $receipt_url;
            }
    
            if ($method === 'boleto') {
                $boleto_details = $dbPaymentIntent->next_action->boleto_display_details ?? null;
                if($boleto_details && $boleto_details->number) {
                    $dbPaymentIntentData['barcode'] = $boleto_details->number;
                }
                if($boleto_details && $boleto_details->expires_at) {
                    $dbPaymentIntentData['expiration_date'] = date('Y-m-d H:i:s', $boleto_details->expires_at);
                }
                if($boleto_details && $boleto_details->hosted_voucher_url) {
                    $dbPaymentIntentData['voucher_url'] = $boleto_details->hosted_voucher_url;
                }
            }
            $dbPaymentIntent->update($dbPaymentIntentData);
        }
        return $dbPaymentIntent;
    }
      
    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    
        $user = Auth::user();
    
        $customerData = [
            'name' => $user->name,
            'email' => $user->email,
            'metadata' => ['user_id' => $user->id],
        ];

        //$customerData['email'] = 'expire_with_delay@example.com'; // for testing purposes REMOVER
    
        if (!$user->customer_id) {
            $customer = Customer::create($customerData);
            $user->update(['customer_id' => $customer->id]);
        } else {
            $customer = Customer::retrieve($user->customer_id);
            Customer::update($user->customer_id, $customerData);
        }
    
        // Replace this line with your Stripe Price ID
        $stripePriceId = config('services.stripe.price_id');

    
        $session = Session::create([
            "allow_promotion_codes" => true,
            'payment_method_types' => ['card', 'boleto'],
            'payment_method_options' => [
                'boleto' => [
                    'expires_after_days' => 3,
                ],
            ],
            'line_items' => [[
                'price' => $stripePriceId,
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer' => $customer->id,
            'metadata' => ['user_id' => $user->id],
            'success_url' => route('payment.success'),
            'cancel_url' => route('payment.cancel'),
        ]);
    
        return response()->json(['id' => $session->id]);
    }  
    public function paymentSuccess(Request $request)
    {
        // Redirect to the profile page with a success message
        return redirect()->route('profile.edit')->with([
            'status' => __('Payment successful!'),
            'status_type' => 'payment_successful',
        ]);
        
    }
    
    public function paymentCancel(Request $request)
    {
        $failureReason = session('payment_failure_reason', __('Payment cancelled.'));
        // Redirect to the profile page with a cancellation message
        return redirect()->route('profile.edit')->with([
            'status' => __('Payment failed... ') . $failureReason,
            'status_type' => 'payment_failed',
        ]);
    }    

    //not being used (getUserPaymentInfoFromDb insted)
    public function getPaymentInfoFromStripe(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    
        $user = Auth::user();
    
        $charges = Charge::all([
            'customer' => $user->customer_id,
            'limit' => 10,
        ]);
    
        if (!empty($charges->data)) {
            // Sort charges by created date in descending order
            usort($charges->data, function ($a, $b) {
                return $b->created <=> $a->created;
            });
    
            $charge = $charges->data[0];
            $chargeInfo = [
                'id' => $charge->id,
                'amount' => $charge->amount / 100,
                'currency' => strtoupper($charge->currency) ?: 'USD',
                'created' => date('Y-m-d H:i:s', $charge->created),
                'payment_method' => $charge->payment_method_details->type,
            ];
    
            return response()->json($chargeInfo);
        }
    
        return response()->json(['error' => __('No payment information found.')]);
    }

    public function getUserPaymentInfoFromDb(Request $request, $user_id)
    {

        $authUser = Auth::user();

        if ($authUser->id != $user_id) {
            return response()->json(['error' => __('Unauthorized access')], 403);
        }

        $paymentIntents = UserPaymentIntent::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    
        if (!$paymentIntents->isEmpty()) {
            $paymentIntent = $paymentIntents->first();
            $chargeInfo = [
                'id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => strtoupper($paymentIntent->currency) ?: 'USD',
                'created' => $paymentIntent->created_at->format('Y-m-d H:i:s'),
                'payment_method' => $paymentIntent->payment_method,
                'receipt_url' => $paymentIntent->receipt_url ?? ''
            ];
            if ($paymentIntent->payment_method === 'card') {
                $chargeInfo['brand'] = $paymentIntent->brand ?? null;
                $chargeInfo['last_digits'] = $paymentIntent->last_digits ?? null;
            } else if($paymentIntent->payment_method === 'boleto') {
                $chargeInfo['barcode'] = $paymentIntent->barcode ?? null;
                $chargeInfo['expiration_date'] = $paymentIntent->expiration_date ?? null;
                $chargeInfo['voucher_url'] = $paymentIntent->voucher_url ?? null;
                $chargeInfo['status'] = $paymentIntent->status ?? null;
            }
    
            return response()->json($chargeInfo);
        }
    
        return response()->json(['error' => __('No payment information found.')]);
    }
    
    //not being used yet ('registered' shouldn't b harcoded)
    public function updateRoleToRegistered(Request $request)
    {
        $user = Auth::user();
        $registeredRole = Role::findByName('registered');
        $user->syncRoles($registeredRole);

        return response()->json(['status' => 'success']);
    }


    private function getProductMetaFromSession($session)
    {
        // Get the line items associated with the checkout session
        $line_items = Session::allLineItems($session->id, ['limit' => 1]);
        foreach ($line_items->autoPagingIterator() as $line_item) {
            if ($line_item->price->product) {
                // Get the product associated with the line item
                $product = Product::retrieve($line_item->price->product);
                // Access the product metadata
                $metadata = $product->metadata ?? [];
                if($metadata) {
                    //convert to array
                    $metadata = json_decode(json_encode($metadata), true);
                }
                //log
                Log::info('Product metadata: ' . json_encode($metadata));
                return $metadata;
            }
        }
    }

    public function handleStripeWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $signatureHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        $app_name = config('app.name');

        try {
            $event = Webhook::constructEvent($payload, $signatureHeader, config('services.stripe.webhook_secret'));
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => $e->getMessage()], 400);
        }

        $event_type = $event->type;
        $session = $event->data->object;
        $payment_status = $session->payment_status ?? null;
        $paid = $payment_status === 'paid';
        $method = null;

        try {
            // Retrieve the PaymentIntent
            $paymentIntent = PaymentIntent::retrieve($session->payment_intent);
            // Get the payment method ID
            $paymentMethodId = $paymentIntent->payment_method;
            // Retrieve the payment method details
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $method = $paymentMethod->type;
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => $e->getMessage()], 400);
        }

        Log::info("Webhook event: $event_type", ['data' => $event->data, 'payment_intent' => $paymentIntent, 'payment_method' => $paymentMethod]);

        $boleto = $method === 'boleto';
        $card = $method === 'card';
        $userId = $session->metadata->user_id;
        //log user id
        Log::info("User id: $userId");
        $user = ($userId) ? User::find($userId) : null;

        $dbPaymentIntent = UserPaymentIntent::where('stripe_payment_intent_id', $session->payment_intent)->first();
        if (!$dbPaymentIntent) {
            if ($userId) {
                $dbPaymentIntent = $this->storePaymentIntent($session, $paymentMethod, $paymentIntent);
            }
        } else {
            //try to get the receipt_url
            $receipt_url = null;
            $latest_charge = $paymentIntent->latest_charge ?? null;
            if ($latest_charge) {
                $charge = Charge::retrieve($latest_charge);
                if ($charge) {
                    $receipt_url = $charge->receipt_url ?? null;
                }
            }
            $dbPaymentIntent = $this->updatePaymentIntent($dbPaymentIntent, $paymentMethod, $payment_status, $receipt_url);
        }

        switch ($event_type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($user, $app_name, $paid, $payment_status, $method, $session, $event, $boleto, $paymentIntent);

                break;

            case 'payment_intent.requires_action':
                $this->handlePaymentIntentRequiresAction($user, $method);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentPaymentFailed($user, $paymentIntent);
                break;

            case 'checkout.session.async_payment_succeeded':
                $this->handleAsyncPaymentSucceeded($user, $app_name, $method, $session);
                break;
            case 'checkout.session.async_payment_failed':
                $this->handleAsyncPaymentFailed($user, $app_name, $method);
                break;
        
            default:
                Log::info('Unhandled webhook event', ['event_type' => $event_type]);
        }
            
        return response()->json(['status' => 'success'], 200); 
    }

    private function updateUserWithProductMetaInfo($user, $product_meta, $costumer_id)
    {
        if($product_meta) {
            $new_role = Role::findByName($product_meta['role']);
            if(!$new_role) {
                $new_role = Role::create(['name' => $product_meta['role']]);
            } 
            if($new_role) {
                $user->syncRoles($new_role);
            }
            if($product_meta['period_frequency'] && $product_meta['period_number']) {
                $number = intval($product_meta['period_number']);
                if($product_meta['period_frequency'] === 'year') {
                    $user->update([
                        'customer_id' => $costumer_id,
                        'subscription_ends_at' => now()->addYears($number),
                    ]);
                } else if($product_meta['period_frequency'] === 'month') {
                    $user->update([
                        'customer_id' => $costumer_id,
                        'subscription_ends_at' => now()->addMonths($number),
                    ]);
                } else if($product_meta['period_frequency'] === 'day') {
                    $user->update([
                        'customer_id' => $costumer_id,
                        'subscription_ends_at' => now()->addDays($number),
                    ]);
                } 
            }
        }
    }

    private function handleCheckoutSessionCompleted($user, $app_name, $paid, $payment_status, $method, $session, $event, $boleto, $paymentIntent)
    {
        if(!$user) {
            $subject = __('Checkout with unindentified user');
            $message = __('Data: ') . json_encode($event->data);
            //get users with admin or superadmin role emails
            $admins_emails = User::role(['admin', 'superadmin'])->pluck('email')->toArray();
            $this->sendEmail($admins_emails, $message, $subject);
            return response()->json(['error' => __('User not found.')], 400);
        }
        
        //check if payment_status is paid
        if ($paid) {
            Log::info('Method is ' . $method . '. Status is PAID. Updating user role, customer_id, and subscription_ends_at', ['user_id' => $user->id]);
            // Update the user's role and subscription_ends_at
            //get the role associated with the product as metadata
            $product_meta = $this->getProductMetaFromSession($session);
            $costumer_id = $session->customer;
            $this->updateUserWithproductMetaInfo($user, $product_meta, $costumer_id);
            
            //send email to user
            $subject = __('Thanks for your purchase!');
            $message = __("Your payment for :app_name was quickly approved and you are now a :new_role.\n\nYour subscription will expire on :subscription_ends_at.\n\nEnjoy!", ['app_name' => $app_name, 'new_role' => $user->role, 'subscription_ends_at' => $user->subscription_ends_at]);
            $this->sendEmail($user, $message, $subject);                 
        } else {
            Log::info("Status: $payment_status. Method: $method. Sending email to user", ['user_id' => $user->id]);
            
            if ($boleto) {    
                $boleto_details = $paymentIntent->next_action->boleto_display_details;
                $expiration = $boleto_details->expires_at;
                $boleto_expiration_date = date(config('app.date_format'), $expiration);
                $boleto_url = $boleto_details->hosted_voucher_url;
                $boleto_cod_barras = $boleto_details->number;
                $subject = __(":app_name: Your Boleto Information", ['app_name' => $app_name]);
                $message = __("Your Boleto for :app_name has the following number (bar code): :boleto_cod_barras.<br><br>It will expire at :boleto_expiration_date.\n\n", ['app_name' => $app_name, 'boleto_cod_barras' => $boleto_cod_barras, 'boleto_expiration_date' => $boleto_expiration_date]);
                $message .= __("You can find your Boleto voucher at the following link: :boleto_url<br><br><br>", ['boleto_url' => $boleto_url]);
                $message .= __("If you have any questions, please contact us at :admin_email<br><br>", ['admin_email' => config('mail.from.address')]);
                $message .= __("Best regards.");
                $this->sendEmail($user, $message, $subject);
            } 
        }
    }

    private function handlePaymentIntentRequiresAction($user, $method)
    {
        Log::info("Method: $method. EVENT: Requires Action", ['user_id' => $user->id]);
    }

    private function handlePaymentIntentPaymentFailed($user, $paymentIntent)
    {
        if($paymentIntent->last_payment_error->message) {
            $payment_failed_reason = $paymentIntent->last_payment_error->message;
        } else {
            $payment_failed_reason = __('Unknown');
        }
        //Send email to user
        $subject = __('Payment Failed');
        $message = __("Your payment failed. Reason: :payment_failed_reason. Please try again.", ['payment_failed_reason' => $payment_failed_reason]);
        $this->sendEmail($user, $message, $subject);

        // Store the failure reason in the user's session
        session([
            'payment_failure_reason' => $payment_failed_reason,
        ]);
    }

    private function handleAsyncPaymentSucceeded($user, $app_name, $method, $session)
    {
        Log::info("Method: $method. Async Payment Succeeded. Updating user role, customer_id, and subscription_ends_at", ['user_id' => $user->id]);
        //get the role associated with the product as metadata
        $product_meta = $this->getProductMetaFromSession($session);
        $costumer_id = $session->customer;
        $this->updateUserWithproductMetaInfo($user, $product_meta, $costumer_id);
        
        //send email to user
        $subject = __('Thanks for your purchase!');
        $message = __("Your payment for :app_name was approved and you are now a :new_role.\n\nYour subscription will expire on :subscription_ends_at.\n\nEnjoy!", ['app_name' => $app_name, 'new_role' => $user->role, 'subscription_ends_at' => $user->subscription_ends_at]);
        $this->sendEmail($user, $message, $subject);
    }

    private function handleAsyncPaymentFailed($user, $app_name, $method)
    {
        Log::warning("Method: $method. Async Payment Failed.", ['user_id' => $user->id]);
        // Send email to the user
        $subject = __(":app_name: Payment Failed", ['app_name' => $app_name]);
        $message = __("We are sorry to inform you that your :method payment for :app_name has failed.<br><br>Please, place a new order.", ['method' => $method, 'app_name' => $app_name]);
        $this->sendEmail($user, $message, $subject);
    }
}
