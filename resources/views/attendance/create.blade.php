
@php
    $template = Auth::user()->hasAnyRole(['Sch_Admin']) ? 'layouts.school.master' : 'layouts.master';
@endphp

@extends($template)

@section('main-content')
<div class="flash-message">
  @foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if(Session::has('alert-' . $msg))
    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
    @endif
  @endforeach
</div>
<div class="container">
    <div class="card-heading row">
        <div class="col-12">
            <h5> {{$stupop->institute->institute}} </h5>
            <p>{{Carbon\Carbon::now()->format('d-m-Y')}}</p>
        </div>
    </div>
    <form method="post" action="{{ route('attendance.store') }}">
    @csrf
        <div class="table-responsive">
            <table class="table" id="myTable">
            <tr>
                <th style="width:50%">Category</th>
                <th style="width:25%">Present</th>
                <th style="width:25%">Total</th>
            </tr> 
            <tr>
                <td style="width:50%">Boys</td>
                <td style="width:25%"><input type="number" class="textbox" id="prboys" name="prboys" autocomplete="off" value="{{old('prboys')}}" required>
                @if($errors->has('prboys'))
                    <p class="error">{{ $errors->first('prboys') }}</p>
                @endif</td>
                <td style="width:25%"><input type="number" class="textbox"  id="totboys" name="totboys" value="{{old('totboys')}}" autocomplete="off" required></td>
            </tr> 
            <tr>
                <td>Girls</td>
                <td><input type="number" class="textbox" id="prgirls" name="prgirls" autocomplete="off" value="{{old('prgirls')}}"  required>
                @if($errors->has('prgirls'))
                    <p class="error">{{ $errors->first('prgirls') }}</p>
                @endif</td>
                <td><input type="number" class="textbox" id="totgirls" name="totgirls" autocomplete="off" value="{{old('totgirls')}}" required></td>
            </tr>
            <tr>
                <td>Total Students</td>
                <td><input type="number" class="textbox"  id="prstu" name="prstu" value="{{old('prstu')}}" required>
                @if($errors->has('prstu'))
                    <p class="error">{{ $errors->first('prstu') }}</p>
                @endif</td>
                <td><input type="number" class="textbox" id="totstu" name="totstu" value="{{old('totstu')}}" required>
                @if($errors->has('totstu'))
                    <p class="error">{{ $errors->first('totstu') }}</p>
                @endif</td>
            </tr>
            <tr> 
                <td>Teachers</td>
                <td><input type="number" class="textbox"   id="prtea" name="prtea" autocomplete="off" value="{{old('prtea')}}" required>
                @if($errors->has('prtea'))
                    <div class="error">{{ $errors->first('prtea') }}</div>
                @endif</td>
                <td><input type="number" class="textbox" id="tottea" name="tottea" autocomplete="off" value="{{old('tottea')}}" required></td>
            </tr>
            <tr>
                <td>Development Officers (Teaching)</td>
                <td><input type="number" class="textbox" id="prtrainee" name="prtrainee" autocomplete="off" value="{{old('prtrainee')}}" required>
                @if($errors->has('prtrainee'))
                    <p class="error">{{ $errors->first('prtrainee') }}</p>
                @endif</td>
                <td><input type="number" class="textbox" sid="tottrainee" name="tottrainee" autocomplete="off" value="{{old('tottrainee')}}" required></td>
            </tr>
            </tr>
            <tr>
                <td>Non-Academic Staff</td>
                <td><input type="number" class="textbox"  id="prnonacademic" name="prnonacademic" autocomplete="off" value="{{old('prnonacademic')}}" required>
                @if($errors->has('prnonacademic'))
                    <p class="error">{{ $errors->first('prnonacademic') }}</p>
                @endif</td>
                <td><input type="number" class="textbox" id="totnonacademic" name="totnonacademic" autocomplete="off" value="{{old('totnonacademic')}}" required></td>
            </tr>
            <tr>
                <td>Principal</td>
                <td colspan="2">
                    <label for="present">On-duty: <input type="radio" name="principal" id="present" value="1" checked></label>&nbsp;
                    <label for="absent">Leave: <input type="radio" name="principal" id="absent" value="0"></label>
                </td>
            </tr>
            </table>
            <p style="color:red;"> * 1ம் தரத்திற்கு சேர்ந்த மாணவர்களையும் உள்வாங்கவும் </p>
            <div style="text-align:center">
                <button class="btn btn-success" type="submit">Add</button>
                <input type="hidden" id="institute_id" name="institute_id" value="{{$instid}}">
            </div>
        </div>
    </form>
</div>    
@endsection

@push('styles')
<style>
.error{
    font-size:14px;
    color: red;
    font-style: italic;
    margin:0;
}
.msg {
  color: green;
  text-align:center;
  font-size:24px;
}
.textbox {
	border: 1px solid #848484;
	-webkit-border-radius: 10px;
	-moz-border-radius: 10px;
	border-radius: 10px;
	outline:0;
	text-align:right;
	width:60%;
}

.container {
    background: #fff;
    box-shadow: 0px 15px 16.83px 0.17px rgba(0, 0, 0, 0.05);
    border-radius: 10px;
}
.card-heading {
    text-align:center;
    padding-top:10px;
}
  
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    $("#myTable").on('input', '.txtCal', function () {
       var calculated_total_sum = 0;
     
       $("#myTable .txtCal").each(function () {
           var get_textbox_value = $(this).val();
           if ($.isNumeric(get_textbox_value)) {
              calculated_total_sum += parseFloat(get_textbox_value);
              }                  
            });
              $("#prstu").val(calculated_total_sum);
    });
     $("#myTable").on('input', '.txtCal2', function () {
       var calculated_total_sum = 0;
     
       $("#myTable .txtCal2").each(function () {
           var get_textbox_value = $(this).val();
           if ($.isNumeric(get_textbox_value)) {
              calculated_total_sum += parseFloat(get_textbox_value);
              }                  
            });
              $("#totstu").val(calculated_total_sum);
    });
});

    var msg = '{{Session::get('alert')}}';
    var exist = '{{Session::has('alert')}}';
    if(exist){
      alert(msg);
    }
</script>
@endpush
