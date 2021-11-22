@extends('layouts.app',['activePage' => 'tax'])

@section('title','Create A Tax')

@section('content')

<section class="section">
    <div class="section-header">
        <h1>{{__('Create Tax')}}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ url('admin/home') }}">{{__('Dashboard')}}</a></div>
            <div class="breadcrumb-item"><a href="{{ url('admin/tax') }}">{{__('Tax')}}</a></div>
            <div class="breadcrumb-item">{{__('create a tax')}}</div>
        </div>
    </div>

    <div class="section-body">
        @if ($errors->any())
        <div class="alert alert-primary alert-dismissible show fade">
            <div class="alert-body">
                <button class="close" data-dismiss="alert">
                    <span>×</span>
                </button>
                @foreach ($errors->all() as $item)
                    {{ $item }}
                @endforeach
            </div>
        </div>
        @endif
        <h2 class="section-title">{{__('Tax management')}}</h2>
        <p class="section-lead">{{__('create Delivery person')}}</p>
        <form class="container-fuild" action="{{ url('admin/tax') }}" method="post">
        @csrf
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-5">
                            <label for="Tax name">{{__('Tax name')}}<span class="text-danger">&nbsp;*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is_invalide @enderror" placeholder="{{__('Tax Name')}}" value="{{old('name')}}" required="">

                            @error('name')
                            <span class="custom_error" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-5">
                            <label for="{{__('Tax')}}">{{__('Tax')}}<span class="text-danger">&nbsp;*</span></label>
                            <input type="number" min="1" name="tax" value="{{ old('tax') }}" class="form-control @error('tax') is_invalide @enderror" placeholder="{{__('Tax')}}">
                            @error('tax')
                            <span class="custom_error" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-5">
                            <label for="{{__('Tax')}}">{{__('Tax type')}}<span class="text-danger">&nbsp;*</span></label>
                            <select name="type" class="form-control">
                                <option value="percentage">{{__('percentage')}}</option>
                                <option value="amount">{{__('amount')}}</option>
                            </select>
                            @error('type')
                            <span class="custom_error" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="{{__('status')}}">{{__('Status')}}</label><br>
                            <label class="switch">
                                <input type="checkbox" name="status">
                                <div class="slider"></div>
                            </label>
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn btn-primary">{{__('Add Tax')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection
