<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QuoteResponseMail;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\QuoteResponse;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with(['assignedTo'])->withCount('items');

        // Status tab filter
        $status = $request->get('status', 'all');
        if ($status !== 'all') $query->where('status', $status);

        // Search
        if ($request->filled('q')) $query->search($request->q);

        // Date range
        if ($request->filled('from')) $query->whereDate('created_at', '>=', $request->from);
        if ($request->filled('to'))   $query->whereDate('created_at', '<=', $request->to);

        // Assigned
        if ($request->filled('assigned_to')) $query->where('assigned_to', $request->assigned_to);

        $quotes = $query->latest()->paginate(25)->withQueryString();

        // Status counts for tabs
        $counts = collect(['all', 'new', 'reviewing', 'quoted', 'won', 'lost'])->mapWithKeys(fn($s) =>
            [$s => $s === 'all' ? Quote::count() : Quote::where('status', $s)->count()]
        );

        $users = User::orderBy('name')->get();

        if ($request->input('export') === 'csv') {
            return $this->export($request);
        }

        return view('admin.quotes.index', compact('quotes', 'counts', 'status', 'users'));
    }

    public function create()  { return redirect()->route('adminhu.quotes.index'); }
    public function store(Request $request) { return redirect()->route('adminhu.quotes.index'); }

    public function show(Quote $quote)
    {
        $quote->load(['items.product', 'responses.user', 'assignedTo', 'order']);
        $users = User::orderBy('name')->get();
        return view('admin.quotes.show', compact('quote', 'users'));
    }

    public function edit(Quote $quote) { return redirect()->route('adminhu.quotes.show', $quote); }

    public function update(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'customer_name'  => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'company_name'   => ['nullable', 'string', 'max:200'],
            'gstin'          => ['nullable', 'string', 'max:20'],
            'message'        => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
            'assigned_to'    => ['nullable', 'exists:users,id'],
        ]);

        $quote->update($data);
        return back()->with('success', 'Quote updated.');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return redirect()->route('adminhu.quotes.index')->with('success', 'Quote deleted.');
    }

    // ── Status update ────────────────────────────────────
    public function updateStatus(Request $request, Quote $quote)
    {
        $request->validate(['status' => ['required', 'in:new,reviewing,quoted,won,lost']]);
        $quote->update(['status' => $request->status]);

        if ($request->expectsJson()) return response()->json(['ok' => true]);
        return back()->with('success', 'Status updated to "' . Quote::statusLabel($request->status) . '".');
    }

    // ── Update item prices ───────────────────────────────
    public function updateItems(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'items'               => ['array'],
            'items.*.id'          => ['required', 'exists:quote_items,id'],
            'items.*.unit_price'  => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'items.*.notes'       => ['nullable', 'string'],
        ]);

        $total = 0;
        foreach ($data['items'] as $itemData) {
            $item = QuoteItem::find($itemData['id']);
            if (!$item || $item->quote_id !== $quote->id) continue;
            $priceInPaise = isset($itemData['unit_price']) ? (int) round($itemData['unit_price'] * 100) : null;
            $item->update([
                'unit_price' => $priceInPaise,
                'quantity'   => $itemData['quantity'],
                'notes'      => $itemData['notes'] ?? null,
            ]);
            $total += ($priceInPaise ?? 0) * $itemData['quantity'];
        }

        $quote->update(['estimated_total' => $total ?: null]);
        return back()->with('success', 'Items updated. Estimated total: ' . ($total ? '₹' . number_format($total / 100, 0, '.', ',') : '—'));
    }

    // ── Respond (email or internal note) ─────────────────
    public function respond(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'type'        => ['required', 'in:email,internal_note'],
            'subject'     => ['nullable', 'string', 'max:250'],
            'body'        => ['required', 'string'],
            'is_internal' => ['nullable', 'boolean'],
            'attachment'  => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,csv,png,jpg,jpeg,zip'],
        ]);

        $isInternal = $data['type'] === 'internal_note';

        $attachmentPath = null;
        $attachmentName = null;
        if (!$isInternal && $request->hasFile('attachment')) {
            $file           = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store("quote-attachments/{$quote->id}", 'local');
        }

        $response = QuoteResponse::create([
            'quote_id'        => $quote->id,
            'user_id'         => auth()->id(),
            'type'            => $data['type'],
            'subject'         => $data['subject'] ?? null,
            'body'            => $data['body'],
            'direction'       => 'outbound',
            'is_internal'     => $isInternal,
            'sent_at'         => $isInternal ? null : now(),
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        // Send email if not internal
        if (!$isInternal) {
            try {
                Mail::to($quote->customer_email)->send(new QuoteResponseMail($quote, $response));
                $quote->update(['last_replied_at' => now()]);
            } catch (\Exception $e) {
                logger()->warning('Quote response mail failed: ' . $e->getMessage());
                return back()->with('error', 'Response saved but email failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', $isInternal ? 'Note added.' : 'Email sent to customer.');
    }

    // ── Assign to user ────────────────────────────────────
    public function assign(Request $request, Quote $quote)
    {
        $request->validate(['assigned_to' => ['nullable', 'exists:users,id']]);
        $quote->update(['assigned_to' => $request->assigned_to ?: null]);
        return back()->with('success', 'Quote assigned.');
    }

    // ── PDF download ──────────────────────────────────────
    public function downloadPdf(Quote $quote)
    {
        $quote->load(['items.product', 'assignedTo']);
        $pdf = Pdf::loadView('pdf.quote', compact('quote'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download("quote-{$quote->quote_number}.pdf");
    }

    // ── CSV export ────────────────────────────────────────
    public function export(Request $request)
    {
        $quotes = Quote::with('items')
            ->when($request->filled('status') && $request->status !== 'all', fn($q) => $q->where('status', $request->status))
            ->when($request->filled('from'), fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'),   fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()->get();

        $filename = 'quotes-'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($quotes) {
            $fh = fopen('php://output', 'w');
            fputcsv($fh, ['Quote #', 'Date', 'Customer', 'Email', 'Phone', 'Company', 'GSTIN', 'Status', 'Items', 'Estimated Total (₹)']);
            foreach ($quotes as $q) {
                $total = $q->items->sum(fn($i) => ($i->unit_price ?? 0) * $i->quantity);
                fputcsv($fh, [
                    $q->quote_number,
                    $q->created_at->format('d M Y'),
                    $q->customer_name,
                    $q->customer_email,
                    $q->customer_phone ?? '',
                    $q->company_name ?? '',
                    $q->gstin ?? '',
                    $q->status,
                    $q->items->count(),
                    $total > 0 ? number_format($total / 100, 2) : '',
                ]);
            }
            fclose($fh);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Duplicate quote ───────────────────────────────────
    public function duplicate(Quote $quote)
    {
        $new = $quote->replicate(['quote_number', 'status', 'created_at', 'updated_at']);
        $new->quote_number = Quote::generateQuoteNumber();
        $new->status       = 'new';
        $new->save();

        foreach ($quote->items as $item) {
            $new->items()->create($item->only(['product_id', 'product_name', 'grade', 'quantity', 'unit_price']));
        }

        return redirect()->route('adminhu.quotes.show', $new)->with('success', 'Quote duplicated as '.$new->quote_number);
    }
}
