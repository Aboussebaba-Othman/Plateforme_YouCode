@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    @if (session('verification'))
                        <div class="alert alert-info" role="alert">
                            {{ session('verification') }}
                        </div>
                    @endif

                    <div class="alert alert-warning">
                        <h5><i class="fas fa-info-circle"></i> Important!</h5>
                        <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
                        <p>{{ __('If you did not receive the email, click the button below to request another one.') }}</p>
                    </div>

                    <div class="text-center mt-4">
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                {{ __('Resend Verification Email') }}
                            </button>
                        </form>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <h5>Next Steps:</h5>
                        <ol>
                            <li>Check your email inbox for the verification link</li>
                            <li>Click the "Verify Email Address" button in the email</li>
                            <li>After verification, you will be automatically redirected to your dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection