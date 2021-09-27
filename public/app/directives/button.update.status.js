angular.module('siaApp').directive('btnUpdateStatus', buttonSaveFunction);

buttonSaveFunction.$inject = ['appService', '$route'];

function buttonSaveFunction(appService, route)
{
    return {
        restrict: 'A',
        link: link
    }

    function link(scope, element, attrs)
    {
        element.on('click', () => {

            const vm = scope.vm;
            let url = `/${attrs.table ? attrs.table : vm.model}/update`;
            if(attrs.targetId)
            {
                url = `${url}/${attrs.targetId}`;
            }else{
                url = `${url}/${scope.vm.data.id}`;
            }

            const data_to_send = {status: attrs.btnUpdateStatus};

            appService.httpCall(url, 'POST', data_to_send).then((response) => {
                route.reload();
            });
        });
    }
}