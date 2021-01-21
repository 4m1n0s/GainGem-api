@component('mail::message')
# {{ $supplierPayment->supplierUser->username }} has requested a {{ $supplierPayment->formatted_method }} payment of ${{ $supplierPayment->formatted_value }}

Payment ID: #{{ $supplierPayment->id }}
<br>
{{ $supplierPayment->method === \App\Models\SupplierPayment::METHOD_BITCOIN ? 'Bitcoin Address' : 'Paypal Email' }}: {{ $supplierPayment->destination }}
<br>
Amount: ${{ $supplierPayment->formatted_value }}

Thanks,<br>
{{ config('app.name') }} Team
@endcomponent
