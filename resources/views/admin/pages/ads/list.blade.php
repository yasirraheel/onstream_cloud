@extends('admin.admin_app')

@section('content')
    <style>
        .ads-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .ad-card {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }

        .ad-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .ad-card-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #f5f5f5;
        }

        .ad-card-body {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .ad-card-title {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin: 0 0 10px 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 40px;
        }

        .ad-price {
            font-size: 24px;
            font-weight: 700;
            color: #2ecc71;
            margin-bottom: 10px;
        }

        .ad-price-original {
            text-decoration: line-through;
            color: #999;
            font-size: 14px;
            margin-left: 8px;
        }

        .ad-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
        }

        .ad-badge {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
            background: #f0f0f0;
            color: #666;
            white-space: nowrap;
        }

        .ad-badge-trending {
            background: #ff6b6b;
            color: #fff;
        }

        .ad-badge-flash {
            background: #ffa502;
            color: #fff;
        }

        .ad-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .ad-rating-stars {
            color: #f39c12;
        }

        .ad-rating-count {
            color: #999;
        }

        .ad-seller {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-top: 10px;
            margin-top: auto;
            border-top: 1px solid #eee;
        }

        .ad-seller-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .ad-seller-name {
            font-size: 13px;
            color: #555;
            font-weight: 500;
        }

        .ad-seller-verified {
            color: #3498db;
            font-size: 14px;
        }

        .ad-category {
            font-size: 12px;
            color: #888;
            margin-bottom: 8px;
        }

        .ad-link {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 16px;
            background: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 13px;
            text-align: center;
            transition: background 0.2s;
        }

        .ad-link:hover {
            background: #2980b9;
            color: #fff;
            text-decoration: none;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .badge-info-list {
            font-size: 12px;
            color: #666;
            line-height: 1.8;
            margin: 10px 0;
        }

        .badge-info-list div {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .badge-info-list strong {
            color: #333;
            font-weight: 600;
        }
    </style>

    <div class="content-page">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card-box">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 class="m-t-0 m-b-30 header-title">{{ $page_title }}</h4>
                                </div>
                                <div class="col-md-4 text-right">
                                    <span class="badge badge-primary" style="font-size: 14px; padding: 8px 12px;">
                                        Total Products: {{ count($products) }}
                                    </span>
                                </div>
                            </div>

                            @if(Session::has('error_flash_message'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    {{ Session::get('error_flash_message') }}
                                </div>
                            @endif

                            @if(count($products) > 0)
                                <div class="ads-grid">
                                    @foreach($products as $product)
                                        <div class="ad-card">
                                            <img src="{{ $product['thumbnail_url'] ?? '' }}"
                                                 alt="{{ $product['title'] ?? 'Product' }}"
                                                 class="ad-card-image"
                                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\'%3E%3Crect fill=\'%23f0f0f0\' width=\'100\' height=\'100\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-family=\'Arial\' font-size=\'14\'%3ENo Image%3C/text%3E%3C/svg%3E'">

                                            <div class="ad-card-body">
                                                <div class="ad-category">
                                                    <i class="fa fa-folder"></i> {{ $product['category']['name'] ?? 'N/A' }}
                                                </div>

                                                <h3 class="ad-card-title" title="{{ $product['title'] ?? '' }}">
                                                    {{ $product['title'] ?? 'No Title' }}
                                                </h3>

                                                <div class="ad-price">
                                                    {{ $product['currency_symbol'] ?? '$' }}{{ number_format($product['final_price'] ?? 0, 2) }}
                                                    @if(isset($product['discount']) && $product['discount'] > 0)
                                                        <span class="ad-price-original">
                                                            {{ $product['currency_symbol'] ?? '$' }}{{ number_format($product['price'] ?? 0, 2) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                @if(isset($product['badges']))
                                                    <div class="ad-badges">
                                                        @if($product['badges']['is_trending'] ?? false)
                                                            <span class="ad-badge ad-badge-trending">
                                                                <i class="fa fa-fire"></i> Trending
                                                            </span>
                                                        @endif
                                                        @if($product['badges']['is_flash'] ?? false)
                                                            <span class="ad-badge ad-badge-flash">
                                                                <i class="fa fa-bolt"></i> Flash Deal
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(isset($product['rating']))
                                                    <div class="ad-rating">
                                                        <span class="ad-rating-stars">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= floor($product['rating']['average'] ?? 0))
                                                                    <i class="fa fa-star"></i>
                                                                @elseif($i - 0.5 <= ($product['rating']['average'] ?? 0))
                                                                    <i class="fa fa-star-half-o"></i>
                                                                @else
                                                                    <i class="fa fa-star-o"></i>
                                                                @endif
                                                            @endfor
                                                        </span>
                                                        <span class="ad-rating-count">
                                                            ({{ $product['rating']['count'] ?? 0 }})
                                                        </span>
                                                    </div>
                                                @endif

                                                @if(isset($product['badges']))
                                                    <div class="badge-info-list">
                                                        @if(isset($product['badges']['duration']))
                                                            <div>
                                                                <span><i class="fa fa-clock-o"></i> Duration:</span>
                                                                <strong>{{ $product['badges']['duration'] }}</strong>
                                                            </div>
                                                        @endif
                                                        @if(isset($product['badges']['delivery_speed']))
                                                            <div>
                                                                <span><i class="fa fa-truck"></i> Delivery:</span>
                                                                <strong>{{ $product['badges']['delivery_speed'] }}</strong>
                                                            </div>
                                                        @endif
                                                        @if(isset($product['badges']['devices']))
                                                            <div>
                                                                <span><i class="fa fa-desktop"></i> Devices:</span>
                                                                <strong>{{ $product['badges']['devices'] }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(isset($product['seller']))
                                                    <div class="ad-seller">
                                                        <img src="{{ $product['seller']['avatar'] ?? '' }}"
                                                             alt="{{ $product['seller']['username'] ?? 'Seller' }}"
                                                             class="ad-seller-avatar"
                                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'32\' height=\'32\'%3E%3Ccircle fill=\'%23ccc\' cx=\'16\' cy=\'16\' r=\'16\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23fff\' font-family=\'Arial\' font-size=\'14\'%3E{{ substr($product['seller']['username'] ?? 'U', 0, 1) }}%3C/text%3E%3C/svg%3E'">
                                                        <span class="ad-seller-name">{{ $product['seller']['username'] ?? 'Unknown' }}</span>
                                                        @if($product['seller']['is_verified'] ?? false)
                                                            <i class="fa fa-check-circle ad-seller-verified" title="Verified Seller"></i>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if(isset($product['url']))
                                                    <a href="{{ $product['url'] }}"
                                                       target="_blank"
                                                       class="ad-link">
                                                        <i class="fa fa-external-link"></i> View Product
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fa fa-shopping-bag"></i>
                                    <h4>No Products Available</h4>
                                    <p>Unable to fetch products from the API at this time.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.copyright')
    </div>
@endsection
