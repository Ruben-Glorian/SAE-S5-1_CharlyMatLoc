<?php

use charlyMatLoc\src\api\actions\getCatalogueAction;

return [
    'getCatalogueActioCn' => function($container) {
        return new getCatalogueAction($container->get('serviceCatalogue'));
    },
];