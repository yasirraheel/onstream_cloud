@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card-box table-responsive" style="background-color: #1a2234; color: #fff;">

            <div class="row">
              <div class="col-6">
                <h4 class="m-t-0 header-title">{{ $page_title }}</h4>
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

            {!! Form::open(array('url' => 'admin/announcements/add_edit','class'=>'form-horizontal','name'=>'announcement_form','id'=>'announcement_form','role'=>'form','enctype' => 'multipart/form-data')) !!}

            <input type="hidden" name="id" value="{{ isset($announcement->id) ? $announcement->id : null }}">

            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Title *</label>
              <div class="col-sm-8">
                <input type="text" name="title" value="{{ isset($announcement->title) ? $announcement->title : old('title') }}" class="form-control" required>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Message *</label>
              <div class="col-sm-8">
                <textarea name="message" class="form-control" rows="6" required>{{ isset($announcement->message) ? $announcement->message : old('message') }}</textarea>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Status</label>
              <div class="col-sm-8">
                <div class="radio radio-success form-check-inline pl-2" style="margin-top: 8px;">
                  <input type="radio" id="status_active" value="1" name="is_active" @if(isset($announcement->is_active) && $announcement->is_active==1) checked @endif {{ isset($announcement->id) ? '' : 'checked' }}>
                  <label for="status_active"> Active </label>
                </div>
                <div class="radio form-check-inline" style="margin-top: 8px;">
                  <input type="radio" id="status_inactive" value="0" name="is_active" @if(isset($announcement->is_active) && $announcement->is_active==0) checked @endif>
                  <label for="status_inactive"> Inactive </label>
                </div>
              </div>
            </div>

            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Show as Popup</label>
              <div class="col-sm-8">
                <div class="radio radio-info form-check-inline pl-2" style="margin-top: 8px;">
                  <input type="radio" id="popup_yes" value="1" name="show_as_popup" @if(isset($announcement->show_as_popup) && $announcement->show_as_popup==1) checked @endif {{ isset($announcement->id) ? '' : 'checked' }}>
                  <label for="popup_yes"> Yes </label>
                </div>
                <div class="radio form-check-inline" style="margin-top: 8px;">
                  <input type="radio" id="popup_no" value="0" name="show_as_popup" @if(isset($announcement->show_as_popup) && $announcement->show_as_popup==0) checked @endif>
                  <label for="popup_no"> No </label>
                </div>
              </div>
            </div>

            @if(isset($announcement->view_count))
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Total Views</label>
              <div class="col-sm-8">
                <p class="form-control-static">{{ number_format($announcement->view_count) }}</p>
              </div>
            </div>
            @endif

            <div class="form-group">
              <div class="offset-sm-3 col-sm-9">
                <button type="submit" class="btn btn-success waves-effect waves-light"> {{ isset($announcement->id) ? 'Update' : 'Add' }} </button>
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
