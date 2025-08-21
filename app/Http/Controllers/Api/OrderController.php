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
use Berkayk\OneSignal\OneSignalFacade as OneSignal;
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

    // public function store(StoreOrderRequest $request)
    // {
    // $data = $request->validated();

    // // Buat order
    // $order = Order::create($data);

    // // Set Xendit API key
    // Configuration::setXenditKey(config('services.xendit.api_key', ''));

    // $apiInstance = new InvoiceApi();
    // $externalId = 'INV-' . $order->id; // external_id unik

    // $create_invoice_request = new CreateInvoiceRequest([
    //     'external_id' => $externalId,
    //     'description' => 'Payment for ' . $order->service,
    //     'amount' => $order->price,
    //     'invoice_duration' => 172800,
    //     'currency' => 'IDR',
    //     'reminder_time' => 1,
    //     'success_redirect_url' => url('flutter/success'),
    //     'failure_redirect_url' => url('flutter/failure'),
    // ]);

    // try {
    //     // Buat invoice
    //     $result = $apiInstance->createInvoice($create_invoice_request);

    //     // Ambil payment_url dan simpan ke order
    //     $order->payment_url = $result->getInvoiceUrl();
    //     $order->external_id = $externalId; // Simpan external_id supaya callback bisa menemukan order
    //     $order->save();

    //     return response()->json([
    //         'status' => 'success',
    //         'data' => [
    //             'order' => $order,
    //             'payment_url' => $order->payment_url,
    //             'external_id' => $externalId,
    //         ]
    //     ]);
    // } catch (\Exception $e) {
    //     return response()->json([
    //         'status' => 'error',
    //         'message' => $e->getMessage()
    //     ], 500);
    // }
    // }

        public function store(StoreOrderRequest $request)
        {
            $data = $request->validated();

            // Buat order
            $order = Order::create($data);
            //XENDIT_SERVER_KEY
            Configuration::setXenditKey(config('services.xendit.api_key', ''));

            $apiInstance = new InvoiceApi();
            $create_invoice_request = new CreateInvoiceRequest([
                'external_id' => 'INV-' . $order->id,
                'description' => 'Payment for ' . $order->service,
                'amount' => $order->price,
                'invoice_duration' => 172800,
                'currency' => 'IDR',
                'reminder_time' => 1,
                'success_redirect_url' => 'flutter/success',
                'failure_redirect_url' => 'flutter/failure',
            ]);


            try {
                $result = $apiInstance->createInvoice($create_invoice_request);
                $payment_url = $result->getInvoiceUrl();
                $order->payment_url = $payment_url;
                $order->save();

                return response()->json([
                    'status' => 'success',
                    'data' => $order
                ], 201);
            } catch (\Xendit\XenditSdkException $e) {
                echo 'Exception when calling InvoiceApi->createInvoice: ', $e->getMessage(), PHP_EOL;
                echo 'Full Error: ', json_encode($e->getFullError()), PHP_EOL;
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

    public function handleCallback(Request $request)
    {
        // cek token callback
        $xenditCallbackToken = env('XENDIT_CALLBACK_TOKEN', '');
        $callbackToken = $request->header('x-callback-token');
        if ($callbackToken != $xenditCallbackToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $data = $request->all();
        // \Log::info('Xendit callback payload:', $data); // buat debug

        $externalId = $data['external_id'] ?? null;
        if (!$externalId) {
            return response()->json([
                'status' => 'error',
                'message' => 'external_id not found'
            ], 400);
        }

        $parts = explode('-', $externalId);
        $orderId = $parts[1] ?? null;
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found'
            ], 404);
        }

        $status = $data['status'] ?? null;
        $order->status = $status;
        $order->status_service = 'Active';
        $order->save();

        $doctor = User::find($order->doctor_id);

        if ($doctor && $doctor->one_signal_token) {
            OneSignal::sendNotificationToUser(
                "You have a new {$order->service} from {$order->patient->name}",
                $doctor->one_signal_token
            );
        }

        // cek status
        if ($status === 'success') {
            $order->status = 'success';
            $order->status_service = 'Active';
            $order->start_time = now();
            $order->end_time = now()->addMinutes($order->duration);
            $order->save();

            // Kirim notifikasi ke pasien
            OneSignal::sendNotificationToUser(
                "Your appointment for {$order->service} with Dr. {$doctor->name} is confirmed.",
                $order->patient->one_signal_token
            );
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
        //total income order status success
        $totalIncome = $orders->where('status', 'success')->sum('price');
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

     public function getOrderByDoctorQuery($doctor_id, $service, $status_service)
    {
        $orders = Order::where('doctor_id', $doctor_id)
            ->where('service', $service)
            ->where('status_service', $status_service)
            ->with('patient', 'doctor', 'clinic')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $orders
        ]);
    }

}
