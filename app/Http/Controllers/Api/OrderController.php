<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\User;
use Berkayk\OneSignal\OneSignalFacade;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class OrderController extends Controller
{
    //

    public function index()
    {
        $orders = Order::with('patient', 'doctor', 'clinic')->get();
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();

        $order = Order::create($data);

        // XENDIT_SERVER_KEY

       Configuration::setXenditKey(config('services.xendit.api_key', ''));
        $apiInstance = new InvoiceApi();
        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => 'INV-' . $order->id,
            'description' => 'Payment for ' . $order->service,
            'amount' => $order->price,
            'invoice_duration' => 172800,
            'currency' => 'IDR',
            'reminder_time' => 1,
            'success_redirect_url' => url('flutter/success'),
            'failure_redirect_url' => url('flutter/failure'),
        ]);

        try {
            $result = $apiInstance->createInvoice($create_invoice_request);
            $payment_url = $result->getInvoiceUrl();
            $order->payment_url = $payment_url;
            $order->save();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'order' => $order,
                    'payment_url' => $payment_url
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        $order = Order::with('patient', 'doctor', 'clinic')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);
    }

     //handle callback xendit
    public function handleCallback(Request $request)
    {
        //check header 'x-callback-token
        $xenditCallbackToken = env('XENDIT_CALLBACK_TOKEN', '');
        $callbackToken = $request->header('x-callback-token');
        if ($callbackToken != $xenditCallbackToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $data = $request->all();
        $externalId = $data['external_id'];
        $order = Order::where('id', explode('-', $externalId)[1])->first();
        $order->status = $data['status'];
        $order->status_service = 'Active';
        $doctor = User::find($order->doctor_id);
        $order->save();
        OneSignalFacade::sendNotificationToUser(
            "You have a new " .$order->service . " from " . $order->patient->name,
            $doctor->one_signal_token,
            $url = null,
            $data = null,
            $buttons = null,
            $schedule = null,
        );

        if($data['status'] == 'Success'){

        }

        return response()->json([
            'status' => 'success',
            'data' => $order
        ]);

    }

    //get order history by patient desc
    public function getOrderByPatient($patient_id)
    {
        $orders = Order::where('patient_id', $patient_id)->with('patient', 'doctor', 'clinic')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    //get order history by doctor desc
    public function getOrderByDoctor($doctor_id)
    {
        $orders = Order::where('doctor_id', $doctor_id)->with('patient', 'doctor', 'clinic')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    //get order history by clinic desc
    public function getOrderByClinic($clinic_id)
    {
        $orders = Order::where('clinic_id', $clinic_id)->with('patient', 'doctor', 'clinic')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

    //admin clinic summary
    public function getSummary($clinic_id)
    {
        $orders = Order::where('clinic_id', $clinic_id)->with('patient', 'doctor', 'clinic')->get();
        $orderCount = $orders->count();
        //total income order status paid
        $totalIncome = $orders->where('status', 'paid')->sum('price');
        //doctor count
        $doctorCount = $orders->groupBy('doctor_id')->count();
        //patient count
        $patientCount = $orders->groupBy('patient_id')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'order_count' => $orderCount,
                'total_income' => $totalIncome,
                'doctor_count' => $doctorCount,
                'patient_count' => $patientCount,
            ]
        ]);
    }
}
