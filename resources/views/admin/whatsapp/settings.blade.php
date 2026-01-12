@extends('admin.admin_app')

@section('content')
    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-box">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h4 class="header-title m-t-0 m-b-30 text-primary" style="font-size: 20px;">
                                        <i class="fa fa-cog"></i> {{ $page_title }}
                                    </h4>
                                </div>
                            </div>

                            <form action="{{ route('admin.whatsapp.settings.update') }}" method="POST">
                                @csrf

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">WhatsApp API URL*</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="api_url" value="{{ old('api_url', $settings['api_url']) }}"
                                            class="form-control" required
                                            placeholder="https://wa-server.shahabtech.com/api/v1/send-message">
                                        <small class="form-text text-muted">Your WhatsApp server API endpoint</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">API Key*</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="api_key" value="{{ old('api_key', $settings['api_key']) }}"
                                            class="form-control" required placeholder="Enter your API key">
                                        <small class="form-text text-muted">API key for authenticating with WhatsApp
                                            server</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Default Account Name*</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="account_name"
                                            value="{{ old('account_name', $settings['account_name']) }}" class="form-control"
                                            required placeholder="e.g., OnStream">
                                        <small class="form-text text-muted">Default account name to use when sending messages
                                            (can be overridden per message)</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="offset-sm-3 col-sm-9 pl-1">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="fa fa-save"></i> Save Settings
                                        </button>
                                        <a href="{{ URL::to('admin/whatsapp') }}" class="btn btn-secondary waves-effect">
                                            <i class="fa fa-arrow-left"></i> Back to Send Message
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="text-muted">Important Notes</h5>
                                    <ul>
                                        <li>After updating settings, clear Laravel cache: <code>php artisan config:clear</code>
                                        </li>
                                        <li>API Key is required to authenticate with your WhatsApp server</li>
                                        <li>Account Name should match an account configured on your WhatsApp server</li>
                                        <li>You can override the default account name when sending individual messages</li>
                                    </ul>
                                </div>
                            </div>

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
                timer: 5000,
                timerProgressBar: false,
            })

            Toast.fire({
                icon: 'success',
                title: '{{ Session::get('flash_message') }}'
            })
        @endif

        @if (Session::has('error_flash_message'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ Session::get('error_flash_message') }}',
                showConfirmButton: true,
                confirmButtonColor: '#10c469',
                background: "#1a2234",
                color: "#fff"
            })
        @endif

        @if (count($errors) > 0)
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: '<p>@foreach ($errors->all() as $error) {{ $error }}<br/> @endforeach</p>',
                showConfirmButton: true,
                confirmButtonColor: '#10c469',
                background: "#1a2234",
                color: "#fff"
            })
        @endif
    </script>
@endsection
