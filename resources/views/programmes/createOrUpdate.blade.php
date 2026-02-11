@extends('layouts.master')

@section('main-content')
<div class="card">
    <h5 class="card-header">{{ isset($item) ? 'Edit ' .ucwords(str_replace("_", " ", Request::segment(1))) : 'Add ' .ucwords(str_replace("_", " ", Request::segment(1))) }}</h5>
    <div class="card-body">
    <form action="{{ isset($item) ? route(Request::segment(1).'.update',$item->id) : route(Request::segment(1).'.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method(isset($item) ? 'PATCH' : 'POST')
        <div class="form-group">
            <label for="name" class="col-form-label">Programme</label>
            <input id="name" type="text" name="name" value="{{old('name', isset($item) ? $item->name : '')}}" class="form-control" required>
            @error('name')
              <span class="text-danger">{{$message}}</span>
            @enderror
        </div>
        <div class="form-group">
            <div class="form-col-2">
                <label for="" class="control-label">Coordinator</label>
            </div>   
            <div class="form-col-10"> 
                <select name="coordinator_id" id="coordinator_id" class="form-control form-control" required>
                    <option disabled selected value>--Select News Category--</option>
                    @foreach ($coordinators as $coordinator)
                    <option value="{{ $coordinator->id}}" {{(isset($item) && $item->coordinator_id == $coordinator->id)  ? 'selected' : ''}}>{{$coordinator->namewithinitial." (".$coordinator->designation->designation.")"}}</option>
                    @endforeach    
                </select>
                @error('coordinator_id')
                    <span class="text-danger">{{$message}}</span>
                @enderror
            </div>
        </div>
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">{{ isset($item) ? 'Update': 'Add' }}</button>
        </div>
      </form>
    </div>
</div>
@endsection


@push('scripts')
<script type="text/javascript">

</script>
@endpush