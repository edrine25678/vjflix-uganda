<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Plan;
use App\Services\AirtelMoneyService;
use App\Services\MtnMobileMoneyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    protected array $paymentServices = [];

    public function __construct()
    {
        $this->paymentServices = [
            'mtn' => new MtnMobileMoneyService,
            'airtel' => new AirtelMoneyService,
        ];
    }

    public function initiate(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|in:mtn,airtel',
            'phone_number' => 'required|string',
        ]);

        $user = Auth::user();
        $plan = Plan::findOrFail($request->plan_id);

        $paymentService = $this->paymentServices[$request->payment_method] ?? null;

        if (! $paymentService) {
            throw ValidationException::withMessages([
                'payment_method' => 'Invalid payment method selected',
            ]);
        }

        // Validate phone number
        if (! $paymentService->validatePhoneNumber($request->phone_number)) {
            throw ValidationException::withMessages([
                'phone_number' => 'Invalid phone number format for '.$request->payment_method,
            ]);
        }

        // Initiate payment
        $result = $paymentService->initiatePayment([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'phone_number' => $request->phone_number,
            'amount' => $plan->price_ugx,
            'currency' => 'UGX',
            'customer_name' => $user->name,
            'customer_email' => $user->email,
        ]);

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('payments.status', [
            'reference' => $result['transaction_reference'],
        ])->with('info', $result['instructions'] ?? 'Payment initiated');
    }

    public function status(Request $request)
    {
        $reference = $request->query('reference');
        $payment = Payment::with(['plan', 'user'])
            ->where('transaction_reference', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('payments.status', compact('payment'));
    }

    public function checkStatus(Request $request, string $reference)
    {
        $payment = Payment::where('transaction_reference', $reference)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $paymentService = $this->paymentServices[$payment->network] ?? null;

        if (! $paymentService) {
            return response()->json([
                'success' => false,
                'message' => 'Payment service not available',
            ]);
        }

        $result = $paymentService->checkPaymentStatus($reference);

        return response()->json($result);
    }

    public function callback(Request $request, string $provider)
    {
        Log::info('Payment callback received', [
            'provider' => $provider,
            'payload' => $request->all(),
        ]);

        $paymentService = $this->paymentServices[$provider] ?? null;

        if (! $paymentService) {
            return response()->json(['error' => 'Invalid provider'], 400);
        }

        $result = $paymentService->processCallback($request->all());

        return response()->json($result);
    }

    public function history()
    {
        $user = Auth::user();
        $payments = $user->payments()
            ->with(['plan', 'subscription'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('payments.history', compact('payments'));
    }

    public function receipt(Payment $payment)
    {
        $this->authorize('view', $payment);

        return view('payments.receipt', compact('payment'));
    }
}
