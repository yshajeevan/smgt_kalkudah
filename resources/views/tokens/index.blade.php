@extends('layouts.master')

@section('main-content')

<div class="container py-4">

    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <h4 class="mb-0">🎫 Generate Token</h4>

                        <a href="/token/dashboard" class="btn btn-outline-primary rounded-pill d-flex align-items-center gap-2">
                            <i class="fas fa-chart-pie"></i>
                            Dashboard
                        </a>

                    </div>

                    @if(session('success'))
                        <div class="alert alert-success text-center fs-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="/token">
                        @csrf

                        <!-- NIC -->
                        <input type="text" id="nic" name="nic"
                            class="form-control form-control-lg mb-3"
                            placeholder="Enter NIC" required autofocus>

                        <!-- Name -->
                        <input type="text" id="name"
                            class="form-control mb-3"
                            placeholder="Name (Auto)"
                            readonly>

                        <!-- Mobile -->
                        <input type="text" id="mobile" name="mobile"
                            class="form-control mb-3"
                            placeholder="Mobile (Required if outsider)">

                        <!-- Purpose -->
                        <select name="purpose" class="form-select mb-3" required>
                            <option value="">Select Purpose</option>
                            <option>Salary</option>
                            <option>Transfer</option>
                            <option>Leave</option>
                            <option>Complaint</option>
                            <option>General Inquiry</option>
                        </select>

                        <!-- Branch -->
                        <select name="branch_id" class="form-select mb-4" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>

                        <!-- Button -->
                        <button class="btn btn-primary btn-lg w-100 rounded-3">
                            Generate Token
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- 🔥 NIC AUTO FETCH -->
<script>
document.getElementById('nic').addEventListener('blur', function(){

    let nic = this.value;

    if(!nic) return;

    fetch('/find-employee/' + nic)
    .then(res => res.json())
    .then(data => {

        if(data.found){
            document.getElementById('name').value = data.name;
            document.getElementById('mobile').value = data.mobile;
        } else {
            document.getElementById('name').value = '';
            document.getElementById('mobile').focus();
        }

    });

});
</script>

@endsection