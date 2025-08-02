<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Inventaris Barang | Login</title>

  <!-- Fonts & Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('theme/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('theme/alert/css/sweetalert2.css') }}">
  <script src="{{ asset('theme/alert/js/sweetalert2.js') }}"></script>

  <style>
    .login-logo img {
      max-width: 80px;
      margin-bottom: 10px;
    }
    .login-title {
      font-size: 26px;
      font-weight: bold;
      text-transform: uppercase;
    }
    .login-box .card {
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      border-radius: 12px;
    }
    .input-group .form-control {
      border-radius: 50px;
      padding: 10px 20px;
    }
    .input-group-text {
      border-radius: 50px;
      background-color: #fff;
    }
    .btn-login {
      border-radius: 50px;
      padding: 10px;
      font-weight: bold;
    }
  </style>
</head>
<body class="hold-transition login-page">

<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <div class="login-logo">
        <img src="{{ asset('logo-bps.png') }}" alt="Logo BPS">
      </div>
      <div class="login-title">SICAPER (Aplikasi Catatan Pemeliharaan) BPS Kota Sukabumi</div>
    </div>

    <div class="card-body">
      <form id="form-login" method="POST">
        @csrf

        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username" id="user">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-4">
          <input type="password" class="form-control" placeholder="Password" id="pw">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-eye" id="icon-pw"></span>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-login">
          {{ __("messages.login") }}
        </button>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="{{ asset('theme/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('theme/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('theme/dist/js/adminlte.min.js') }}"></script>

<script>
  $("#icon-pw").click(function () {
    let input = $("#pw");
    let icon = $(this);
    input.attr("type") === "password" ? input.attr("type", "text") : input.attr("type", "password");
    icon.toggleClass("fa-eye fa-eye-slash");
  });

  $("#form-login").submit(function (e) {
    e.preventDefault();
    const username = $("#user").val();
    const password = $("#pw").val();

    if (!username) {
      return Swal.fire({ icon: "warning", title: "{{ __('messages.oops') }}", text: "{{ __('validation.required', ['attribute' => 'username']) }}", timer: 1500 });
    }
    if (!password) {
      return Swal.fire({ icon: "warning", title: "{{ __('messages.oops') }}", text: "{{ __('validation.required', ['attribute' => 'password']) }}", timer: 1500 });
    }

    $.ajax({
      url: "{{ route('login.auth') }}",
      type: "POST",
      dataType: "JSON",
      cache: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { username, password },
      statusCode: {
        200: function () {
          Swal.fire({ icon: "success", title: "{{ __('auth.success') }}", text: "{{ __('messages.redirect-to', ['dest' => 'dashboard']) }}", timer: 1500 }).then(() => {
            window.location.href = "{{ route('dashboard') }}";
          });
        },
        401: function () {
          Swal.fire({ icon: "error", title: "{{ __('auth.failed') }}!", text: "{{ __('auth.failed-message') }}", timer: 1500 });
        },
        500: function (xhr) {
          console.log(xhr.responseText);
        }
      },
      error: function () {
        Swal.fire({ icon: "error", title: "{{ __('messages.oops') }}", text: "{{ __('messages.server-error') }}", timer: 1500 });
      }
    });
  });
</script>

</body>
</html>
