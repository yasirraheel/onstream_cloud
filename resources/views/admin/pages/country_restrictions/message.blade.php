@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-box">

            <div class="row">
              <div class="col-sm-12">
                  <h4 class="header-title m-t-0">{{ $page_title }}</h4>
                  <p class="text-muted">Customize the message shown to users from blocked countries</p>
              </div>
            </div>

            @if(Session::has('flash_message'))
              <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                {{ Session::get('flash_message') }}
              </div>
            @endif

            <hr>

            {!! Form::open(array('url' => 'admin/country-restrictions/message/update','class'=>'form-horizontal','name'=>'message_form','id'=>'message_form','role'=>'form')) !!}

            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Restriction Message</label>
              <div class="col-sm-9">
                <textarea name="country_restriction_message" id="country_restriction_message" class="form-control">{{ $message }}</textarea>
                <small class="form-text text-muted">This message will be displayed to users from blocked countries when they try to watch content</small>
              </div>
            </div>

            <div class="form-group row">
              <div class="col-sm-9 offset-sm-3">
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Message</button>
              </div>
            </div>

            {!! Form::close() !!}

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section("scripts")
<script src="{{ URL::asset('admin_assets/plugins/ckeditor/ckeditor.js') }}"></script>
<script>
  CKEDITOR.replace('country_restriction_message', {
    height: 400,
    removePlugins: 'image',
    allowedContent: true
  });
</script>
@endsection
