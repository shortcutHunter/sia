angular.module('siaApp').directive('btnDelete', buttonSaveFunction);

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
            const url = `/${vm.model}/delete/${vm.data.id}`;
            appService.httpCall(url, 'POST').then((response) => {
                const redirectUrl = `/${vm.model}`;
                location.path(redirectUrl);
            });
        });
    }
}