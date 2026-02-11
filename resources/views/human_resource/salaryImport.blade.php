@extends('layouts.master')

@section('main-content')
<div class="card">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    <h2>Import Salary File</h2>
     <form action="{{route('salary.import')}}" method="post" enctype="multipart/form-data">
        @csrf 
        <div class="row">
            <div class="col-6">
              <label for="code" class="form-label">File upload</label>
              <input type="file" name="document[]" accept=".jpg,.jpeg,.bmp,.png,.gif,.doc,.docx,.csv,.rtf,.xlsx,.xls,.txt,.pdf,.zip" >
              @error('file')
                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
              @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-6">
              <label for="code" class="form-label">File upload</label>
              <input type="file" name="document[]" accept=".jpg,.jpeg,.bmp,.png,.gif,.doc,.docx,.csv,.rtf,.xlsx,.xls,.txt,.pdf,.zip" >
              @error('file')
                <div class="alert alert-danger mt-1 mb-1">{{ $message }}</div>
              @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-6">         
              <div style="text-align: left;">
                  <input type="submit" id="saveBtn" class="btn btn-warning" value="Submit">
              </div>
            </div>
        </div>
    </form>
</div>
@endsection


