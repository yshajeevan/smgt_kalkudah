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
        {{ isset($checklist) ? "Edit Checklist" : "Add Checklist"}}
    </h6>
  </div>

  <div class="card-body">
    <form action="{{ isset($checklist) ? route('checklist.update',$checklist->id) : route('checklist.store') }}"
          method="post"
          enctype="multipart/form-data">

        @csrf
        @if(isset($checklist))
            @method('PUT')
        @endif

        {{-- Document Name --}}
        <div class="row">
            <div class="col-md-12">
                <div class="form-group text-dark">
                    <label>Document Name</label>
                    <input type="text"
                           class="form-control"
                           name="name"
                           value="{{ old('name', isset($checklist) ? $checklist->name : '') }}">
                </div>
            </div>
        </div>

        {{-- File Upload --}}
        <div class="row">
            <div class="col-md-12">
                <div class="form-group text-dark">
                    <label>Upload Supportive Document (Optional)</label>
                    <input type="file" name="supportive_doc" class="form-control">
                </div>

                {{-- Show existing file --}}
                @if(isset($checklist) && $checklist->supportive_doc)

                @php
                    $extension = pathinfo($checklist->supportive_doc, PATHINFO_EXTENSION);
                @endphp

                <a href="{{ asset($checklist->supportive_doc) }}" target="_blank">
                    @if(in_array(strtolower($extension), ['jpg','jpeg','png','gif']))
                        {{-- Image Thumbnail --}}
                        <img src="{{ asset('images/image-icon.png') }}"
                            width="60"
                            style="border:1px solid #ccc;padding:3px;">
                    @elseif($extension == 'pdf')
                        {{-- PDF Icon --}}
                        <img src="{{ asset('images/pdf-icon.png') }}"
                            width="40">
                    @elseif(in_array(strtolower($extension), ['doc','docx']))
                        {{-- Word Icon --}}
                        <img src="{{ asset('images/word-icon.png') }}"
                            width="40">
                    @else
                        {{-- Generic File Icon --}}
                        <img src="{{ asset('images/file-icon.png') }}"
                            width="40">
                    @endif
                </a>
            @endif
            </div>
        </div>

        {{-- Remarks Field --}}
        <div class="row">
            <div class="col-md-12">
                <div class="form-group text-dark">
                    <label>Remarks</label>
                    <input type="text"
                           class="form-control"
                           name="remarks"
                           value="{{ old('remarks', isset($checklist) ? $checklist->remarks : '') }}">
                </div>
            </div>
        </div>

        {{-- Hidden Service ID --}}
        <input type="hidden"
               name="service_id"
               value="{{ isset($checklist) ? $checklist->service_id : $id }}">

        <div class="mt-3">
            <input type="submit"
                   class="btn btn-warning"
                   value="{{ isset($checklist) ? 'Update' : 'Add' }}">
        </div>

    </form>
  </div>
</div>
@endsection
