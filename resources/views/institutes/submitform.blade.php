@extends('layouts.master')

@section('main-content')
<div class="row">
    <div class="col-md-12">
        @include('layouts.notification')
    </div>
</div>
<form id="formx" name="formx" method="post" action="{{ route('institutes.list') }}">
@csrf 
<div class="card-body box-profile">
  <ul class="list-group list-group-unbordered mb-3">
    <li class="list-group-item">
  
            <input type="checkbox" id="institute" class="toggle-vis" name="col[]" value="institute" checked>
            <label for="institute">Institute</label><br>
            <input type="checkbox" id="census" class="toggle-vis" name="col[]" value="census">
            <label for="census">Census</label><br>
            <input type="checkbox" id="schoolid" class="toggle-vis" name="col[]" value="schoolid" checked>
            <label for="schoolid">School ID</label><br>
            <input type="checkbox" id="examid" class="toggle-vis" name="col[]" value="examid">
            <label for="examid">Exam ID</label><br>
            <input type="checkbox" id="" class="toggle-vis" name="col[]" value="type" checked>
            <label for="type">Type</label><br>
            <input type="checkbox" id="" class="toggle-vis" name="col[]" value="span">
            <label for="span">Span</label><br>
            <input type="checkbox" id="" class="toggle-vis" name="col[]" value="division" checked>
            <label for="division">Division</label><br>
            <input type="checkbox" id="startdate" class="toggle-vis" name="col[]" value="startdate">
            <label for="startdate">Start Date</label><br>
            <input type="checkbox" id="principal" class="toggle-vis" name="col[]" value="principal" checked>
            <label for="principal">Principal</label><br>
            <input type="checkbox" id="mobile" class="toggle-vis" name="col[]" value="mobile" checked>
            <label for="mobile">Phone</label><br>
            <input type="checkbox" id="schaddress" class="toggle-vis" name="col[]" value="schaddress" checked>
            <label for="schaddress">Address</label><br>
            <input type="checkbox" id="gn" class="toggle-vis" name="col[]" value="gn">
            <label for="gn">GN Division</label><br>
            <input type="checkbox" id="gnaea" class="toggle-vis" name="col[]" value="gnarea">
            <label for="gnaea">GN Area</label><br>
            <input type="checkbox" id="email" class="toggle-vis" name="col[]" value="email">
            <label for="email">eMail</label><br>
            <input type="checkbox" id="" class="toggle-vis" name="col[]" value="category">
            <label for="category">Category</label><br>
            <input type="checkbox" id="" class="toggle-vis" name="col[]" value="police">
            <label for="police">Police Station</label><br>
            <input type="checkbox" id="" class="toggle-vis" name="col[]" value="postoffice">
            <label for="postoffice">Post Office</label><br>
            <input type="checkbox" id="electorate" class="toggle-vis" name="col[]" value="electorate">
            <label for="electorate">Electorate</label><br>
            <input type="checkbox" id="gpslocation" class="toggle-vis" name="col[]" value="gpslocation">
            <label for="gpslocation">GPS Location</label><br>
            <input type="checkbox" id="disfrmzeo" class="toggle-vis" name="col[]" value="disfrmzeo">
            <label for="disfrmzeo">Distance to ZEO</label><br>
            <input type="checkbox" id="cluster" class="toggle-vis" name="col[]" value="cluster">
            <label for="cluster">Cluster</label><br>
            <input type="checkbox" id="clustercode" class="toggle-vis" name="col[]" value="clustercode" >
            <label for="clustercode">Cluster Code</label><br>
            <input type="checkbox" id="students" class="toggle-ext" name="col[]" value="students">
            <label for="students">Students</label><br>
            <input type="checkbox" id="academic" class="toggle-ext" name="col[]" value="academic">
            <label for="">Academic Staff</label><br>
            <input type="checkbox" id="nonacademic" class="toggle-ext" name="col[]" value="nonacademic">
            <label for="">Non-Academic Staff</label><br>
    
  
    </li>
    <li class="list-group-item">
      <input type="submit" name="report" id="report" value="Report">
    </li>
  </ul>
</div>
</form>
@endsection

@push('styles')
<style>
.scroll {
  max-height: 400px;
  overflow-y: auto;
}

</style>
@endpush

@push('scripts')
<script type="text/javascript">
 
</script>
@endpush