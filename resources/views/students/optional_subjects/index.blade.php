@extends('layouts.master')

@section('main-content')
<div class="container-fluid">
    <h4>Update Student Optional Subjects</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name or id">
        </div>
        <div class="col-auto">
            <input name="grade" value="{{ request('grade') }}" class="form-control" placeholder="Grade (optional)">
        </div>
        <div class="col-auto">
            <button class="btn btn-secondary">Filter</button>
        </div>
    </form>

    <div style="max-height:70vh; overflow:auto;">
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Optional 1</th>
                <th>Optional 2</th>
                <th>Optional 3</th>
                <th>Optional 4</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $stu)
            <tr>
                <td>{{ $loop->iteration + ($students->currentPage()-1)*$students->perPage() }}</td>
                <td>{{ $stu->name }} ({{ $stu->id }})</td>

                <form method="POST" action="{{ route('students.optionals.update', $stu->id) }}">
                    @csrf
                    <td>
                        <select name="cadresubject1_id" class="form-select form-select-sm">
                            <option value="">-- None --</option>
                            @foreach($basket1Subjects as $c)
                                <option value="{{ $c->id }}" {{ $stu->cadresubject1_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->cadre }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="cadresubject2_id" class="form-select form-select-sm">
                            <option value="">-- None --</option>
                            @foreach($basket2Subjects as $c)
                                <option value="{{ $c->id }}" {{ $stu->cadresubject2_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->cadre }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="cadresubject3_id" class="form-select form-select-sm">
                            <option value="">-- None --</option>
                            @foreach($basket3Subjects as $c)
                                <option value="{{ $c->id }}" {{ $stu->cadresubject3_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->cadre }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="cadresubject4_id" class="form-select form-select-sm">
                            <option value="">-- None --</option>
                            @foreach($religionSubjects as $c)
                                <option value="{{ $c->id }}" {{ $stu->cadresubject4_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->cadre }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td style="white-space:nowrap;">
                        <button class="btn btn-sm btn-primary" type="submit">Update</button>
                    </td>
                </form>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <div class="mt-2">
        {{ $students->links() }}
    </div>
</div>
@endsection
