define(['jquery', 'core/ajax', 'core/custom_interaction_events',
    'core/notification'], function ($, Ajax, CustomEvents, Notification) {

    var init = function() {
        alert('Hi alone');
    };

    return {
        init: init
    };
});