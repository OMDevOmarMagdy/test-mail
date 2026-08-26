<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>

<body>

    <h1>Reset Password</h1>

    <form method="POST" action="/reset-password">

        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $token }}"
        >

        <input
            type="hidden"
            name="email"
            value="{{ $email }}"
        >

        <div>
            <label>New Password</label>

            <input
                type="password"
                name="password"
                required
            >
        </div>

        <br>

        <div>
            <label>Confirm Password</label>

            <input
                type="password"
                name="password_confirmation"
                required
            >
        </div>

        <br>

        <button type="submit">
            Reset Password
        </button>

    </form>

</body>
</html>