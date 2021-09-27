angular.module('siaApp').directive('btnSaveToCtrl', buttonSaveFunction);

buttonSaveFunction.$inject = ['$route', 'appService'];

function buttonSaveFunction(route, appService)
{
    return {
        restrict: 'A',
        link: link
    }

    function link(scope, element, attrs)
    {
        element.on('click', () => {
            const active_id = route.current.params.modelId;
            let data = scope.vm.data;
            data[attrs.btnSaveToCtrl] = active_id;

            appService.httpCall(`/${attrs.target}/add`, 'POST', data).then((response) => {
                route.reload();
            });
        });
    }
}