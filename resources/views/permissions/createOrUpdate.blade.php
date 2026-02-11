@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">{{ isset($permissions) ? 'Edit Record' : 'Create new Record' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($permissions) ? route('permissions.update',$permissions->id) : route('permissions.store') }}" id="permissions_form" name="permissions_form" method="post">
            @csrf 
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group text-dark">
                        <label for="" class="control-label">Permission Name</label>
                        <input type="text" class="form-control form-control-sm" name="name" id="name" value="{{ old('name', isset($permissions) ? $permissions->name : '') }}">
                    </div>
                </div>
            </div>         
            <br/>
            <div class="form-group" align="center">
                <input type="submit" id="saveBtn" class="btn btn-warning" value="{{ isset($permissions) ? 'Update': 'Add' }}">
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
   
</script>
@endpush