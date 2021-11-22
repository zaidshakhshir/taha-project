@extends('layouts.app',['activePage' => 'setting'])

@section('title','Payment Setting')

@section('content')
    @if (Session::has('msg'))
    @include('layouts.msg')
    @endif

    <section class="section">
        <div class="section-header">
            <h1>{{__('Payment setting')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('admin/home') }}">{{__('Dashboard')}}</a></div>
                <div class="breadcrumb-item active"><a href="{{ url('admin/setting') }}">{{__('Setting')}}</a></div>
                <div class="breadcrumb-item">{{__('Payment setting')}}</div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                {{__('Payment setting')}}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <label for="{{__('COD')}}">{{__('COD')}}</label><br>
                        <label class="switch">
                            <input type="checkbox" name="COD" {{ $PaymentSetting->cod == 1 ? 'checked' : '' }}>
                            <div class="slider"></div>
                        </label>
                    </div>
                    <div class="col">
                        <label for="{{__('stripe')}}">{{__('stripe')}}</label><br>
                        <label class="switch">
                            <input type="checkbox" name="stripe" {{ $PaymentSetting->stripe == 1 ? 'checked' : '' }}>
                            <div class="slider"></div>
                        </label>
                    </div>
                    <div class="col">
                        <label for="{{__('paypal')}}">{{__('paypal')}}</label><br>
                        <label class="switch">
                            <input type="checkbox" name="paypal" {{ $PaymentSetting->paypal == 1 ? 'checked' : '' }}>
                            <div class="slider"></div>
                        </label>
                    </div>
                    <div class="col">
                        <label for="{{__('razor pay')}}">{{__('razor pay')}}</label><br>
                        <label class="switch">
                            <input type="checkbox" name="razorpay" {{ $PaymentSetting->razorpay == 1 ? 'checked' : '' }}>
                            <div class="slider"></div>
                        </label>
                    </div>
                    <div class="col">
                        <label for="{{__('Flutterwave')}}">{{__('Flutterwave')}}</label><br>
                        <label class="switch">
                            <input type="checkbox" name="flutterwave" {{ $PaymentSetting->flutterwave == 1 ? 'checked' : '' }}>
                            <div class="slider"></div>
                        </label>
                    </div>
                    <div class="col">
                        <label for="{{__('Wallet')}}">{{__('Wallet')}}</label><br>
                        <label class="switch">
                            <input type="checkbox" name="wallet" {{ $PaymentSetting->wallet == 1 ? 'checked' : '' }}>
                            <div class="slider"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-5 stripe_card {{ $PaymentSetting->stripe == 0 ? 'hide' : '' }}">
            <div class="card-header">
                {{__('Stripe')}}
            </div>
            <form action="{{ url('admin/update_stripe_setting') }}" method="post">
            @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="stripe_key">{{__('Stripe publish key')}}</label>
                            <input type="text" name="stripe_publish_key" value="{{ $PaymentSetting->stripe_publish_key }}" required class="form-control" style="text-transform: none" >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="stripe secret key">{{__('Stripe secret key')}}</label>
                            <input type="text" name="stripe_secret_key" value="{{ $PaymentSetting->stripe_secret_key }}" required class="form-control" style="text-transform: none">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <input type="submit" value="{{__('Update')}}"  class="btn btn-primary float-right">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mt-5 paypal_card {{ $PaymentSetting->paypal == 0 ? 'hide' : '' }}">
            <div class="card-header">
                {{__('paypal')}}
            </div>
            <form action="{{ url('admin/update_paypal') }}" method="post">
            @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="onesignal_app_id">{{__('paypal production key ( For Admin )')}}</label>
                            <input type="text" name="paypal_production" value="{{ $PaymentSetting->paypal_production }}" required class="form-control" style="text-transform: none">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="onesignal_app_id">{{__('paypal sendbox key ( for admin )')}}</label>
                            <input type="text" name="paypal_sendbox" value="{{ $PaymentSetting->paypal_sendbox }}" required class="form-control" style="text-transform: none">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="onesignal_app_id">{{__('paypal Secret key ( For user )')}}</label>
                            <input type="text" name="paypal_secret_key" value="{{ $PaymentSetting->paypal_secret_key }}" required class="form-control" style="text-transform: none">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="onesignal_app_id">{{__('paypal Client Id ( For user )')}}</label>
                            <input type="text" name="paypal_client_id" value="{{ $PaymentSetting->paypal_client_id }}" required class="form-control" style="text-transform: none">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <input type="submit" value="{{__('Update')}}"  class="btn btn-primary float-right">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mt-5 razorpay_card {{ $PaymentSetting->razorpay == 0 ? 'hide' : '' }}">
            <div class="card-header">
                {{__('Razorpay')}}
            </div>
            <form action="{{ url('admin/update_razorpay') }}" method="post">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="razorpay">{{__('Razorpay publish key')}}</label>
                            <input type="text" name="razorpay_publish_key" value="{{ $PaymentSetting->razorpay_publish_key }}" required class="form-control" style="text-transform: none">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <input type="submit" value="{{__('Update')}}"  class="btn btn-primary float-right">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card mt-5 flutterwave_card {{ $PaymentSetting->flutterwave == 0 ? 'hide' : '' }}">
            <div class="card-header">
                {{__('Flutterwave')}}
            </div>
            <form action="{{ url('admin/update_flutterwave') }}" method="post">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="Flutterwave">{{__('Flutterwave public key')}}</label>
                            <input type="text" name="public_key" value="{{ $PaymentSetting->public_key }}" required class="form-control" style="text-transform: none">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <input type="submit" value="{{__('Update')}}"  class="btn btn-primary float-right">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
