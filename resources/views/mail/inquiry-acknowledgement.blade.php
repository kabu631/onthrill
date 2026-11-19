<x-mail::message>
# {{ wording('email_greeting', ['name' => \Illuminate\Support\Str::of($inquiry->name)->before(' ')]) }}

{{ wording('email_enquiry_intro', ['site' => setting('site_name'), 'phone' => $inquiry->phone]) }}

@if ($inquiry->topic())
**{{ $inquiry->topic() }}**
@endif

<x-mail::panel>
{{ $inquiry->message }}
</x-mail::panel>

@if (filled(setting('phone')))
{{ wording('email_call_us', ['phone' => setting('phone')]) }}
@endif

<x-mail::button :url="url('/')">{{ wording('email_website_button') }}</x-mail::button>

{{ wording('email_signoff') }}

{{ setting('site_name') }}
</x-mail::message>
