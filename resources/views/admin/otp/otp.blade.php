@extends('admin.admin_app')

@section('content')
    <style type="text/css">
        .iframe-container {
            overflow: hidden;
            padding-top: 56.25% !important;
            position: relative;
        }

        .iframe-container iframe {
            border: 0;
            height: 100%;
            left: 0;
            position: absolute;
            top: 0;
            width: 100%;
        }
    </style>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <div class="row">
                                <div class="col-sm-6">
                                    <a href="{{ URL::to('admin/pages') }}">
                                        <h4 class="header-title m-t-0 m-b-30 text-primary pull-left"
                                            style="font-size: 20px;"><i class="fa fa-arrow-left"></i>
                                            {{ trans('words.back') }}</h4>
                                    </a>
                                </div>
                                @if (isset($page_info->id))
                                    <div class="col-sm-6">
                                        <a href="{{ URL::to('page/' . $page_info->page_slug) }}" target="_blank">
                                            <h4 class="header-title m-t-0 m-b-30 text-primary pull-right"
                                                style="font-size: 20px;">{{ trans('words.preview') }} <i
                                                    class="fa fa-eye"></i></h4>
                                        </a>
                                    </div>
                                @endif
                            </div>



                            <form action="{{ url('api/v1/otp_send') }}" method="POST">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">{{ trans('Mobile No') }}*</label>
                                    <div class="col-sm-8">
                                        <input type="number" name="mob_no"
                                            value="{{ isset($otp->mob_no) ? stripslashes($otp->mob_no) : null }}"
                                            class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">{{ trans('Message') }}*</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="message"
                                            value="{{ isset($otp->message) ? stripslashes($otp->message) : null }}"
                                            class="form-control">
                                    </div>
                                </div>



                                <div class="form-group">
                                    <div class="offset-sm-3 col-sm-9 pl-1">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            {{ trans('Send') }}</button>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('admin.copyright')

    </div>

    <script type="text/javascript">
        @if (Session::has('flash_message'))

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
                /*didOpen: (toast) => {
                  toast.addEventListener('mouseenter', Swal.stopTimer)
                  toast.addEventListener('mouseleave', Swal.resumeTimer)
                }*/
            })

            Toast.fire({
                icon: 'success',
                title: '{{ Session::get('flash_message') }}'
            })
        @endif

        @if (count($errors) > 0)

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '<p>@foreach ($errors->all() as $error) {{ $error }}<br/> @endforeach</p>',
                showConfirmButton: true,
                confirmButtonColor: '#10c469',
                background: "#1a2234",
                color: "#fff"
            })
        @endif
    </script>
@endsection
