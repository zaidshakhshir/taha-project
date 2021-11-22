@extends('layouts.app',['activePage' => 'vendor'])

@section('title','Vendor Finance Details')

@section('content')

<section class="section">
    <div class="section-header">
        <h1>{{ $vendor->name }}&nbsp;{{ 'Finance details' }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ url('admin/home') }}">{{__('Dashboard')}}</a></div>
            <div class="breadcrumb-item active"><a href="{{ url('admin/vendor/'.$vendor->id) }}">{{ App\Models\Vendor::find($vendor->id)->name }}</a></div>
            <div class="breadcrumb-item">{{__('vendor finance details')}}</div>
        </div>
    </div>

    <div class="section-body">
        <h2 class="section-title">{{__('Vendor Management System')}}</h2>
        <p class="section-lead">{{__('Finance details')}}</p>
        <div class="card">
            <div class="card-header text-right">
                <h4>{{__('Last 7 days earning')}}</h4>
            </div>
            <div class="card-body">
                <table id="monthFinance" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{__('Date')}}</th>
                            <th>{{__('Order Amount')}}</th>
                            <th>{{__('Admin Commission')}}</th>
                            <th>{{__('vendor earning')}}</th>
                        </tr>
                    </thead>
                    <tbody class="month_finance">
                        @foreach ($orders as $order)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{ $order['date'] }}</td>
                            <td>{{ $currency }}{{ $order['amount'] }}</td>
                            <td>{{ $currency }}{{ $order['admin_commission'] }}</td>
                            <td>{{ $currency }}{{ $order['vendor_amount'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header text-right">
                <h4>{{__('Settlements')}}</h4>
                <span class="badge badge-success">{{__('admin gives to vendor')}}</span>&nbsp;
                <span class="badge badge-danger">{{__('vendor gives to admin')}}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('duration')}}</th>
                                <th>{{__('Order count')}}</th>
                                <th>{{__('Admin Earning')}}</th>
                                <th>{{__('Vendor earning')}}</th>
                                <th>{{__('Settles amount')}}</th>
                                <th>{{__('Settles')}}</th>
                                <th>{{__('view')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($settels as $settel)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td id="duration{{$loop->iteration}}">{{ $settel['duration'] }}</td>
                                    <td>{{ $settel['d_total_task'] }}</td>
                                    <td>{{ $currency }}{{ $settel['admin_earning'] }}</td>
                                    <td>{{ $currency }}{{ $settel['vendor_earning'] }}</td>
                                    <td>
                                        @if($settel['d_balance'] > 0)
                                            {{-- admin dese --}}
                                            <span class="badge badge-success">{{ $currency }}{{abs($settel['d_balance'])}}</span>
                                        @else
                                            {{-- admin lese --}}
                                            <span class="badge badge-danger">{{ $currency }}{{abs($settel['d_balance'])}}</span>
                                        @endif
                                    </td>
                                    <td>
                                       @if(abs($settel['d_balance']) > 0)
                                          <form action="{{ url('admin/make_payment') }}" method="post">
                                            @csrf
                                                <input type="hidden" name="duration" value="{{ $settel['duration'] }}">
                                                <input type="hidden" name="vendor" value="{{ $vendor->id }}">
                                                <button type="submit" class="btn btn-primary">{{__('Settle')}}</button>
                                          </form>
                                        @else
                                            <button type="submit" class="btn btn-primary" disabled>{{__('Settle')}}</button>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary" onclick="show_settle_details({{$loop->iteration}})" data-toggle="modal" data-target="#exampleModal">
                                            {{__('Show settlement details')}}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__('Show settlement details')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body details_body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
            </div>
        </div>
    </div>
</div>

@endsection
