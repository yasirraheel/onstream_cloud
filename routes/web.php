<?php

use App\Http\Controllers\PaystackController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {

    Route::get('/', 'IndexController@index');
    Route::patch('/transaction/update-status/{id}', [PaystackController::class, 'updateTransactionStatus'])->name('update.transaction.status');

    Route::get('login', [ 'as' => 'login', 'uses' => 'IndexController@index']);

    Route::post('login', 'IndexController@postLogin');
    Route::get('logout', 'IndexController@logout');

    Route::get('dashboard', 'DashboardController@index');
    Route::get('profile', 'AdminController@profile');
    Route::post('profile', 'AdminController@updateProfile');
    Route::get('verify_purchase', 'AdminController@verify_purchase');

    Route::get('settings', 'SettingsController@settings');

    Route::get('find_imdb_movie', 'ImportImdbController@find_imdb_movie');
    Route::get('find_imdb_show', 'ImportImdbShowController@find_imdb_show');
    Route::get('find_imdb_episode', 'ImportImdbShowController@find_imdb_episode');

    // API URLs Module
    Route::get('api_urls', 'ApiUrlController@index');
    Route::get('api_urls/fetch', 'ApiUrlController@fetch_urls');

    // GD URLs Module
    Route::get('gd_urls', 'GdUrlController@index');
    Route::get('gd_urls/fetch', 'GdUrlController@fetch_urls');
    Route::get('gd_urls/settings', 'GdUrlController@settings');
    Route::post('gd_urls/settings', 'GdUrlController@update_settings');

    Route::get('language', 'LanguageController@languag_list');
    Route::get('language/add_language', 'LanguageController@addLanguage');
    Route::get('language/edit_language/{id}', 'LanguageController@editLanguage');
    Route::post('language/add_edit_language', 'LanguageController@addnew');
    Route::get('language/delete/{id}', 'LanguageController@delete');

    Route::get('genres', 'GenresController@genres_list');
    Route::get('genres/add_genre', 'GenresController@addGenre');
    Route::get('genres/edit_genre/{id}', 'GenresController@editGenre');
    Route::post('genres/add_edit_genre', 'GenresController@addnew');
    Route::get('genres/delete/{id}', 'GenresController@delete');

    Route::get('movies', 'MoviesController@movies_list');
    Route::get('upcoming_movies', 'MoviesController@upcoming_movies_list');
    Route::get('movies/add_movie', 'MoviesController@addMovie');
    Route::get('movies/edit_movie/{id}', 'MoviesController@editMovie');
    Route::post('movies/add_edit_movie', 'MoviesController@addnew');
    Route::get('movies/delete/{id}', 'MoviesController@delete');


    Route::get('series', 'SeriesController@series_list');
    Route::get('series/add_series', 'SeriesController@addSeries');
    Route::get('series/edit_series/{id}', 'SeriesController@editSeries');
    Route::post('series/add_edit_series', 'SeriesController@addnew');
    Route::get('series/delete/{id}', 'SeriesController@delete');


    Route::get('season', 'SeasonController@season_list');
    Route::get('season/add_season', 'SeasonController@addSeason');
    Route::get('season/edit_season/{id}', 'SeasonController@editSeason');
    Route::post('season/add_edit_season', 'SeasonController@addnew');
    Route::get('season/delete/{id}', 'SeasonController@delete');

    Route::get('episodes', 'EpisodesController@episodes_list');
    Route::get('episodes/add_episode', 'EpisodesController@addEpisode');
    Route::get('episodes/edit_episode/{id}', 'EpisodesController@editEpisode');
    Route::post('episodes/add_edit_episode', 'EpisodesController@addnew');
    Route::get('episodes/duplicate_episode/{id}', 'EpisodesController@duplicateEpisode');
    Route::get('episodes/delete/{id}', 'EpisodesController@delete');

    Route::get('ajax_get_season/{id}', 'EpisodesController@ajax_get_season_list');

    Route::get('sports_category', 'SportsCategoryController@category_list');
    Route::get('sports_category/add_category', 'SportsCategoryController@addCategory');
    Route::get('sports_category/edit_category/{id}', 'SportsCategoryController@editCategory');
    Route::post('sports_category/add_edit_category', 'SportsCategoryController@addnew');
    Route::get('sports_category/delete/{id}', 'SportsCategoryController@delete');

    Route::get('sports', 'SportsController@sports_video_list');
    Route::get('sports/add_video', 'SportsController@addVideo');
    Route::get('sports/edit_video/{id}', 'SportsController@editVideo');
    Route::post('sports/add_edit_video', 'SportsController@addnew');
    Route::get('sports/delete/{id}', 'SportsController@delete');

    Route::get('tv_category', 'TvCategoryController@category_list');
    Route::get('tv_category/add_category', 'TvCategoryController@addCategory');
    Route::get('tv_category/edit_category/{id}', 'TvCategoryController@editCategory');
    Route::post('tv_category/add_edit_category', 'TvCategoryController@addnew');
    Route::get('tv_category/delete/{id}', 'TvCategoryController@delete');

    Route::get('live_tv', 'LiveTvController@live_tv_list');
    Route::get('live_tv/add_live_tv', 'LiveTvController@addTv');
    Route::get('live_tv/edit_live_tv/{id}', 'LiveTvController@editTv');
    Route::post('live_tv/add_edit_live_tv', 'LiveTvController@addnew');
    Route::get('live_tv/delete/{id}', 'LiveTvController@delete');


    Route::get('slider', 'SliderController@slider_list');
    Route::get('slider/add_slider', 'SliderController@addSlider');
    Route::get('slider/edit_slider/{id}', 'SliderController@editSlider');
    Route::post('slider/add_edit_slider', 'SliderController@addnew');
    Route::get('slider/delete/{id}', 'SliderController@delete');

    Route::get('home_sections', 'HomeSectionsController@list');
    Route::get('home_sections/add', 'HomeSectionsController@add');
    Route::get('home_sections/edit/{id}', 'HomeSectionsController@edit');
    Route::post('home_sections/add_edit', 'HomeSectionsController@addnew');
    Route::get('home_sections/delete/{id}', 'HomeSectionsController@delete');


    Route::get('users', 'UsersController@user_list');
    Route::get('users/add_user', 'UsersController@addUser');
    Route::get('users/edit_user/{id}', 'UsersController@editUser');
    Route::post('users/add_edit_user', 'UsersController@addnew');
    Route::get('users/delete/{id}', 'UsersController@delete');
    Route::get('users/history/{id}', 'UsersController@user_history');
    Route::get('users/export', 'UsersController@user_export');

    Route::get('sub_admin', 'UsersController@admin_user_list');
    Route::get('sub_admin/add_user', 'UsersController@admin_addUser');
    Route::get('sub_admin/edit_user/{id}', 'UsersController@admin_editUser');
    Route::post('sub_admin/add_edit_user', 'UsersController@admin_addnew');
    Route::get('sub_admin/delete/{id}', 'UsersController@admin_delete');

    Route::get('deleted_users', 'UsersController@deleted_user_list');

    Route::get('subscription_plan', 'SubscriptionPlanController@subscription_plan_list');
    Route::get('subscription_plan/add_plan', 'SubscriptionPlanController@addSubscriptionPlan');
    Route::get('subscription_plan/edit_plan/{id}', 'SubscriptionPlanController@editSubscriptionPlan');
    Route::post('subscription_plan/add_edit_plan', 'SubscriptionPlanController@addnew');
    Route::get('subscription_plan/delete/{id}', 'SubscriptionPlanController@delete');

    Route::get('otp_view', 'OtpController@index');
    // Route::post('otp_send', 'OtpController@sendOtp');


    Route::get('transactions', 'TransactionsController@transactions_list');
    Route::post('transactions/export', 'TransactionsController@transactions_export');

    // Search History
    Route::get('search_history', 'SearchHistoryController@index');
    Route::get('search_history/analytics', 'SearchHistoryController@analytics');
    Route::get('search_history/delete/{id}', 'SearchHistoryController@delete');
    Route::get('search_history/clear', 'SearchHistoryController@clear_all');

    // Movie Requests
    Route::get('movie_requests', 'MovieRequestController@index');
    Route::get('movie_requests/delete/{id}', 'MovieRequestController@delete');
    Route::get('movie_requests/status/{id}/{status}', 'MovieRequestController@status');

    Route::get('ads', 'AdManagementController@ads_list');

    Route::get('pages', 'PagesController@pages_list');
    Route::get('pages/add', 'PagesController@add');
    Route::get('pages/edit/{id}', 'PagesController@edit');
    Route::post('pages/add_edit', 'PagesController@addnew');
    Route::get('pages/delete/{id}', 'PagesController@delete');


    Route::get('general_settings', 'SettingsController@general_settings');
    Route::post('general_settings', 'SettingsController@update_general_settings');
    Route::get('email_settings', 'SettingsController@email_settings');
    Route::post('email_settings', 'SettingsController@update_email_settings');
    Route::get('test_smtp_settings', 'SettingsController@test_smtp_settings');
    Route::get('payment_settings', 'SettingsController@payment_settings');
    Route::post('payment_settings', 'SettingsController@update_payment_settings');
    Route::get('social_login_settings', 'SettingsController@social_login_settings');
    Route::post('social_login_settings', 'SettingsController@update_social_login_settings');

    Route::get('menu_settings', 'SettingsController@menu_settings');
    Route::post('menu_settings', 'SettingsController@update_menu_settings');

    Route::get('player_settings', 'SettingsPlayerController@player_settings');
    Route::post('player_settings', 'SettingsPlayerController@update_player_settings');

    Route::get('player_ad_settings', 'SettingsPlayerController@player_ad_settings');
    Route::post('player_ad_settings', 'SettingsPlayerController@update_player_ad_settings');

    Route::get('google_derive_player', 'SettingsPlayerController@google_derive_player');

    Route::get('recaptcha_settings', 'SettingsController@recaptcha_settings');
    Route::post('recaptcha_settings', 'SettingsController@update_recaptcha_settings');

    Route::get('web_ads_settings', 'SettingsController@web_ads_settings');
    Route::post('web_ads_settings', 'SettingsController@update_web_ads_settings');

    Route::get('site_maintenance', 'SettingsController@site_maintenance');
    Route::post('site_maintenance', 'SettingsController@update_site_maintenance');

    Route::post('site_maintenance_on_off', 'SettingsController@site_maintenance_on_off');

    Route::get('verify_purchase_app', 'SettingsAndroidAppController@verify_purchase_app');
    Route::post('verify_purchase_app', 'SettingsAndroidAppController@verify_purchase_app_update');

    Route::get('android_settings', 'SettingsAndroidAppController@android_settings');
    Route::post('android_settings', 'SettingsAndroidAppController@update_android_settings');

    Route::get('android_notification', 'SettingsAndroidAppController@android_notification');
    Route::post('android_notification', 'SettingsAndroidAppController@send_android_notification');


});

