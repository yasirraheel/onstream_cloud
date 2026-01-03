@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card-box">
                        <h4 class="m-t-0 header-title">{{ $page_title }}</h4>
                        <p class="text-muted m-b-30 font-14">Configure Google Drive API settings for fetching files</p>

                        <form action="{{ URL::to('admin/gd_urls/settings') }}" method="POST">
                            @csrf

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Google Drive API Key <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" name="gd_api_key" value="{{ isset($settings->gd_api_key) ? stripslashes($settings->gd_api_key) : '' }}" class="form-control" placeholder="Enter your Google Drive API Key">
                                    <small class="form-text text-muted">
                                        Get your API key from <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a>
                                    </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Google Drive Folder IDs <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <textarea name="gd_folder_ids" class="form-control" rows="4" placeholder="Enter folder IDs (comma-separated for multiple folders)">{{ isset($settings->gd_folder_ids) ? stripslashes($settings->gd_folder_ids) : '' }}</textarea>
                                    <small class="form-text text-muted">
                                        Example: 1J03UKvMPr2EEgAgkfSy9RIHjQblUwG10, 1ABC123xyz, 1XYZ789abc<br>
                                        Get folder ID from the URL of your Google Drive folder
                                    </small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label"></label>
                                <div class="col-sm-8">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">{{ trans('words.save_settings') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

<script type="text/javascript">
    @if(Session::has('flash_message'))
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

    @if (count($errors) > 0)
        Swal.fire({
            icon: 'error',
            title: '{{ trans('words.wrong') }}',
            text: '@foreach($errors->all() as $error) {{ $error }} @endforeach',
            confirmButtonColor: '#10c469',
            background:"#1a2234",
            color:"#fff"
        })
    @endif
</script>

@endsection
