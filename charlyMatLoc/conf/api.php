<?php

use charlyMatLoc\src\api\actions\getCatalogueAction;
use charlyMatLoc\src\api\actions\getCatalogueHtmlAction;

return [
    'getCatalogueActioCn' => function($container) {
        return new getCatalogueAction($container->get('serviceCatalogue'));
    },
    'getCatalogueHtmlAction' => function($container) {
        return new getCatalogueHtmlAction($container->get('serviceCatalogue'));
    },
];