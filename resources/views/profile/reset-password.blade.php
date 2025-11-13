<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - Recofy</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box; /* memastikan width tidak melar karena padding/border */
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f9f9f9;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-container {
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }

    h2 {
      margin-bottom: 20px;
      font-size: 20px;
      font-weight: 600;
    }

    .form-group {
      margin-bottom: 16px;
    }

    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: 1.5px solid #6c63ff;
      border-radius: 6px;
      outline: none;
      font-size: 14px;
      color: #333;
      background-color: white;
      box-sizing: border-box;
    }

    input[type="password"]::placeholder {
      color: #999;
    }

    button {
      width: 100%;
      padding: 12px;
      background-color: #4f46e5;
      color: white;
      font-weight: 600;
      font-size: 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    button:hover {
      background-color: #4338ca;
    }

    .alert {
      color: red;
      margin-bottom: 10px;
      font-size: 14px;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>Reset Password</h2>

    @if ($errors->any())
      <div class="alert">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('password.reset') }}" method="POST">
      @csrf

      <div class="form-group">
        <input type="password" name="password" placeholder="Password Baru" required>
      </div>

      <div class="form-group">
        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required>
      </div>

      <button type="submit">Simpan Password</button>
    </form>
  </div>

</body>
</html>
