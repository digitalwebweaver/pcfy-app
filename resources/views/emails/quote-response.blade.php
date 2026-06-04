<x-mail::message>
# {{ $response->subject ?: 'Update on your Quote — '.$quote->quote_number }}

Hi **{{ $quote->customer_name }}**,

{!! nl2br(e($response->body)) !!}

---

**Quote Reference:** {{ $quote->quote_number }}
@if($quote->company_name)
**Company:** {{ $quote->company_name }}
@endif

For any questions, please reach us at:

📞 **+91 98765 43210** &nbsp;|&nbsp; ✉ **hello@pcfy.in**

<x-mail::button :url="config('app.url')" color="blue">
Visit pcfy.in
</x-mail::button>

Thanks,<br>
**Team pcfy.in** — Everything your system needs
</x-mail::message>
