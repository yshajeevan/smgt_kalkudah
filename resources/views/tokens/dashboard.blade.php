@extends('layouts.master')

@section('main-content')

<div class="container-fluid py-4">

    <!-- TOP BAR -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📊 Dashboard</h3>

        <a href="/token" class="btn btn-primary rounded-pill shadow">
            <i class="fas fa-plus"></i> New Token
        </a>
    </div>

    <!-- STATS -->
    <div class="row g-3 mb-4">

        <div class="col-md-2"><div class="card bg-primary text-white text-center p-3">
            Total <h3>{{ $total }}</h3></div></div>

        <div class="col-md-2"><div class="card bg-success text-white text-center p-3">
            Completed <h3>{{ $completed }}</h3></div></div>

        <div class="col-md-2"><div class="card bg-warning text-dark text-center p-3">
            Pending <h3>{{ $pending }}</h3></div></div>

        <div class="col-md-2"><div class="card bg-info text-white text-center p-3">
            Satisfaction <h3>{{ round($avgSatisfaction,1) }}/5</h3></div></div>

        <div class="col-md-2"><div class="card bg-secondary text-white text-center p-3">
            Internal <h3>{{ $internal }}</h3></div></div>

        <div class="col-md-2"><div class="card bg-dark text-white text-center p-3">
            External <h3>{{ $external }}</h3></div></div>

    </div>

    <!-- FILTER -->
    <div class="row mb-3">
        <div class="col-md-3">
            <input type="date" id="filterDate" value="{{ $date }}" class="form-control">
        </div>

        <div class="col-md-3">
            <select id="filterStatus" class="form-select">
                <option value="all">All Visitors</option>
                <option value="waiting">Pending Only</option>
            </select>
        </div>

        <div class="col-md-3">
            <input type="text" id="tokenSearch" class="form-control" placeholder="🔍 Search Token">
        </div>
    </div>

    <!-- TABLE -->
    <div class="card p-3 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Token</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </div>

    <!-- CHARTS -->
    <div class="row mt-4">
        <div class="col-md-6"><canvas id="branchChart"></canvas></div>
        <div class="col-md-6"><canvas id="purposeChart"></canvas></div>
    </div>

    <div class="mt-4">
        <canvas id="hourChart"></canvas>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

// LOAD TABLE
function loadTable() {
    let date = filterDate.value;
    let status = filterStatus.value;

    fetch(`/dashboard-data?date=${date}&status=${status}`)
    .then(res => res.json())
    .then(data => {

        let html = '';

        data.forEach(t => {

            html += `
            <tr id="row-${t.id}">
                <td class="token-col">${t.token_number}</td>
                <td>${t.visitor?.name ?? t.visitor?.nic}</td>
                <td>${t.branch?.name}</td>

                <td>
                    ${t.status=='waiting'
                        ? '<span class="badge bg-warning">Waiting</span>'
                        : t.status=='serving'
                        ? '<span class="badge bg-primary">Serving</span>'
                        : '<span class="badge bg-success">Completed</span>'}
                </td>

                <td>${t.created_at.substring(11,16)}</td>

                <td>
                    ${t.status!='completed'
                        ? `<button class="btn btn-success btn-sm checkout-btn" data-id="${t.id}">✔</button>`
                        : ''}
                </td>
            </tr>`;
        });

        tableBody.innerHTML = html;

        attachCheckout();
    });
}


// FILTER EVENTS
filterDate.onchange = loadTable;
filterStatus.onchange = loadTable;


// SEARCH
tokenSearch.onkeyup = function(){
    let val = this.value.toLowerCase();
    document.querySelectorAll('#tableBody tr').forEach(row=>{
        let token = row.querySelector('.token-col').innerText.toLowerCase();
        row.style.display = token.includes(val) ? '' : 'none';
    });
};


// CHECKOUT
function attachCheckout(){
    document.querySelectorAll('.checkout-btn').forEach(btn=>{
        btn.onclick = function(){

            let id = this.dataset.id;

            fetch('/complete/'+id,{
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':'{{ csrf_token() }}',
                    'Content-Type':'application/json'
                },
                body: JSON.stringify({satisfaction:5})
            })
            .then(()=> document.getElementById('row-'+id).remove());
        };
    });
}


// INITIAL LOAD
loadTable();


// CHARTS
new Chart(branchChart,{
    type:'bar',
    data:{
        labels:{!! json_encode($byBranch->pluck('branch.name')) !!},
        datasets:[{data:{!! json_encode($byBranch->pluck('total')) !!}}]
    }
});

new Chart(purposeChart,{
    type:'pie',
    data:{
        labels:{!! json_encode($byPurpose->pluck('purpose')) !!},
        datasets:[{data:{!! json_encode($byPurpose->pluck('total')) !!}}]
    }
});

new Chart(hourChart,{
    type:'line',
    data:{
        labels:{!! json_encode($byHour->pluck('hour')) !!},
        datasets:[{data:{!! json_encode($byHour->pluck('total')) !!}}]
    }
});

</script>

@endsection