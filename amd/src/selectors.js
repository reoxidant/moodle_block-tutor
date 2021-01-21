define([], function () {
    return {
        tabs: {
            navLinkShow: '.block_tutor .show',
            navLinkActive: ".block_tutor .active",
        },
        containers: {
            loadingIcon: '[data-region="overlay-icon-container"]',
        },
        tabSelector: {
            groupListDropDown: "#groupsdropdown + .dropdown-menu .dropdown-item",
            studentListDropDown: "#studentsdropdown + .dropdown-menu .dropdown-item",
            activeItemGroup: "#groupsdropdown + .dropdown-menu .active",
            activeItemStudent: "#studentsdropdown + .dropdown-menu .active",
            dropDownButton:"#dropdown-btn",
        }
    };
});
