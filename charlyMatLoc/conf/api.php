<?php

use charlyMatLoc\src\api\actions\getCatalogueAction;
use charlyMatLoc\webui\actions\getCatalogueViewAction;

return [
    'getCatalogueAction' => function($container) {
        return new getCatalogueAction($container->get('serviceCatalogue'));
    },
    'getCatalogueHtmlAction' => function($container) {
        return new getCatalogueViewAction($container->get('serviceCatalogue'));
    },
];