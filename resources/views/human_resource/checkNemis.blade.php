@extends('layouts.master')

@section('main-content')
<div class="card">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    <h2>Cross Check School Academic Staff with NEMIS</h2>
    <h5>Not in NEMIS</h5>
    <table class="table1" style="width: 100%;border:1px solid #ccc">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>NIC</th>
                <th>Institute</th>
            </tr>
        </thead>    
        <tbody>
         @if($notinnemis->count())
            @foreach($notinnemis as $item)
                <tr>
                    <td></td>
                    <td> {{ $item->name }} </td>
                    <td> {{ $item->nic }} </td>
                    <td> {{ $item->institute }} </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="2"> No record found </td>
            </tr>
        @endif
        </tbody>
    </table>
    <br>
    <h5>Not in Employee(SMgt)</h5>
    <table class="table2" style="width: 100%;border:1px solid #ccc">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>NIC</th>
                <th>Institute</th>
            </tr>
        </thead>    
        <tbody>
         @if($notinsmgt->count())
            @foreach($notinsmgt as $item)
                <tr>
                    <td></td>
                    <td> {{ $item->name }} </td>
                    <td> {{ $item->nic }} </td>
                    <td> {{ $item->institute }} </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="2"> No record found </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
@endsection

@push('styles')
<style>
.table1{
	counter-reset: Serial;          
}
.table1{
	border-collapse: separate;
}
.table1 tr td:first-child:before{
	counter-increment: Serial;      
	content: counter(Serial); 
}
.table2{
	border-collapse: separate;
}
.table2{
	counter-reset: Serial;          
}
.table2 tr td:first-child:before{
	counter-increment: Serial;      
	content: counter(Serial); 
}
</style>
@endpush
