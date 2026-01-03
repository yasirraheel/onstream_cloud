@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card-box table-responsive">

            <div class="row">
              <div class="col-sm-6">
                  <h4 class="header-title m-t-0">{{ $page_title }}</h4>
                  <p class="text-muted">Select countries to block from accessing your website</p>
              </div>
              <div class="col-sm-6">
                  <div class="text-right">
                    <a href="{{ URL::to('admin/country-restrictions/message') }}" class="btn btn-info btn-sm" style="margin-right: 10px;"><i class="fa fa-edit"></i> Edit Restriction Message</a>
                    <span class="badge badge-danger" style="font-size: 14px; padding: 8px 12px;">Blocked: {{ $blocked_count }}</span>
                    <span class="badge badge-success" style="font-size: 14px; padding: 8px 12px; margin-left: 10px;">Allowed: {{ $total_count - $blocked_count }}</span>
                  </div>
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

            {!! Form::open(array('url' => 'admin/country-restrictions/update','class'=>'form-horizontal','name'=>'country_form','id'=>'country_form','role'=>'form')) !!}

            <div class="row mb-3">
              <div class="col-md-6">
                <div class="input-group">
                  <input type="text" class="form-control" id="searchCountry" placeholder="Search countries..." onkeyup="filterCountries()">
                  <span class="input-group-btn">
                    <button class="btn btn-secondary" type="button"><i class="fa fa-search"></i></button>
                  </span>
                </div>
              </div>
              <div class="col-md-6 text-right">
                <button type="button" class="btn btn-danger btn-sm" onclick="selectAll()"><i class="fa fa-ban"></i> Block All</button>
                <button type="button" class="btn btn-success btn-sm" onclick="deselectAll()"><i class="fa fa-check"></i> Allow All</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Save Changes</button>
              </div>
            </div>

            <div class="row" id="countriesList">
              @foreach($countries as $country)
              <div class="col-md-3 col-sm-4 col-6 mb-3 country-item" data-country="{{ strtolower($country->country_name) }}">
                <div class="checkbox checkbox-danger">
                  <input type="checkbox" 
                         id="country_{{ $country->country_code }}" 
                         name="blocked_countries[]" 
                         value="{{ $country->country_code }}"
                         @if($country->is_blocked) checked @endif>
                  <label for="country_{{ $country->country_code }}">
                    {{ $country->country_name }} ({{ $country->country_code }})
                  </label>
                </div>
              </div>
              @endforeach
            </div>

            <hr>

            <div class="row">
              <div class="col-12">
                <button type="submit" class="btn btn-primary pull-right"><i class="fa fa-save"></i> Save Changes</button>
              </div>
            </div>

            {!! Form::close() !!}

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function selectAll() {
  $('.country-item:visible input[name="blocked_countries[]"]').prop('checked', true);
}

function deselectAll() {
  $('.country-item:visible input[name="blocked_countries[]"]').prop('checked', false);
}

function filterCountries() {
  var input = document.getElementById('searchCountry');
  var filter = input.value.toLowerCase();
  var items = document.getElementsByClassName('country-item');
  
  for (var i = 0; i < items.length; i++) {
    var countryName = items[i].getAttribute('data-country');
    if (countryName.indexOf(filter) > -1) {
      items[i].style.display = '';
    } else {
      items[i].style.display = 'none';
    }
  }
}
</script>

@endsection
