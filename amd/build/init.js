/**
 * Javascript used to save the user's tab preference.
 *
 * @package    block_tutor
 */

define([
    'jquery',
    'core/ajax',
    'core/custom_interaction_events',
    'core/notification',
    'core_tutor/selectors',
    'core_tutor/ajax_repository'
], function (
    $,
    Ajax,
    CustomEvents,
    Notification,
    ItemSelectors,
    AjaxRepository,
) {
    var startLoading = function (root) {
        var loadingIconContainer = root.find(ItemSelectors.containers.loadingIcon);
        loadingIconContainer.removeClass('hidden');
    };

    var stopLoading = function (root) {
        var loadingIconContainer = root.find(ItemSelectors.containers.loadingIcon);
        loadingIconContainer.addClass('hidden');
    };

    var registerEventListeners = function (root, type = null) {
        root = $(root);

        // Bind click events to event links.
        root.on(CustomEvents.events.activate, "[data-toggle='tab']", function (e) {
            var tabname = $(e.currentTarget).data('tabname');
            // Bootstrap does not change the URL when using BS tabs, so need to do this here.
            // Also check to make sure the browser supports the history API.
            if (type == 'stidentlist') {
                type = 'block_tutor_studentlist_tab';
            } else {
                type = 'block_tutor_last_tab';
                if (typeof window.history.pushState === "function") {
                    window.history.pushState(null, null, '?tutortab=' + tabname);
                }
            }
            return LoadTabContent(root, type, tabname);
        });
    };

    var LoadTabContent = function (root, type, tabname) {
        startLoading(root);

        return AjaxRepository.getContentData(root, type, tabname)
            .always(function () {
                M.util.js_complete([root.get('id'), type, tabname].join('-'));
                return stopLoading(root);
            })
            .fail(Notification.exception);
    };

    return {
        init: registerEventListeners
    };
});