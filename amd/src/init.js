define(['jquery', 'core/ajax', 'core/custom_interaction_events',
    'core/notification'], function ($, ajax, config, renderer) {

    const init = $(document).ready(() => {
        const URL = config.wwwroot + '/blocks/';

        const settings = {
            type: 'POST',
            dataType: 'json'
        };

        $.ajax(URL, settings).done(() => {
            console.log('success');
        });
    });

    return {
        init: init
    };
});