<x-mail::message>
# Quote Request Received — {{ $quote->quote_number }}

Hi **{{ $quote->customer_name }}**,

Thank you for your interest in **pcfy.in**! We've received your quote request and will review it within **24 hours**.

Your quote reference number is: **{{ $quote->quote_number }}**

@if($quote->items->count())
## Items Requested

| Product | Grade | Qty |
|---|---|---|
@foreach($quote->items as $item)
| {{ $item->product_name }} | Grade {{ $item->grade }} | {{ $item->quantity }} |
@endforeach

@endif
@if($quote->company_name)
**Company:** {{ $quote->company_name }}
@endif
@if($quote->message)

**Your message:** {{ $quote->message }}
@endif

---

We'll respond to **{{ $quote->customer_email }}** with pricing and availability within 24 hours.

📞 **+91 98765 43210** &nbsp;|&nbsp; ✉ **hello@pcfy.in**

<x-mail::button :url="config('app.url')" color="blue">
Browse More Products
</x-mail::button>

Thanks,<br>
**Team pcfy.in** — Everything your system needs
</x-mail::message>
