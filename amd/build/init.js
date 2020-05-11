define([
    'jquery',
    'core/ajax',
    'core/custom_interaction_events',
    'core_tutor/selectors',
], function (
    $,
    Ajax,
    CustomEvents,
    ItemSelectors
) {

    var init = function () {
        /**
         * Set the element state to loading.
         *
         * @param {object} root The container element
         * @method startLoading
         */
        var startLoading = function (root) {
            var loadingIconContainer = root.find(ItemSelectors.containers.loadingIcon);

            loadingIconContainer.removeClass('hidden');
        };

        /**
         * Remove the loading state from the element.
         *
         * @param {object} root The container element
         * @method stopLoading
         */
        var stopLoading = function (root) {
            var loadingIconContainer = root.find(ItemSelectors.containers.loadingIcon);

            loadingIconContainer.addClass('hidden');
        };
    };

    return {
        init: init
    };
});