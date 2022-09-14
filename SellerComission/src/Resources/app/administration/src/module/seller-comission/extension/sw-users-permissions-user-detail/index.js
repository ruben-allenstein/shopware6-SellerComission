import template from "./sw-users-permissions-user-detail.html.twig";

const {Component} = Shopware;

Component.override('sw-users-permissions-user-detail', {
    template,
    watch: {
        user() {
            if (this.user && !this.user.customFields) {
                this.user.customFields = {};
            }
        }
    }
});
