<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/graphql' => [[['_route' => 'api_graphql_entrypoint', '_controller' => 'api_platform.graphql.action.entrypoint', '_graphql' => true], null, null, null, false, false, null]],
        '/api/hub' => [[['_route' => 'mercure'], null, null, null, false, false, null]],
        '/_profiler' => [[['_route' => '_profiler_home', '_controller' => 'web_profiler.controller.profiler::homeAction'], null, null, null, true, false, null]],
        '/_profiler/search' => [[['_route' => '_profiler_search', '_controller' => 'web_profiler.controller.profiler::searchAction'], null, null, null, false, false, null]],
        '/_profiler/search_bar' => [[['_route' => '_profiler_search_bar', '_controller' => 'web_profiler.controller.profiler::searchBarAction'], null, null, null, false, false, null]],
        '/_profiler/phpinfo' => [[['_route' => '_profiler_phpinfo', '_controller' => 'web_profiler.controller.profiler::phpinfoAction'], null, null, null, false, false, null]],
        '/_profiler/xdebug' => [[['_route' => '_profiler_xdebug', '_controller' => 'web_profiler.controller.profiler::xdebugAction'], null, null, null, false, false, null]],
        '/_profiler/open' => [[['_route' => '_profiler_open_file', '_controller' => 'web_profiler.controller.profiler::openAction'], null, null, null, false, false, null]],
        '/ai-puzzle' => [[['_route' => 'ai_puzzle_index', '_controller' => 'App\\Controller\\AITravelPuzzleController::index'], null, null, null, false, false, null]],
        '/ai-puzzle/daily' => [[['_route' => 'ai_puzzle_daily', '_controller' => 'App\\Controller\\AITravelPuzzleController::daily'], null, null, null, false, false, null]],
        '/ai-puzzle/world-tour' => [[['_route' => 'ai_puzzle_world_tour', '_controller' => 'App\\Controller\\AITravelPuzzleController::worldTour'], null, null, null, false, false, null]],
        '/ai-puzzle/leaderboard' => [[['_route' => 'ai_puzzle_leaderboard', '_controller' => 'App\\Controller\\AITravelPuzzleController::leaderboard'], null, null, null, false, false, null]],
        '/ai-puzzle/api/destination' => [[['_route' => 'ai_puzzle_api_destination', '_controller' => 'App\\Controller\\AITravelPuzzleController::apiDestination'], null, null, null, false, false, null]],
        '/activites/decouverte-reelle' => [[['_route' => 'activity_discovery', '_controller' => 'App\\Controller\\ActivityController::discovery'], null, ['GET' => 0], null, false, false, null]],
        '/activites' => [[['_route' => 'activity_index', '_controller' => 'App\\Controller\\ActivityController::index'], null, ['GET' => 0], null, false, false, null]],
        '/activites/autour-de-moi' => [[['_route' => 'activity_nearby_map', '_controller' => 'App\\Controller\\ActivityController::nearbyMap'], null, ['GET' => 0], null, false, false, null]],
        '/activites/api/recommended' => [[['_route' => 'activity_recommended', '_controller' => 'App\\Controller\\ActivityController::getRecommended'], null, ['GET' => 0], null, false, false, null]],
        '/activites/api/trending' => [[['_route' => 'activity_trending', '_controller' => 'App\\Controller\\ActivityController::getTrending'], null, ['GET' => 0], null, false, false, null]],
        '/admin' => [
            [['_route' => 'admin_dashboard', '_controller' => 'App\\Controller\\AdminController::dashboard'], null, null, null, false, false, null],
            [['_route' => 'sonata_admin_redirect', '_controller' => ['Symfony\\Bundle\\FrameworkBundle\\Controller\\RedirectController', 'redirectAction'], 'route' => 'sonata_admin_dashboard', 'permanent' => true], null, null, null, true, false, null],
        ],
        '/admin/users' => [[['_route' => 'admin_users', '_controller' => 'App\\Controller\\AdminController::users'], null, null, null, false, false, null]],
        '/admin/hebergements' => [[['_route' => 'admin_hebergements', '_controller' => 'App\\Controller\\AdminController::hebergements'], null, null, null, false, false, null]],
        '/admin/hebergement/new' => [[['_route' => 'admin_hebergement_new', '_controller' => 'App\\Controller\\AdminController::hebergementNew'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/admin/hebergement/import-rapidapi' => [[['_route' => 'admin_hebergement_import_rapidapi', '_controller' => 'App\\Controller\\AdminController::importFromRapidApi'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/admin/hebergements/search-rapidapi' => [[['_route' => 'admin_hebergements_search_rapidapi', '_controller' => 'App\\Controller\\AdminController::searchRapidApi'], null, null, null, false, false, null]],
        '/admin/hebergements/test-api' => [[['_route' => 'admin_hebergements_test_api', '_controller' => 'App\\Controller\\AdminController::testApi'], null, null, null, false, false, null]],
        '/admin/reservations' => [[['_route' => 'admin_reservations', '_controller' => 'App\\Controller\\AdminController::reservations'], null, null, null, false, false, null]],
        '/admin/avis' => [[['_route' => 'admin_avis', '_controller' => 'App\\Controller\\AdminController::avis'], null, null, null, false, false, null]],
        '/admin/circuits' => [[['_route' => 'admin_circuits', '_controller' => 'App\\Controller\\AdminController::circuits'], null, null, null, false, false, null]],
        '/admin/circuit/new' => [[['_route' => 'admin_circuit_new', '_controller' => 'App\\Controller\\AdminController::circuitNew'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/admin/transports' => [[['_route' => 'admin_transports', '_controller' => 'App\\Controller\\AdminController::transports'], null, null, null, false, false, null]],
        '/admin/transport/new' => [[['_route' => 'admin_transport_new', '_controller' => 'App\\Controller\\AdminController::transportNew'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/admin/reservations-transports' => [[['_route' => 'admin_res_transports', '_controller' => 'App\\Controller\\AdminController::resTransports'], null, null, null, false, false, null]],
        '/admin/avis-transports' => [[['_route' => 'admin_avis_transports', '_controller' => 'App\\Controller\\AdminController::avisTransports'], null, null, null, false, false, null]],
        '/admin/avis-circuits' => [[['_route' => 'admin_avis_circuits', '_controller' => 'App\\Controller\\AdminController::avisCircuits'], null, null, null, false, false, null]],
        '/admin/reservations-circuits' => [[['_route' => 'admin_res_circuits', '_controller' => 'App\\Controller\\AdminController::resCircuits'], null, null, null, false, false, null]],
        '/admin/activites' => [[['_route' => 'admin_activities', '_controller' => 'App\\Controller\\AdminController::activities'], null, null, null, false, false, null]],
        '/admin/activite/new' => [[['_route' => 'admin_activity_new', '_controller' => 'App\\Controller\\AdminController::activityNew'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/admin/faq' => [[['_route' => 'admin_faq', '_controller' => 'App\\Controller\\AdminController::faq'], null, null, null, false, false, null]],
        '/admin/faq/new' => [[['_route' => 'admin_faq_new', '_controller' => 'App\\Controller\\AdminController::faqNew'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/admin/faq/spellcheck' => [[['_route' => 'admin_faq_spellcheck', '_controller' => 'App\\Controller\\AdminController::faqSpellCheck'], null, ['POST' => 0], null, false, false, null]],
        '/admin/faq/autocomplete' => [[['_route' => 'admin_faq_autocomplete', '_controller' => 'App\\Controller\\AdminController::faqAutocomplete'], null, null, null, false, false, null]],
        '/admin/faq/ai-suggest' => [[['_route' => 'admin_faq_ai_suggest', '_controller' => 'App\\Controller\\AdminController::faqAiSuggest'], null, null, null, false, false, null]],
        '/admin/faq/ai-generate' => [[['_route' => 'admin_faq_ai_generate', '_controller' => 'App\\Controller\\AdminController::faqAiGenerate'], null, null, null, false, false, null]],
        '/admin/faq/sync-embeddings' => [[['_route' => 'admin_faq_sync_embeddings', '_controller' => 'App\\Controller\\AdminController::faqSyncEmbeddings'], null, null, null, false, false, null]],
        '/admin/bookings' => [[['_route' => 'admin_bookings', '_controller' => 'App\\Controller\\AdminController::bookings'], null, null, null, false, false, null]],
        '/admin/reviews' => [[['_route' => 'admin_reviews', '_controller' => 'App\\Controller\\AdminController::reviews'], null, null, null, false, false, null]],
        '/admin/forum' => [[['_route' => 'admin_forum', '_controller' => 'App\\Controller\\AdminController::forum'], null, null, null, false, false, null]],
        '/admin/stats/revenus' => [[['_route' => 'admin_stats_revenus', '_controller' => 'App\\Controller\\AdminController::statsRevenus'], null, null, null, false, false, null]],
        '/admin/stats/villes' => [[['_route' => 'admin_stats_villes', '_controller' => 'App\\Controller\\AdminController::statsVilles'], null, null, null, false, false, null]],
        '/admin/export/pdf' => [[['_route' => 'admin_export_pdf', '_controller' => 'App\\Controller\\AdminController::exportPdf'], null, null, null, false, false, null]],
        '/admin/calendar' => [[['_route' => 'admin_calendar', '_controller' => 'App\\Controller\\AdminController::calendar'], null, null, null, false, false, null]],
        '/admin/calendar/events' => [[['_route' => 'admin_calendar_events', '_controller' => 'App\\Controller\\AdminController::calendarEvents'], null, null, null, false, false, null]],
        '/admin/calendar/new' => [[['_route' => 'admin_calendar_new', '_controller' => 'App\\Controller\\AdminController::calendarNew'], null, ['POST' => 0], null, false, false, null]],
        '/admin/ajax/geocode' => [[['_route' => 'admin_geocode', '_controller' => 'App\\Controller\\AdminController::geocode'], null, ['POST' => 0], null, false, false, null]],
        '/ajax/check-availability' => [[['_route' => 'ajax_check_availability', '_controller' => 'App\\Controller\\AjaxController::checkAvailability'], null, ['POST' => 0], null, false, false, null]],
        '/ajax/apply-promo' => [[['_route' => 'ajax_apply_promo', '_controller' => 'App\\Controller\\AjaxController::applyPromo'], null, ['POST' => 0], null, false, false, null]],
        '/api/activities/nearby' => [[['_route' => 'api_activities_nearby', '_controller' => 'App\\Controller\\Api\\ActivityApiController::nearby'], null, ['GET' => 0], null, false, false, null]],
        '/api/activities/search' => [[['_route' => 'api_activities_search', '_controller' => 'App\\Controller\\Api\\ActivityApiController::search'], null, ['GET' => 0], null, false, false, null]],
        '/api/activities/recommendations' => [[['_route' => 'api_activities_recommendations', '_controller' => 'App\\Controller\\Api\\ActivityApiController::recommendations'], null, ['GET' => 0], null, false, false, null]],
        '/api/activities/recommend-ai' => [[['_route' => 'api_activities_recommend_ai', '_controller' => 'App\\Controller\\Api\\ActivityApiController::recommendWithAi'], null, ['POST' => 0], null, false, false, null]],
        '/api/analytics/summary' => [[['_route' => 'api_analytics_summary', '_controller' => 'App\\Controller\\Api\\AnalyticsController::getSummary'], null, ['GET' => 0], null, false, false, null]],
        '/api/analytics/top-circuits' => [[['_route' => 'api_analytics_top_circuits', '_controller' => 'App\\Controller\\Api\\AnalyticsController::getTopCircuits'], null, ['GET' => 0], null, false, false, null]],
        '/api/analytics/conversion' => [[['_route' => 'api_analytics_conversion', '_controller' => 'App\\Controller\\Api\\AnalyticsController::getConversionRate'], null, ['GET' => 0], null, false, false, null]],
        '/api/analytics/destinations' => [[['_route' => 'api_analytics_destinations', '_controller' => 'App\\Controller\\Api\\AnalyticsController::getTrendingDestinations'], null, ['GET' => 0], null, false, false, null]],
        '/api/analytics/forecast' => [[['_route' => 'api_analytics_forecast', '_controller' => 'App\\Controller\\Api\\AnalyticsController::getForecast'], null, ['GET' => 0], null, false, false, null]],
        '/api/analytics/trend' => [[['_route' => 'api_analytics_trend', '_controller' => 'App\\Controller\\Api\\AnalyticsController::getMonthlyTrend'], null, ['GET' => 0], null, false, false, null]],
        '/api/analytics/all' => [[['_route' => 'api_analytics_all', '_controller' => 'App\\Controller\\Api\\AnalyticsController::getAll'], null, ['GET' => 0], null, false, false, null]],
        '/api/circuit/search' => [[['_route' => 'api_circuit_search', '_controller' => 'App\\Controller\\Api\\CircuitSearchController::search'], null, ['GET' => 0], null, false, false, null]],
        '/api/circuit/search/examples' => [[['_route' => 'api_circuit_search_examples', '_controller' => 'App\\Controller\\Api\\CircuitSearchController::getExamples'], null, ['GET' => 0], null, false, false, null]],
        '/api/forum/moderate' => [[['_route' => 'forum_api_moderate', '_controller' => 'App\\Controller\\Api\\ForumApiController::moderate'], null, ['POST' => 0], null, false, false, null]],
        '/api/forum/translate' => [[['_route' => 'forum_api_translate', '_controller' => 'App\\Controller\\Api\\ForumApiController::translate'], null, ['POST' => 0], null, false, false, null]],
        '/api/forum/detect-language' => [[['_route' => 'forum_api_detect', '_controller' => 'App\\Controller\\Api\\ForumApiController::detectLanguage'], null, ['POST' => 0], null, false, false, null]],
        '/api/forum/languages' => [[['_route' => 'forum_api_languages', '_controller' => 'App\\Controller\\Api\\ForumApiController::getLanguages'], null, ['GET' => 0], null, false, false, null]],
        '/api/forum/batch-moderate' => [[['_route' => 'forum_api_batch_moderate', '_controller' => 'App\\Controller\\Api\\ForumApiController::batchModerate'], null, ['POST' => 0], null, false, false, null]],
        '/api/gif/search' => [[['_route' => 'api_gif_search', '_controller' => 'App\\Controller\\Api\\GifController::search'], null, ['GET' => 0], null, false, false, null]],
        '/api/gif/trending' => [[['_route' => 'api_gif_trending', '_controller' => 'App\\Controller\\Api\\GifController::trending'], null, ['GET' => 0], null, false, false, null]],
        '/api/image/search' => [[['_route' => 'image_search', '_controller' => 'App\\Controller\\Api\\ImageSearchController::search'], null, ['POST' => 0], null, false, false, null]],
        '/api/share/message' => [[['_route' => 'api_share_message', '_controller' => 'App\\Controller\\Api\\ShareApiController::shareToMessage'], null, ['POST' => 0], null, false, false, null]],
        '/api/share/forum' => [[['_route' => 'api_share_forum', '_controller' => 'App\\Controller\\Api\\ShareApiController::shareToForum'], null, ['POST' => 0], null, false, false, null]],
        '/api/share/search-users' => [[['_route' => 'api_share_search_users', '_controller' => 'App\\Controller\\Api\\ShareApiController::searchUsers'], null, ['GET' => 0], null, false, false, null]],
        '/api/auth/facebook' => [
            [['_route' => 'api_facebook_login', '_controller' => 'App\\Controller\\Api\\SocialLoginController::facebookLogin'], null, ['POST' => 0], null, false, false, null],
            [['_route' => 'api_auth_facebook', '_controller' => 'App\\Controller\\SecurityController::apiAuthFacebook'], null, ['POST' => 0], null, false, false, null],
        ],
        '/api/auth/google' => [[['_route' => 'api_google_login', '_controller' => 'App\\Controller\\Api\\SocialLoginController::googleLogin'], null, ['POST' => 0], null, false, false, null]],
        '/api/story/create' => [[['_route' => 'api_story_create', '_controller' => 'App\\Controller\\Api\\StoryApiController::create'], null, ['POST' => 0], null, false, false, null]],
        '/api/story/feed' => [[['_route' => 'api_story_feed', '_controller' => 'App\\Controller\\Api\\StoryApiController::feed'], null, ['GET' => 0], null, false, false, null]],
        '/api/user/me' => [[['_route' => 'api_user_me', '_controller' => 'App\\Controller\\ApiController::getMe'], null, ['GET' => 0], null, false, false, null]],
        '/api/activities' => [[['_route' => 'api_activities', '_controller' => 'App\\Controller\\ApiController::activities'], null, ['GET' => 0], null, false, false, null]],
        '/api/hebergements' => [[['_route' => 'api_hebergements', '_controller' => 'App\\Controller\\ApiController::hebergements'], null, ['GET' => 0], null, false, false, null]],
        '/api/transports' => [[['_route' => 'api_transports', '_controller' => 'App\\Controller\\ApiController::transports'], null, ['GET' => 0], null, false, false, null]],
        '/api/search' => [[['_route' => 'api_search', '_controller' => 'App\\Controller\\ApiController::search'], null, ['GET' => 0], null, false, false, null]],
        '/api-explorer' => [[['_route' => 'api_explorer', '_controller' => 'App\\Controller\\ApiExplorerController::index'], null, null, null, false, false, null]],
        '/api-explorer/test' => [[['_route' => 'api_test', '_controller' => 'App\\Controller\\ApiExplorerController::test'], null, null, null, false, false, null]],
        '/api/calls/initiate' => [[['_route' => 'api_calls_initiate', '_controller' => 'App\\Controller\\CallController::initiateCall'], null, ['POST' => 0], null, false, false, null]],
        '/api/calls/accept' => [[['_route' => 'api_calls_accept', '_controller' => 'App\\Controller\\CallController::acceptCall'], null, ['POST' => 0], null, false, false, null]],
        '/api/calls/reject' => [[['_route' => 'api_calls_reject', '_controller' => 'App\\Controller\\CallController::rejectCall'], null, ['POST' => 0], null, false, false, null]],
        '/api/calls/end' => [[['_route' => 'api_calls_end', '_controller' => 'App\\Controller\\CallController::endCall'], null, ['POST' => 0], null, false, false, null]],
        '/api/calls/status' => [[['_route' => 'api_calls_status', '_controller' => 'App\\Controller\\CallController::getCallStatus'], null, ['GET' => 0], null, false, false, null]],
        '/chatbot' => [[['_route' => 'chatbot_index', '_controller' => 'App\\Controller\\ChatbotController::index'], null, null, null, false, false, null]],
        '/chatbot/send' => [[['_route' => 'chatbot_send', '_controller' => 'App\\Controller\\ChatbotController::sendMessage'], null, ['POST' => 0], null, false, false, null]],
        '/chatbot/clear' => [[['_route' => 'chatbot_clear', '_controller' => 'App\\Controller\\ChatbotController::clearHistory'], null, ['POST' => 0], null, false, false, null]],
        '/chatbot/quick-reply' => [[['_route' => 'chatbot_quick_reply', '_controller' => 'App\\Controller\\ChatbotController::quickReply'], null, ['POST' => 0], null, false, false, null]],
        '/chatbot/feedback' => [[['_route' => 'chatbot_feedback', '_controller' => 'App\\Controller\\ChatbotController::feedback'], null, ['POST' => 0], null, false, false, null]],
        '/chatbot/hotel-advice' => [[['_route' => 'chatbot_hotel_advice', '_controller' => 'App\\Controller\\ChatbotController::hotelAdvice'], null, ['POST' => 0], null, false, false, null]],
        '/chatbot/test-api' => [[['_route' => 'chatbot_test_api', '_controller' => 'App\\Controller\\ChatbotController::testApi'], null, null, null, false, false, null]],
        '/chatbot/compare-hebergements' => [[['_route' => 'chatbot_compare_hebergements', '_controller' => 'App\\Controller\\ChatbotController::compareHebergements'], null, ['POST' => 0], null, false, false, null]],
        '/circuits' => [[['_route' => 'circuit_index', '_controller' => 'App\\Controller\\CircuitController::index'], null, ['GET' => 0], null, false, false, null]],
        '/circuits/personnalise' => [[['_route' => 'circuit_ai_create', '_controller' => 'App\\Controller\\CircuitController::createAi'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/favoris' => [[['_route' => 'favoris_all', '_controller' => 'App\\Controller\\FavorisController::index'], null, null, null, false, false, null]],
        '/favoris/hebergements' => [[['_route' => 'favoris_hebergements', '_controller' => 'App\\Controller\\FavorisController::hebergements'], null, null, null, false, false, null]],
        '/favoris/circuits' => [[['_route' => 'favoris_circuits', '_controller' => 'App\\Controller\\FavorisController::circuits'], null, null, null, false, false, null]],
        '/favoris/activites' => [[['_route' => 'favoris_activities', '_controller' => 'App\\Controller\\FavorisController::activities'], null, null, null, false, false, null]],
        '/favoris/transport' => [[['_route' => 'favoris_transports', '_controller' => 'App\\Controller\\FavorisController::transports'], null, null, null, false, false, null]],
        '/favoris/posts' => [[['_route' => 'favoris_posts', '_controller' => 'App\\Controller\\FavorisController::posts'], null, null, null, false, false, null]],
        '/ajax/forum/comment/create' => [[['_route' => 'ajax_forum_comment_create', '_controller' => 'App\\Controller\\ForumAjaxController::createComment'], null, ['POST' => 0], null, false, false, null]],
        '/forum' => [[['_route' => 'forum_index', '_controller' => 'App\\Controller\\ForumController::index'], null, ['GET' => 0], null, false, false, null]],
        '/forum/nouveau' => [[['_route' => 'forum_new', '_controller' => 'App\\Controller\\ForumController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
        '/api/friend/nickname' => [[['_route' => 'api_friend_nickname', '_controller' => 'App\\Controller\\FriendController::setNickname'], null, ['POST' => 0], null, false, false, null]],
        '/api/friend/received' => [[['_route' => 'api_friend_received', '_controller' => 'App\\Controller\\FriendController::getReceivedRequests'], null, ['GET' => 0], null, false, false, null]],
        '/api/friend/sent' => [[['_route' => 'api_friend_sent', '_controller' => 'App\\Controller\\FriendController::getSentRequests'], null, ['GET' => 0], null, false, false, null]],
        '/api/friend/list' => [[['_route' => 'api_friend_list', '_controller' => 'App\\Controller\\FriendController::getFriends'], null, ['GET' => 0], null, false, false, null]],
        '/api/friend/pending/count' => [[['_route' => 'api_friend_pending_count', '_controller' => 'App\\Controller\\FriendController::getPendingCount'], null, ['GET' => 0], null, false, false, null]],
        '/api/friend/ids' => [[['_route' => 'api_friend_ids', '_controller' => 'App\\Controller\\FriendController::getFriendIds'], null, ['GET' => 0], null, false, false, null]],
        '/api/friend/search' => [[['_route' => 'api_friend_search', '_controller' => 'App\\Controller\\FriendController::searchByName'], null, ['GET' => 0], null, false, false, null]],
        '/jeux' => [[['_route' => 'games_index', '_controller' => 'App\\Controller\\GameController::index'], null, null, null, false, false, null]],
        '/jeux/quiz' => [[['_route' => 'games_quiz', '_controller' => 'App\\Controller\\GameController::quiz'], null, null, null, false, false, null]],
        '/jeux/spin' => [[['_route' => 'games_spin', '_controller' => 'App\\Controller\\GameController::spin'], null, null, null, false, false, null]],
        '/jeux/memory' => [[['_route' => 'games_memory', '_controller' => 'App\\Controller\\GameController::memory'], null, null, null, false, false, null]],
        '/jeux/price' => [[['_route' => 'games_price', '_controller' => 'App\\Controller\\GameController::price'], null, null, null, false, false, null]],
        '/api/quiz/check' => [[['_route' => 'api_quiz_check', '_controller' => 'App\\Controller\\GameController::checkQuiz'], null, null, null, false, false, null]],
        '/api/spin/wheel' => [[['_route' => 'api_spin_wheel', '_controller' => 'App\\Controller\\GameController::spinWheel'], null, null, null, false, false, null]],
        '/api/quiz/submit' => [[['_route' => 'api_quiz_submit', '_controller' => 'App\\Controller\\GameController::submitQuiz'], null, ['POST' => 0], null, false, false, null]],
        '/api/groups/create' => [[['_route' => 'api_groups_create', '_controller' => 'App\\Controller\\GroupController::createGroup'], null, ['POST' => 0], null, false, false, null]],
        '/hebergements' => [[['_route' => 'hebergement_index', '_controller' => 'App\\Controller\\HebergementController::index'], null, null, null, false, false, null]],
        '/hebergement/top' => [[['_route' => 'hebergement_top', '_controller' => 'App\\Controller\\HebergementController::top'], null, null, null, false, false, null]],
        '/payment/success' => [[['_route' => 'payment_success', '_controller' => 'App\\Controller\\HebergementController::paymentSuccess'], null, ['GET' => 0], null, false, false, null]],
        '/historique' => [[['_route' => 'user_history', '_controller' => 'App\\Controller\\HistoryController::index'], null, null, null, false, false, null]],
        '/historique/delete' => [[['_route' => 'history_delete', '_controller' => 'App\\Controller\\HistoryController::delete'], null, ['POST' => 0], null, false, false, null]],
        '/historique/clear' => [[['_route' => 'history_clear', '_controller' => 'App\\Controller\\HistoryController::clearAll'], null, ['POST' => 0], null, false, false, null]],
        '/' => [[['_route' => 'home', '_controller' => 'App\\Controller\\HomeController::index'], null, null, null, false, false, null]],
        '/api/messages/conversations' => [[['_route' => 'api_messages_conversations', '_controller' => 'App\\Controller\\MessageController::getConversations'], null, ['GET' => 0], null, false, false, null]],
        '/api/messages/user/online' => [[['_route' => 'api_user_online', '_controller' => 'App\\Controller\\MessageController::updateOnline'], null, ['POST' => 0], null, false, false, null]],
        '/api/messages/upload-audio' => [[['_route' => 'api_messages_upload_audio', '_controller' => 'App\\Controller\\MessageController::uploadAudio'], null, ['POST' => 0], null, false, false, null]],
        '/api/messages/upload-image' => [[['_route' => 'api_messages_upload_image', '_controller' => 'App\\Controller\\MessageController::uploadImage'], null, ['POST' => 0], null, false, false, null]],
        '/api/messages/archived' => [[['_route' => 'api_messages_archived', '_controller' => 'App\\Controller\\MessageController::getArchivedConversations'], null, ['GET' => 0], null, false, false, null]],
        '/messenger' => [[['_route' => 'messenger_index', '_controller' => 'App\\Controller\\MessengerController::index'], null, null, null, true, false, null]],
        '/sse/hebergement' => [[['_route' => 'sse_hebergement', '_controller' => 'App\\Controller\\NotificationController::hebergementStream'], null, null, null, false, false, null]],
        '/sse/test' => [[['_route' => 'sse_test', '_controller' => 'App\\Controller\\NotificationController::testNotification'], null, null, null, false, false, null]],
        '/passport' => [[['_route' => 'passport_index', '_controller' => 'App\\Controller\\Passport\\PuzzleController::index'], null, null, null, false, false, null]],
        '/payment/create-intent' => [[['_route' => 'payment_create_intent', '_controller' => 'App\\Controller\\PaymentController::createPaymentIntent'], null, ['POST' => 0], null, false, false, null]],
        '/payment/create-order' => [[['_route' => 'payment_create_order', '_controller' => 'App\\Controller\\PaymentController::createPaypalOrder'], null, ['POST' => 0], null, false, false, null]],
        '/payment/process' => [[['_route' => 'payment_process', '_controller' => 'App\\Controller\\PaymentController::processPayment'], null, ['POST' => 0], null, false, false, null]],
        '/admin/profils' => [[['_route' => 'admin_profils', '_controller' => 'App\\Controller\\ProfilVoyageurController::index'], null, null, null, false, false, null]],
        '/admin/profils/stats' => [[['_route' => 'admin_profils_stats', '_controller' => 'App\\Controller\\ProfilVoyageurController::stats'], null, ['GET' => 0], null, false, false, null]],
        '/mon-espace/profil-voyageur' => [[['_route' => 'user_profil_voyageur', '_controller' => 'App\\Controller\\ProfilVoyageurController::userProfile'], null, null, null, false, false, null]],
        '/mon-espace/profil-voyageur/voir' => [[['_route' => 'user_profil_voyageur_view', '_controller' => 'App\\Controller\\ProfilVoyageurController::viewProfile'], null, null, null, false, false, null]],
        '/admin/promos' => [[['_route' => 'app_admin_promos', '_controller' => 'App\\Controller\\PromoCodeController::index'], null, null, null, false, false, null]],
        '/admin/promos/new' => [[['_route' => 'app_admin_promo_new', '_controller' => 'App\\Controller\\PromoCodeController::new'], null, null, null, false, false, null]],
        '/reset-password' => [[['_route' => 'app_forgot_password_request', '_controller' => 'App\\Controller\\ResetPasswordController::request'], null, null, null, false, false, null]],
        '/reset-password/check-email' => [[['_route' => 'app_forgot_password_check_email', '_controller' => 'App\\Controller\\ResetPasswordController::checkEmail'], null, null, null, false, false, null]],
        '/login' => [[['_route' => 'app_login', '_controller' => 'App\\Controller\\SecurityController::login'], null, null, null, false, false, null]],
        '/post-login' => [[['_route' => 'app_after_login', '_controller' => 'App\\Controller\\SecurityController::afterLogin'], null, null, null, false, false, null]],
        '/logout' => [[['_route' => 'app_logout', '_controller' => 'App\\Controller\\SecurityController::logout'], null, null, null, false, false, null]],
        '/register' => [[['_route' => 'app_register', '_controller' => 'App\\Controller\\SecurityController::register'], null, null, null, false, false, null]],
        '/oauth2callback' => [[['_route' => 'oauth2_callback', '_controller' => 'App\\Controller\\SecurityController::oauth2Callback'], null, null, null, false, false, null]],
        '/facebook/callback' => [[['_route' => 'facebook_callback', '_controller' => 'App\\Controller\\SecurityController::facebookCallback'], null, null, null, false, false, null]],
        '/api/auth/google-signin' => [[['_route' => 'api_auth_google', '_controller' => 'App\\Controller\\SecurityController::apiAuthGoogle'], null, ['POST' => 0], null, false, false, null]],
        '/api/sentiment/analyze' => [[['_route' => 'sentiment_analyze', '_controller' => 'App\\Controller\\SentimentController::analyze'], null, ['POST' => 0], null, false, false, null]],
        '/api/sentiment/reviews' => [[['_route' => 'sentiment_reviews', '_controller' => 'App\\Controller\\SentimentController::getReviews'], null, ['GET' => 0], null, false, false, null]],
        '/api/sentiment/analyze-all' => [[['_route' => 'sentiment_analyze_all', '_controller' => 'App\\Controller\\SentimentController::analyzeAllReviews'], null, ['POST' => 0], null, false, false, null]],
        '/api/sentiment/keywords' => [[['_route' => 'sentiment_keywords', '_controller' => 'App\\Controller\\SentimentController::extractKeywords'], null, ['POST' => 0], null, false, false, null]],
        '/api/sentiment/batch' => [[['_route' => 'sentiment_batch', '_controller' => 'App\\Controller\\SentimentController::batchAnalyze'], null, ['POST' => 0], null, false, false, null]],
        '/api/share/to-conversation' => [[['_route' => 'api_share_to_conversation', '_controller' => 'App\\Controller\\ShareController::shareToConversation'], null, ['POST' => 0], null, false, false, null]],
        '/api/share/get-conversations' => [[['_route' => 'api_share_get_conversations', '_controller' => 'App\\Controller\\ShareController::getConversations'], null, ['GET' => 0], null, false, false, null]],
        '/api/share/create-conversation' => [[['_route' => 'api_share_create_conversation', '_controller' => 'App\\Controller\\ShareController::createConversation'], null, ['POST' => 0], null, false, false, null]],
        '/api/translate' => [[['_route' => 'api_translate', '_controller' => 'App\\Controller\\TranslationController::translate'], null, ['POST' => 0], null, false, false, null]],
        '/api/translate/detect' => [[['_route' => 'api_translate_detect', '_controller' => 'App\\Controller\\TranslationController::detect'], null, ['POST' => 0], null, false, false, null]],
        '/api/translate/batch' => [[['_route' => 'api_translate_batch', '_controller' => 'App\\Controller\\TranslationController::translateBatch'], null, ['POST' => 0], null, false, false, null]],
        '/api/translate/avis' => [[['_route' => 'api_translate_avis', '_controller' => 'App\\Controller\\TranslationController::translateAvis'], null, ['POST' => 0], null, false, false, null]],
        '/api/translate/languages' => [[['_route' => 'api_translate_languages', '_controller' => 'App\\Controller\\TranslationController::languages'], null, ['GET' => 0], null, false, false, null]],
        '/api/translate/status' => [[['_route' => 'api_translate_status', '_controller' => 'App\\Controller\\TranslationController::status'], null, ['GET' => 0], null, false, false, null]],
        '/transport' => [[['_route' => 'transport_index', '_controller' => 'App\\Controller\\TransportController::index'], null, ['GET' => 0], null, false, false, null]],
        '/transport/mes-reservations' => [[['_route' => 'transport_my_bookings', '_controller' => 'App\\Controller\\TransportController::myBookings'], null, ['GET' => 0], null, false, false, null]],
        '/mon-espace/profil' => [[['_route' => 'user_profile', '_controller' => 'App\\Controller\\UserSpaceController::profile'], null, null, null, false, false, null]],
        '/mon-espace' => [[['_route' => 'user_dashboard', '_controller' => 'App\\Controller\\UserSpaceController::dashboard'], null, null, null, false, false, null]],
        '/mes-reservations' => [[['_route' => 'user_reservations', '_controller' => 'App\\Controller\\UserSpaceController::reservations'], null, null, null, false, false, null]],
        '/mes-avis' => [[['_route' => 'user_avis', '_controller' => 'App\\Controller\\UserSpaceController::avis'], null, null, null, false, false, null]],
        '/mes-circuits' => [[['_route' => 'user_circuits', '_controller' => 'App\\Controller\\UserSpaceController::circuits'], null, null, null, false, false, null]],
        '/api/weather' => [[['_route' => 'api_weather', '_controller' => 'App\\Controller\\WeatherController::getWeather'], null, ['GET' => 0], null, false, false, null]],
        '/admin/dashboard' => [[['_route' => 'sonata_admin_dashboard', '_controller' => 'sonata.admin.action.dashboard'], null, null, null, false, false, null]],
        '/admin/core/get-form-field-element' => [[['_route' => 'sonata_admin_retrieve_form_element', '_controller' => 'sonata.admin.action.retrieve_form_field_element'], null, null, null, false, false, null]],
        '/admin/core/append-form-field-element' => [[['_route' => 'sonata_admin_append_form_element', '_controller' => 'sonata.admin.action.append_form_field_element'], null, null, null, false, false, null]],
        '/admin/core/set-object-field-value' => [[['_route' => 'sonata_admin_set_object_field_value', '_controller' => 'sonata.admin.action.set_object_field_value'], null, null, null, false, false, null]],
        '/admin/search' => [[['_route' => 'sonata_admin_search', '_controller' => 'sonata.admin.action.search'], null, null, null, false, false, null]],
        '/admin/core/get-autocomplete-items' => [[['_route' => 'sonata_admin_retrieve_autocomplete_items', '_controller' => 'sonata.admin.action.retrieve_autocomplete_items'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/a(?'
                    .'|pi(?'
                        .'|/\\.well\\-known/genid/([^/]++)(*:46)'
                        .'|(?:/(index)(?:\\.([^/]++))?)?(*:81)'
                        .'|/(?'
                            .'|docs(?:\\.([^/]++))?(*:111)'
                            .'|c(?'
                                .'|ontexts/([^.]+)(?:\\.(jsonld))?(*:153)'
                                .'|ircuits(?'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(*:197)'
                                    .'|(?:\\.([^/]++))?(?'
                                        .'|(*:223)'
                                    .')'
                                    .'|/([^/\\.]++)(?:\\.([^/]++))?(?'
                                        .'|(*:261)'
                                    .')'
                                    .'|(*:270)'
                                .')'
                            .')'
                            .'|activities/(?'
                                .'|([^/]++)/(?'
                                    .'|reviews(*:313)'
                                    .'|images(*:327)'
                                    .'|book(*:339)'
                                .')'
                                .'|(\\d+)(*:353)'
                            .')'
                            .'|f(?'
                                .'|orum/(?'
                                    .'|post/([^/]++)/translate(*:397)'
                                    .'|comment/([^/]++)/translate(*:431)'
                                .')'
                                .'|riend/(?'
                                    .'|re(?'
                                        .'|quest/([^/]++)(*:468)'
                                        .'|ject/([^/]++)(*:489)'
                                        .'|move/([^/]++)(*:510)'
                                    .')'
                                    .'|accept/([^/]++)(*:534)'
                                    .'|cancel/([^/]++)(*:557)'
                                    .'|status/([^/]++)(*:580)'
                                    .'|block/([^/]++)(*:602)'
                                    .'|unblock/([^/]++)(*:626)'
                                .')'
                            .')'
                            .'|s(?'
                                .'|tory/([^/]++)(?'
                                    .'|/(?'
                                        .'|view(*:664)'
                                        .'|re(?'
                                            .'|act(*:680)'
                                            .'|ply(*:691)'
                                        .')'
                                    .')'
                                    .'|(*:701)'
                                .')'
                                .'|entiment/(?'
                                    .'|analyze\\-review/([^/]++)(*:746)'
                                    .'|hotel/([^/]++)/score(*:774)'
                                .')'
                                .'|hare/get\\-post/([^/]++)(*:806)'
                            .')'
                            .'|groups/([^/]++)/(?'
                                .'|add(*:837)'
                                .'|remove(*:851)'
                                .'|members(*:866)'
                            .')'
                            .'|messages/(?'
                                .'|conversation/([^/]++)(?'
                                    .'|(*:911)'
                                    .'|/(?'
                                        .'|m(?'
                                            .'|essages(*:934)'
                                            .'|ute(*:945)'
                                        .')'
                                        .'|t(?'
                                            .'|yping(*:963)'
                                            .'|heme(*:975)'
                                        .')'
                                        .'|read(*:988)'
                                        .'|nickname(*:1004)'
                                        .'|archive(*:1020)'
                                        .'|unarchive(*:1038)'
                                    .')'
                                    .'|(*:1048)'
                                .')'
                                .'|message/([^/]++)/react(*:1080)'
                            .')'
                        .')'
                    .')'
                    .'|i\\-puzzle/play(?:/([^/]++))?(*:1120)'
                    .'|ctivites/(?'
                        .'|(\\d+)(*:1146)'
                        .'|(\\d+)/reserver(*:1169)'
                        .'|(\\d+)/avis(*:1188)'
                        .'|api/share/activity/([^/]++)/(?'
                            .'|forum(*:1233)'
                            .'|message(*:1249)'
                        .')'
                    .')'
                    .'|dmin/(?'
                        .'|user/([^/]++)/(?'
                            .'|toggle(*:1291)'
                            .'|role(*:1304)'
                            .'|delete(*:1319)'
                            .'|edit(*:1332)'
                        .')'
                        .'|hebergement/([^/]++)/(?'
                            .'|edit(*:1370)'
                            .'|delete(*:1385)'
                        .')'
                        .'|re(?'
                            .'|s(?'
                                .'|ervation(?'
                                    .'|/([^/]++)/(?'
                                        .'|statut(*:1434)'
                                        .'|delete(*:1449)'
                                    .')'
                                    .'|\\-transport/([^/]++)/(?'
                                        .'|statut(*:1489)'
                                        .'|delete(*:1504)'
                                    .')'
                                .')'
                                .'|\\-circuit/([^/]++)/(?'
                                    .'|statut(*:1543)'
                                    .'|delete(*:1558)'
                                .')'
                            .')'
                            .'|view/([^/]++)/delete(*:1589)'
                        .')'
                        .'|a(?'
                            .'|vis(?'
                                .'|/([^/]++)/delete(*:1625)'
                                .'|\\-(?'
                                    .'|transport/([^/]++)/delete(*:1664)'
                                    .'|circuit/([^/]++)/delete(*:1696)'
                                .')'
                            .')'
                            .'|ctivite/([^/]++)/(?'
                                .'|edit(*:1731)'
                                .'|delete(*:1746)'
                            .')'
                        .')'
                        .'|c(?'
                            .'|ircuit/([^/]++)/(?'
                                .'|edit(*:1784)'
                                .'|delete(*:1799)'
                            .')'
                            .'|alendar/(?'
                                .'|edit/([^/]++)(*:1833)'
                                .'|delete/([^/]++)(*:1857)'
                            .')'
                            .'|ore/get\\-short\\-object\\-description(?:\\.(html|json))?(*:1920)'
                        .')'
                        .'|transport/([^/]++)/(?'
                            .'|edit(*:1956)'
                            .'|delete(*:1971)'
                        .')'
                        .'|f(?'
                            .'|aq/([^/]++)/(?'
                                .'|edit(*:2004)'
                                .'|delete(*:2019)'
                            .')'
                            .'|orum/([^/]++)/(?'
                                .'|moderate(*:2054)'
                                .'|delete(*:2069)'
                            .')'
                        .')'
                        .'|booking/([^/]++)/(?'
                            .'|statut(*:2106)'
                            .'|delete(*:2121)'
                        .')'
                        .'|pro(?'
                            .'|fils/([^/]++)(*:2150)'
                            .'|mos/(?'
                                .'|edit/([^/]++)(*:2179)'
                                .'|([^/]++)/(?'
                                    .'|delete(*:2206)'
                                    .'|toggle(*:2221)'
                                .')'
                            .')'
                        .')'
                    .')'
                    .'|jax/(?'
                        .'|f(?'
                            .'|avorite/(?'
                                .'|([^/]++)/([^/]++)(*:2273)'
                                .'|hebergement/([^/]++)(*:2302)'
                                .'|circuit/([^/]++)(*:2327)'
                                .'|activity/([^/]++)(*:2353)'
                                .'|transport/([^/]++)(*:2380)'
                                .'|post/([^/]++)(*:2402)'
                            .')'
                            .'|orum/(?'
                                .'|comment(?'
                                    .'|s/([^/]++)(*:2440)'
                                    .'|/([^/]++)/(?'
                                        .'|vote(*:2466)'
                                        .'|pin(*:2478)'
                                        .'|delete(*:2493)'
                                    .')'
                                .')'
                                .'|post/([^/]++)/(?'
                                    .'|vote(*:2525)'
                                    .'|likes(*:2539)'
                                .')'
                            .')'
                        .')'
                        .'|like/([^/]++)/([^/]++)(*:2573)'
                        .'|rating/refresh/([^/]++)/([^/]++)(*:2614)'
                    .')'
                .')'
                .'|/qr\\-code/([^/]++)/([\\w\\W]+)(*:2653)'
                .'|/_(?'
                    .'|error/(\\d+)(?:\\.([^/]++))?(*:2693)'
                    .'|wdt/([^/]++)(*:2714)'
                    .'|profiler/(?'
                        .'|font/([^/\\.]++)\\.woff2(*:2757)'
                        .'|([^/]++)(?'
                            .'|/(?'
                                .'|search/results(*:2795)'
                                .'|router(*:2810)'
                                .'|exception(?'
                                    .'|(*:2831)'
                                    .'|\\.css(*:2845)'
                                .')'
                            .')'
                            .'|(*:2856)'
                        .')'
                    .')'
                .')'
                .'|/c(?'
                    .'|ircuits/(?'
                        .'|(\\d+)(*:2889)'
                        .'|(\\d+)/pdf(*:2907)'
                        .'|(\\d+)/reserver(*:2930)'
                        .'|(\\d+)/avis(*:2949)'
                    .')'
                    .'|heckout/(?'
                        .'|(\\d+)(*:2975)'
                        .'|(\\d+)/pay(*:2993)'
                    .')'
                .')'
                .'|/f(?'
                    .'|orum/(?'
                        .'|(\\d+)(*:3022)'
                        .'|comment/([^/]++)/pin(*:3051)'
                        .'|([^/]++)/comments/page/([^/]++)(*:3091)'
                    .')'
                    .'|acture/(\\d+)(*:3113)'
                .')'
                .'|/hebergement/(?'
                    .'|(\\d+)(*:3144)'
                    .'|(\\d+)/avis(*:3163)'
                    .'|(\\d+)/reserver(*:3186)'
                .')'
                .'|/rese(?'
                    .'|rvation/(?'
                        .'|(\\d+)/cancel(*:3227)'
                        .'|([^/]++)/facture(?'
                            .'|(*:3255)'
                            .'|/preview(*:3272)'
                        .')'
                    .')'
                    .'|t\\-password/reset/([^/]++)(*:3309)'
                .')'
                .'|/payment/(?'
                    .'|receipt/([^/]++)(*:3347)'
                    .'|download/(?'
                        .'|pdf/([^/]++)(*:3380)'
                        .'|word/([^/]++)(*:3402)'
                    .')'
                    .'|qrcode/([^/]++)(*:3427)'
                    .'|confirmation/([^/]++)(*:3457)'
                .')'
                .'|/transport/(?'
                    .'|offre/(?'
                        .'|(\\d+)(*:3495)'
                        .'|(\\d+)/reserver(*:3518)'
                    .')'
                    .'|reservation/([^/]++)/annuler(*:3556)'
                .')'
                .'|/mes\\-(?'
                    .'|reservations/(?'
                        .'|hebergement/([^/]++)/annuler(*:3619)'
                        .'|activite/([^/]++)/annuler(*:3653)'
                        .'|circuit/([^/]++)/annuler(*:3686)'
                    .')'
                    .'|avis/(?'
                        .'|hebergement/([^/]++)/supprimer(*:3734)'
                        .'|activite/([^/]++)/supprimer(*:3770)'
                        .'|circuit/([^/]++)/supprimer(*:3805)'
                    .')'
                    .'|circuits/([^/]++)/supprimer(*:3842)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        46 => [[['_route' => 'api_genid', '_controller' => 'api_platform.action.not_exposed', '_api_respond' => 'true'], ['id'], null, null, false, true, null]],
        81 => [[['_route' => 'api_entrypoint', '_controller' => 'api_platform.action.entrypoint', '_format' => '', '_api_respond' => 'true', 'index' => 'index'], ['index', '_format'], null, null, false, true, null]],
        111 => [[['_route' => 'api_doc', '_controller' => 'api_platform.action.documentation', '_format' => '', '_api_respond' => 'true'], ['_format'], null, null, false, true, null]],
        153 => [[['_route' => 'api_jsonld_context', '_controller' => 'api_platform.jsonld.action.context', '_format' => 'jsonld', '_api_respond' => 'true'], ['shortName', '_format'], null, null, false, true, null]],
        197 => [[['_route' => '_api_/circuits/{id}.{_format}_get', '_controller' => 'api_platform.action.placeholder', '_format' => null, '_stateless' => null, '_api_resource_class' => 'App\\Entity\\Circuit', '_api_operation_name' => '_api_/circuits/{id}.{_format}_get'], ['id', '_format'], ['GET' => 0], null, false, true, null]],
        223 => [
            [['_route' => '_api_/circuits.{_format}_get_collection', '_controller' => 'api_platform.action.placeholder', '_format' => null, '_stateless' => null, '_api_resource_class' => 'App\\Entity\\Circuit', '_api_operation_name' => '_api_/circuits.{_format}_get_collection'], ['_format'], ['GET' => 0], null, false, true, null],
            [['_route' => '_api_/circuits.{_format}_post', '_controller' => 'api_platform.action.placeholder', '_format' => null, '_stateless' => null, '_api_resource_class' => 'App\\Entity\\Circuit', '_api_operation_name' => '_api_/circuits.{_format}_post'], ['_format'], ['POST' => 0], null, false, true, null],
        ],
        261 => [
            [['_route' => '_api_/circuits/{id}.{_format}_put', '_controller' => 'api_platform.action.placeholder', '_format' => null, '_stateless' => null, '_api_resource_class' => 'App\\Entity\\Circuit', '_api_operation_name' => '_api_/circuits/{id}.{_format}_put'], ['id', '_format'], ['PUT' => 0], null, false, true, null],
            [['_route' => '_api_/circuits/{id}.{_format}_patch', '_controller' => 'api_platform.action.placeholder', '_format' => null, '_stateless' => null, '_api_resource_class' => 'App\\Entity\\Circuit', '_api_operation_name' => '_api_/circuits/{id}.{_format}_patch'], ['id', '_format'], ['PATCH' => 0], null, false, true, null],
            [['_route' => '_api_/circuits/{id}.{_format}_delete', '_controller' => 'api_platform.action.placeholder', '_format' => null, '_stateless' => null, '_api_resource_class' => 'App\\Entity\\Circuit', '_api_operation_name' => '_api_/circuits/{id}.{_format}_delete'], ['id', '_format'], ['DELETE' => 0], null, false, true, null],
        ],
        270 => [[['_route' => 'api_circuits', '_controller' => 'App\\Controller\\ApiController::circuits'], [], ['GET' => 0], null, false, false, null]],
        313 => [[['_route' => 'api_activities_reviews', '_controller' => 'App\\Controller\\Api\\ActivityApiController::reviews'], ['id'], ['GET' => 0], null, false, false, null]],
        327 => [[['_route' => 'api_activities_images', '_controller' => 'App\\Controller\\Api\\ActivityApiController::images'], ['id'], ['GET' => 0], null, false, false, null]],
        339 => [[['_route' => 'api_activities_book', '_controller' => 'App\\Controller\\Api\\ActivityApiController::book'], ['id'], ['POST' => 0], null, false, false, null]],
        353 => [[['_route' => 'api_activity_detail', '_controller' => 'App\\Controller\\ApiController::activityDetail'], ['id'], ['GET' => 0], null, false, true, null]],
        397 => [[['_route' => 'forum_api_post_translate', '_controller' => 'App\\Controller\\Api\\ForumApiController::translatePost'], ['id'], ['POST' => 0], null, false, false, null]],
        431 => [[['_route' => 'forum_api_comment_translate', '_controller' => 'App\\Controller\\Api\\ForumApiController::translateComment'], ['id'], ['POST' => 0], null, false, false, null]],
        468 => [[['_route' => 'api_friend_request', '_controller' => 'App\\Controller\\FriendController::sendRequest'], ['userId'], ['POST' => 0], null, false, true, null]],
        489 => [[['_route' => 'api_friend_reject', '_controller' => 'App\\Controller\\FriendController::rejectRequest'], ['requestId'], ['POST' => 0], null, false, true, null]],
        510 => [[['_route' => 'api_friend_remove', '_controller' => 'App\\Controller\\FriendController::removeFriend'], ['userId'], ['POST' => 0], null, false, true, null]],
        534 => [[['_route' => 'api_friend_accept', '_controller' => 'App\\Controller\\FriendController::acceptRequest'], ['requestId'], ['POST' => 0], null, false, true, null]],
        557 => [[['_route' => 'api_friend_cancel', '_controller' => 'App\\Controller\\FriendController::cancelRequest'], ['requestId'], ['POST' => 0], null, false, true, null]],
        580 => [[['_route' => 'api_friend_status', '_controller' => 'App\\Controller\\FriendController::getStatus'], ['userId'], ['GET' => 0], null, false, true, null]],
        602 => [[['_route' => 'api_friend_block', '_controller' => 'App\\Controller\\FriendController::blockUser'], ['userId'], ['POST' => 0], null, false, true, null]],
        626 => [[['_route' => 'api_friend_unblock', '_controller' => 'App\\Controller\\FriendController::unblockUser'], ['userId'], ['POST' => 0], null, false, true, null]],
        664 => [[['_route' => 'api_story_view', '_controller' => 'App\\Controller\\Api\\StoryApiController::view'], ['id'], ['POST' => 0], null, false, false, null]],
        680 => [[['_route' => 'api_story_react', '_controller' => 'App\\Controller\\Api\\StoryApiController::react'], ['id'], ['POST' => 0], null, false, false, null]],
        691 => [[['_route' => 'api_story_reply', '_controller' => 'App\\Controller\\Api\\StoryApiController::reply'], ['id'], ['POST' => 0], null, false, false, null]],
        701 => [[['_route' => 'api_story_delete', '_controller' => 'App\\Controller\\Api\\StoryApiController::delete'], ['id'], ['DELETE' => 0], null, false, true, null]],
        746 => [[['_route' => 'sentiment_analyze_review', '_controller' => 'App\\Controller\\SentimentController::analyzeReview'], ['id'], ['POST' => 0], null, false, true, null]],
        774 => [[['_route' => 'sentiment_hotel_score', '_controller' => 'App\\Controller\\SentimentController::getHotelScore'], ['id'], ['GET' => 0], null, false, false, null]],
        806 => [[['_route' => 'api_share_get_post', '_controller' => 'App\\Controller\\ShareController::getPost'], ['id'], ['GET' => 0], null, false, true, null]],
        837 => [[['_route' => 'api_groups_add_member', '_controller' => 'App\\Controller\\GroupController::addMember'], ['id'], ['POST' => 0], null, false, false, null]],
        851 => [[['_route' => 'api_groups_remove_member', '_controller' => 'App\\Controller\\GroupController::removeMember'], ['id'], ['POST' => 0], null, false, false, null]],
        866 => [[['_route' => 'api_groups_members', '_controller' => 'App\\Controller\\GroupController::getMembers'], ['id'], ['GET' => 0], null, false, false, null]],
        911 => [[['_route' => 'api_messages_conversation', '_controller' => 'App\\Controller\\MessageController::getConversation'], ['id'], ['GET' => 0], null, false, true, null]],
        934 => [[['_route' => 'api_messages_send', '_controller' => 'App\\Controller\\MessageController::sendMessage'], ['id'], ['POST' => 0], null, false, false, null]],
        945 => [[['_route' => 'api_messages_mute', '_controller' => 'App\\Controller\\MessageController::toggleMute'], ['id'], ['POST' => 0], null, false, false, null]],
        963 => [[['_route' => 'api_messages_typing', '_controller' => 'App\\Controller\\MessageController::typing'], ['id'], ['POST' => 0], null, false, false, null]],
        975 => [[['_route' => 'api_messages_theme', '_controller' => 'App\\Controller\\MessageController::setConversationTheme'], ['id'], ['POST' => 0], null, false, false, null]],
        988 => [[['_route' => 'api_messages_read', '_controller' => 'App\\Controller\\MessageController::markAsRead'], ['id'], ['POST' => 0], null, false, false, null]],
        1004 => [[['_route' => 'api_messages_nickname', '_controller' => 'App\\Controller\\MessageController::setNickname'], ['id'], ['POST' => 0], null, false, false, null]],
        1020 => [[['_route' => 'api_messages_archive', '_controller' => 'App\\Controller\\MessageController::toggleArchive'], ['id'], ['POST' => 0], null, false, false, null]],
        1038 => [[['_route' => 'api_messages_unarchive', '_controller' => 'App\\Controller\\MessageController::unarchiveConversation'], ['id'], ['POST' => 0], null, false, false, null]],
        1048 => [[['_route' => 'api_messages_delete', '_controller' => 'App\\Controller\\MessageController::deleteConversation'], ['id'], ['DELETE' => 0], null, false, true, null]],
        1080 => [[['_route' => 'api_messages_react', '_controller' => 'App\\Controller\\MessageController::reactToMessage'], ['id'], ['POST' => 0], null, false, false, null]],
        1120 => [[['_route' => 'ai_puzzle_play', 'difficulty' => 'easy', '_controller' => 'App\\Controller\\AITravelPuzzleController::play'], ['difficulty'], null, null, false, true, null]],
        1146 => [[['_route' => 'activity_show', '_controller' => 'App\\Controller\\ActivityController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        1169 => [[['_route' => 'activity_book', '_controller' => 'App\\Controller\\ActivityController::book'], ['id'], ['POST' => 0], null, false, false, null]],
        1188 => [[['_route' => 'activity_review', '_controller' => 'App\\Controller\\ActivityController::addReview'], ['id'], ['POST' => 0], null, false, false, null]],
        1233 => [[['_route' => 'activity_share_forum', '_controller' => 'App\\Controller\\ActivityController::shareToForum'], ['id'], ['POST' => 0], null, false, false, null]],
        1249 => [[['_route' => 'activity_share_message', '_controller' => 'App\\Controller\\ActivityController::shareToMessage'], ['id'], ['POST' => 0], null, false, false, null]],
        1291 => [[['_route' => 'admin_user_toggle', '_controller' => 'App\\Controller\\AdminController::toggleUser'], ['id'], ['POST' => 0], null, false, false, null]],
        1304 => [[['_route' => 'admin_user_role', '_controller' => 'App\\Controller\\AdminController::toggleRole'], ['id'], ['POST' => 0], null, false, false, null]],
        1319 => [[['_route' => 'admin_user_delete', '_controller' => 'App\\Controller\\AdminController::deleteUser'], ['id'], ['POST' => 0], null, false, false, null]],
        1332 => [[['_route' => 'admin_user_edit', '_controller' => 'App\\Controller\\AdminController::editUser'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1370 => [[['_route' => 'admin_hebergement_edit', '_controller' => 'App\\Controller\\AdminController::hebergementEdit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1385 => [[['_route' => 'admin_hebergement_delete', '_controller' => 'App\\Controller\\AdminController::hebergementDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1434 => [[['_route' => 'admin_reservation_statut', '_controller' => 'App\\Controller\\AdminController::reservationStatut'], ['id'], ['POST' => 0], null, false, false, null]],
        1449 => [[['_route' => 'admin_reservation_delete', '_controller' => 'App\\Controller\\AdminController::reservationDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1489 => [[['_route' => 'admin_res_transport_statut', '_controller' => 'App\\Controller\\AdminController::resTransportStatut'], ['id'], ['POST' => 0], null, false, false, null]],
        1504 => [[['_route' => 'admin_res_transport_delete', '_controller' => 'App\\Controller\\AdminController::resTransportDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1543 => [[['_route' => 'admin_res_circuit_statut', '_controller' => 'App\\Controller\\AdminController::resCircuitStatut'], ['id'], ['POST' => 0], null, false, false, null]],
        1558 => [[['_route' => 'admin_res_circuit_delete', '_controller' => 'App\\Controller\\AdminController::resCircuitDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1589 => [[['_route' => 'admin_review_delete', '_controller' => 'App\\Controller\\AdminController::reviewDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1625 => [[['_route' => 'admin_avis_delete', '_controller' => 'App\\Controller\\AdminController::avisDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1664 => [[['_route' => 'admin_avis_transport_delete', '_controller' => 'App\\Controller\\AdminController::avisTransportDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1696 => [[['_route' => 'admin_avis_circuit_delete', '_controller' => 'App\\Controller\\AdminController::avisCircuitDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1731 => [[['_route' => 'admin_activity_edit', '_controller' => 'App\\Controller\\AdminController::activityEdit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1746 => [[['_route' => 'admin_activity_delete', '_controller' => 'App\\Controller\\AdminController::activityDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1784 => [[['_route' => 'admin_circuit_edit', '_controller' => 'App\\Controller\\AdminController::circuitEdit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1799 => [[['_route' => 'admin_circuit_delete', '_controller' => 'App\\Controller\\AdminController::circuitDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        1833 => [[['_route' => 'admin_calendar_edit', '_controller' => 'App\\Controller\\AdminController::calendarEdit'], ['id'], ['POST' => 0], null, false, true, null]],
        1857 => [[['_route' => 'admin_calendar_delete', '_controller' => 'App\\Controller\\AdminController::calendarDelete'], ['id'], null, null, false, true, null]],
        1920 => [[['_route' => 'sonata_admin_short_object_information', '_controller' => 'sonata.admin.action.get_short_object_description', '_format' => 'html'], ['_format'], null, null, false, true, null]],
        1956 => [[['_route' => 'admin_transport_edit', '_controller' => 'App\\Controller\\AdminController::transportEdit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        1971 => [[['_route' => 'admin_transport_delete', '_controller' => 'App\\Controller\\AdminController::transportDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        2004 => [[['_route' => 'admin_faq_edit', '_controller' => 'App\\Controller\\AdminController::faqEdit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        2019 => [[['_route' => 'admin_faq_delete', '_controller' => 'App\\Controller\\AdminController::faqDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        2054 => [[['_route' => 'admin_forum_moderate', '_controller' => 'App\\Controller\\AdminController::moderatePost'], ['id'], ['POST' => 0], null, false, false, null]],
        2069 => [[['_route' => 'admin_forum_delete', '_controller' => 'App\\Controller\\AdminController::forumDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        2106 => [[['_route' => 'admin_booking_statut', '_controller' => 'App\\Controller\\AdminController::bookingStatut'], ['id'], ['POST' => 0], null, false, false, null]],
        2121 => [[['_route' => 'admin_booking_delete', '_controller' => 'App\\Controller\\AdminController::bookingDelete'], ['id'], ['POST' => 0], null, false, false, null]],
        2150 => [[['_route' => 'admin_profils_show', '_controller' => 'App\\Controller\\ProfilVoyageurController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        2179 => [[['_route' => 'app_admin_promo_edit', '_controller' => 'App\\Controller\\PromoCodeController::edit'], ['id'], null, null, false, true, null]],
        2206 => [[['_route' => 'app_admin_promo_delete', '_controller' => 'App\\Controller\\PromoCodeController::delete'], ['id'], null, null, false, false, null]],
        2221 => [[['_route' => 'app_admin_promo_toggle', '_controller' => 'App\\Controller\\PromoCodeController::toggle'], ['id'], null, null, false, false, null]],
        2273 => [[['_route' => 'ajax_favorite_toggle', '_controller' => 'App\\Controller\\AjaxController::toggleFavorite'], ['type', 'id'], ['POST' => 0], null, false, true, null]],
        2302 => [[['_route' => 'ajax_favorite_hebergement', '_controller' => 'App\\Controller\\AjaxController::favoriteHebergement'], ['id'], ['POST' => 0], null, false, true, null]],
        2327 => [[['_route' => 'ajax_favorite_circuit', '_controller' => 'App\\Controller\\AjaxController::favoriteCircuit'], ['id'], ['POST' => 0], null, false, true, null]],
        2353 => [[['_route' => 'ajax_favorite_activity', '_controller' => 'App\\Controller\\AjaxController::favoriteActivity'], ['id'], ['POST' => 0], null, false, true, null]],
        2380 => [[['_route' => 'ajax_favorite_transport', '_controller' => 'App\\Controller\\AjaxController::favoriteTransport'], ['id'], ['POST' => 0], null, false, true, null]],
        2402 => [[['_route' => 'ajax_favorite_post', '_controller' => 'App\\Controller\\AjaxController::favoritePost'], ['id'], ['POST' => 0], null, false, true, null]],
        2440 => [[['_route' => 'ajax_forum_comments', '_controller' => 'App\\Controller\\ForumAjaxController::getComments'], ['postId'], ['GET' => 0], null, false, true, null]],
        2466 => [[['_route' => 'ajax_forum_comment_vote', '_controller' => 'App\\Controller\\ForumAjaxController::voteComment'], ['id'], ['POST' => 0], null, false, false, null]],
        2478 => [[['_route' => 'ajax_forum_comment_pin', '_controller' => 'App\\Controller\\ForumAjaxController::pinComment'], ['id'], ['POST' => 0], null, false, false, null]],
        2493 => [[['_route' => 'ajax_forum_comment_delete', '_controller' => 'App\\Controller\\ForumAjaxController::deleteComment'], ['id'], ['POST' => 0], null, false, false, null]],
        2525 => [[['_route' => 'ajax_forum_post_vote', '_controller' => 'App\\Controller\\ForumAjaxController::votePost'], ['id'], ['POST' => 0], null, false, false, null]],
        2539 => [[['_route' => 'ajax_forum_post_likes', '_controller' => 'App\\Controller\\ForumAjaxController::getPostLikes'], ['id'], ['GET' => 0], null, false, false, null]],
        2573 => [[['_route' => 'ajax_like', '_controller' => 'App\\Controller\\AjaxController::like'], ['type', 'id'], ['POST' => 0], null, false, true, null]],
        2614 => [[['_route' => 'ajax_refresh_rating', '_controller' => 'App\\Controller\\AjaxController::refreshRating'], ['type', 'id'], ['GET' => 0], null, false, true, null]],
        2653 => [[['_route' => 'qr_code_generate', '_controller' => 'Endroid\\QrCodeBundle\\Controller\\GenerateController'], ['builder', 'data'], null, null, false, true, null]],
        2693 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        2714 => [[['_route' => '_wdt', '_controller' => 'web_profiler.controller.profiler::toolbarAction'], ['token'], null, null, false, true, null]],
        2757 => [[['_route' => '_profiler_font', '_controller' => 'web_profiler.controller.profiler::fontAction'], ['fontName'], null, null, false, false, null]],
        2795 => [[['_route' => '_profiler_search_results', '_controller' => 'web_profiler.controller.profiler::searchResultsAction'], ['token'], null, null, false, false, null]],
        2810 => [[['_route' => '_profiler_router', '_controller' => 'web_profiler.controller.router::panelAction'], ['token'], null, null, false, false, null]],
        2831 => [[['_route' => '_profiler_exception', '_controller' => 'web_profiler.controller.exception_panel::body'], ['token'], null, null, false, false, null]],
        2845 => [[['_route' => '_profiler_exception_css', '_controller' => 'web_profiler.controller.exception_panel::stylesheet'], ['token'], null, null, false, false, null]],
        2856 => [[['_route' => '_profiler', '_controller' => 'web_profiler.controller.profiler::panelAction'], ['token'], null, null, false, true, null]],
        2889 => [[['_route' => 'circuit_show', '_controller' => 'App\\Controller\\CircuitController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        2907 => [[['_route' => 'circuit_pdf', '_controller' => 'App\\Controller\\CircuitController::downloadPdf'], ['id'], ['GET' => 0], null, false, false, null]],
        2930 => [[['_route' => 'circuit_reserver', '_controller' => 'App\\Controller\\CircuitController::reserver'], ['id'], ['POST' => 0], null, false, false, null]],
        2949 => [[['_route' => 'circuit_review', '_controller' => 'App\\Controller\\CircuitController::addReview'], ['id'], ['POST' => 0], null, false, false, null]],
        2975 => [[['_route' => 'payment_checkout', '_controller' => 'App\\Controller\\HebergementController::checkout'], ['id'], ['GET' => 0], null, false, true, null]],
        2993 => [[['_route' => 'payment_pay', '_controller' => 'App\\Controller\\HebergementController::processPayment'], ['id'], ['POST' => 0], null, false, false, null]],
        3022 => [[['_route' => 'forum_show', '_controller' => 'App\\Controller\\ForumController::show'], ['id'], ['GET' => 0, 'POST' => 1], null, false, true, null]],
        3051 => [[['_route' => 'forum_comment_pin', '_controller' => 'App\\Controller\\ForumController::pinComment'], ['id'], ['POST' => 0], null, false, false, null]],
        3091 => [[['_route' => 'forum_post_comments', '_controller' => 'App\\Controller\\ForumController::comments'], ['id', 'page'], ['GET' => 0], null, false, true, null]],
        3113 => [[['_route' => 'public_facture', '_controller' => 'App\\Controller\\InvoiceController::publicFacture'], ['id'], ['GET' => 0], null, false, true, null]],
        3144 => [[['_route' => 'hebergement_show', '_controller' => 'App\\Controller\\HebergementController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        3163 => [[['_route' => 'hebergement_avis', '_controller' => 'App\\Controller\\HebergementController::addAvis'], ['id'], ['POST' => 0], null, false, false, null]],
        3186 => [[['_route' => 'hebergement_reserver', '_controller' => 'App\\Controller\\HebergementController::reserver'], ['id'], ['POST' => 0], null, false, false, null]],
        3227 => [[['_route' => 'reservation_cancel', '_controller' => 'App\\Controller\\HebergementController::cancelReservation'], ['id'], ['POST' => 0], null, false, false, null]],
        3255 => [[['_route' => 'reservation_invoice', '_controller' => 'App\\Controller\\InvoiceController::generateInvoice'], ['id'], ['GET' => 0], null, false, false, null]],
        3272 => [[['_route' => 'reservation_invoice_preview', '_controller' => 'App\\Controller\\InvoiceController::previewInvoice'], ['id'], ['GET' => 0], null, false, false, null]],
        3309 => [[['_route' => 'app_forgot_password_reset', '_controller' => 'App\\Controller\\ResetPasswordController::reset'], ['token'], null, null, false, true, null]],
        3347 => [[['_route' => 'payment_receipt', '_controller' => 'App\\Controller\\PaymentController::getReceipt'], ['bookingId'], ['GET' => 0], null, false, true, null]],
        3380 => [[['_route' => 'payment_download_pdf', '_controller' => 'App\\Controller\\PaymentController::downloadPdf'], ['bookingId'], ['GET' => 0], null, false, true, null]],
        3402 => [[['_route' => 'payment_download_word', '_controller' => 'App\\Controller\\PaymentController::downloadWord'], ['bookingId'], ['GET' => 0], null, false, true, null]],
        3427 => [[['_route' => 'payment_qrcode', '_controller' => 'App\\Controller\\PaymentController::getQrCode'], ['bookingId'], ['GET' => 0], null, false, true, null]],
        3457 => [[['_route' => 'payment_confirmation', '_controller' => 'App\\Controller\\PaymentController::confirmation'], ['bookingId'], ['GET' => 0], null, false, true, null]],
        3495 => [[['_route' => 'transport_show', '_controller' => 'App\\Controller\\TransportController::show'], ['id'], ['GET' => 0], null, false, true, null]],
        3518 => [[['_route' => 'transport_book', '_controller' => 'App\\Controller\\TransportController::book'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        3556 => [[['_route' => 'transport_cancel_booking', '_controller' => 'App\\Controller\\TransportController::cancelBooking'], ['id'], ['POST' => 0], null, false, false, null]],
        3619 => [[['_route' => 'user_hebergement_cancel', '_controller' => 'App\\Controller\\UserSpaceController::cancelHebergementReservation'], ['id'], ['POST' => 0], null, false, false, null]],
        3653 => [[['_route' => 'user_activity_cancel', '_controller' => 'App\\Controller\\UserSpaceController::cancelActivityReservation'], ['id'], ['POST' => 0], null, false, false, null]],
        3686 => [[['_route' => 'user_circuit_cancel', '_controller' => 'App\\Controller\\UserSpaceController::cancelCircuitReservation'], ['id'], ['POST' => 0], null, false, false, null]],
        3734 => [[['_route' => 'user_hebergement_avis_delete', '_controller' => 'App\\Controller\\UserSpaceController::deleteHebergementAvis'], ['id'], ['POST' => 0], null, false, false, null]],
        3770 => [[['_route' => 'user_activity_avis_delete', '_controller' => 'App\\Controller\\UserSpaceController::deleteActivityAvis'], ['id'], ['POST' => 0], null, false, false, null]],
        3805 => [[['_route' => 'user_circuit_avis_delete', '_controller' => 'App\\Controller\\UserSpaceController::deleteCircuitAvis'], ['id'], ['POST' => 0], null, false, false, null]],
        3842 => [
            [['_route' => 'user_circuit_delete', '_controller' => 'App\\Controller\\UserSpaceController::deleteCircuit'], ['id'], ['POST' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
