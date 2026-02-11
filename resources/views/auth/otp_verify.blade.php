@include('layouts.head')

<div id="admin-content" class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="mb-4">Verify OTP</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        @if (session('message'))
                            <div class="alert alert-info text-center">
                                {{ session('message') }}
                            </div>
                        @endif
                        <p class="text-center">Enter the OTP sent to your phone to reset your password.</p>
                        <form action="{{ route('otp.verify') }}" method="post">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="otp" class="form-label">OTP Code</label>
                                <input type="text" class="form-control @error('otp') is-invalid @enderror" name="otp" id="otp" placeholder="Enter OTP" required>
                                @error('otp')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <input type="hidden" name="phone" value="{{ session('phone') }}">
                            <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
                        </form>
                        @error('phone')
                            <div class="alert alert-danger mt-3" role="alert">
                                {{ $message }}
                            </div>
                        @enderror
                        @if (session('error'))
                            <div class="alert alert-danger mt-3">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>