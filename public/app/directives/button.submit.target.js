angular.module('siaApp').directive('btnSubmitTarget', btnSubmitTargetFunction);

btnSubmitTargetFunction.$inject = ['$route', 'appService'];

function btnSubmitTargetFunction(route, appService)
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
            data[attrs.activeIdName] = active_id;

            console.log(data);

            // appService.httpCall(attrs.target, 'POST', data).then((response) => {
            //     route.reload();
            // });
        });
    }
}