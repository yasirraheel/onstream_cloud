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
                                        <i class="fa fa-whatsapp"></i> {{ $page_title }}
                                    </h4>
                                </div>
                            </div>

                            @if (!$isConfigured)
                                <div class="alert alert-warning">
                                    <strong>Warning!</strong> WhatsApp API is not configured. Please set WA_API_KEY and
                                    WA_API_URL in your .env file.
                                </div>
                            @endif

                            <form action="{{ route('admin.whatsapp.send') }}" method="POST">
                                @csrf

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Phone Number*</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="number" value="{{ old('number') }}" class="form-control"
                                            placeholder="e.g., 1234567890 or +923001234567" required>
                                        <small class="form-text text-muted">Enter phone number with country code (without
                                            spaces or special characters)</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Message*</label>
                                    <div class="col-sm-8">
                                        <textarea name="message" class="form-control" rows="5" placeholder="Enter your message here..." required>{{ old('message') }}</textarea>
                                        <small class="form-text text-muted">Maximum 1000 characters</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Account Name (Optional)</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="account_name" value="{{ old('account_name', 'OnStream') }}"
                                            class="form-control" placeholder="e.g., OnStream">
                                        <small class="form-text text-muted">Leave blank to use default account</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Session ID (Optional)</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="session_id" value="{{ old('session_id') }}"
                                            class="form-control" placeholder="Enter session ID if using session-based auth">
                                        <small class="form-text text-muted">Use either Account Name or Session ID, not
                                            both</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="offset-sm-3 col-sm-9 pl-1">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light"
                                            {{ !$isConfigured ? 'disabled' : '' }}>
                                            <i class="fa fa-paper-plane"></i> Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="text-muted">API Configuration</h5>
                                    <p><strong>API URL:</strong> {{ env('WA_API_URL', 'Not configured') }}</p>
                                    <p><strong>API Key:</strong> {{ env('WA_API_KEY') ? '****' . substr(env('WA_API_KEY'), -4) : 'Not configured' }}
                                    </p>
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
                timer: 3000,
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
