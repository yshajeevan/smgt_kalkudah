@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ isset($cadreSubject) ? 'Edit CadreSubject' : 'Add CadreSubject' }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ isset($cadreSubject) ? route('cadre-subject.update', $cadreSubject->id) : route('cadre-subject.store') }}" method="POST">
            @csrf
            @if(isset($cadreSubject)) @method('PUT') @endif

            <div class="form-group">
                <label for="cadre">Cadre</label>
                <input type="text" name="cadre" class="form-control" value="{{ old('cadre', $cadreSubject->cadre ?? '') }}">
                @error('cadre') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="cadre_code">Cadre Code</label>
                <input type="text" name="cadre_code" class="form-control" value="{{ old('cadre_code', $cadreSubject->cadre_code ?? '') }}">
                @error('cadre_code') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" name="category" class="form-control" value="{{ old('category', $cadreSubject->category ?? '') }}">
                @error('category') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="subject_number">Subject Number</label>
                <input type="number" name="subject_number" class="form-control" value="{{ old('subject_number', $cadreSubject->subject_number ?? '') }}">
                @error('subject_number') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" class="form-control">
                    <option value="primary" {{ old('category', $cadreSubject->category ?? '') == 'primary' ? 'selected' : '' }}>Primary</option>
                    <option value="secondary_b1" {{ old('category', $cadreSubject->category ?? '') == 'secondary_b1' ? 'selected' : '' }}>Secondary B1</option>
                    <option value="secondary_b2" {{ old('category', $cadreSubject->category ?? '') == 'secondary_b2' ? 'selected' : '' }}>Secondary B2</option>
                    <option value="secondary_b3" {{ old('category', $cadreSubject->category ?? '') == 'secondary_b3' ? 'selected' : '' }}>Secondary B3</option>
                    <option value="advanced_level" {{ old('category', $cadreSubject->category ?? '') == 'advanced_level' ? 'selected' : '' }}>Advanced Level</option>
                    <option value="school_non_academic" {{ old('category', $cadreSubject->category ?? '') == 'school_non_academic' ? 'selected' : '' }}>School Non-Academic</option>
                    <option value="school_administration" {{ old('category', $cadreSubject->category ?? '') == 'school_administration' ? 'selected' : '' }}>School Administration</option>
                    <option value="office_academic" {{ old('category', $cadreSubject->category ?? '') == 'office_academic' ? 'selected' : '' }}>Office Academic</option>
                    <option value="office_non_academic" {{ old('category', $cadreSubject->category ?? '') == 'office_non_academic' ? 'selected' : '' }}>Office Non-Academic</option>
                    <option value="13_years_education" {{ old('category', $cadreSubject->category ?? '') == '13_years_education' ? 'selected' : '' }}>13 Years Education</option>
                    <option value="others" {{ old('category', $cadreSubject->category ?? '') == 'others' ? 'selected' : '' }}>Others</option>
                </select>
                @error('category') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="app_cadre">App Cadre</label>
                <input type="number" name="app_cadre" class="form-control" value="{{ old('app_cadre', $cadreSubject->app_cadre ?? '') }}">
                @error('app_cadre') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ isset($cadreSubject) ? 'Update' : 'Save' }}</button>
        </form>
    </div>
</div>
@endsection