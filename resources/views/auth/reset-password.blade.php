<x-layout :title="wording('reset_title')" noindex>
    <div class="container-page section">
        <div data-reveal class="card mx-auto max-w-md p-6 sm:p-8">
            <h1 class="text-center text-3xl font-bold">{{ wording('reset_title') }}</h1>
            <p class="mt-2 text-center text-sm">{{ wording('reset_intro') }}</p>

            <form action="{{ route('password.update') }}" method="post" class="mt-8 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <x-field name="email" :label="wording('field_email')" type="email" required autocomplete="email" :value="$email" maxlength="160" />
                <x-field name="password" :label="wording('field_new_password')" type="password" required autocomplete="new-password" minlength="8" autofocus />
                <x-field name="password_confirmation" :label="wording('field_confirm_password')" type="password" required autocomplete="new-password" minlength="8" />
                <button type="submit" class="btn btn-primary w-full">{{ wording('reset_button') }}</button>
            </form>
        </div>
    </div>
</x-layout>
