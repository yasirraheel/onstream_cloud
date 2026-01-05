@extends('site_app')

@section('head_title', 'Exclusive Offers - ' . getcong('site_name'))

@section('head_url', Request::url())

@section('content')

<div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Exclusive Offers</h1>
            </div>
        </div>
    </div>
</div>

<div class="main-wrap">
    <div class="section section-padding">
        <div class="container">
            
            <style>
                .sponsored-deal-card {
                    background: linear-gradient(135deg, #1e272e 0%, #2c3e50 100%);
                    border: 1px solid #34495e;
                    border-radius: 12px;
                    padding: 15px;
                    display: flex;
                    gap: 15px;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                    cursor: pointer;
                    height: 100%;
                    min-height: 160px;
                    margin-bottom: 30px;
                }

                .sponsored-deal-card::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
                    transition: left 0.5s;
                }

                .sponsored-deal-card:hover::before {
                    left: 100%;
                }

                .sponsored-deal-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 8px 25px rgba(255, 133, 8, 0.3);
                    border-color: #ff8508;
                }

                .deal-icon {
                    width: 100px;
                    height: 130px;
                    border-radius: 10px;
                    object-fit: cover;
                    border: 2px solid #445566;
                    flex-shrink: 0;
                    transition: transform 0.3s ease;
                }

                .sponsored-deal-card:hover .deal-icon {
                    transform: scale(1.05) rotate(2deg);
                }

                .deal-content {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    min-width: 0;
                }

                .deal-header {
                    display: flex;
                    align-items: start;
                    gap: 10px;
                }

                .deal-title {
                    font-size: 14px;
                    font-weight: 700;
                    color: #fff;
                    line-height: 1.3;
                    margin: 0 0 8px 0;
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    flex: 1;
                }

                .deal-hot-badge {
                    background: linear-gradient(135deg, #e74c3c, #c0392b);
                    color: #fff;
                    font-size: 10px;
                    font-weight: 700;
                    padding: 4px 8px;
                    border-radius: 5px;
                    white-space: nowrap;
                    animation: pulse 2s infinite;
                    box-shadow: 0 0 15px rgba(231, 76, 60, 0.5);
                }

                .deal-flash-badge {
                    background: linear-gradient(135deg, #f39c12, #e67e22);
                    animation: flash 1.5s infinite;
                    box-shadow: 0 0 15px rgba(243, 156, 18, 0.5);
                }

                @keyframes pulse {
                    0%, 100% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                }

                @keyframes flash {
                    0%, 50%, 100% { opacity: 1; }
                    25%, 75% { opacity: 0.7; }
                }

                .deal-price-section {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 8px;
                }

                .deal-price {
                    font-size: 24px;
                    font-weight: 900;
                    color: #1abc9c;
                    text-shadow: 0 2px 10px rgba(26, 188, 156, 0.3);
                }

                .deal-discount {
                    background: #e74c3c;
                    color: #fff;
                    font-size: 11px;
                    font-weight: 700;
                    padding: 3px 6px;
                    border-radius: 4px;
                }

                .deal-meta {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                    margin-bottom: 8px;
                }

                .deal-badge {
                    background: rgba(52, 152, 219, 0.2);
                    border: 1px solid rgba(52, 152, 219, 0.4);
                    color: #5dade2;
                    font-size: 10px;
                    padding: 3px 8px;
                    border-radius: 5px;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    gap: 4px;
                }

                .deal-badge.green {
                    background: rgba(46, 204, 113, 0.2);
                    border-color: rgba(46, 204, 113, 0.4);
                    color: #58d68d;
                }

                .deal-footer {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .deal-seller {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-size: 11px;
                    color: #bdc3c7;
                }

                .deal-seller img {
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    border: 1px solid #445566;
                }

                .deal-rating {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    background: rgba(52, 152, 219, 0.15);
                    padding: 4px 10px;
                    border-radius: 15px;
                    font-size: 12px;
                    font-weight: 700;
                    color: #3498db;
                }

                .deal-cta {
                    position: absolute;
                    bottom: 12px;
                    right: 12px;
                    background: linear-gradient(135deg, #ff8508, #fd0575);
                    color: #fff;
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    box-shadow: 0 4px 15px rgba(255, 133, 8, 0.4);
                    transition: all 0.3s ease;
                }

                .sponsored-deal-card:hover .deal-cta {
                    transform: scale(1.1);
                    box-shadow: 0 6px 20px rgba(255, 133, 8, 0.6);
                }

                .deal-verified {
                    color: #1abc9c;
                    margin-left: 3px;
                }
            </style>

            <div class="row">
                @if(count($ads_products) > 0)
                    @foreach ($ads_products as $product)
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                            <a href="javascript:void(0);"
                               onclick="trackAdClick({{ $product['id'] }}, '{{ $product['url'] ?? '#' }}')"
                               style="text-decoration: none; display: block;">
                                <div class="sponsored-deal-card">
                                    <img src="{{ $product['thumbnail_url'] ?? '' }}"
                                         alt="{{ $product['title'] ?? 'Product' }}"
                                         class="deal-icon"
                                         onerror="this.src='{{ URL::asset('site_assets/images/video-placeholder.jpg') }}'">

                                    <div class="deal-content">
                                        <div>
                                            <div class="deal-header">
                                                <h4 class="deal-title">{{ $product['title'] ?? 'No Title' }}</h4>
                                                @if ($product['badges']['is_trending'] ?? false)
                                                    <span class="deal-hot-badge">
                                                        <i class="fa fa-fire"></i> HOT
                                                    </span>
                                                @elseif ($product['badges']['is_flash'] ?? false)
                                                    <span class="deal-hot-badge deal-flash-badge">
                                                        <i class="fa fa-bolt"></i> FLASH
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="deal-price-section">
                                                <span class="deal-price">
                                                    {{ $product['currency_symbol'] ?? '$' }}{{ number_format($product['final_price'] ?? 0, 2) }}
                                                </span>
                                                @if(isset($product['discount']) && $product['discount'] > 0)
                                                    <span class="deal-discount">-{{ $product['discount'] }}%</span>
                                                @endif
                                            </div>

                                            <div class="deal-meta">
                                                @if(isset($product['badges']['duration']))
                                                    <span class="deal-badge">
                                                        <i class="fa fa-clock-o"></i> {{ $product['badges']['duration'] }}
                                                    </span>
                                                @endif
                                                @if(isset($product['badges']['delivery_speed']))
                                                    <span class="deal-badge green">
                                                        <i class="fa fa-truck"></i> {{ $product['badges']['delivery_speed'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="deal-footer">
                                            <div class="deal-seller">
                                                <img src="{{ $product['seller']['avatar'] ?? '' }}"
                                                     alt="{{ $product['seller']['username'] ?? 'Seller' }}"
                                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\'%3E%3Ccircle fill=\'%2334495e\' cx=\'10\' cy=\'10\' r=\'10\'/%3E%3C/svg%3E'">
                                                <span>{{ $product['seller']['username'] ?? 'Seller' }}</span>
                                                @if($product['seller']['is_verified'] ?? false)
                                                    <i class="fa fa-check-circle deal-verified"></i>
                                                @endif
                                            </div>

                                            @if(isset($product['rating']['percentage']) && $product['rating']['percentage'] > 0)
                                                <div class="deal-rating">
                                                    <i class="fa fa-thumbs-up"></i>
                                                    {{ number_format($product['rating']['percentage'], 0) }}%
                                                    <span style="color: #7f8c8d;">({{ $product['rating']['count'] ?? 0 }})</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <span class="deal-cta">
                                        Get Deal <i class="fa fa-arrow-right"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="col-md-12 text-center">
                        <h3>No offers available right now. Please check back later.</h3>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection