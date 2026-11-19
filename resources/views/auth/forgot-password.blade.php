<x-layout :title="wording('forgot_title')" noindex>
    <div class="container-page section">
        <div data-reveal class="card mx-auto max-w-md p-6 sm:p-8">
            <h1 class="text-center text-3xl font-bold">{{ wording('forgot_title') }}</h1>
            <p class="mt-2 text-center text-sm">{{ wording('forgot_intro') }}</p>

            @if (session('status'))
                <div role="status" class="mt-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="post" class="mt-6 space-y-4" x-data="{ sending: false }" @submit="sending = true">
                @csrf
                <x-field name="email" :label="wording('field_email')" type="email" required autocomplete="email" autofocus maxlength="160" />
                <button type="submit" class="btn btn-primary w-full" :disabled="sending">
                    <span x-show="!sending">{{ wording('forgot_button') }}</span>
                    <span x-cloak x-show="sending">{{ wording('form_sending') }}</span>
                </button>
            </form>

            <p class="mt-6 text-center text-sm">{{ wording('forgot_back') }} <a href="{{ route('login') }}" class="font-semibold text-saffron-700 hover:underline">{{ wording('login_title') }}</a></p>
            @if (filled(setting('phone')))
                <p class="mt-2 text-center text-xs text-ink/60">{{ wording('email_call_us', ['phone' => setting('phone')]) }}</p>
            @endif
        </div>
    </div>
</x-layout>
