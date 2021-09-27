angular.module('siaApp').directive('btnPenolakan', btnPenolakanFunction);

btnPenolakanFunction.$inject = ['appService', '$route'];

function btnPenolakanFunction(appService, route)
{
    return {
        restrict: 'C',
        link: link
    }

    function link(scope, element, attrs)
    {
        element.on('click', () => {

            let data = {
                status: 'tolak',
                alasan: scope.vm.data.alasan
            };

            appService.httpCall(`/pengajuan_ks/update/${scope.$parent.vm.data.id}`, 'POST', data).then((response) => {
                route.reload();
            });
        });
    }
}