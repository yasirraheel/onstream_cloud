@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card-box table-responsive">

                            <div class="row">
                                <div class="col-sm-3">
                                    <select class="form-control" name="gateway_select" id="gateway_select">
                                        <option value="">{{ trans('words.filter_by_gateway') }}</option>

                                        @foreach ($gateway_list as $gateway_data)
                                            <?php $gateway_name = $gateway_data->gateway_name; ?>
                                            <option value="?gateway={{ $gateway_name }}"
                                                @if (isset($_GET['gateway']) && $_GET['gateway'] == $gateway_name) selected @endif>
                                                {{ $gateway_data->gateway_name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::open([
                                        'url' => 'admin/transactions',
                                        'class' => 'app-search',
                                        'id' => 'search',
                                        'role' => 'form',
                                        'method' => 'get',
                                    ]) !!}
                                    <input type="text" name="s"
                                        placeholder="{{ trans('words.search_by_payment_id_email') }}" class="form-control">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                    {!! Form::close() !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::open([
                                        'url' => 'admin/transactions',
                                        'class' => 'app-search',
                                        'id' => 'search',
                                        'role' => 'form',
                                        'method' => 'get',
                                    ]) !!}
                                    <input type="text" name="date" placeholder="mm/dd/yyyy" class="form-control"
                                        id="datepicker-autoclose" autocomplete="off">
                                    <button type="submit"><i class="fa fa-search"></i></button>
                                    {!! Form::close() !!}
                                </div>


                                <div class="col-md-3">
                                    <!-- <a href="{{ URL::to('admin/transactions/export') }}" class="btn btn-info btn-md waves-effect waves-light m-b-20 pull-right" data-toggle="tooltip" title="{{ trans('words.export_transactions') }}"><i class="fa fa-file-excel-o"></i> {{ trans('words.export_transactions') }}</a> -->

                                    <a href="#" data-toggle="modal" data-target="#export_model"
                                        title="{{ trans('words.export_transactions') }}"
                                        class="btn btn-info btn-md waves-effect waves-light m-b-20 mt-2 pull-right"><i
                                            class="fa fa-file-excel-o"></i> {{ trans('words.export_transactions') }}</a>

                                    <div id="export_model" class="modal fade" tabindex="-1" role="dialog"
                                        aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel">Export Transactions</h4>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-hidden="true">×</button>
                                                </div>
                                                <div class="modal-body">

                                                    @if (Auth::User()->usertype != 'Admin')
                                                        <p
                                                            style="text-align: center;font-size: 16px;font-weight: 500;color: red;">
                                                            Access denied!</p>
                                                    @else
                                                        {!! Form::open([
                                                            'url' => ['admin/transactions/export'],
                                                            'class' => 'form-horizontal',
                                                            'name' => 'category_form',
                                                            'id' => 'category_form',
                                                            'role' => 'form',
                                                            'enctype' => 'multipart/form-data',
                                                        ]) !!}


                                                        <div class="form-group row">
                                                            <label
                                                                class="col-sm-3 col-form-label">{{ trans('words.start_date') }}</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="start_date"
                                                                    placeholder="{{ trans('words.start_date') }}"
                                                                    class="form-control datepicker_trans"
                                                                    id="datepicker_trans1" autocomplete="off" required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row mb-0">
                                                            <label
                                                                class="col-sm-3 col-form-label">{{ trans('words.end_date') }}</label>
                                                            <div class="col-sm-9">
                                                                <input type="text" name="end_date"
                                                                    placeholder="{{ trans('words.end_date') }}"
                                                                    class="form-control datepicker_trans"
                                                                    id="datepicker_trans2" autocomplete="off" required>
                                                            </div>
                                                        </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                        Submit</button>
                                                </div>
                                                {!! Form::close() !!}
                                                @endif

                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div>
                                </div>

                            </div>

                            @if (Session::has('flash_message'))
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    {{ Session::get('flash_message') }}
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('words.name') }}</th>
                                            <th>{{ trans('words.email') }}</th>
                                            <th>{{ trans('words.plan') }}</th>
                                            <th>{{ trans('words.amount') }}</th>
                                            <th>{{ trans('words.payment_gateway') }}</th>
                                            <th>{{ trans('words.payment_id') }}</th>
                                            <th>Payment Proof</th>
                                            <th>{{ trans('words.payment_date') }}</th>
                                            <th>{{ trans('Payment Status') }}</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transactions_list as $i => $transaction_data)
                                            <tr>
                                                <td><a href="{{ url('admin/users/history/' . $transaction_data->user_id) }}"
                                                        data-toggle="tooltip"
                                                        title="User History">{{ \App\User::getUserFullname($transaction_data->user_id) }}</a>
                                                </td>
                                                <td>{{ $transaction_data->email }}</td>
                                                <td>{{ \App\SubscriptionPlan::getSubscriptionPlanInfo($transaction_data->plan_id, 'plan_name') }}
                                                </td>
                                                <td>{{ html_entity_decode(getCurrencySymbols(getcong('currency_code'))) }}{{ number_format($transaction_data->payment_amount, 2) }}
                                                    @if ($transaction_data->coupon_code != '')
                                                        <br />
                                                        (Coupon Used: {{ $transaction_data->coupon_code }})
                                                    @endif
                                                </td>
                                                <td>{{ $transaction_data->gateway }}</td>
                                                <td>{{ $transaction_data->payment_id }}</td>
                                                <td>
                                                    @if($transaction_data->payment_proof)
                                                        @php
                                                            $extension = pathinfo($transaction_data->payment_proof, PATHINFO_EXTENSION);
                                                            $isPdf = strtolower($extension) === 'pdf';
                                                        @endphp
                                                        @if($isPdf)
                                                            <a href="{{ URL::asset('upload/payment_proofs/'.$transaction_data->payment_proof) }}" target="_blank" class="btn btn-sm btn-primary">
                                                                <i class="fa fa-file-pdf-o"></i> View PDF
                                                            </a>
                                                        @else
                                                            <a href="{{ URL::asset('upload/payment_proofs/'.$transaction_data->payment_proof) }}" target="_blank" data-toggle="tooltip" title="Click to view">
                                                                <img src="{{ URL::asset('upload/payment_proofs/'.$transaction_data->payment_proof) }}" alt="Payment Proof" style="max-width: 60px; max-height: 60px; border-radius: 5px; border: 2px solid #ddd;">
                                                            </a>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">No proof</span>
                                                    @endif
                                                </td>
                                                <td>{{ date('M d Y h:i A', $transaction_data->date) }}</td>
                                                <td class="text-center">
                                                    <form action="{{ route('update.transaction.status', $transaction_data->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="dropdown">
                                                            <select
                                                                name="payment_status"
                                                                class="form-select {{ $transaction_data->payment_status == 1 ? 'bg-success text-white' : ($transaction_data->payment_status == 3 ? 'bg-warning text-dark' : 'bg-danger text-white') }}"
                                                                onchange="this.form.submit()"
                                                                style="width: auto; padding: 5px; font-weight: bold; text-align: center; border: none;">
                                                                <option value="1" class="bg-success text-white"
                                                                    {{ $transaction_data->payment_status == 1 ? 'selected' : '' }}>
                                                                    {{ trans('words.paid') }}
                                                                </option>
                                                                <option value="0" class="bg-danger text-white"
                                                                    {{ $transaction_data->payment_status == 0 ? 'selected' : '' }}>
                                                                    {{ trans('words.pending') }}
                                                                </option>
                                                                <option value="3" class="bg-warning text-dark"
                                                                    {{ $transaction_data->payment_status == 3 ? 'selected' : '' }}>
                                                                    {{ trans('words.rejected') }}
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </form>
                                                </td>



                                            </tr>
                                        @endforeach



                                    </tbody>
                                </table>
                            </div>
                            <nav class="paging_simple_numbers">
                                @include('admin.pagination', ['paginator' => $transactions_list])
                            </nav>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.copyright')
    </div>
@endsection
