@extends('layouts.master')

@section('main-content')
<div class="container-fluid">
    <h4>Create Student</h4>

    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('students.store') }}">
        @csrf
        <div class="mb-2">
            <label>Admission Number</label>
            <input type="text" name="admission_number" class="form-control" value="{{ old('admission_number') }}" required>
        </div>

        <div class="mb-2">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="mb-2">
            <label>Grade</label>
            <input type="number" name="grade" class="form-control" value="{{ old('grade') }}" required>
        </div>

        <div class="mb-2">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Active">Active</option>
                <option value="Droped out">Droped out</option>
            </select>
        </div>

        <div class="mb-2">
            <label>EWS Color</label>
            <select name="ews_color" class="form-control">
                <option value="">-- none --</option>
                <option value="1">Green</option>
                <option value="2">Orange</option>
                <option value="3">Red</option>
            </select>
        </div>

        <div class="mt-3">
            <button class="btn btn-primary">Create</button>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>
@endsection
