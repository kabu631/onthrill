<x-layout :title="wording('login_title')" noindex>
    <div class="container-page section">
        <div data-reveal class="card mx-auto max-w-md p-6 sm:p-8">
            <h1 class="text-center text-3xl font-bold">{{ wording('login_title') }}</h1>
            <p class="mt-2 text-center text-sm">{{ wording('login_intro') }}</p>

            <form action="{{ route('login') }}" method="post" class="mt-8 space-y-4">
                @csrf
                <x-field name="email" :label="wording('field_email')" type="email" required autocomplete="email" autofocus />
                <x-field name="password" :label="wording('field_password')" type="password" required autocomplete="current-password" />
                <div class="text-right text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-saffron-700 hover:underline">{{ wording('login_forgot') }}</a>
                </div>
                <button type="submit" class="btn btn-primary w-full">{{ wording('login_title') }}</button>
            </form>

            <p class="mt-6 text-center text-sm">{{ wording('login_new') }} <a href="{{ route('register') }}" class="font-semibold text-saffron-700 hover:underline">{{ wording('login_create') }}</a></p>
        </div>
    </div>
</x-layout>
