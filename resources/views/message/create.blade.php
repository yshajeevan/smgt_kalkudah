@extends('layouts.master')

@section('main-content')
  <div class="card shadow mb-4">
    <div class="row">
      <div class="col-md-12">
        @include('layouts.notification')
      </div>
    </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Compose Message</h6>
    </div>
    <div class="card-body">
      <form action="{{route('message.store')}}" id="msg" name="msg" method="post" enctype="multipart/form-data">
      @csrf 
      <div class="row">
        <div class="col-md-6">
          <div class="form-group text-dark">
            <label for="" class="control-label">Recipient<span>*</span></label>
            <select name="reciever[]" id="reciever" class="form-control form-control-sm" multiple>
              @foreach ($users as $user)
              <option value="{{ $user->id}}">{{ $user->name}}</option>
              @endforeach    
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group text-dark">
            <label for="" class="control-label">Mobile</label>
              <input type="text" class="form-control form-control-sm" name="mobile" id="mobile" placeholder="Enter Mobile">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group text-dark">
            <label for="" class="control-label">Subject<span>*</span></label>
              <input type="text" class="form-control form-control-sm" name="subject" id="subject" placeholder="Enter Subject">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group text-dark">
            <label for="" class="control-label">Message<span>*</span></label>
            <textarea class="form-control form-control-sm" name="msg" id="msg" rows="5"></textarea>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-6">
          <label for="code" class="form-label">File upload</label>
          <input type="file" name="file" id="chooseFile" accept=".jpg,.jpeg,.bmp,.png,.gif,.doc,.docx,.csv,.rtf,.xlsx,.xls,.txt,.pdf,.zip">
          @error('file')
            <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
          @enderror
        </div>
      </div>
      <div class="row">
        <div class="col-6">         
          <div style="text-align: center;">
              <input type="submit" id="saveBtn" class="btn btn-warning" value="Send">
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>

</style>
@endpush

@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
    $('#reciever').select2();
});

$('#reciever').on('select2:select', function(e){
	// var data = e.params.data;
	var empid = $('#reciever').val();
      $.ajax({
        url: "{{ url('/mobiledetails') }}",
        method: 'get',
          data: {
            empid:empid
            },
            success: function(response){
              $("#mobile").val(response);
            }
      });
});  
</script>
@endpush