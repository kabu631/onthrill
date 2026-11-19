@php
    $user = auth()->user();
    // A customer account that opened the admin panel gets an explanation and a way out.
    $customerAtAdmin = $user && ! $user->isStaff() && request()->is('admin', 'admin/*');
@endphp

<x-layout title="Not allowed" noindex>
    <div class="container-page section">
        <div class="mx-auto max-w-xl text-center">
            <p class="font-display text-7xl font-bold text-saffron-500">403</p>

            @if ($customerAtAdmin)
                <h1 class="mt-4 text-3xl font-bold sm:text-4xl">This account cannot open the admin panel</h1>
                <p class="mt-4">
                    You are signed in as <strong class="text-ink">{{ $user->email }}</strong>, which is a customer account.
                    The admin panel is only for staff accounts.
                </p>
                <p class="mt-3 text-sm">
                    To manage the website, sign out and sign in with your admin account.
                    If this account should have admin access, the Super Admin can give it a role under Settings &gt; Users.
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <input type="hidden" name="next" value="admin-login">
                        <button type="submit" class="btn btn-primary">Sign out and go to admin login</button>
                    </form>
                    <a href="{{ route('account.index') }}" class="btn btn-outline">My Account</a>
                </div>
            @else
                <h1 class="mt-4 text-3xl font-bold sm:text-4xl">You do not have permission to open this page</h1>
                <p class="mt-4">
                    @if ($user?->isStaff())
                        Your role does not include this part of the admin panel. Ask the Super Admin if you need it.
                    @else
                        This page is not available to your account.
                    @endif
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    @if ($user?->isStaff())
                        <a href="{{ url('/admin') }}" class="btn btn-primary">Back to the admin panel</a>
                    @endif
                    <a href="{{ route('home') }}" class="btn btn-outline">Go to Home</a>
                </div>
            @endif
        </div>
    </div>
</x-layout>
