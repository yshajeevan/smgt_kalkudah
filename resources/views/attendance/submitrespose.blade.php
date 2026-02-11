@extends('layouts.master')

@section('main-content')
<div class="container">
    <div class="card-heading row">
        <div class="col-12">
            <h5>{{\Auth::user()->name}}</h5>
            <p>{{Carbon\Carbon::now()->format('d-m-Y')}}</p>
        </div>
    </div>
    <div class="card-main-t">
        <table class="table">
            <tr>
                <td style="width:50%">Students</td>
                <td style="width:30%">{{$percstu}}%</td>
                <td style="width:30%">
                    @if($percstu == 100)
                        <img src="{{asset('backend/img/reward.png')}}" alt="" width="40px" height="auto">
                    @elseif($percstu < 100 && $percstu >= 90)
                        <img src="{{asset('backend/img/1.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($percstu < 90 && $percstu >= 85)
                        <img src="{{asset('backend/img/2.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($percstu < 85 && $percstu >= 80)
                        <img src="{{asset('backend/img/3.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($percstu < 80 && $percstu >= 75)
                        <img src="{{asset('backend/img/4.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($percstu < 75)
                        <img src="{{asset('backend/img/5.jpg')}}" alt="" width="40px" height="auto">
                    @endif
                </td>
            </tr> 
            @if($tottea > 0)
            <tr>
                <td>Teachers</td>
                <td>{{$perctea}}%</td>
                <td>@if($perctea == 100)
                        <img src="{{asset('backend/img/reward.png')}}" alt="" width="40px" height="auto">
                    @elseif($perctea < 100 && $perctea >= 90)
                        <img src="{{asset('backend/img/1.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($perctea < 90 && $perctea >= 85)
                        <img src="{{asset('backend/img/2.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($perctea < 85 && $perctea >= 80)
                        <img src="{{asset('backend/img/3.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($perctea < 80 && $perctea >= 75)
                        <img src="{{asset('backend/img/4.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($perctea < 75)
                        <img src="{{asset('backend/img/5.jpg')}}" alt="" width="40px" height="auto">
                    @endif
                </td>
            </tr>
            @endif
            @if($totnonac > 0)
            <tr>
                <td>Non-academic Staff</td>
                <td>{{$percnonac}}%</td>
                <td style="width:30%">
                    @if($percnonac == 100)
                        <img src="{{asset('backend/img/reward.png')}}" alt="" width="40px" height="auto">
                    @elseif($percnonac < 100 && $percnonac >= 90)
                        <img src="{{asset('backend/img/1.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($percnonac < 90 && $percnonac >= 85)
                        <img src="{{asset('backend/img/2.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($percnonac < 85 && $percnonac >= 80)
                        <img src="{{asset('backend/img/3.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($percnonac < 80 && $percnonac >= 75)
                        <img src="{{asset('backend/img/4.jpg')}}" alt="" width="40px" height="auto">
                    @elseif($percnonac < 75)
                        <img src="{{asset('backend/img/5.jpg')}}" alt="" width="40px" height="auto">
                    @endif
                </td>
            </tr>
            @endif
        </table>
    </div>
    <div class="card-end">
        <p>Successfully Submitted! Thank you.</p><br>
        <button type="button" class="btn btn-success" onclick="window.location='{{ route('school') }}'">Home</button>
    </div>
</div>    


@endsection

@push('styles')
<style>
.container {
    background: #fff;
    box-shadow: 0px 15px 16.83px 0.17px rgba(0, 0, 0, 0.05);
    border-radius: 10px;
}
.card-heading {
    text-align:center;
    padding-top:10px;
}
.card-end{
    text-align:center;
    font-size:1em;
    color: green;
    font-weight: bold;
}
  
</style>
@endpush
