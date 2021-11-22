@extends('layouts.app',['activePage' => 'setting'])

@section('title','Notification Setting')

@section('content')

    @if (Session::has('msg'))
    @include('layouts.msg')
    @endif

    <section class="section">
        <div class="section-header">
            <h1>{{__('Notification and mail setting')}}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ url('admin/home') }}">{{__('Dashboard')}}</a></div>
                <div class="breadcrumb-item active"><a href="{{ url('admin/setting') }}">{{__('Setting')}}</a></div>
                <div class="breadcrumb-item">{{__('Notification and mail setting')}}</div>
            </div>
        </div>
        <div class="section-body">
            <h2 class="section-title">{{__('notification related setting')}}</h2>
            <p class="section-lead">{{__('notification related setting')}}</p>

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills" id="myTab3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active show" id="home-tab3" data-toggle="tab" href="#home3" role="tab" aria-controls="home"
                                aria-selected="false">{{__('Push notification')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab3" data-toggle="tab" href="#profile3" role="tab"
                                aria-controls="profile" aria-selected="true">{{__('Mail notification')}}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent2">
                        <div class="tab-pane fade active show" id="home3" role="tabpanel" aria-labelledby="home-tab3">
                            <div class="card">
                                <div class="card-header">
                                    <h4>{{__('notification setting')}}</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="{{__('customer_notification')}}">{{__('Customer notification')}}</label><br>
                                            <label class="switch">
                                                <input type="checkbox" name="customer_notification" {{ $notification_setting->customer_notification == '1' ? 'checked' : '' }}>
                                                <div class="slider"></div>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="{{__('vendor_notification')}}">{{__('vendor notification')}}</label><br>
                                            <label class="switch">
                                                <input type="checkbox" name="vendor_notification" {{ $notification_setting->vendor_notification == '1' ? 'checked' : '' }}>
                                                <div class="slider"></div>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="{{__('driver_notification')}}">{{__('driver notification')}}</label><br>
                                            <label class="switch">
                                                <input type="checkbox" name="driver_notification" {{ $notification_setting->driver_notification == '1' ? 'checked' : '' }}>
                                                <div class="slider"></div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mt-5 customer_notification_card {{ $notification_setting->customer_notification == 0 ? 'hide' : '' }}">
                                <div class="card-header">
                                    <h4>{{__('customer notification')}}</h4>
                                </div>
                                <form action="{{ url('admin/update_customer_notification') }}" method="post">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="onesignal_app_id">{{__('One signal app id')}}</label>
                                                <input type="text" name="customer_app_id" value="{{ $notification_setting->customer_app_id }}" required class="form-control" style="text-transform: none">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="onesignal_app_id">{{__('One signal auth key')}}</label>
                                                <input type="text" name="customer_auth_key" value="{{ $notification_setting->customer_auth_key }}" required class="form-control" style="text-transform: none">
                                            </div>
                                        </div>

                                        <div class="row mb-12">
                                            <div class="col-md-12">
                                                <label for="onesignal_app_id">{{__('One signal api key')}}</label>
                                                <input type="text" name="customer_api_key" value="{{ $notification_setting->customer_api_key }}"  required class="form-control" style="text-transform: none">
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

                            <div class="card mt-5 driver_notification_card {{ $notification_setting->driver_notification == 0 ? 'hide' : '' }}">
                                <div class="card-header">
                                    <h4>{{__('Driver notification')}}</h4>
                                </div>
                                <form action="{{ url('admin/update_driver_notification') }}" method="post">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="onesignal_app_id">{{__('One signal app id')}}</label>
                                                <input type="text" required value="{{ $notification_setting->driver_app_id }}" name="driver_app_id" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="onesignal_app_id">{{__('One signal auth key')}}</label>
                                                <input type="text" required value="{{ $notification_setting->driver_auth_key }}" name="driver_auth_key" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="onesignal_app_id">{{__('One signal api key')}}</label>
                                                <input type="text" required value="{{ $notification_setting->driver_api_key }}"  name="driver_api_key" class="form-control">
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

                            <div class="card mt-5 vendor_notification_card {{ $notification_setting->vendor_notification == 0 ? 'hide' : '' }}">
                                <div class="card-header">
                                    {{__('vendor notification')}}
                                </div>
                                <form action="{{ url('admin/update_vendor_notification') }}" method="post">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="onesignal_app_id">{{__('One signal app id')}}</label>
                                                <input type="text" name="vendor_app_id" required value="{{ $notification_setting->vendor_app_id }}" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="onesignal_app_id">{{__('One signal auth key')}}</label>
                                                <input type="text" name="vendor_auth_key" required value="{{ $notification_setting->vendor_auth_key }}" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <label for="onesignal_app_id">{{__('One signal api key')}}</label>
                                                <input type="text" name="vendor_api_key" required value="{{ $notification_setting->vendor_api_key }}" class="form-control">
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
                        </div>
                        <div class="tab-pane fade" id="profile3" role="tabpanel" aria-labelledby="profile-tab3">
                            <form action="{{ url('admin/update_mail_setting') }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="{{__('customer_notification')}}">{{__('Customer mail notification')}}</label><br>
                                        <label class="switch">
                                            <input type="checkbox" name="customer_mail" {{ $notification_setting->customer_mail == '1' ? 'checked' : '' }}>
                                            <div class="slider"></div>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="{{__('vendor_notification')}}">{{__('vendor mail notification')}}</label><br>
                                        <label class="switch">
                                            <input type="checkbox" name="vendor_mail" {{ $notification_setting->vendor_mail == '1' ? 'checked' : '' }}>
                                            <div class="slider"></div>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="{{__('driver_notification')}}">{{__('driver mail notification')}}</label><br>
                                        <label class="switch">
                                            <input type="checkbox" name="driver_mail" {{ $notification_setting->driver_mail == '1' ? 'checked' : '' }}>
                                            <div class="slider"></div>
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="onesignal_app_id">{{__('Mail Host')}}</label>
                                            <input type="text" required value="{{ $notification_setting->mail_host }}" name="mail_host" class="form-control" style="text-transform: none;">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="onesignal_app_id">{{__('Mail user name')}}</label>
                                            <input type="text" required value="{{ $notification_setting->mail_username }}"  name="mail_username" class="form-control" style="text-transform: none;">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="onesignal_app_id">{{__('Mail password')}}</label>
                                            <input type="password" required value="{{ $notification_setting->mail_password }}"  name="mail_password" class="form-control" style="text-transform: none;">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="onesignal_app_id">{{__('Mail Encryption')}}</label>
                                            <input type="text" required value="{{ $notification_setting->mail_encryption }}"  name="mail_encryption" class="form-control" style="text-transform: none;">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="onesignal_app_id">{{__('Mail From address')}}</label>
                                            <input type="text" required value="{{ $notification_setting->mail_from_address }}"  name="mail_from_address" class="form-control" style="text-transform: none;">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="onesignal_app_id">{{__('Mail port')}}</label>
                                            <input type="text" required value="{{ $notification_setting->mail_port }}"  name="mail_port" class="form-control" style="text-transform: none;">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12">
                                            <input type="submit" value="{{__('Update')}}" class="btn btn-primary float-right">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
