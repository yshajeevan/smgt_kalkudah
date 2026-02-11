@extends('layouts.master')

@section('main-content')
<div class="row">
    <div class="col-md-12">
        @include('layouts.notification')
    </div>
</div>
<form id="formx" name="formx" method="get" action="{{ url($action) }}">
@csrf 
<div class="card-body box-profile">
  <ul class="list-group list-group-unbordered mb-3">
  @if($uname != 'Sch_Admin')
    @if($action != 'cadredetailed' && $action != 'cadrexport')
      <li class="list-group-item">
        <div class="row">
          <div class="col-lg-9">
            <input type="text" placeholder="Search Institute.." name="institute_search" id="institute_search" class="form-control">
            <button type="reset" id="resetbtn">&times;</button>
            <img src="{{asset('backend/img/search.png')}}" alt="searchbtn" name="nicbtn" id="nicbtn">
            <input type='hidden' id="txtid" name="txtid" value="">
          </div>
        </div> 
      </li>
    @endif  
  @endif
  @if($action != 'attencreate' && $action != 'schoolatten' && $action != 'room_list')
    <li class="list-group-item">
      <label>Designation: </label>
      <input type="checkbox" name="des[]" value="8"  checked> Teachers
      <input type="checkbox" name="des[]" value="13"  checked> Teacher Assistants
      <input type="checkbox" name="des[]" value="9"> Performing Principals
      <input type="checkbox" name="des[]" value="22">Development Officers
    </li>
    <div class="form-group">
        <div class="form-col-2">
            <label for="" class="control-label">Edu.Division</label>
        </div>
        <div class="form-col-10">
            <select name="division" id="division" class="form-control form-control-sm">
                <option value="">--Select Division--</option>
                <option value="MW">MW</option>
                <option value="MSW">MSW</option>
                <option value="EP">EP</option>
            </select>
        </div>
    </div>
  @endif
  @if($action == 'cadredetailed')
    <li class="list-group-item scroll">
        @foreach($cols as $col)
        <ul>
          <li>
            <input type="checkbox" name="col[]" value="{{$col->cadre_code}}"> {{$col->cadre}}
          </li>
        </ul>
        @endforeach
    </li>
  @endif
    <li class="list-group-item">
      <input type="submit" name="report" id="report" value="Submit">
    </li>
  </ul>
</div>
</form>

@if($action == 'attencreate')
 @include('submitforms/partials/atten_pending');
@endif
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
<style>
.scroll {
  max-height: 400px;
  overflow-y: auto;
}
/* reset button */
form input:placeholder-shown + button{
  opacity: 0;
  pointer-events: none;
} 

#resetbtn {
    position: absolute;
    border: none;
    display: block;
    width: 15px;
    height: 15px;
    line-height: 16px;
    font-size: 12px;
    border-radius: 50%;
    top: 0;
    bottom: 0;
    right: 110px;
    margin: auto;
    background: #ddd;
    padding: 0;
    outline: none;
    cursor: pointer;
    transition: .1s;
}
/* Main form */
#institute_search {
  padding: 10px;
  font-size: 17px;
  border: 1px solid grey;
  float: left;
  width: 90%;
  
}

#nicbtn {
  float: left;
  width: 5%;
  background: #2196F3;
  padding: 5px;
  color: white;
  font-size: 17px;
  border-left: none;
  height:40px;
}

.button:hover {
  background: #0b7dda;
}

#addform::after {
  content: "";
  clear: both;
  display: table;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" ></script>
<script type="text/javascript">
    $(document).ready(function() {
      $("#institutetbl").DataTable({
        columnDefs : [
        { targets : [3] }
        ],  
      });
      
    $("#sname").val('');

    $( "#institute_search" ).autocomplete({
    source: function( request, response ) {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      // Fetch data
      $.ajax({
        url:"{{route('institutes.searchInstitute')}}",
        type: 'post',
        dataType: "json",
        data: {
          search: request.term
        },
        success: function( data ) {
          response( data );
        }
      });
    },
      select: function (event, ui) {
        $('#institute_search').val(ui.item.label); // display the selected text
            $('#txtid').val(ui.item.value); // save selected id to input
            return false;
      }
    });
  });

</script>
@endpush