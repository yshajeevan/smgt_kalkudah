@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">

    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>

    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Transfer List</h6>
    </div>

    <div class="card-body">

        {{-- 🔍 FILTERS --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">

                    {{-- PF Clerk --}}
                    <div class="col-lg-3">
                        <label><strong>PF Clerk :</strong></label>
                        <select id="pfclerk" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($pfclerks as $clerk)
                                <option value="{{ $clerk->id }}">
                                    {{ $clerk->name_with_initial_e }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- School --}}
                    <div class="col-lg-3">
                        <label><strong>School :</strong></label>
                        <select id="school" class="form-control form-control-sm">
                            <option value="">All</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">
                                    {{ $school->institute }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
        </div>

        {{-- ➕ ADD BUTTON --}}
        @can('prpocess-create')
        <a href="{{ route('process.create') }}" 
           class="btn btn-primary btn-sm float-right mb-2"
           title="Add Transfer">
            <i class="fas fa-plus"></i> Add Transfer
        </a>
        @endcan

        {{-- 📊 TABLE --}}
        <div class="table-responsive">
            <table class="table table-bordered" id="employees">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Process ID</th>
                        <th>Name</th>
                        <th>Transfer From</th>
                        <th>Transfer To</th>
                        <th>Effective Date</th>
                        <th>PF Officer</th>
                        <th>Approved</th>
                        <th>Printed</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
#employees_filter input {
    width: 300px !important;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {

    var table = $('#employees').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        paging: true,
        autoWidth: true,

        ajax: {
            url: "{{ route('transfer.index') }}",
            data: function (d) {
                d.pfclerk = $('#pfclerk').val(); // 🎯 filter
                d.school  = $('#school').val();  // 🎯 filter
            }
        },

        columns: [
            {data: 'id', name: 'id'},

            {
                data: 'process_id',
                name: 'process_id',
                render: function(data, type, row){
                    return "<a href='/process/" + row.process_id + "/edit'>" + data + "</a>";
                }
            },

            {data: 'employee_id', name: 'employee.name_with_initial_e'},
            {data: 'transfer_from', name: 'institute1.institute'},
            {data: 'transfer_to', name: 'institute.institute'},
            {data: 'effect_from', name: 'effect_from'},
            {data: 'pfclerk', name: 'institute1.pfclerk_id'},
            {data: 'is_approved', name: 'is_approved'},
            {data: 'is_printed', name: 'is_printed'},
        ]
    });

    // 🔄 Reload on filter change
    $('#pfclerk, #school').change(function () {
        table.draw();
    });

    $('#employees_filter input').attr('placeholder', 'Search by Process ID/Name/NIC');
});
</script>
@endpush