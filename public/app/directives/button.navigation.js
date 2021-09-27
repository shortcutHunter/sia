angular.module('siaApp').directive('btnNavigation', btnNavigationFunction);

btnNavigationFunction.$inject = ['dataFactory'];

function btnNavigationFunction(dataFactory)
{
    return {
        restrict: 'C',
        link: link
    }

    function link(scope, element, attrs)
    {
        element.on('click', () => {
            dataFactory.active_parent = attrs.parentModel;
            dataFactory.active_model = attrs.model;
        });
    }
}