angular.module('siaApp').directive('btnShowManyForm', btnShowManyFormFunction);

btnShowManyFormFunction.$inject = ['appService', '$compile'];

function btnShowManyFormFunction(appService, compile)
{
    return {
        restrict: 'A',
        link: link
    }

    function link(scope, element, attrs, ctrl)
    {   
        element.on('click', () => {
            appService.showModalOne2Many(scope, `/template/component/${attrs.btnShowManyForm}`, element);
        });
    }
}