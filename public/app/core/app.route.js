import { dataViewController, userFunction, dataFunction } from '../controllers/data.view.controller';
import { formViewController, optionFunction } from '../controllers/form.view.controller';

angular.module('siaApp').controller('formViewController', formViewController);
formViewController.$inject = ['pageData', '$routeParams', 'dataFactory', '$scope', 'option'];

angular.module('siaApp').controller('dataViewController', dataViewController);
dataViewController.$inject = ['pageData', 'dataFactory', '$scope', 'option'];

angular.module('siaApp').config(routeConfig);
routeConfig.$inject = ['$routeProvider'];

userFunction.$inject = ['dataFactory'];
dataFunction.$inject = ['dataFactory', '$route'];
optionFunction.$inject = ['dataFactory', '$route'];

function routeConfig($routeProvider)
{
    $routeProvider
    .when(
        '/:model',
        {
            templateUrl: (urlattr) => {
                return `/template/table/${urlattr.model}`;
            },
            controller: 'dataViewController',
            controllerAs: 'vm',
            resolve: {
                // user: userFunction,
                pageData: dataFunction,
                option: optionFunction
            }
        }
    )
    .when(
        '/:model/detail/:modelId',
        {
            templateUrl: (urlattr) => {
                return `/template/detail/${urlattr.model}`;
            },
            controller: 'dataViewController',
            controllerAs: 'vm',
            resolve: {
                // user: userFunction,
                pageData: dataFunction,
                option: optionFunction
            }
        }
    )
    .when(
        '/:model/:action',
        {
            templateUrl: (urlattr) => {
                return `/template/form/${urlattr.model}`;
            },
            controller: 'formViewController',
            controllerAs: 'vm',
            resolve: {
                // user: userFunction,
                pageData: dataFunction,
                option: optionFunction
            }
        }
    )
    .when(
        '/:model/:action/:modelId',
        {
            templateUrl: (urlattr) => {
                return `/template/form/${urlattr.model}`;
            },
            controller: 'formViewController',
            controllerAs: 'vm',
            resolve: {
                // user: userFunction,
                pageData: dataFunction,
                option: optionFunction
            }
        }
    )
    
    .otherwise('/home');
}
