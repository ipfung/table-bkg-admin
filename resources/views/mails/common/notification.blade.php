<div>
    <p>{{ __('Dear') }} {{ $payload['data']['customer_name'] }},</p>
    <p>{{ $payload['title'] }}</p>
    <p><b>{{ $payload['body'] }}</b></p>
    <p></p>
    <p>{{ __('Thank you for your attention.') }}</p>
</div>
