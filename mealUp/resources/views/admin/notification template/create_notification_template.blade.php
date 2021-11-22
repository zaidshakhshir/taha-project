@extends('layouts.app',['activePage' => 'notification template'])

@section('title','Create A notification template')

@section('content')

<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ url('admin/notification_template') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            <h1>{{__('Create new notification template')}}</h1>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <form class="container-fuild" action="{{ url('admin/notification_template') }}" method="post" enctype="multipart/form-data">
                @csrf
                    <div class="card mt-2">
                        <div class="card-body">
                            <div class="mT-30">

                                <div class="row">
                                    <div class="col-md-12 mb-5">
                                        <label for="subject">{{__('Subject')}}</label>
                                        <input type="text" name="subject" placeholder="{{__('subject')}}" class="form-control" required style="text-transform: none;">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-5">
                                        <label for="subject">{{__('title')}}</label>
                                        <input type="text" name="title" placeholder="{{__('title')}}" class="form-control" required style="text-transform: none;">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-5">
                                        <label for="subject">{{__('notification content')}}</label>
                                        <textarea name="notification_content" placeholder="{{__('notification content')}}" class="form-control" required style="text-transform: none;"></textarea>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12 mb-5">
                                        <label for="subject">{{__('mail content')}}</label>
                                        <textarea name="mail_content" placeholder="{{__('notification content')}}" class="form-control textarea_editor" required style="text-transform: none;"></textarea>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary" type="submit">{{__('Add notification template')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
