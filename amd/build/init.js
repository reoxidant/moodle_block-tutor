define(['jquery', 'core/ajax', 'core/custom_interaction_events',
    'core/notification'], function ($, Ajax, CustomEvents, Notification) {

    var init = function () {
        $(window.document).ready(function () {
            console.log('Hello Admin');
        });
    };

    return {
        init: init
    };
});