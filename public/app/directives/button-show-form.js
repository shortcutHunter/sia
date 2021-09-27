angular.module('siaApp').directive('btnShowForm', buttonSaveFunction);

buttonSaveFunction.$inject = ['appService', '$compile'];

function buttonSaveFunction(appService, compile)
{
    return {
        restrict: 'A',
        link: link
    }

    function link(scope, element, attrs, ctrl)
    {   
        element.on('click', () => {
            appService.showCustomModal(scope, `/template/component/${attrs.btnShowForm}`, element);
        });
    }
}