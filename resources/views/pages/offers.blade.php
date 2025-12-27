@extends('site_app')

@section('head_title', $page_title.' | '.getcong('site_name') )

@section('head_url', Request::url())

@section('content')

<style>
    .offers-page {
        padding: 60px 0 40px 0;
        min-height: 600px;
    }

    .offers-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .offers-header h1 {
        font-size: 36px;
        font-weight: 900;
        color: #fff;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .offers-header p {
        font-size: 16px;
        color: #bdc3c7;
    }

    .offers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .offer-card {
        background: linear-gradient(135deg, #1e272e 0%, #2c3e50 100%);
        border: 1px solid #34495e;
        border-radius: 15px;
        padding: 20px;
        display: flex;
        gap: 18px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        min-height: 180px;
    }

    .offer-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
        transition: left 0.6s;
    }

    .offer-card:hover::before {
        left: 100%;
    }

    .offer-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(255, 133, 8, 0.35);
        border-color: #ff8508;
    }

    .offer-icon {
        width: 120px;
        height: 140px;
        border-radius: 12px;
        object-fit: cover;
        border: 2px solid #445566;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }

    .offer-card:hover .offer-icon {
        transform: scale(1.08) rotate(3deg);
    }

    .offer-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
    }

    .offer-top {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }

    .offer-title {
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        line-height: 1.4;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }

    .offer-hot-badge {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 6px;
        white-space: nowrap;
        animation: pulse 2s infinite;
        box-shadow: 0 0 20px rgba(231, 76, 60, 0.6);
    }

    .offer-flash-badge {
        background: linear-gradient(135deg, #f39c12, #e67e22);
        animation: flash 1.5s infinite;
        box-shadow: 0 0 20px rgba(243, 156, 18, 0.6);
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    @keyframes flash {
        0%, 50%, 100% { opacity: 1; }
        25%, 75% { opacity: 0.6; }
    }

    .offer-price-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .offer-price {
        font-size: 28px;
        font-weight: 900;
        color: #1abc9c;
        text-shadow: 0 3px 15px rgba(26, 188, 156, 0.4);
    }

    .offer-discount {
        background: #e74c3c;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 5px;
    }

    .offer-info-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }

    .offer-badge {
        background: rgba(52, 152, 219, 0.2);
        border: 1px solid rgba(52, 152, 219, 0.4);
        color: #5dade2;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 6px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .offer-badge.green {
        background: rgba(46, 204, 113, 0.2);
        border-color: rgba(46, 204, 113, 0.4);
        color: #58d68d;
    }

    .offer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid #34495e;
    }

    .offer-seller {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #bdc3c7;
    }

    .offer-seller img {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 1px solid #445566;
    }

    .offer-verified {
        color: #1abc9c;
        margin-left: 4px;
    }

    .offer-rating {
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(52, 152, 219, 0.2);
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        color: #3498db;
    }

    .offer-cta {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: linear-gradient(135deg, #ff8508, #fd0575);
        color: #fff;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 5px 20px rgba(255, 133, 8, 0.5);
        transition: all 0.3s ease;
    }

    .offer-card:hover .offer-cta {
        transform: scale(1.15);
        box-shadow: 0 8px 25px rgba(255, 133, 8, 0.7);
    }

    .offer-click-count {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(52, 152, 219, 0.9);
        color: #fff;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .empty-offers {
        text-align: center;
        padding: 100px 20px;
        color: #7f8c8d;
    }

    .empty-offers i {
        font-size: 80px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .empty-offers h3 {
        color: #95a5a6;
        font-size: 24px;
    }

    @media (max-width: 768px) {
        .offers-page {
            padding: 30px 0 20px 0;
        }

        .offers-header {
            margin-bottom: 30px;
            padding: 0 10px;
        }

        .offers-header h1 {
            font-size: 22px;
            letter-spacing: 0;
        }

        .offers-header p {
            font-size: 13px;
        }

        .offers-grid {
            grid-template-columns: 1fr;
            gap: 15px;
            padding: 0 10px;
        }

        .offer-card {
            padding: 12px;
            gap: 12px;
            min-height: auto;
        }

        .offer-icon {
            width: 80px;
            height: 95px;
        }

        .offer-top {
            gap: 8px;
            margin-bottom: 8px;
        }

        .offer-title {
            font-size: 13px;
        }

        .offer-hot-badge {
            font-size: 9px;
            padding: 3px 6px;
        }

        .offer-price-row {
            gap: 8px;
            margin-bottom: 8px;
        }

        .offer-price {
            font-size: 22px;
        }

        .offer-discount {
            font-size: 10px;
            padding: 3px 6px;
        }

        .offer-info-badges {
            gap: 5px;
            margin-bottom: 8px;
        }

        .offer-badge {
            font-size: 9px;
            padding: 3px 7px;
        }

        .offer-bottom {
            padding-top: 8px;
        }

        .offer-seller {
            gap: 6px;
            font-size: 11px;
        }

        .offer-seller img {
            width: 20px;
            height: 20px;
        }

        .offer-rating {
            gap: 4px;
            padding: 4px 8px;
            font-size: 11px;
        }

        .offer-cta {
            bottom: 10px;
            right: 10px;
            padding: 8px 14px;
            font-size: 11px;
        }

        .offer-click-count {
            top: 10px;
            right: 10px;
            padding: 4px 8px;
            font-size: 10px;
        }

        .empty-offers {
            padding: 60px 20px;
        }

        .empty-offers i {
            font-size: 60px;
        }

        .empty-offers h3 {
            font-size: 20px;
        }
    }

    @media (max-width: 480px) {
        .offers-header h1 {
            font-size: 18px;
        }

        .offer-card {
            padding: 10px;
            gap: 10px;
        }

        .offer-icon {
            width: 70px;
            height: 85px;
        }

        .offer-title {
            font-size: 12px;
        }

        .offer-price {
            font-size: 20px;
        }

        .offer-cta {
            padding: 6px 12px;
            font-size: 10px;
        }
    }
</style>

<div class="offers-page">
    <div class="container-fluid">
        <div class="offers-header">
            <h1>
                <i class="fa fa-bullhorn" style="color: #ff8508; margin-right: 10px;"></i>
                {{ $page_title }}
            </h1>
            <p>Discover amazing deals and exclusive offers from top sellers</p>
        </div>

        @if(count($ads_products) > 0)
            <div class="offers-grid">
                @foreach($ads_products as $product)
                    <a href="javascript:void(0);"
                       onclick="trackAdClick({{ $product['id'] }}, '{{ $product['url'] ?? '#' }}')"
                       style="text-decoration: none; display: block;">
                        <div class="offer-card">
                            <img src="{{ $product['thumbnail_url'] ?? '' }}"
                                 alt="{{ $product['title'] ?? 'Product' }}"
                                 class="offer-icon"
                                 onerror="this.src='{{ URL::asset('site_assets/images/video-placeholder.jpg') }}'">

                            <div class="offer-details">
                                <div>
                                    <div class="offer-top">
                                        <h3 class="offer-title">{{ $product['title'] ?? 'No Title' }}</h3>
                                        @if($product['badges']['is_trending'] ?? false)
                                            <span class="offer-hot-badge">
                                                <i class="fa fa-fire"></i> HOT
                                            </span>
                                        @elseif($product['badges']['is_flash'] ?? false)
                                            <span class="offer-hot-badge offer-flash-badge">
                                                <i class="fa fa-bolt"></i> FLASH
                                            </span>
                                        @endif
                                    </div>

                                    <div class="offer-price-row">
                                        <span class="offer-price">
                                            {{ $product['currency_symbol'] ?? '$' }}{{ number_format($product['final_price'] ?? 0, 2) }}
                                        </span>
                                        @if(isset($product['discount']) && $product['discount'] > 0)
                                            <span class="offer-discount">-{{ $product['discount'] }}%</span>
                                        @endif
                                    </div>

                                    <div class="offer-info-badges">
                                        @if(isset($product['badges']['duration']))
                                            <span class="offer-badge">
                                                <i class="fa fa-clock-o"></i> {{ $product['badges']['duration'] }}
                                            </span>
                                        @endif
                                        @if(isset($product['badges']['delivery_speed']))
                                            <span class="offer-badge green">
                                                <i class="fa fa-truck"></i> {{ $product['badges']['delivery_speed'] }}
                                            </span>
                                        @endif
                                        @if(isset($product['badges']['devices']))
                                            <span class="offer-badge">
                                                <i class="fa fa-desktop"></i> {{ Str::limit($product['badges']['devices'], 15) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="offer-bottom">
                                    <div class="offer-seller">
                                        <img src="{{ $product['seller']['avatar'] ?? '' }}"
                                             alt="{{ $product['seller']['username'] ?? 'Seller' }}"
                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\'%3E%3Ccircle fill=\'%2334495e\' cx=\'12\' cy=\'12\' r=\'12\'/%3E%3C/svg%3E'">
                                        <span>{{ $product['seller']['username'] ?? 'Seller' }}</span>
                                        @if($product['seller']['is_verified'] ?? false)
                                            <i class="fa fa-check-circle offer-verified"></i>
                                        @endif
                                    </div>

                                    @if(isset($product['rating']['percentage']) && $product['rating']['percentage'] > 0)
                                        <div class="offer-rating">
                                            <i class="fa fa-thumbs-up"></i>
                                            {{ number_format($product['rating']['percentage'], 0) }}%
                                            <span style="color: #7f8c8d; font-weight: 400;">({{ $product['rating']['count'] ?? 0 }})</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <span class="offer-cta">
                                Get Deal <i class="fa fa-arrow-right"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="empty-offers">
                <i class="fa fa-shopping-bag"></i>
                <h3>No Offers Available</h3>
                <p>Check back later for exclusive deals!</p>
            </div>
        @endif
    </div>
</div>

@endsection
