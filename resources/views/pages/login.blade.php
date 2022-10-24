@extends('layouts.theme')

@section('content')
    <div class="container vh-aligner" style="min-height: calc(100vh - 50px)">
        <form action="{{ route('login') }}" method="POST" class="login-form">
            @csrf

            <div class="mb-2">
                <label for="email" class="form-label">Email address</label>
                <input type="email" id="email" name="email" value="demo@financialhouse.io" class="form-control">
            </div>

            @error('email')
                <div class="error mb-2">{{ $message }}</div>
            @enderror

            <div class="mb-2">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password">
            </div>

            @error('password')
                <div class="error mb-2">{{ $message }}</div>
            @enderror

            @error('login')
                <div class="error mb-2">{{ $message }}</div>
            @enderror
        
            <div style="float: right">
                <button type="submit" class="btn btn-dark">Login</button>
            </div>
        </form>
    </div>
@endsection