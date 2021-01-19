@component('mail::message')
# #{{ $supplierPayment->id }} {{ $supplierPayment->supplierUser->username }} has requested a {{ $supplierPayment->method }} payment of ${{ $supplierPayment->formatted_value }}

{{ $supplierPayment->method === \App\Models\SupplierPayment::METHOD_BITCOIN ? 'Bitcoin Address' : 'Paypal Email' }}: {{ $supplierPayment->destination }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
