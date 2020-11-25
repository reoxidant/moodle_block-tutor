/**
 * Javascript used to save the user's tab preference.
 *
 * @package block_tutor
 *
 */

define([
    'jquery',
    'core/ajax',
    'core/custom_interaction_events',
    'core/templates',
    'core/notification',
    'block_tutor/selectors',
    'block_tutor/ajax_repository'
], function (
    $,
    Ajax,
    CustomEvents,
    Templates,
    Notification,
    ItemSelectors,
    AjaxRepository
) {
    var startLoading = function (root) {
        var loadingIconContainer = root.find(ItemSelectors.containers.loadingIcon);
        loadingIconContainer.removeClass('hidden');
    };

    var stopLoading = function (root) {
        var loadingIconContainer = root.find(ItemSelectors.containers.loadingIcon);
        loadingIconContainer.addClass('hidden');
    };

    var registerEventListeners = function (root, type = null, template = "block_tutor/main") {
        root = $(root);
        startLoading(root);

        // Bind click events to event links.
        root.on(CustomEvents.events.activate, "[data-toggle='tab']", function (e) {
            startLoading(root);
            var tabname = $(e.currentTarget).data('tabname');
            // Bootstrap does not change the URL when using BS tabs, so need to do this here.
            // Also check to make sure the browser supports the history API.
            if (type == 'studentlist') {
                type = 'block_tutor_studentlist_tab';
            } else {
                type = 'block_tutor_last_tab';
                if (typeof window.history.pushState === "function") {
                    window.history.pushState(null, null, '?tutortab=' + tabname);
                }
            }
            return LoadTabContent(root, type, tabname);
        });

        root.on('click', ItemSelectors.tabSelector.groupListDropDown, function (e) {
            startLoading(root);

            setTimeout(function () {
                var groupId = root.find(ItemSelectors.tabSelector.activeItemGroup)[0].dataset.group;

                $.ajax({
                    type: "POST",
                    data: {selectList: "grouplist", groupId: groupId},
                    url: location.origin + "/blocks/tutor/ajax.php",
                    beforeSend: function () {
                        startLoading(root);
                    },
                    complete: function () {
                        stopLoading(root);
                    },
                    cache: "false",
                    error: function () {
                        Notification.addNotification({
                            message: "Ошибка при вызове группы, похлже такого преподавателя несуществует",
                            type: "error"
                        });
                    }
                });
            }, 500);
        });

        root.on('click', ItemSelectors.tabSelector.studentListDropDown, function (e) {
            startLoading(root);

            setTimeout(function () {
                var studentId = root.find(ItemSelectors.tabSelector.activeItemStudent)[0].dataset.student;

                $.ajax({
                    type: "POST",
                    data: {selectList: "studentlist", studentId: studentId},
                    url: location.origin + "/blocks/tutor/ajax.php",
                    beforeSend: function () {
                        startLoading(root);
                    },
                    complete: function () {
                        stopLoading(root);
                    },
                    cache: "false",
                    error: function () {
                        Notification.addNotification({
                            message: "Ошибка при выборе студента, похоже такого студента несуществует",
                            type: "error"
                        });
                    }
                });
            }, 500);
        });

        return AjaxRepository.getContentData(root, type)
            .always(function () {
                return stopLoading(root);
            })
            .fail(Notification.exception);
    };

    var LoadTabContent = function (root, type, tabname) {
        return AjaxRepository.getContentData(root, type, tabname)
            .done(function () {
                return stopLoading(root);
            })
            .fail(Notification.exception);
    };

    return {
        registerEventListeners: registerEventListeners
    };
});