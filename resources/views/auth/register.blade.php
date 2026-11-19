<x-layout :title="wording('register_title')" noindex>
    <div class="container-page section">
        <div data-reveal class="card mx-auto max-w-md p-6 sm:p-8">
            <h1 class="text-center text-3xl font-bold">{{ wording('register_title') }}</h1>
            <p class="mt-2 text-center text-sm">{{ wording('register_intro') }}</p>

            <form action="{{ route('register') }}" method="post" class="mt-8 space-y-4">
                @csrf
                <x-field name="name" :label="wording('field_full_name')" required autocomplete="name" maxlength="120" />
                <x-field name="email" :label="wording('field_email')" type="email" required autocomplete="email" maxlength="160" />
                <x-field name="phone" :label="wording('field_phone')" type="tel" autocomplete="tel" inputmode="tel" maxlength="20" />
                <x-field name="password" :label="wording('field_password')" type="password" required autocomplete="new-password" :hint="wording('field_password_hint')" />
                <x-field name="password_confirmation" :label="wording('field_confirm_password')" type="password" required autocomplete="new-password" />
                <button type="submit" class="btn btn-primary w-full">{{ wording('register_button') }}</button>
            </form>

            <p class="mt-6 text-center text-sm">{{ wording('register_have') }} <a href="{{ route('login') }}" class="font-semibold text-saffron-700 hover:underline">{{ wording('login_title') }}</a></p>
        </div>
    </div>
</x-layout>
