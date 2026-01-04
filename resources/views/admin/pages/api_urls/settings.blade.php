@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <div class="row">
                            <div class="col-sm-6">
                                <a href="{{ URL::to('admin/api_urls') }}">
                                    <h4 class="m-t-0 header-title text-primary"><i class="fa fa-arrow-left"></i> {{trans('words.back')}}</h4>
                                </a>
                            </div>
                        </div>

                        <h4 class="m-t-0 header-title">{{ $page_title }}</h4>
                        <p class="text-muted m-b-30 font-14">Configure API URL settings for fetching files</p>

                        <form action="{{ URL::to('admin/api_urls/settings') }}" method="POST">
                            @csrf

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Base URL <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="api_url_base_url" value="{{ isset($settings->api_url_base_url) ? stripslashes($settings->api_url_base_url) : '' }}" class="form-control" placeholder="Enter API Base URL (e.g. https://example.com/api/files)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">API Key <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="api_url_api_key" value="{{ isset($settings->api_url_api_key) ? stripslashes($settings->api_url_api_key) : '' }}" class="form-control" placeholder="Enter API Key">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-8 offset-sm-3">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        {{ trans('words.save_settings') }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
