<?php

use charlyMatLoc\src\api\actions\getCatalogueAction;

return [
    'getCatalogueAction' => function($container) {
        return new getCatalogueAction($container->get('serviceCatalogue'));
    },
];