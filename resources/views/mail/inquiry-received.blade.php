<x-mail::message>
# New enquiry

**{{ $inquiry->name }}** · {{ $inquiry->phone }}@if ($inquiry->email) · {{ $inquiry->email }}@endif

@if ($inquiry->topic())
About: **{{ $inquiry->topic() }}**
@endif

<x-mail::panel>
{{ $inquiry->message }}
</x-mail::panel>

<x-mail::button :url="url('/admin/inquiries/'.$inquiry->id.'/edit')">Open in admin</x-mail::button>

{{ setting('site_name') }}
</x-mail::message>
