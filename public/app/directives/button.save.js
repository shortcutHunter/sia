angular.module('siaApp').directive('btnSave', buttonSaveFunction);

buttonSaveFunction.$inject = ['appService', '$location'];

function buttonSaveFunction(appService, location)
{
    return {
        restrict: 'C',
        link: link
    }

    function link(scope, element)
    {
        element.on('click', () => {
            const vm = scope.vm;
            const data = vm.data;
            let url = `/${vm.model}/`;

            if(data.id)
            {
                url += `update/${data.id}`;
            }else{
                url += 'add';
            }

            appService.httpCall(url, 'POST', data).then((response) => {
                const redirectUrl = `/${vm.model}/detail/${response.data.id}`;
                location.path(redirectUrl);
            });
        });
    }
}