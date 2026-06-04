<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShipment;
use App\Models\Product;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $q = Order::with(['assignedTo'])
            ->when($request->search, fn($q, $s) =>
                $q->where(fn($q) =>
                    $q->where('order_number', 'like', "%$s%")
                      ->orWhere('customer_name', 'like', "%$s%")
                      ->orWhere('customer_email', 'like', "%$s%")
                ))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->payment, fn($q, $p) => $q->where('payment_status', $p))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.orders.index', ['orders' => $q]);
    }

    public function create()
    {
        $users    = User::orderBy('name')->get();
        $products = Product::active()->with('brand')->orderBy('name')->get();
        return view('admin.orders.create', compact('users', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'           => ['required', 'string', 'max:200'],
            'customer_email'          => ['required', 'email'],
            'customer_phone'          => ['nullable', 'string', 'max:30'],
            'company_name'            => ['nullable', 'string', 'max:200'],
            'gstin'                   => ['nullable', 'string', 'max:20'],
            'billing_address'         => ['nullable', 'string'],
            'shipping_address'        => ['nullable', 'string'],
            'payment_status'          => ['required', 'in:pending,partial,paid'],
            'payment_notes'           => ['nullable', 'string', 'max:500'],
            'estimated_delivery_date' => ['nullable', 'date'],
            'notes'                   => ['nullable', 'string'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.product_name'    => ['required', 'string', 'max:500'],
            'items.*.quantity'        => ['required', 'integer', 'min:1'],
            'items.*.unit_price'      => ['nullable', 'numeric', 'min:0'],
            'items.*.grade'           => ['nullable', 'string', 'max:5'],
            'items.*.notes'           => ['nullable', 'string', 'max:500'],
        ]);

        $order = DB::transaction(function () use ($request) {
            $order = Order::create([
                'order_number'            => Order::generateOrderNumber(),
                'assigned_to'             => $request->assigned_to ?: null,
                'customer_name'           => $request->customer_name,
                'customer_email'          => $request->customer_email,
                'customer_phone'          => $request->customer_phone,
                'company_name'            => $request->company_name,
                'gstin'                   => $request->gstin,
                'billing_address'         => $request->billing_address,
                'shipping_address'        => $request->shipping_address,
                'status'                  => 'confirmed',
                'payment_status'          => $request->payment_status,
                'payment_notes'           => $request->payment_notes,
                'estimated_delivery_date' => $request->estimated_delivery_date ?: null,
                'notes'                   => $request->notes,
                'confirmed_at'            => now(),
            ]);

            $total = 0;
            foreach ($request->items as $row) {
                if (empty(trim($row['product_name']))) continue;
                $unitPrice = isset($row['unit_price']) && $row['unit_price'] !== ''
                    ? (int) round($row['unit_price'] * 100) : 0;
                $qty = (int) $row['quantity'];
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $row['product_id'] ?? null,
                    'product_name' => $row['product_name'],
                    'product_sku'  => $row['product_sku'] ?? null,
                    'grade'        => $row['grade'] ?? null,
                    'quantity'     => $qty,
                    'unit_price'   => $unitPrice,
                    'notes'        => $row['notes'] ?? null,
                ]);
                $total += $unitPrice * $qty;
            }

            $order->update(['total_amount' => $total]);
            return $order;
        });

        return redirect()->route('adminhu.orders.show', $order)
            ->with('success', "Order {$order->order_number} created.");
    }

    public function show(Order $order)
    {
        $order->load(['quote', 'items.product', 'shipments', 'assignedTo']);
        $users = User::orderBy('name')->get();
        return view('admin.orders.show', compact('order', 'users'));
    }

    // ── Convert won quote to order ────────────────────────
    public function convertFromQuote(Quote $quote)
    {
        if ($quote->order()->exists()) {
            return redirect()->route('adminhu.orders.show', $quote->order)
                ->with('info', 'This quote already has an order.');
        }

        if ($quote->status !== 'won') {
            return back()->with('error', 'Only won quotes can be converted to orders.');
        }

        $order = DB::transaction(function () use ($quote) {
            $total = $quote->items->sum(fn($i) => ($i->unit_price ?? 0) * $i->quantity);

            $order = Order::create([
                'order_number'   => Order::generateOrderNumber(),
                'quote_id'       => $quote->id,
                'assigned_to'    => $quote->assigned_to,
                'customer_name'  => $quote->customer_name,
                'customer_email' => $quote->customer_email,
                'customer_phone' => $quote->customer_phone,
                'company_name'   => $quote->company_name,
                'gstin'          => $quote->gstin,
                'total_amount'   => $total,
                'status'         => 'confirmed',
                'confirmed_at'   => now(),
            ]);

            foreach ($quote->items as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product_name,
                    'product_sku'  => $item->product_sku,
                    'grade'        => $item->grade,
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->unit_price ?? 0,
                    'notes'        => $item->notes,
                ]);
            }

            return $order;
        });

        return redirect()->route('adminhu.orders.show', $order)
            ->with('success', "Order {$order->order_number} created from Quote {$quote->quote_number}.");
    }

    // ── Update order details ──────────────────────────────
    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'customer_name'           => ['required', 'string', 'max:200'],
            'customer_email'          => ['required', 'email'],
            'customer_phone'          => ['nullable', 'string', 'max:30'],
            'company_name'            => ['nullable', 'string', 'max:200'],
            'gstin'                   => ['nullable', 'string', 'max:20'],
            'billing_address'         => ['nullable', 'string'],
            'shipping_address'        => ['nullable', 'string'],
            'payment_status'          => ['required', 'in:pending,partial,paid'],
            'payment_notes'           => ['nullable', 'string', 'max:500'],
            'estimated_delivery_date' => ['nullable', 'date'],
            'notes'                   => ['nullable', 'string'],
        ]);

        $order->update($data);

        // Recalculate total from items
        $total = $order->items->sum(fn($i) => $i->unit_price * $i->quantity);
        $order->update(['total_amount' => $total]);

        return back()->with('success', 'Order updated.');
    }

    // ── Update order status ───────────────────────────────
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipped,delivered,cancelled'],
        ]);

        $timestamps = [];
        if ($data['status'] === 'confirmed' && !$order->confirmed_at) {
            $timestamps['confirmed_at'] = now();
        }
        if ($data['status'] === 'shipped' && !$order->shipped_at) {
            $timestamps['shipped_at'] = now();
        }
        if ($data['status'] === 'delivered' && !$order->delivered_at) {
            $timestamps['delivered_at'] = now();
        }

        $order->update(array_merge($data, $timestamps));

        return back()->with('success', 'Status updated to ' . Order::statusLabel($data['status']) . '.');
    }

    // ── Update item prices/quantities ─────────────────────
    public function updateItems(Request $request, Order $order)
    {
        $request->validate([
            'items'                  => ['required', 'array'],
            'items.*.id'             => ['required', 'exists:order_items,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'items.*.unit_price'     => ['nullable', 'numeric', 'min:0'],
            'items.*.notes'          => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($request->items as $row) {
            $item = $order->items()->find($row['id']);
            if (!$item) continue;
            $item->update([
                'quantity'   => $row['quantity'],
                'unit_price' => isset($row['unit_price']) ? (int) round($row['unit_price'] * 100) : $item->unit_price,
                'notes'      => $row['notes'] ?? null,
            ]);
        }

        $total = $order->fresh()->items->sum(fn($i) => $i->unit_price * $i->quantity);
        $order->update(['total_amount' => $total]);

        return back()->with('success', 'Items updated.');
    }

    // ── Add shipment ──────────────────────────────────────
    public function addShipment(Request $request, Order $order)
    {
        $data = $request->validate([
            'courier_name'      => ['nullable', 'string', 'max:100'],
            'tracking_number'   => ['nullable', 'string', 'max:100'],
            'tracking_url'      => ['nullable', 'url', 'max:500'],
            'status'            => ['required', 'in:preparing,dispatched,in_transit,out_for_delivery,delivered,exception'],
            'estimated_delivery'=> ['nullable', 'date'],
            'notes'             => ['nullable', 'string'],
        ]);

        if ($data['status'] === 'dispatched' || $data['status'] === 'in_transit') {
            $data['dispatched_at'] = now();
        }
        if ($data['status'] === 'delivered') {
            $data['delivered_at'] = now();
        }

        $order->shipments()->create($data);

        // Auto-update order status
        if ($data['status'] === 'delivered') {
            $order->update(['status' => 'delivered', 'delivered_at' => now()]);
        } elseif (in_array($data['status'], ['dispatched', 'in_transit', 'out_for_delivery'])) {
            if ($order->status !== 'delivered') {
                $order->update(['status' => 'shipped', 'shipped_at' => $order->shipped_at ?? now()]);
            }
        }

        return back()->with('success', 'Shipment entry added.');
    }

    // ── Update shipment ───────────────────────────────────
    public function updateShipment(Request $request, Order $order, OrderShipment $shipment)
    {
        $data = $request->validate([
            'courier_name'      => ['nullable', 'string', 'max:100'],
            'tracking_number'   => ['nullable', 'string', 'max:100'],
            'tracking_url'      => ['nullable', 'url', 'max:500'],
            'status'            => ['required', 'in:preparing,dispatched,in_transit,out_for_delivery,delivered,exception'],
            'estimated_delivery'=> ['nullable', 'date'],
            'notes'             => ['nullable', 'string'],
        ]);

        if ($data['status'] === 'delivered' && !$shipment->delivered_at) {
            $data['delivered_at'] = now();
            $order->update(['status' => 'delivered', 'delivered_at' => now()]);
        }

        $shipment->update($data);

        return back()->with('success', 'Shipment updated.');
    }
}
