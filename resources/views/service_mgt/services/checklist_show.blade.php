@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>

    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">
          Service Checklist - {{ $service->service }}
      </h6>
    </div>

    <div class="card-body">
        <div class="checklist-wrapper">

            @foreach($checklists as $checklist)
            <div class="row align-items-start">
                
                <div class="col-8">

                    <div class="mb-2">
                        {{-- Continuous Number --}}
                        <strong>{{ $loop->iteration }}.</strong>

                        {{-- Checklist Name --}}
                        {{$checklist->name}}

                        {{-- File Thumbnail --}}
                        @if($checklist->supportive_doc)

                            @php
                                $extension = strtolower(pathinfo($checklist->supportive_doc, PATHINFO_EXTENSION));
                            @endphp

                            <a href="{{ asset($checklist->supportive_doc) }}" target="_blank" style="margin-left:10px;">

                                @if(in_array($extension, ['jpg','jpeg','png','gif']))
                                    <img src="{{ asset('images/image-icon.png') }}"
                                         width="40"
                                         style="border:1px solid #ccc;padding:2px;">
                                @elseif($extension == 'pdf')
                                    <img src="{{ asset('images/pdf-icon.png') }}" width="25">
                                @elseif(in_array($extension, ['doc','docx']))
                                    <img src="{{ asset('images/word-icon.png') }}" width="25">
                                @else
                                    <img src="{{ asset('images/file-icon.png') }}" width="25">
                                @endif

                            </a>
                        @endif
                    </div>

                    {{-- Remarks --}}
                    @if($checklist->remarks)
                        <div style="margin-left:25px;">
                            <b><i>({{ $checklist->remarks }})</i></b>
                        </div>
                    @endif

                </div>

                @can('service-create')
                <div class="col-2">
                    <a href="{{route('checklist.edit',$checklist->id)}}"
                       class="btn btn-xs btn-primary btn-sm"
                       style="height:30px; width:30px;"
                       title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    <form action="{{ route('checklist.destroy', $checklist->id) }}"
                        method="POST"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endcan

            </div>
            <hr>
            @endforeach

        </div>

        @can('service-create')
            <a href="{{route('checklist.create',$id)}}"
               class="btn btn-primary btn-sm float-left">
               <i class="fas fa-plus"></i> Add Checklist
            </a>
        @endcan

    </div>
</div>
@endsection

@push('styles')
<style>
.card{
    height:600px;
}
.checklist-wrapper ul{
    list-style-type:none;
    padding-left:20px;
    color:#666;
}
.checklist-wrapper li{
    position: relative;
    padding-left:20px;
    margin-bottom:10px;
}




</style>
@endpush