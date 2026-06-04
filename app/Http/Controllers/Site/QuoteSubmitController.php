<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Mail\QuoteReceivedMail;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuoteSubmitController extends Controller
{
    public function show()
    {
        $cart = collect(session('cart', []));
        if ($cart->isEmpty()) {
            return redirect()->route('quote.cart')->with('error', 'Your quote cart is empty.');
        }
        $authUser = auth()->user();
        return view('quotes.submit', compact('cart', 'authUser'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'  => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:200'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'company_name'   => ['nullable', 'string', 'max:200'],
            'gstin'          => ['nullable', 'string', 'max:20'],
            'message'        => ['nullable', 'string', 'max:2000'],
        ]);

        $cart = collect(session('cart', []));
        if ($cart->isEmpty()) {
            return redirect()->route('quote.cart')->with('error', 'Your cart is empty.');
        }

        // Create quote
        $quote = Quote::create([
            'user_id'        => auth()->id(),
            'quote_number'   => Quote::generateQuoteNumber(),
            'customer_name'  => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'company_name'   => $data['company_name'] ?? null,
            'gstin'          => $data['gstin'] ?? null,
            'message'        => $data['message'] ?? null,
            'status'         => 'new',
            'source'         => 'website',
        ]);

        // Create quote items from cart
        foreach ($cart as $item) {
            QuoteItem::create([
                'quote_id'    => $quote->id,
                'product_id'  => $item['product_id'],
                'product_name'=> $item['product_name'],
                'product_sku' => $item['sku'] ?? null,
                'grade'       => $item['grade'],
                'quantity'    => $item['qty'],
                'unit_price'  => null, // admin fills after review
            ]);
        }

        // Send confirmation email to customer
        try {
            Mail::to($quote->customer_email)->send(new QuoteReceivedMail($quote));
        } catch (\Exception $e) {
            // Non-fatal — quote still created even if mail fails
            logger()->warning('Quote mail failed: ' . $e->getMessage());
        }

        // Clear cart
        session()->forget('cart');

        return redirect()->route('quote.confirmation', $quote->quote_number);
    }

    public function confirmation(string $quoteNumber)
    {
        $quote = Quote::where('quote_number', $quoteNumber)->with('items')->firstOrFail();
        return view('quotes.confirmation', compact('quote'));
    }
}
