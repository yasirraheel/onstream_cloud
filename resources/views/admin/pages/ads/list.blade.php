@extends('admin.admin_app')

@section('content')
    <style>
        .ads-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }

        .ad-card {
            background: #2c3e50;
            border: 1px solid #34495e;
            border-radius: 12px;
            padding: 16px;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ad-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
            border-color: #3498db;
        }

        .ad-card-header {
            display: flex;
            gap: 12px;
            position: relative;
        }

        .ad-hot-badge {
            position: absolute;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 8px rgba(231, 76, 60, 0.4);
            z-index: 1;
        }

        .ad-flash-badge {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            box-shadow: 0 2px 8px rgba(243, 156, 18, 0.4);
        }

        .ad-content-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-top: 26px;
        }

        .ad-content-right {
            flex-shrink: 0;
        }

        .ad-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            object-fit: cover;
            background: #34495e;
            border: 1px solid #445566;
        }

        .ad-title {
            font-size: 13px;
            font-weight: 600;
            color: #ecf0f1;
            line-height: 1.4;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .ad-description {
            font-size: 11px;
            color: #1abc9c;
            line-height: 1.4;
            display: flex;
            align-items: flex-start;
            gap: 4px;
        }

        .ad-description i {
            margin-top: 2px;
            flex-shrink: 0;
        }

        .ad-meta-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 4px;
        }

        .ad-meta-badge {
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .ad-badge-private {
            background: rgba(52, 152, 219, 0.2);
            color: #5dade2;
            border: 1px solid rgba(52, 152, 219, 0.3);
        }

        .ad-badge-plus {
            background: rgba(46, 204, 113, 0.2);
            color: #58d68d;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .ad-badge-platform {
            background: rgba(149, 165, 166, 0.2);
            color: #aab7b8;
            border: 1px solid rgba(149, 165, 166, 0.3);
        }

        .ad-seller-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .ad-seller-info {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ad-seller-avatar {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid #445566;
        }

        .ad-seller-name {
            font-size: 11px;
            color: #bdc3c7;
            font-weight: 500;
            text-transform: uppercase;
        }

        .ad-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #3498db;
            font-weight: 600;
        }

        .ad-rating i {
            font-size: 12px;
        }

        .ad-rating-count {
            color: #7f8c8d;
            font-weight: 400;
        }

        .ad-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 12px;
            border-top: 1px solid #34495e;
        }

        .ad-price {
            font-size: 20px;
            font-weight: 700;
            color: #1abc9c;
        }

        .ad-delivery {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            color: #95a5a6;
        }

        .ad-delivery i {
            font-size: 12px;
        }

        .ad-link {
            display: block;
            text-decoration: none;
            color: inherit;
        }

        .ad-link:hover {
            color: inherit;
            text-decoration: none;
        }

        .ad-category-tag {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 9px;
            color: #95a5a6;
            background: rgba(52, 73, 94, 0.8);
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 500;
            border: 1px solid #445566;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-state h4 {
            color: #95a5a6;
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
                                        <a href="{{ $product['url'] ?? '#' }}" target="_blank" class="ad-link">
                                            <div class="ad-card">
                                                @if($product['badges']['is_trending'] ?? false)
                                                    <div class="ad-hot-badge">
                                                        <i class="fa fa-fire"></i> HOT
                                                    </div>
                                                @elseif($product['badges']['is_flash'] ?? false)
                                                    <div class="ad-hot-badge ad-flash-badge">
                                                        <i class="fa fa-bolt"></i> FLASH
                                                    </div>
                                                @endif

                                                <div class="ad-card-header">
                                                    <div class="ad-content-left">
                                                        <h3 class="ad-title">{{ $product['title'] ?? 'No Title' }}</h3>

                                                        @if(isset($product['badges']['plan']))
                                                            <div class="ad-description">
                                                                <i class="fa fa-check-circle"></i>
                                                                <span>{{ $product['badges']['plan'] ?? '' }}: Access to the latest "fro...</span>
                                                            </div>
                                                        @endif

                                                        <div class="ad-meta-badges">
                                                            @if(isset($product['badges']['delivery_method']))
                                                                <span class="ad-meta-badge ad-badge-private">
                                                                    <i class="fa fa-user"></i> {{ $product['badges']['delivery_method'] }}
                                                                </span>
                                                            @endif
                                                            @if(isset($product['badges']['plan']))
                                                                <span class="ad-meta-badge ad-badge-plus">
                                                                    <i class="fa fa-check"></i> {{ $product['badges']['plan'] }}
                                                                </span>
                                                            @endif
                                                            @if(isset($product['badges']['devices']))
                                                                <span class="ad-meta-badge ad-badge-platform">
                                                                    <i class="fa fa-desktop"></i> {{ Str::limit($product['badges']['devices'], 12) }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="ad-content-right">
                                                        @if(isset($product['category']['name']))
                                                            <div class="ad-category-tag">
                                                                <i class="fa fa-clock-o"></i> {{ $product['badges']['duration'] ?? 'N/A' }}
                                                            </div>
                                                        @endif
                                                        <img src="{{ $product['thumbnail_url'] ?? '' }}"
                                                             alt="{{ $product['title'] ?? 'Product' }}"
                                                             class="ad-image"
                                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'80\' height=\'80\'%3E%3Crect fill=\'%2334495e\' width=\'80\' height=\'80\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%237f8c8d\' font-family=\'Arial\' font-size=\'10\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                                                    </div>
                                                </div>

                                                <div class="ad-seller-row">
                                                    <div class="ad-seller-info">
                                                        <img src="{{ $product['seller']['avatar'] ?? '' }}"
                                                             alt="{{ $product['seller']['username'] ?? 'Seller' }}"
                                                             class="ad-seller-avatar"
                                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\'%3E%3Ccircle fill=\'%2334495e\' cx=\'10\' cy=\'10\' r=\'10\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23ecf0f1\' font-family=\'Arial\' font-size=\'10\' font-weight=\'bold\'%3E{{ strtoupper(substr($product['seller']['username'] ?? 'U', 0, 1)) }}%3C/text%3E%3C/svg%3E'">
                                                        <span class="ad-seller-name">{{ strtoupper($product['seller']['username'] ?? 'Unknown') }}</span>
                                                    </div>

                                                    <div class="ad-rating">
                                                        <i class="fa fa-thumbs-up"></i>
                                                        @if(isset($product['rating']['percentage']))
                                                            {{ number_format($product['rating']['percentage'], 1) }}%
                                                        @else
                                                            100.0%
                                                        @endif
                                                        @if(isset($product['rating']['count']))
                                                            <span class="ad-rating-count">({{ $product['rating']['count'] }})</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="ad-footer">
                                                    <div class="ad-price">
                                                        {{ $product['currency_symbol'] ?? '$' }}{{ number_format($product['final_price'] ?? 0, 2) }}
                                                    </div>

                                                    <div class="ad-delivery">
                                                        <i class="fa fa-clock-o"></i>
                                                        {{ $product['badges']['delivery_speed'] ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
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
