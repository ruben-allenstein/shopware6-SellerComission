import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

import './extension/sw-users-permissions-user-detail';

Shopware.Module.register('sellerComission', {
    type: 'plugin',
    name: 'sellerComission',
    title: 'seller-comission.general.title',
    description: 'seller-comission.general.title',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },
});
