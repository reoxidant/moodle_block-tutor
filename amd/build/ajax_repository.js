/**
 * Javascript used to save the user's tab preference.
 *
 * @package    block_tutor
 */

define(
    [
        'jquery',
        'core/ajax'
    ], function (
        $,
        Ajax
    ) {
        /**
         * Registers an event that saves the user's tab preference when switching between them.
         *
         * @param {object} root The container element
         */

        var getContentData = function (root, type, tabname = "needgradingn") {
            var request = {
                methodname: "core_user_update_user_preferences",
                args: {
                    preferences: [
                        {
                            type: type,
                            value: tabname
                        }
                    ]
                }
            };
            return Ajax.call([request])[0];
        };

        return {
            getContentData: getContentData
        };
    });

