/**
 * Javascript used to save the user's tab preference.
 *
 * @package    block_tutor
 */

define(
    'block_tutor/tab_preferences',
    [
        'jquery',
        'core/ajax',
        'core/custom_interaction_events',
        'core/notification'
    ], function (
        $,
        Ajax,
        CustomEvents,
        Notification
    ) {

        /**
         * Registers an event that saves the user's tab preference when switching between them.
         *
         * @param {object} root The container element
         */
        var registerTutorEventListeners = function (root, type = null) {
            CustomEvents.define(root, [CustomEvents.events.activate]);
            root.on(CustomEvents.events.activate, "[data-toggle='tab']", function (e) {
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
                var request = {
                    methodname: 'core_user_update_user_preferences',
                    args: {
                        preferences: [
                            {
                                type: type,
                                value: tabname
                            }
                        ]
                    }
                };

                Ajax.call([request])[0]
                    .fail(Notification.exception);
            });
        };

        return {
            registerTutorEventListeners: registerTutorEventListeners
        };
    });