Route::group(['middleware' => ['web']], function() {

    Route::get('/', 'IndexController@index');

    Route::get('login', 'IndexController@login');
    Route::post('login', 'IndexController@postLogin');
    Route::get('signup', 'IndexController@signup');
    Route::post('signup', 'IndexController@postSignup');
    Route::get('logout', 'IndexController@logout');

    Route::get('password/email', 'Auth\PasswordController@getEmail');
    Route::post('password/email', 'Auth\PasswordController@postEmail');
    Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
    Route::post('password/reset', 'Auth\PasswordController@postReset');

    Route::get('dashboard', 'UsersController@dashboard');
    Route::get('profile', 'UsersController@profile');
    Route::post('profile', 'UsersController@updateProfile');

    Route::get('membership_plan', 'UsersController@membership_plan');
    Route::get('membership_plan/paypal/{id}', 'UsersController@membership_plan_paypal');
    Route::get('paypal_success/{id}', 'UsersController@paypal_success');
    Route::get('paypal_cancel/{id}', 'UsersController@paypal_cancel');

    Route::get('membership_plan/stripe/{id}', 'UsersController@membership_plan_stripe');
    Route::post('stripe/payment', 'UsersController@stripe_payment');

    Route::get('membership_plan/paystack/{id}', 'UsersController@membership_plan_paystack');
    Route::post('/pay', [PaystackController::class, 'redirectToGateway'])->name('pay');
    Route::get('/payment/callback', [PaystackController::class, 'handleGatewayCallback']);


    Route::get('membership_plan/razorpay/{id}', 'UsersController@membership_plan_razorpay');
    Route::post('razorpay/payment', 'UsersController@razorpay_payment');

    Route::get('movies', 'MoviesController@movies');
    Route::get('movies/details/{slug}/{id}', 'MoviesController@movies_details');
    Route::get('movies/watch/{slug}/{id}', 'MoviesController@movies_watch');

    Route::get('shows', 'ShowsController@shows');
    Route::get('shows/details/{slug}/{id}', 'ShowsController@show_details');
    Route::get('shows/{series_slug}/{slug}/{id}', 'ShowsController@shows_watch');

    Route::get('sports', 'SportsController@sports');
    Route::get('sports/details/{slug}/{id}', 'SportsController@sports_details');
    Route::get('sports/watch/{slug}/{id}', 'SportsController@sports_watch');

    Route::get('livetv', 'LiveTvController@live_tv_list');
    Route::get('livetv/details/{slug}/{id}', 'LiveTvController@live_tv_details');
    Route::get('livetv/watch/{slug}/{id}', 'LiveTvController@live_tv_watch');

    Route::get('search', 'IndexController@search');
    Route::get('search_elastic', 'IndexController@search_elastic');

    Route::get('sitemap.xml', 'IndexController@sitemap');
    Route::get('sitemap_misc.xml', 'IndexController@sitemap_misc');
    Route::get('sitemap_movies.xml', 'IndexController@sitemap_movies');
    Route::get('sitemap_show.xml', 'IndexController@sitemap_show');
    Route::get('sitemap_sports.xml', 'IndexController@sitemap_sports');
    Route::get('sitemap_livetv.xml', 'IndexController@sitemap_livetv');

    Route::get('page/{slug}', 'IndexController@pages');

    Route::get('movies_request', 'IndexController@movies_request');
    Route::post('movies_request', 'IndexController@post_movies_request');

    Route::get('google_login', 'IndexController@google_login');
    Route::get('facebook_login', 'IndexController@facebook_login');

    Route::get('watchlist', 'UsersController@watchlist');
    Route::get('watchlist/add', 'UsersController@watchlist_add');
    Route::get('watchlist/remove', 'UsersController@watchlist_remove');

    // Database Update Route
    Route::get('db_update', function() {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('search_history', 'country')) {
                \Illuminate\Support\Facades\Schema::table('search_history', function ($table) {
                    $table->string('country')->nullable()->after('ip_address');
                    $table->string('country_code')->nullable()->after('country');
                });
            }

            if (!\Illuminate\Support\Facades\Schema::hasTable('movie_requests')) {
                \Illuminate\Support\Facades\Schema::create('movie_requests', function ($table) {
                    $table->id();
                    $table->integer('user_id')->nullable();
                    $table->string('movie_name');
                    $table->string('language')->nullable();
                    $table->text('message')->nullable();
                    $table->string('email')->nullable();
                    $table->enum('status', ['Pending', 'Completed'])->default('Pending');
                    $table->timestamps();
                });
                return "Success: movie_requests table created.";
            }

            return "Info: Updates already applied.";
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    });

    // Catch-all route for home collections - MUST be last
    Route::get('{slug}/{id}', 'IndexController@home_collections');

});
