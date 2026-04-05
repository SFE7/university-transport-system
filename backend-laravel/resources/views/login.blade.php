<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobilITé - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #667eea;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .logo p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
        }

        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .success-message {
            background: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        button:hover {
            transform: translateY(-2px);
        }

        .test-accounts {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
            font-size: 13px;
            color: #666;
        }

        .test-accounts h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 14px;
        }

        .account {
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-left: 3px solid #667eea;
            padding-left: 15px;
        }

        .account strong {
            display: block;
            color: #333;
            margin-bottom: 5px;
        }

        .account code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        .account-type {
            font-size: 12px;
            color: #999;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🚗 MobilITé</h1>
            <p>Carpooling Platform</p>
        </div>

        @if ($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your email"
                    required
                >
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="test-accounts">
            <h3>📝 Test Accounts (No Password Required)</h3>

            <div class="account">
                <strong>👨‍💼 Driver Account</strong>
                <code>ali@example.com</code>
                <div class="account-type">Post rides & manage bookings</div>
            </div>

            <div class="account">
                <strong>👩‍🎓 Student Account</strong>
                <code>sarah@student.com</code>
                <div class="account-type">Search & book rides</div>
            </div>

            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; font-size: 12px; color: #999;">
                💡 <strong>Tip:</strong> Enter any email above to login.
                Click logout from dashboard to switch accounts.
            </div>
        </div>
    </div>
</body>
</html>
