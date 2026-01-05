@component('mail::message')
# Quotation: {{ $quotation->quotation_number }}

Client: {{ $quotation->client->name }}

@component('mail::button', ['url' => asset($quotation->file_path)])
Download PDF
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
