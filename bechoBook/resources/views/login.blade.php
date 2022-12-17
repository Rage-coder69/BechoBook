<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="d-flex flex-column justify-content-center align-items-center vh-100 bg-light">
        <form action="{{ route('pdfForm') }}" class="bg-white p-5 rounded-4 shadow-lg w-25" method="post">
            @csrf
            @method('GET')
            <div class="mb-3 d-flex flex-column align-items-center">
                <img src="{{ asset('/storage/user_profile_images/other.jpg') }}" class="w-50 h-50">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="email">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword1" name="password">
            </div>
            <div class="mb-3 d-flex flex-column align-items-center">
                <button type="submit" class="btn btn-primary ">Login</button>
            </div>
            @if(session()->has('message'))
                <div class="mb-3">
                    <p class="text-danger">{{ session('message') }}</p>
                </div>
            @endif
        </form>
</div>
</body>
</html>
