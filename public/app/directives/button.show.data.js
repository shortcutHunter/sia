angular.module('siaApp').directive('btnShowData', btnShowDataFunction);

btnShowDataFunction.$inject = ['$route', 'appService'];

function btnShowDataFunction(route, appService)
{
    return {
        restrict: 'A',
        link: link,
        scope: {
            data: '='
        }
    }

    function link(scope, element, attrs)
    {
        element.on('click', () => {
            appService.showModalWithData(scope, `/template/component/${attrs.viewName}`, element, attrs, scope.data);
        });
    }
}