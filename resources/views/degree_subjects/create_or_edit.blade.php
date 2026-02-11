@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($degSubject) ? 'Edit Subject' : 'Add Subject' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($degSubject) ? route('deg-subjects.update', $degSubject->id) : route('deg-subjects.store') }}" method="POST">
            @csrf
            @if(isset($degSubject)) @method('PUT') @endif

            <div class="form-group">
                <label for="degreesub">Subject</label>
                <input type="text" name="degreesub" class="form-control" value="{{ old('degreesub', $degSubject->degreesub ?? '') }}">
                @error('degreesub') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($degSubject) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection
