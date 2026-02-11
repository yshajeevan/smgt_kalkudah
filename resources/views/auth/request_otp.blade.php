@include('layouts.head')
<div id="admin-content" class="py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h2 class="mb-4">Request OTP</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <form action="{{ route('otp.send-otp') }}" method="post" autocomplete="off">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="inputPhone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="inputPhone" name="phone" 
                                       placeholder="Phone Number (e.g., 94*********)" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">Enter your registered phone number to receive an OTP code.</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Request OTP</button>
                        </form>
                        @if (session('message'))
                            <div class="alert alert-success mt-3">
                                {{ session('message') }}
                            </div>
                        @endif
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