@extends('layouts.app',['activePage' => 'language'])

@section('title','Language')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>{{__('Language')}}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ url('admin/home') }}">{{__('Dashboard')}}</a></div>
            <div class="breadcrumb-item active"><a href="{{ url('admin/language') }}">{{__('Language')}}</a></div>
            <div class="breadcrumb-item">{{__('edit Language')}}</div>
        </div>
    </div>

    <div class="section-body">
        <h2 class="section-title">{{__('Language management')}}</h2>
        <p class="section-lead">{{__('Language')}}</p>
        <form class="container-fuild" action="{{ url('admin/language/'.$language->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card p-2">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            <label for="language">{{__('Language Image')}}</label>
                            <div class="logoContainer">
                                <img id="image" src="{{ $language->image }}" width="180" height="150">
                            </div>
                            <div class="fileContainer sprite">
                                <span>{{__('Image')}}</span>
                                <input type="file" name="image" value="Choose File" id="previewImage" data-id="edit"  accept=".png, .jpg, .jpeg, .svg">

                            </div>
                            @error('image')
                            <div class="custom_error" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="">{{__('Name of Language')}}({{__("cann't edit")}})<span class="text-danger">&nbsp;*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is_invalide @enderror" value="{{ $language->name }}" required="true" readonly style="text-transform: none">
                            @error('name')
                            <span class="custom_error" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="">{{__('Language Json File')}}<span class="text-danger">&nbsp;*</span></label>
                            <input type="file" name="file" class="form-control" accept=".json">
                            @error('file')
                                <span class="custom_error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="">{{__('Language Direction')}}<span class="text-danger">&nbsp;*</span></label>
                            <select name="direction" class="form-control">
                                <option value="ltr" {{ $language->direction == 'ltr' ? 'selected' : '' }}>{{__('ltr')}}</option>
                                <option value="rtl" {{ $language->direction == 'rtl' ? 'selected' : '' }}>{{__('rtl')}}</option>
                            </select>
                            @error('direction')
                                <span class="custom_error" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="status">{{__('Status')}}</label><br>
                            <label class="switch">
                                <input type="checkbox" name="status" {{ $language->status == 1 ? 'checked' : '' }}>
                                <div class="slider"></div>
                            </label>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">{{__('edit Language')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
