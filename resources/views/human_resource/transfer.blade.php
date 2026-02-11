@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Transfer Validation</h6>
    </div>
    <div class="card-body">
        <div class="row">
          <div class="col-lg-9">
            <input type="text" placeholder="Search NIC.." id="employee_search" class="form-control" required>
            <button type="reset" id="resetbtn">&times;</button>
            <img src="{{asset('backend/img/search.png')}}" alt="Girl in a jacket" name="nicbtn" id="nicbtn">
          </div>
        </div><br>
        <div class="row">
          <div class="col-lg-6">
            <table class="table profile">
                <tbody>
                        
                </tbody>
            </table>
          </div>
          <div class="col-lg-6">
            <table class="table servtable">
                <tbody>
                        
                </tbody>
            </table>
          </div>
        </div>
        <br>
        <hr>
        <div class="row">
          <div class="col-lg-7">
            <div class="containertbl" style="overflow-y:auto;">
                <table class="table cadretransfer" id="cadretable">
                    <tbody>
                            
                    </tbody>
                </table>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="containertbl" style="overflow-y:auto;">
                <table class="table dist" id="dist">
                    <tbody>
                            
                    </tbody>
                </table>
            </div>
          </div>
        </div>
    </div>
</div>
<input type="hidden" id="empid" name="empid">
<input type="hidden" id="cadre_code" name="cadre_code">
<input type="hidden" id="gpslocation" name="gpslocation">
<p id="display-array"></p>
<!-- Image loader -->
  <img id="loader" src="{{asset('backend/img/114.gif')}}" width="32px" height="32px">
@endsection

@push('styles')
<link rel="stylesheet" href=""https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"" />

<style>
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
    right: 45px;
    margin: auto;
    background: #ddd;
    padding: 0;
    outline: none;
    cursor: pointer;
    transition: .1s;
}

/* Search input property */
#employee_search {
  padding: 10px;
  font-size: 17px;
  border: 1px solid grey;
  float: left;
  width: 95%;
}

#nicbtn {
  float: left;
  width: 5%;
  background: #2196F3;
  padding: 5px;
  color: white;
  font-size: 17px;
  border-left: none;
  cursor: pointer;
  height:40px;
}

.button:hover {
  background: #0b7dda;
}

.box{
 box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
 padding: 10px;
 border-radius: 10px;
}

.table tr{
    line-height: 10px; 
}
.containertbl{
    height: 300px;
}
 /*Ajax loader*/
    .overlay{
    display: none;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 999;
    background: rgba(255,255,255,0.8) url("/images/loader.gif") center no-repeat;
    }
    /* Turn off scrollbar when body element has the loading class */
    body.loading{
        overflow: hidden;   
    }
    /* Make spinner image visible when body element has the loading class */
    body.loading .overlay{
        display: block;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" ></script>
<script>
// Add remove loading class on body element based on Ajax request status
$(document).on({
    ajaxStart: function(){
        $("body").addClass("loading"); 
    },
    ajaxStop: function(){ 
        $("body").removeClass("loading"); 
    }    
});

$(document).ready(function(){
     $('#loader').hide();
  $( "#employee_search" ).autocomplete({
    source: function( request, response ) {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      // Fetch data
      $.ajax({
        url:"{{route('employees.searchEmployees')}}",
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
        console.log(ui.item.designation);
            $('#employee_search').val(ui.item.label);  
            $('#empid').val(ui.item.value);
            console.log(ui.item.label); 
            $('#cadre_code').val(ui.item.cadrecode);
            $('#gpslocation').val(ui.item.gpslocation);
            var ins = '';
            if(ui.item.institute == 'OAC') { 
                ins ='Zonal Education Office'
            } else {
               ins = ui.item.institute;
            }
 
            var html =
            '<tr>'+
                '<td>'+ 'Name in Full:' +'</td>'+
                '<td>'+ui.item.fullname+'</td>'+
           '</tr>'+
           '<tr>'+
                '<td>'+ 'Address:' +'</td>'+
                '<td>'+ui.item.address+'</td>'+
           '</tr>'+
           '<tr>'+
                '<td>'+ 'GN-Division:' +'</td>'+
                '<td>'+ui.item.gndivision+'</td>'+
           '</tr>'+
           '<tr>'+
                '<td>'+ 'Designation:' +'</td>'+
                '<td>'+ui.item.designation+'</td>'+
           '</tr>'+
           '<tr>'+
                '<td>'+ 'Institute:' +'</td>'+
                '<td>'+ins+'</td>'+
           '</tr>'+
           '<tr>'+
                '<td>'+ 'Cadre Subject:' +'</td>'+
                '<td>'+ui.item.cadresubject+'</td>'+
           '</tr>'+
           '<tr>'+
                '<td>'+ 'Subject Clerk:' +'</td>'+
                '<td>'+ui.item.pfclerk+'</td>'+
           '</tr>'+
           '<tr>'+
                '<td>'+ '' +'</td>'+
                '<td>'+'<input type="button" id="servhistory" name="servhistory" value="Service History" onclick="myFunction()">'+'</td>'+
           '</tr>';
           $('.profile').append(html);
           return false;
    }
  });
});

function myFunction() {
    var empnic = $('#employee_search').val();
        $.ajax({
        url: "{{ url('/servicehistory') }}",
            method: 'get',
            data: {
                empnic:empnic
                },
            success: function(response){
            var res='';
            res +=
            '<tr>'+
                '<th>'+'Institute'+'</th>'+
                '<th>'+'Date from'+'</th>'+
                '<th>'+'Date to'+'</th>'+
           '</tr>';
            $.each (response, function (key, value) {
            res +=
            '<tr>'+
                '<td>'+value.institute+'</td>'+
                '<td>'+value.date_from+'</td>'+
                '<td>'+value.date_to+'</td>'+
           '</tr>';
            });
            res +=
            '<tr>'+
                '<td>'+ '' +'</td>'+
                '<td>'+ '' +'</td>'+
                '<td>'+'<input type="button" id="cadre" name="cadre" value="View Cadre" onclick="cadreFunction()">'+'</td>'+
            '</tr>';
            $('.servtable').append(res);
            }
        });
}

function cadreFunction() {
    $('#loader').show();
    var cadrecode = $('#cadre_code').val();
    var gpsloc = $('#gpslocation').val();
        $.ajax({
        url: "{{ url('/cadre-transfer') }}",
            method: 'get',
            data: {
                cadrecode:cadrecode,
                gpsloc:gpsloc
                },
            success: function(response){
                $('#loader').hide();
                console.log(response[1]);
                var html ='';
                $.each (response[1], function (key, value) {
                html +=
                    '<tr>'+
                        '<td>'+key+'</td>'+
                        '<td>'+value+'</td>'+
                  '</tr>';
                });
                $('.dist').append(html);
                var res = '';
                res +=
                '<tr>'+
                    '<th>'+'Institute'+'</th>'+
                    '<th>'+'Approved'+'</th>'+
                    '<th>'+'Available'+'</th>'+
                    '<th>'+'Ex/D'+'</th>'+
              '</tr>';
                $.each (response[0], function (key, value) {
                    res +=
                    '<tr>'+
                        '<td>'+value.institute+'</td>'+
                        '<td>'+value.app+'</td>'+
                        '<td>'+value.avi+'</td>'+
                        '<td>'+value.exd+'</td>'+
                  '</tr>';
                    });
                $('.cadretransfer').append(res);
          
            }
        });
}

</script>
@endpush