@extends('layouts.master')

@section('main-content')
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h6 class="m-0 font-weight-bold text-primary float-left">{{ isset($item) ? 'Update Employee Status' : '' }}</h6>
        </div> 
    </div> 
    <form action="{{ isset($employee) ? route('employee.update',$employee->id) : route('employee.store') }}" id="employee_form" name="employee_form" method="post">
        @csrf
        <table>
          <tr>
            <th>Employee Name</th>
            <th>Status</th>
          </tr>
          <tr>
            @foreach($items as $item)
                <td>Alfreds Futterkiste</td>
                <td>Maria Anders</td>
            @endforeach
          </tr>
        </table>
        <div class="form-group" align="center">
            <input type="submit" id="saveBtn" class="btn btn-warning" value="{{ isset($employee) ? 'Update': 'Add' }}">
        </div>
    </form>