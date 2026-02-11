@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Cadre Summary</h6>
    </div>
    <div class="card-body">
        <a href="{{ url('/cadrexport?export=true') }}" class="commonButton">
            <i class="fas fa-download"></i>&nbsp;Export to Excel
        </a>
        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
            <table width='100%' border='1' id='cadretbl' style='border-collapse: collapse;' class="table-sm table-bordered">
                <thead>
                    <tr>
                        <th rowspan="3" class="frozen-column">School Name</th>
                        @foreach($cadres as $cadre)
                            <th colspan="3" class="category-header">{{ $cadre->category }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($cadres as $cadre)
                            <th colspan="3" class="merged-header">{{ $cadre->cadre }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($cadres as $cadre)
                            <th>Approved</th>
                            <th>Available</th>
                            <th>Ex/DE</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($structuredData as $data)
                        <tr>
                            <td class="frozen-column">{{ $data['institute_name'] }}</td>
                            @foreach($data['cadres'] as $cadreData)
                                <td>{{ $cadreData['approved'] }}</td>
                                <td>{{ $cadreData['available'] }}</td>
                                <td>{{ $cadreData['ex_de'] }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-responsive {
        overflow: auto;
        position: relative;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        position: sticky;
        top: 0; /* Ensure the whole thead sticks to the top */
        z-index: 1; /* Ensure it's above the table body */
        background-color: #f2f2f2; /* Background color for the header */
    }

    thead th {
        position: sticky;
        top: 0;
        background-color: #f2f2f2;
        border: 1px solid black;
        padding: 8px;
        text-align: center;
        z-index: 2; /* Higher z-index to ensure it overlays other content */
    }

    thead .category-header {
        background-color: #ddd; /* Different background color for category header */
    }

    thead .merged-header {
        background-color: #eee; /* Different background color for merged header */
    }

    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }

    .frozen-column {
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        background-color: #fff;
        z-index: 3; /* Higher z-index to ensure it overlays other content */
        border-right: 2px solid #000;
    }
</style>
@endpush

@push('scripts')
<script>

</script>
@endpush
