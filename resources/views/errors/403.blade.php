{{--
    "Not allowed": mostly seen at /admin by a customer account, or by staff whose
    role does not include a section. A plain page of its own (not the website app).
--}}
@php
    $user = auth()->user();
    // A customer account that opened the admin panel gets an explanation and a way out.
    $customerAtAdmin = $user && ! $user->isStaff() && request()->is('admin', 'admin/*');
@endphp
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <title>Not allowed | {{ setting('site_name') }}</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <style>
        body { margin: 0; font-family: system-ui, sans-serif; background: #f3f7f9; color: #1b2a33; display: grid; min-height: 100vh; place-items: center; padding: 1.5rem; box-sizing: border-box; }
        main { max-width: 36rem; text-align: center; background: #fff; border: 1px solid #dfeaef; border-radius: 1rem; padding: 2.5rem 2rem; }
        .code { font-size: 4rem; font-weight: 700; color: #ec6a1c; margin: 0; }
        h1 { color: #13293a; font-size: 1.6rem; margin: .75rem 0; }
        p { line-height: 1.6; }
        .actions { display: flex; flex-wrap: wrap; justify-content: center; gap: .75rem; margin-top: 1.75rem; }
        .btn { display: inline-block; border-radius: .5rem; padding: .8rem 1.2rem; font-weight: 600; font-size: .9rem; text-decoration: none; cursor: pointer; border: 1px solid #c1d6df; background: #fff; color: #13293a; }
        .btn-primary { background: #ec6a1c; border-color: #ec6a1c; color: #fff; }
    </style>
</head>
<body>
<main>
    <p class="code">403</p>
    @if ($customerAtAdmin)
        <h1>This account cannot open the admin panel</h1>
        <p>You are signed in as <strong>{{ $user->email }}</strong>, which is a customer account. The admin panel is only for staff accounts.</p>
        <p><small>To manage the website, sign out and sign in with your admin account. If this account should have admin access, the Super Admin can give it a role under Settings &gt; Users.</small></p>
        <div class="actions">
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <input type="hidden" name="next" value="admin-login">
                <button type="submit" class="btn btn-primary">Sign out and go to admin login</button>
            </form>
            <a href="{{ route('account.index') }}" class="btn">My Account</a>
        </div>
    @else
        <h1>You do not have permission to open this page</h1>
        <p>
            @if ($user?->isStaff())
                Your role does not include this part of the admin panel. Ask the Super Admin if you need it.
            @else
                This page is not available to your account.
            @endif
        </p>
        <div class="actions">
            @if ($user?->isStaff())
                <a href="{{ url('/admin') }}" class="btn btn-primary">Back to the admin panel</a>
            @endif
            <a href="{{ route('home') }}" class="btn">Go to Home</a>
        </div>
    @endif
</main>
</body>
</html>
